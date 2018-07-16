<?php
/*
 * The MIT License
 *
 * Copyright 2018 Jordi Jolink.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 */
if (!defined('BOOTSTRAP')) { die('Access denied'); }

/**
 * Get the current iDeal issuers list
 * @return array
 * @throws \Tygh\Exceptions\DeveloperException
 */
function fn_soneritics_buckaroo_get_ideal_issuers()
{
    $settings = new SoneriticsBuckarooSettings;
    $cacheKey = 'soneritics_buckaroo_ideal_cache';
    \Tygh\Registry::registerCache($cacheKey, 3600 * 24, \Tygh\Registry::cacheLevel('time'), true);

    if ($settings->isTestMode() || !\Tygh\Registry::isExist($cacheKey)) {
        $authentication = new \Buckaroo\Authentication\Authentication($settings->getSecretKey(), $settings->getWebsiteKey());
        $buckaroo = new \Buckaroo\Buckaroo($authentication, $settings->isTestMode());

        try {
            $issuers = $buckaroo
                ->getTransactionSpecificationRequest(new \Buckaroo\Services\Pay\iDeal)
                ->request()
                ->getActions()['Pay']
                ->getRequestParameters()['issuer']->getListItemDescriptions();

            \Tygh\Registry::set($cacheKey, $issuers);
        } catch (Exception $e) {
            fn_set_notification('E', 'Error fetching banks', 'An error occured fetching the iDeal bank list.');
        }
    }

    return \Tygh\Registry::get($cacheKey);
}

/**
 * Start a payment
 * @param \Buckaroo\Services\Pay\AbstractPayService $service
 * @param array $orderInfo
 */
function fn_soneritics_buckaroo_start_payment(\Buckaroo\Services\Pay\AbstractPayService $service, array $orderInfo)
{
    $settings = new SoneriticsBuckarooSettings;
    $authentication = new \Buckaroo\Authentication\Authentication($settings->getSecretKey(), $settings->getWebsiteKey());
    $buckaroo = new \Buckaroo\Buckaroo($authentication, $settings->isTestMode());

    try {
        $orderId = (bool)$orderInfo['repaid'] ?
            $orderInfo['order_id'] . '_' . $orderInfo['repaid'] :
            $orderInfo['order_id'];

        $transactionRequest = $buckaroo->getTransactionRequest($service)
            ->setAmountDebit($orderInfo['total'])
            ->setInvoice($orderId)
            ->setReturnURL(fn_url("payment_notification.soneritics_buckaroo?order_id=" . $orderInfo['order_id']))
            ->request();

        // Save the transaction key for later use
        $transactionKey = $transactionRequest['Key'];
        $invoice = $orderId;
        db_query(
            "REPLACE INTO ?:soneritics_buckaroo(order_id, transactionkey, invoice) VALUES(?i, ?s, ?s)",
            $orderInfo['order_id'],
            $transactionKey,
            $invoice
        );

        // If the user should be redirected to Buckaroo, do so
        if (!empty($transactionRequest['RequiredAction']['RedirectURL'])) {
            header('Location: ' . $transactionRequest['RequiredAction']['RedirectURL']);
            die;
        }

        // If the response is already received, process it
        //fn_soneritics_buckaroo_validate_response($transactionRequest, $orderInfo['total'], $orderInfo['total'], $invoice);
        fn_soneritics_buckaroo_validate_payment((int)$orderInfo['order_id']);
    } catch (Exception $e) {
        fn_set_notification('E', 'Error starting payment', 'The payment could not be started.');
        fn_set_notification('I', $e->getMessage(), $e->getTraceAsString());
    }
}

/**
 * Validate the payment of a certain order
 * Uses the fn_soneritics_buckaroo_validate_response function to validate a response
 * @param int $orderId
 */
