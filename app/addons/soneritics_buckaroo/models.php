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
class SoneriticsBuckarooSettings
{
    /**
     * @var string
     */
    private $websiteKey;

    /**
     * @var string
     */
    private $secretKey;

    /**
     * @var string
     */
    private $test;

    /**
     * SonerliticsBuckarooSettings constructor.
     */
    public function __construct()
    {
        $this->websiteKey = \Tygh\Registry::get('addons.soneritics_buckaroo.websitekey');
        $this->secretKey = \Tygh\Registry::get('addons.soneritics_buckaroo.secretkey');
        $this->test = \Tygh\Registry::get('addons.soneritics_buckaroo.test') != 'N';
    }

    /**
     * @return string
     */
    public function getWebsiteKey(): string
    {
        return $this->websiteKey;
    }

    /**
     * @return string
     */
    public function getSecretKey(): string
    {
        return $this->secretKey;
    }

    /**
     * @return bool
     */
    public function isTestMode(): bool
    {
        return $this->test;
    }
}