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
namespace Buckaroo\Enums;

/**
 * Class PaymentStatus
 * @package Buckaroo\Enums
 */
class PaymentStatus
{
    // The transaction has succeeded and the payment has been received/approved.
    const SUCCESS = 190;

    // The transaction has failed.
    const FAILED = 490;

    // The transaction request contained errors and could not be processed correctly
    const VALIDATION_FAILURE = 491;

    // Some technical failure prevented the completion of the transactions
    const TECHNICAL_FAILURE = 492;

    // The transaction was cancelled by the customer.
    const CANCELLED_BY_USER = 890;

    // The merchant cancelled the transaction.
    const CANCELLED_BY_MERCHANT = 891;

    // The transaction has been rejected by the (third party) payment provider.
    const REJECTED = 690;

    // The transaction is on hold while the payment engine is waiting on further input from the consumer.
    const PENDING_INPUT = 790;

    // The transaction is being processed.
    const PENDING_PROCESSING = 791;

    // The Payment Engine is waiting for the consumer to return from a 3rd party website to complete the transaction.
    const AWAITING_CONSUMER = 792;
}