function fn_soneritics_buckaroo_validate_payment(int $orderId)
{
    $orderInfo = fn_get_order_info($orderId);

    // If already sent or paid, do not change the state
    if (in_array($orderInfo['status'], ['C', 'P'])) {
        fn_order_placement_routines('route', $orderId);
        return;
    }

    $paymentData = db_get_array("SELECT * FROM ?:soneritics_buckaroo WHERE order_id = ?i", $orderId);

    if (empty($orderInfo)) {
        fn_set_notification('E', 'No order info', 'Order info could not be found');
    } elseif (empty($paymentData)) {
        fn_set_notification('E', 'No payment data', 'Payment data could not be found');
    } else {
        $settings = new SoneriticsBuckarooSettings;
        $authentication = new \Buckaroo\Authentication\Authentication($settings->getSecretKey(), $settings->getWebsiteKey());
        $buckaroo = new \Buckaroo\Buckaroo($authentication, $settings->isTestMode());

        $result = false;
        foreach ($paymentData as $payment) {
            try {
                $transactionKey = $payment['transactionkey'];
                $invoice = $payment['invoice'];
                $transactionStatusResponse = $buckaroo->getTransactionStatusRequest($transactionKey)->request();

                $paidOrPending = fn_soneritics_buckaroo_validate_response(
                    $transactionStatusResponse,
                    $orderId,
                    $orderInfo['total'],
                    $invoice
                );

                if ($paidOrPending) {
                    $result = true;
                }
            } catch (Exception $e) {
                fn_set_notification('W', 'Error fetching details', 'Could not get payment details');
            }
        }

        if ($result === false) {
            fn_set_notification('E', 'Unknown error', 'An unknown error occured');
        }

        fn_order_placement_routines('route', $orderId);
    }
}

/**
 * Validate a response based on the payment data
 * @param array $response
 * @param int $orderId
 * @param $amount
 * @param $invoiceNr
 * @return bool
 */
function fn_soneritics_buckaroo_validate_response(array $response, int $orderId, $amount, $invoiceNr): bool
{
    if (empty($response['Invoice']) ||
        empty($response['AmountDebit']) ||
        empty($response['Status']['Code']['Code'])
    ) {
        fn_set_notification('W', 'Could not process', 'Could not process payment details');
        return false;
    } else {
        if ($response['Invoice'] != $invoiceNr || $response['AmountDebit'] != $amount) {
            fn_set_notification('E', 'Error checking payment', 'Invoice nr does not match!');
            return false;
        } else {
            $statusCode = (int)$response['Status']['Code']['Code'];
            $statusDescription = $response['Status']['SubCode']['Description'] ?? '';
            fn_soneritics_buckaroo_set_order_state($orderId, $statusCode, $statusDescription);

            return in_array(
                $statusCode,
                [\Buckaroo\Enums\PaymentStatus::SUCCESS, \Buckaroo\Enums\PaymentStatus::PENDING_PROCESSING]
            );
        }
    }
}

/**
 * Process an order's state
 * The order and the request must have been validated already
 * @param int $orderId
 * @param int $statusCode
 * @param string $description
 */
function fn_soneritics_buckaroo_set_order_state(int $orderId, int $statusCode, string $description)
{
    $csCartOrderState = '';
    switch ($statusCode) {
        case \Buckaroo\Enums\PaymentStatus::SUCCESS:
            $csCartOrderState = 'P';
            break;

        case \Buckaroo\Enums\PaymentStatus::PENDING_PROCESSING:
        case \Buckaroo\Enums\PaymentStatus::AWAITING_CONSUMER:
        case \Buckaroo\Enums\PaymentStatus::PENDING_INPUT:
            $csCartOrderState = 'O';
            break;

        case \Buckaroo\Enums\PaymentStatus::CANCELLED_BY_MERCHANT:
        case \Buckaroo\Enums\PaymentStatus::CANCELLED_BY_USER:
        $csCartOrderState = 'I';
            break;

        // Default state does not have to be updated
        default:
            return;
    }

    $ppResponse = [
        'reason_text' => $description,
        'order_status' => $csCartOrderState
    ];

    fn_change_order_status($orderId, $csCartOrderState, '', false);
    fn_finish_payment($orderId, $ppResponse);
}

/**
 * Check if a phone number is valid
 * @param string $phoneNrRaw
 * @return bool
 */
function fn_soneritics_buckaroo_is_valid_phonenumber(string $phoneNrRaw): bool
{
    return strlen($phoneNrRaw) >= 8;
}

