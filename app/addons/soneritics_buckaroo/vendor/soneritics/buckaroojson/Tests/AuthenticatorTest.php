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
require_once 'TestAbstract.php';

use Buckaroo\Authentication\Authenticator;
use Buckaroo\Exceptions\UnsupportedHttpMethodException;
use Buckaroo\Exceptions\InvalidHttpMethodException;

/**
 * Class AuthenticatorTest
 */
class AuthenticatorTest extends TestAbstract
{
    /**
     * Get an Authenticator object that can be used in tests
     * @return Authenticator
     */
    private function getAuthenticator(): Authenticator
    {
        return (new Authenticator($this->getAuthentication()))->setNonceGenerator(new MockNonceGenerator);
    }

    /**
     * Test the default authentication header.
     * @see https://testcheckout.buckaroo.nl/json/Docs/AuthenticationDebugger
     * @see https://checkout.buckaroo.nl/json/Docs/AuthenticationDebugger
     */
    public function testAuthenticationHeader()
    {
        $jsonData = '{"test": "value"}';
        $requestUrl = 'https://testcheckout.buckaroo.nl/json/TransactionRequest';
        $httpMethod = 'POST';
        $timestamp = 1530612200;

        $expected = 'hmac websitekey:06YXwk74QhYCb/+h99Bi0dPwArn2m1sXCNu5YCvhlgM=:mocknonce:1530612200';
        $actual = $this->getAuthenticator()->getAuthenticationHeader($jsonData, $requestUrl, $httpMethod, $timestamp);

        $this->assertEquals($expected, $actual);
    }

    /**
     * Test an authentication with no data and GET method.
     * @see https://testcheckout.buckaroo.nl/json/Docs/AuthenticationDebugger
     * @see https://checkout.buckaroo.nl/json/Docs/AuthenticationDebugger
     */
    public function testAuthenticationHeaderWithEmptyDataAndGETMethod()
    {
        $jsonData = '';
        $requestUrl = 'https://testcheckout.buckaroo.nl/json/Transaction/Status';
        $httpMethod = 'GET';
        $timestamp = 1530646598;

        $expected = 'hmac websitekey:40obicSlTa6FsxGSeQrebKiY4FrNy8/N5PfetToRQ9w=:mocknonce:1530646598';
        $actual = $this->getAuthenticator()->getAuthenticationHeader($jsonData, $requestUrl, $httpMethod, $timestamp);

        $this->assertEquals($expected, $actual);
    }

    /**
     * Test for unsupported HTTP Methods
     */
    public function testAuthenticationHeaderUnsupportedHttpMethodException()
    {
        $httpMethod = 'PUT';
        $jsonData = '{"test": "value"}';
        $requestUrl = 'https://testcheckout.buckaroo.nl/json/TransactionRequest';
        $timestamp = 1530612200;

        $this->expectException(UnsupportedHttpMethodException::class);
        $this->getAuthenticator()->getAuthenticationHeader($jsonData, $requestUrl, $httpMethod, $timestamp);
    }

    /**
     * Test for invalid HTTP Methods
     */
    public function testAuthenticationHeaderInvalidHttpMethodException()
    {
        $httpMethod = 'POST';
        $jsonData = '';
        $requestUrl = 'https://testcheckout.buckaroo.nl/json/TransactionRequest';
        $timestamp = 1530612200;

        $this->expectException(InvalidHttpMethodException::class);
        $this->getAuthenticator()->getAuthenticationHeader($jsonData, $requestUrl, $httpMethod, $timestamp);
    }
}
