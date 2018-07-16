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
use Buckaroo\Services\AbstractService;

/**
 * Class MultiServiceTransactionRequest
 * @package Buckaroo\Requests
 */
class MultiServiceTransactionRequest extends TransactionRequest
{
    /**
     * @var array
     */
    private $serviceNames = [];

    /**
     * TransactionRequest constructor.
     * @param Authenticator $authenticator
     * @param string $endpoint
     */
    public function __construct(Authenticator $authenticator, string $endpoint)
    {
        parent::__construct($authenticator, null, $endpoint);
    }

    /**
     * Generate the call's payload
     * @return array
     */
    protected function getPayload(): array
    {
        $payload = parent::getPayload();

        $payload['ContinueOnIncomplete'] = 'RedirectToHTML';
        $payload['ServicesSelectableByClient'] = implode(',', $this->serviceNames);

        return $payload;
    }

    /**
     * Add a service
     * @param AbstractService $service
     * @return MultiServiceTransactionRequest
     */
    public function addService(AbstractService $service): MultiServiceTransactionRequest
    {
        $name = $service->getName();
        $this->serviceNames[$name] = $name;
        return $this;
    }
}
