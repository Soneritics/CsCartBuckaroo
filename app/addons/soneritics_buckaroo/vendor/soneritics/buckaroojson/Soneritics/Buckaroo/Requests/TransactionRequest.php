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
use Buckaroo\Exceptions\MissingParameterException;
use Buckaroo\Exceptions\RestRequestException;
use Buckaroo\Exceptions\WrongParameterCombinationException;
use Buckaroo\Services\AbstractService;

/**
 * Class TransactionRequest
 * @package Buckaroo\Requests
 */
class TransactionRequest implements ITransactionRequest
{
    const API_URL = 'json/Transaction';

    /**
     * @var Authenticator
     */
    private $authenticator;

    /**
     * @var AbstractService
     */
    private $service;

    /**
     * @var string
     */
    private $endpoint;

    /**
     * @var string
     */
    private $currency = 'EUR';

    /**
     * @var double
     */
    private $amountDebit;

    /**
     * @var double
     */
    private $amountCredit;

    /**
     * @var string
     */
    private $invoice;

    /**
     * @var string
     */
    private $order;

    /**
     * @var string
     */
    private $description;

    /**
     * @var string
     */
    private $returnURL;

    /**
     * @var string
     */
    private $returnURLCancel;

    /**
     * @var string
     */
    private $returnURLError;

    /**
     * @var string
     */
    private $returnURLReject;

    /**
     * @var string
     */
    private $originalTransactionKey;

    /**
     * @var bool
     */
    private $startRecurrent = false;

    /**
     * @var string
     */
    private $pushURL;

    /**
     * @var string
     */
    private $PushURLFailure;

    /**
     * TransactionRequest constructor.
     * @param Authenticator $authenticator
     * @param AbstractService|null $service
     * @param string $endpoint
     */
    public function __construct(Authenticator $authenticator, ?AbstractService $service, string $endpoint)
    {
        $this->authenticator = $authenticator;
        $this->service = $service;
        $this->endpoint = $endpoint;
    }

    /**
     * Perform the request
     * @return array
     * @throws \Buckaroo\Exceptions\InvalidHttpMethodException
     * @throws \Buckaroo\Exceptions\MissingParameterException
     * @throws \Buckaroo\Exceptions\UnsupportedHttpMethodException
     * @throws WrongParameterCombinationException
     * @throws RestRequestException
     */
    public function request(): array
    {
        // First validate local
        $this->validate();

        // Perform the request
        $data = $this->getPayload();
        $headers = ['authorization' => $this->authenticator->getAuthenticationHeader(
            json_encode($data),
            $this->endpoint . $this::API_URL
        )];

        return $this->performActualRequest(
            $this->endpoint . $this::API_URL,
            $data,
            $headers
        );
    }

    /**
     * Perform the actual HTTP request
     * @param string $url
     * @param array $data
     * @param array $headers
     * @return array
     * @throws RestRequestException
     */
    private function performActualRequest(string $url, array $data, array $headers = []): array
    {
        $request = (new \GuzzleHttp\Client)->post($url, ['json' => $data, 'headers' => $headers, 'verify' => false]);

        if ($request->getStatusCode() !== 200) {
            throw new RestRequestException((string)$request->getStatusCode() . ' ' . $request->getReasonPhrase());
        }

        return json_decode($request->getBody()->getContents(), true);
    }

    /**
     * The currency for the transaction
     * @param string $currency
     * @return TransactionRequest
     */
    public function setCurrency(string $currency): TransactionRequest
    {
        $this->currency = $currency;
        return $this;
    }

    /**
     * The transaction debit amount (Either this or AmountCredit is required)
     * @param decimal $amountDebit
     * @return TransactionRequest
     */
    public function setAmountDebit($amountDebit): TransactionRequest
    {
        $this->amountDebit = $amountDebit;
        return $this;
    }

    /**
     * The transaction credit amount (Either this or AmountDebit is required)
     * @param decimal $amountCredit
     * @return TransactionRequest
     */
    public function setAmountCredit($amountCredit): TransactionRequest
    {
        $this->amountCredit = $amountCredit;
        return $this;
    }

    /**
     * The invoice number for the transaction
     * @param string $invoice
     * @return TransactionRequest
     */
    public function setInvoice(string $invoice): TransactionRequest
    {
        $this->invoice = $invoice;
        return $this;
    }

    /**
     * The order number for the transaction
     * @param string $order
     * @return TransactionRequest
     */
    public function setOrder(string $order): TransactionRequest
    {
        $this->order = $order;
        return $this;
    }

    /**
     * The description for the transaction
     * @param string $description
     * @return TransactionRequest
     */
    public function setDescription(string $description): TransactionRequest
    {
        $this->description = $description;
        return $this;
    }

