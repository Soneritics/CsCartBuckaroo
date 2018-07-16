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

use Buckaroo\Enums\DataType;
use Buckaroo\Enums\ListType;
use Buckaroo\Result\IResult;

/**
 * Class RequestParameter
 * @package Buckaroo\Result\Models
 * @see https://testcheckout.buckaroo.nl/json/Docs/ResourceModel?modelName=ParameterDescription
 */
class RequestParameter implements IResult
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var array
     */
    private $listItemDescriptions = [];

    /**
     * @var bool
     */
    private $isRequestParameter;

    /**
     * @see DataType
     * @var int
     */
    private $dataType;

    /**
     * @see ListType
     * @var int
     */
    private $list = 0;

    /**
     * @var ?int
     */
    private $maxOccurs;

    /**
     * @var ?int
     */
    private $maxLength;

    /**
     * @var bool
     */
    private $global;

    /**
     * @var string
     */
    private $group;

    /**
     * @var string
     */
    private $description;

    /**
     * @var string
     */
    private $displayName;

    /**
     * @var string
     */
    private $inputPattern;

    /**
     * @var string
     */
    private $autoCompleteType;

    /**
     * Parse from an array
     * @param array $data
     */
    public function __construct(array $data)
    {
        # Booleans
        $this->isRequestParameter = (bool)$data['isRequestParameter'] ?? false;
        $this->global = (bool)$data['Global'] ?? false;

        # Ints
        if (!empty($data['List'])) {
            $this->list = (int)$data['List'];
        }

        if (!empty($data['DataType'])) {
            $this->dataType = (int)$data['DataType'];
        }
        if (!empty($data['MaxOccurs'])) {
            $this->maxOccurs = (int)$data['MaxOccurs'];
        }

        if (!empty($data['MaxLength'])) {
            $this->maxLength = (int)$data['MaxLength'];
        }

        # Strings
        if (isset($data['Name'])) {
            $this->name = $data['Name'];
        }

        if (isset($data['Group'])) {
            $this->group = $data['Group'];
        }

        if (isset($data['Description'])) {
            $this->description = $data['Description'];
        }

        if (isset($data['DisplayName'])) {
            $this->displayName = $data['DisplayName'];
        }

        if (isset($data['InputPattern'])) {
            $this->inputPattern = $data['InputPattern'];
        }

        if (isset($data['AutoCompleteType'])) {
            $this->autoCompleteType = $data['AutoCompleteType'];
        }

        # Special
        if (!empty($data['ListItemDescriptions'])) {
            foreach ($data['ListItemDescriptions'] as $listItemDescription) {
                $value = $listItemDescription['Value'] ?? null;
                $description = $listItemDescription['Description'] ?? null;

                if (!is_null($value) && !is_null($description)) {
                    $this->listItemDescriptions[$value] = $description;
                }
            }
        }

        # Custom
        if (empty($this->description) && !empty($data['ExplanationHTML'])) {
            $this->description = $data['ExplanationHTML'];
        }
    }

    /**
     * @return mixed
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return array
     */
    public function getListItemDescriptions(): array
    {
        return $this->listItemDescriptions;
    }

    /**
     * @return bool
     */
    public function isRequestParameter(): bool
    {
        return $this->isRequestParameter;
    }

    /**
     * @return int
     */
    public function getDataType(): int
    {
        return $this->dataType;
    }

    /**
     * @return int
     */
    public function getList(): int
    {
        return $this->list;
    }

    /**
     * @return mixed
     */
    public function getMaxOccurs(): ?int
    {
        return $this->maxOccurs;
    }

    /**
     * @return mixed
     */
    public function getMaxLength(): ?int
    {
        return $this->maxLength;
    }

    /**
     * @return bool
     */
    public function isGlobal(): bool
    {
        return $this->global;
    }

    /**
     * @return string
     */
    public function getGroup(): string
    {
        return $this->group;
    }

    /**
     * @return string
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * @return string
     */
    public function getDisplayName(): string
    {
        return $this->displayName;
    }

    /**
     * @return string
     */
    public function getInputPattern(): string
    {
        return $this->inputPattern;
    }

    /**
     * @return string
     */
    public function getAutoCompleteType(): string
    {
        return $this->autoCompleteType;
    }
}
