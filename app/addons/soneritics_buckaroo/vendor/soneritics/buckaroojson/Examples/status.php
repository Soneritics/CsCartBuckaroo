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

$transactionKey = ''; // Your transaction key here

$authentication = new \Buckaroo\Authentication\Authentication($secretKey, $websiteKey);
$buckaroo = new \Buckaroo\Buckaroo($authentication, true);
$transactionStatusRequest = $buckaroo->getTransactionStatusRequest($transactionKey)->request();

print_r($transactionStatusRequest);
echo "This transaction is a " . ((bool)$transactionStatusRequest['IsTest'] ? 'TEST' : 'normal') . " transaction." . PHP_EOL;
echo "Status: " . $transactionStatusRequest['Status']['Code']['Code'] . " ({$transactionStatusRequest['Status']['Code']['Description']})" . PHP_EOL;
echo "Check statuses in the Enums\PaymentStatus class." . PHP_EOL;

if ($transactionStatusRequest['Status']['Code']['Code'] == \Buckaroo\Enums\PaymentStatus::SUCCESS) {
    echo 'Order is paid!' . PHP_EOL;
}
