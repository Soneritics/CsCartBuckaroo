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
/**
 * @var array $order_info
 */
if (!defined('BOOTSTRAP')) { die('Access denied'); }

if (defined('PAYMENT_NOTIFICATION')) {
    fn_soneritics_buckaroo_validate_payment((int)$_GET['order_id']);
    die;
} else {
    if (empty($order_info['payment_info']['accepted_terms'])) {
        fn_set_notification('E', 'Terms not accepted', 'Accept the terms!');
        return false;
    } elseif (!fn_soneritics_buckaroo_is_valid_phonenumber($order_info['payment_info']['afterpay_phone_number'])) {
        fn_set_notification('E', 'Terms not accepted', 'Accept the terms!');
        return false;
    } else {
        $service = fn_soneritics_buckaroo_get_afterpay_service($order_info);
        fn_soneritics_buckaroo_start_payment($service, $order_info);
    }
}
