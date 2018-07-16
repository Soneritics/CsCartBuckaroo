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
namespace Buckaroo\Authentication;

use Buckaroo\Exceptions\InvalidHttpMethodException;
use Buckaroo\Exceptions\UnsupportedHttpMethodException;

/**
 * Class Authenticator
 * @package Buckaroo\Authentication
 */
class Authenticator
{
    /**
     * @var Authentication
     */
    private $authentication;

    /**
     * @var INonceGenerator
     */
    private $nonceGenerator;

    /**
     * Authenticator constructor.
     * @param Authentication $authentication
     */
    public function __construct(Authentication $authentication)
    {
        $this->authentication = $authentication;
    }

    /**
     * Get the authentication header.
     * @param string $jsonData
     * @param string $requestUrl
     * @param string $httpMethod
     * @param int $unixTimestamp
     * @return string
     * @throws UnsupportedHttpMethodException
     * @throws InvalidHttpMethodException
     */
    public function getAuthenticationHeader(
        string $jsonData,
        string $requestUrl,
        string $httpMethod = 'POST',
        int $unixTimestamp = null
    ): string {
        // Only valid HTTP methods are allowed
        if (!in_array($httpMethod, ['GET', 'POST'])) {
            throw new UnsupportedHttpMethodException($httpMethod);
        }

        // When there is no data, HTTP Method must be GET
        if (empty($jsonData) && $httpMethod !== 'GET') {
            throw new InvalidHttpMethodException();
        }

        // Timestamp default
        if ($unixTimestamp === null) {
            $unixTimestamp = time();
        }

        // Prepare data
        $encodedUrl = $this->getEncodedUrl($requestUrl);
        $nonce = $this->getNonceGenerator()->generate();
        $md5Content = empty($jsonData) ? '' : md5($jsonData, true);
        $base64OfContentMd5 = empty($md5Content) ? '' : base64_encode($md5Content);
        $websiteKey = $this->authentication->getWebsiteKey();
        $concatenated = "{$websiteKey}{$httpMethod}{$encodedUrl}{$unixTimestamp}{$nonce}{$base64OfContentMd5}";
        $hmacSignedWithSecret = hash_hmac('sha256', $concatenated, $this->authentication->getSecretKey(), true);
        $base64Hash = base64_encode($hmacSignedWithSecret);

        # Debug
        if (false) {
            print_r([
                'authentication' => $this->authentication,
                'jsonData' => $jsonData,

                '1' => $httpMethod,
                '4' => $encodedUrl,
                '5' => $md5Content,
                '6' => $base64OfContentMd5,
                '7' => $concatenated,
                '8' => $hmacSignedWithSecret,
                '9' => $base64Hash,
                '10' => "hmac {$websiteKey}:{$base64Hash}:{$nonce}:{$unixTimestamp}"
            ]);
        }

        return "hmac {$websiteKey}:{$base64Hash}:{$nonce}:{$unixTimestamp}";
    }

    /**
     * @param INonceGenerator $nonceGenerator
     * @return Authenticator
     */
    public function setNonceGenerator(INonceGenerator $nonceGenerator): Authenticator
    {
        $this->nonceGenerator = $nonceGenerator;
        return $this;
    }

    /**
     * @return INonceGenerator
     */
    private function getNonceGenerator(): INonceGenerator
    {
        if (empty($this->nonceGenerator)) {
            $this->nonceGenerator = new NonceGenerator;
        }

        return $this->nonceGenerator;
    }

    /**
     * Get an encoded version of the URL that can be used in the request
     * @param $rawUrl
     * @return string
     */
    private function getEncodedUrl($rawUrl): string
    {
        $prepared = strtolower($rawUrl);

        if (substr($prepared, 0, 8) === 'https://') {
            $prepared = substr($prepared, 8);
        }

        return strtolower(urlencode($prepared));
    }
}