/**
 * Get a phone number to use with Afterpay
 * @param string $rawPhoneNr
 * @return string
 */
function fn_soneritics_buckaroo_get_afterpay_phonenumber(string $rawPhoneNr): string
{
    return str_replace('.', '', str_replace('+', '00', str_replace(' ', '', trim($rawPhoneNr))));
}

/**
 * Get an AfterpayDigiAccept object from an order info array
 * @param array $orderInfo
 * @return \Buckaroo\Services\Pay\AfterpayDigiAccept
 */
function fn_soneritics_buckaroo_get_afterpay_service(array $orderInfo): \Buckaroo\Services\Pay\AfterpayDigiAccept
{
    $result = new \Buckaroo\Services\Pay\AfterpayDigiAccept;

    $dobDay = str_pad($orderInfo['payment_info']['date_of_birth_raw']['day'], 2, '0', STR_PAD_LEFT);
    $dobMonth = str_pad($orderInfo['payment_info']['date_of_birth_raw']['month'], 2, '0', STR_PAD_LEFT);
    $dobYear = $orderInfo['payment_info']['date_of_birth_raw']['year'];

    $result
        ->setAccept(true)
        ->setBillingBirthDate(new \DateTime("{$dobDay}-{$dobMonth}-{$dobYear} 12:00:00"))
        ->setBillingInitials(strtoupper($orderInfo['b_firstname'][0]))
        ->setBillingLastName($orderInfo['b_lastname'])
        ->setBillingGender((int)$orderInfo['payment_info']['gender'])
        ->setBillingStreet($orderInfo['b_address'])
        ->setBillingHouseNumber((int)$orderInfo['b_address_2'])
        ->setBillingPostalCode($orderInfo['b_zipcode'])
        ->setBillingCity($orderInfo['b_city'])
        ->setBillingCountry($orderInfo['b_country'])
        ->setBillingLanguage('nl')
        ->setBillingPhoneNumber(fn_soneritics_buckaroo_get_afterpay_phonenumber($orderInfo['payment_info']['afterpay_phone_number']))
        ->setBillingEmail($orderInfo['email'])
        ->setCustomerIPAddress($orderInfo['ip_address'])
        ->setShippingCosts($orderInfo['shipping_cost'] + $orderInfo['payment_surcharge']);

    if (!is_numeric($orderInfo['b_address_2'])) {
        $suffix = substr($orderInfo['b_address_2'], strlen((int)$orderInfo['b_address_2']));
        $result->setBillingHouseNumberSuffix($suffix);
    }

    if ($orderInfo['b_zipcode'] != $orderInfo['s_zipcode'] || $orderInfo['b_address_2'] != $orderInfo['s_address_2']) {
        $result
            ->setAddressesDiffer(true)
            ->setShippingBirthDate(new \DateTime("{$dobDay}-{$dobMonth}-{$dobYear} 12:00:00"))
            ->setShippingInitials(strtoupper($orderInfo['s_firstname'][0]))
            ->setShippingLastName($orderInfo['s_lastname'])
            ->setShippingGender((int)$orderInfo['payment_info']['gender'])
            ->setShippingStreet($orderInfo['s_address'])
            ->setShippingHouseNumber((int)$orderInfo['s_address_2'])
            ->setShippingPostalCode($orderInfo['s_zipcode'])
            ->setShippingCity($orderInfo['s_city'])
            ->setShippingCountryCode($orderInfo['s_country'])
            ->setShippingLanguage('nl')
            ->setShippingPhoneNumber(fn_soneritics_buckaroo_get_afterpay_phonenumber($orderInfo['payment_info']['afterpay_phone_number']))
            ->setShippingEmail($orderInfo['email']);

        if (!is_numeric($orderInfo['s_address_2'])) {
            $suffix = substr($orderInfo['s_address_2'], strlen((int)$orderInfo['s_address_2']));
            $result->setShippingHouseNumberSuffix($suffix);
        }
    }

    foreach ($orderInfo['products'] as $product) {
        $result->addArticle(
            $product['product_code'],
            $product['product'],
            (int)$product['amount'],
            round($product['price'], 2)
        );
    }

    return $result;
}
