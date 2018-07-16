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
namespace Buckaroo\Requests;

use Buckaroo\Authentication\Authenticator;
use Buckaroo\Exceptions\RestRequestException;
use Buckaroo\Result\TransactionSpecificationResult;
use Buckaroo\Services\Pay\AbstractPayService;

/**
 * Class TransactionSpecificationRequest
 * @package Buckaroo\Requests
 */
class TransactionSpecificationRequest implements ITransactionRequest
{
    const API_URL = 'json/transaction/specification';

    /**
     * @var Authenticator
     */
    private $authenticator;

    /**
     * @var AbstractPayService
     */
    private $service;

    /**
     * @var string
     */
    private $endpoint;

    /**
     * TransactionRequest constructor.
     * @param Authenticator $authenticator
     * @param AbstractPayService $service
     * @param string $endpoint
     */
    public function __construct(Authenticator $authenticator, AbstractPayService $service, string $endpoint)
    {
        $this->authenticator = $authenticator;
        $this->service = $service;
        $this->endpoint = $endpoint;
    }

    /**
     * Perform the request
     * @return TransactionSpecificationResult
     * @throws RestRequestException
     * @throws \Buckaroo\Exceptions\InvalidHttpMethodException
     * @throws \Buckaroo\Exceptions\UnsupportedHttpMethodException
     */
    public function request(): TransactionSpecificationResult
    {
        $url = $this->endpoint . $this::API_URL . '/' . $this->service->getName();
        $headers = ['authorization' => $this->authenticator->getAuthenticationHeader('', $url, 'GET')];
        $request = (new \GuzzleHttp\Client)->get($url, ['headers' => $headers, 'verify' => false]);

        if ($request->getStatusCode() !== 200) {
            throw new RestRequestException((string)$request->getStatusCode() . ' ' . $request->getReasonPhrase());
        }

        $requestData = json_decode($request->getBody()->getContents(), true);
        return new TransactionSpecificationResult($requestData);
    }
}