    /**
     * The url in the webshop where the customer should return if the transaction requires the customer to be sent to
     * an external website
     * @param string $returnURL
     * @return TransactionRequest
     */
    public function setReturnURL(string $returnURL): TransactionRequest
    {
        $this->returnURL = $returnURL;
        return $this;
    }

    /**
     * The url in the webshop where the customer should return after the transaction fails
     * @param string $returnURLCancel
     * @return TransactionRequest
     */
    public function setReturnURLCancel(string $returnURLCancel): TransactionRequest
    {
        $this->returnURLCancel = $returnURLCancel;
        return $this;
    }

    /**
     * The url in the webshop where the customer should return after the transaction results in an error
     * @param string $returnURLError
     * @return TransactionRequest
     */
    public function setReturnURLError(string $returnURLError): TransactionRequest
    {
        $this->returnURLError = $returnURLError;
        return $this;
    }

    /**
     * The url in the webshop where the customer should return after the transaction is cancelled
     * @param string $returnURLReject
     * @return TransactionRequest
     */
    public function setReturnURLReject(string $returnURLReject): TransactionRequest
    {
        $this->returnURLReject = $returnURLReject;
        return $this;
    }

    /**
     * The transaction key of the original transaction for which this transaction request is a follow up.
     * For example when requesting a Refund or doing a recurring charge.
     * @param string $originalTransactionKey
     * @return TransactionRequest
     */
    public function setOriginalTransactionKey(string $originalTransactionKey): TransactionRequest
    {
        $this->originalTransactionKey = $originalTransactionKey;
        return $this;
    }

    /**
     * Specifies if the current transaction is the start of a recurrent payment sequence. The default is false
     * @param bool $startRecurrent
     * @return TransactionRequest
     */
    public function setStartRecurrent(bool $startRecurrent): TransactionRequest
    {
        $this->startRecurrent = $startRecurrent;
        return $this;
    }

    /**
     * The url in the webshop where the push messages for this transaction should be delivered
     * @param string $pushURL
     * @return TransactionRequest
     */
    public function setPushURL(string $pushURL): TransactionRequest
    {
        $this->pushURL = $pushURL;
        return $this;
    }

    /**
     * The url in the webshop where the push messages for this transaction should be delivered for failure statuses
     * @param string $PushURLFailure
     * @return TransactionRequest
     */
    public function setPushURLFailure(string $PushURLFailure): TransactionRequest
    {
        $this->PushURLFailure = $PushURLFailure;
        return $this;
    }

    /**
     * Validate the parameters
     * @throws \Buckaroo\Exceptions\MissingParameterException
     * @throws WrongParameterCombinationException
     */
    private function validate(): void
    {
        // First validate the service params
        if (!is_null($this->service)) {
            $this->service->validate();
        }

        // Validate the own class' properties on being set
        $mandatoryProperties = ['currency', 'invoice'];
        foreach ($mandatoryProperties as $mandatoryProperty) {
            if (empty($this->$mandatoryProperty)) {
                throw new MissingParameterException($mandatoryProperty);
            }
        }

        // Other validations on the properties
        if (empty($this->amountCredit) && empty($this->amountDebit)) {
            throw new MissingParameterException('amountDebit');
        }

        if (empty($this->amountCredit) && empty($this->amountDebit)) {
            throw new WrongParameterCombinationException('amountDebit & amountCredit can not both have a value');
        }
    }

    /**
     * Generate the call's payload
     * @return array
     */
    protected function getPayload(): array
    {
        $result = [];
        $values = [
            'Currency',
            'AmountDebit',
            'AmountCredit',
            'Invoice',
            'Order',
            'Description',
            'ReturnURL',
            'ReturnURLCancel',
            'ReturnURLError',
            'ReturnURLReject',
            'OriginalTransactionKey',
            'StartRecurrent',
            'PushURL',
            'PushURLFailure'
        ];
        foreach ($values as $value) {
            $prop = lcfirst($value);
            if (!empty($this->$prop)) {
                $result[$value] = $this->$prop;
            }
        }

        if (!is_null($this->service)) {
            $parameterList = [];
            foreach ($this->service->getParameters() as $parameterName => $parameterValue) {
                $parameterList[] = [
                    'Name' => $parameterName,
                    'Value' => $parameterValue
                ];
            }

            $parameterList = $this->service->complementParameterList($parameterList);

            $result['Services'] = [
                'ServiceList' => [[
                    'Name' => $this->service->getName(),
                    'Action' => $this->service->getAction(),
                    'Parameters' => $parameterList
                ]]
            ];
        } else {
            $result['Services'] = ['ServiceList' => []];
        }

        return $result;
    }
}
