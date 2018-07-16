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
namespace Buckaroo\Enums;

/**
 * Class VatCategory
 * @package Buckaroo\Enums
 */
class VatCategory
{
    const HIGH = 1;
    const LOW = 2;
    const ZERO = 3;
    const NULL = 4;
    const MIDDLE = 5;

    /**
     * Get a VAT rate based on a country code and percentage.
     * @param string $countryCode
     * @param decimal $percentage
     * @param int $default
     * @return int
     */
    public static function calculate(string $countryCode, $percentage, int $default = VatCategory::HIGH): int
    {
        $rates = [
            'nl' => [
                '21' => VatCategory::HIGH,
                '6' => VatCategory::LOW,
                '0' => VatCategory::ZERO
            ]
        ];

        if (isset($rates[$countryCode]) && isset($rates[$countryCode][(string)$percentage])) {
            return $rates[$countryCode][(string)$percentage];
        }

        return $default;
    }
}
