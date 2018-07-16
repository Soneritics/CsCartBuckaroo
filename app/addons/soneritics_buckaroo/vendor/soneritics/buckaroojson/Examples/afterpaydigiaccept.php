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
require_once '_require.php';
require_once '_keys.php';

$authentication = new \Buckaroo\Authentication\Authentication($secretKey, $websiteKey);
$buckaroo = new \Buckaroo\Buckaroo($authentication, true);

$afterpayPayService = (new \Buckaroo\Services\Pay\AfterpayDigiAccept)
    ->setBillingBirthDate(new \DateTime('13-04-1986 12:00:00'))
    ->setBillingInitials('J')
    ->setBillingLastName('Jolink')
    ->setBillingGender(\Buckaroo\Enums\Gender::MALE)
    ->setBillingStreet('Teststraat')
    ->setBillingHouseNumber(1)
    ->setBillingPostalCode('1234AA')
    ->setBillingCity('Amsterdam')
    ->setBillingCountry('NL')
    ->setBillingLanguage('nl')
    ->setBillingPhoneNumber('0612345678')
    ->setBillingEmail('jordi@soneritics.nl')
    ->setShippingCosts(10)
    ->addArticle('ABC-001', 'Test product', 2, 20);

$afterpayTransactionRequest = $buckaroo->getTransactionRequest($afterpayPayService)
    ->setAmountDebit(50)
    ->setInvoice('ap-111')
    ->request();

print_r($afterpayTransactionRequest);

echo "Your transaction has the key: {$afterpayTransactionRequest['Key']}" . PHP_EOL;
echo "You should go to the following URL to complete the payment: " . PHP_EOL;
echo $afterpayTransactionRequest['RequiredAction']['RedirectURL'] . PHP_EOL;
