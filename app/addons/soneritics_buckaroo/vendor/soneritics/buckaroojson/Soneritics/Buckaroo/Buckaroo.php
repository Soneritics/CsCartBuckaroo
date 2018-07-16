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
namespace Buckaroo;

use Buckaroo\Authentication\Authentication;
use Buckaroo\Authentication\Authenticator;
use Buckaroo\Requests\MultiServiceTransactionRequest;
use Buckaroo\Requests\TransactionRequest;
use Buckaroo\Requests\TransactionSpecificationRequest;
use Buckaroo\Requests\TransactionStatusRequest;
use Buckaroo\Services\AbstractService;
use Buckaroo\Services\Pay\AbstractPayService;

/**
 * Class Buckaroo
 * @package Buckaroo
 */
class Buckaroo
{
    /**
     * Authentication data
     * @var Authentication
     */
    private $authentication;

    /**
     * Test mode
     * @var bool
     */
    private $test;

    /**
     * Buckaroo constructor.
     * @param Authentication $authentication
     * @param bool $test
     */
    public function __construct(Authentication $authentication, bool $test = true)
    {
        $this->authentication = $authentication;
        $this->test = $test;
    }

    /**
     * Get the TransactionRequest
     * @param AbstractService $service
     * @return TransactionRequest
     */
    public function getTransactionRequest(AbstractService $service): TransactionRequest
    {
        return new TransactionRequest(new Authenticator($this->authentication), $service, $this->getEndpoint());
    }

    /**
     * Get the TransactionRequest for multiple service requests, where a user gets a page to choose one
     * @return MultiServiceTransactionRequest
     */
    public function getMultiServiceTransactionRequest(): MultiServiceTransactionRequest
    {
        return new MultiServiceTransactionRequest(new Authenticator($this->authentication), $this->getEndpoint());
    }

    /**
     * Get the TransactionStatusRequest
     * @param string $code
     * @return TransactionStatusRequest
     */
    public function getTransactionStatusRequest(string $code): TransactionStatusRequest
    {
        return new TransactionStatusRequest(new Authenticator($this->authentication), $code, $this->getEndpoint());
    }

    /**
     * TransactionSpecificationRequest
     * @param AbstractPayService $service
     * @return TransactionSpecificationRequest
     */
    public function getTransactionSpecificationRequest(AbstractPayService $service): TransactionSpecificationRequest
    {
        return new TransactionSpecificationRequest(
            new Authenticator($this->authentication),
            $service,
            $this->getEndpoint()
        );
    }

    /**
     * Get the endpoint of the API based on the mode (test or production)
     * @return string
     */
    private function getEndpoint(): string
    {
        return $this->test ? 'https://testcheckout.buckaroo.nl/' : 'https://checkout.buckaroo.nl/';
    }
}
