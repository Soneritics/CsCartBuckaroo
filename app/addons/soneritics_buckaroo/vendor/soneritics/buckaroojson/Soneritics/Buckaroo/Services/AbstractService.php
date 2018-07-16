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
namespace Buckaroo\Services;

use Buckaroo\Exceptions\MissingParameterException;

/**
 * Class AbstractService
 * @package Buckaroo\Services
 */
abstract class AbstractService
{
    /**
     * @var array
     */
    private $parameters = [];

    /**
     * Get this service's name
     * @return string
     */
    abstract public function getName(): string;

    /**
     * Get this service's action
     * @return string
     */
    abstract public function getAction(): string;

    /**
     * Validate the parameters that have been filled
     * Can be overridden in the implementing class to add extra validations.
     * @param array $parameters
     * @param array $mandatory
     * @throws MissingParameterException
     */
    protected function validateParameters(array $parameters, array $mandatory = []): void
    {
        if (!empty($mandatory)) {
            foreach ($mandatory as $item) {
                if (!isset($parameters[$item]) || empty($parameters[$item])) {
                    throw new MissingParameterException($item);
                }
            }
        }
    }

    /**
     * Validate the parameters that have been filled
     * @throws MissingParameterException
     */
    public function validate(): void
    {
        $this->validateParameters($this->getParameters());
    }

    /**
     * Get the service parameters
     * @return array
     */
    public function getParameters(): array
    {
        return $this->parameters;
    }

    /**
     * Give the possibility to add extra information to the parameters list
     * @param array $parameters
     * @return array
     */
    public function complementParameterList(array $parameters): array
    {
        return $parameters;
    }

    /**
     * @param string $key
     * @param mixed $value
     * @return AbstractService
     */
    protected function set(string $key, $value): AbstractService
    {
        $this->parameters[$key] = $value;
        return $this;
    }
}
