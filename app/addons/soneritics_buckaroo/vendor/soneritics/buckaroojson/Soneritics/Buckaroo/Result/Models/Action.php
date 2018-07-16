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
namespace Buckaroo\Result\Models;

use Buckaroo\Result\IResult;

/**
 * Class Action
 * @package Buckaroo\Result\Models
 */
class Action implements IResult
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var bool
     */
    private $default;

    /**
     * @var string
     */
    private $description;

    /**
     * @var array
     */
    private $requestParameters = [];

    /**
     * Parse from an array
     * @param array $data
     */
    public function __construct(array $data)
    {
        $this->mapAttributes($data);

        if (isset($data['RequestParameters'])) {
            $this->mapRequestParameters($data['RequestParameters']);
        }
    }

    /**
     * Map the default attributes
     * @param array $data
     */
    private function mapAttributes(array $data): void
    {
        $this->name = $data['Name'] ?? '';
        $this->description = $data['Description'] ?? '';
        $this->default = (bool)$data['Default'] ?? false;
    }

    /**
     * Map the request parameters
     * @param array $requestParameters
     */
    private function mapRequestParameters(array $requestParameters): void
    {
        if (!empty($requestParameters)) {
            foreach ($requestParameters as $requestParameter) {
                if (!empty($requestParameter['Name'])) {
                    $this->requestParameters[$requestParameter['Name']] = new RequestParameter($requestParameter);
                }
            }
        }
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return bool
     */
    public function isDefault(): bool
    {
        return $this->default;
    }

    /**
     * @return string
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * @return RequestParameter[]
     */
    public function getRequestParameters(): array
    {
        return $this->requestParameters;
    }
}
