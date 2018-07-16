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
namespace Buckaroo\Services\Pay;

use Buckaroo\Enums\Gender;
use Buckaroo\Enums\VatCategory;
use Buckaroo\Exceptions\MissingParameterException;
use Buckaroo\Exceptions\NoArticlesProvidedException;

/**
 * Class AfterpayDigiAccept
 * @package Buckaroo\Services\Pay
 */
class AfterpayDigiAccept extends AbstractPayService
{
    /**
     * @var array
     */
    private $articles = [];

    /**
     * AfterpayDigiAccept constructor.
     */
    public function __construct()
    {
        $this->setDefaults();
    }

    /**
     * Get this service's name
     * @return string
     */
    public function getName(): string
    {
        return 'afterpaydigiaccept';
    }

    /**
     * Validate the parameters that have been filled
     * @param array $parameters
     * @param array $mandatory
     * @throws MissingParameterException
     * @throws NoArticlesProvidedException
     */
    public function validateParameters(array $parameters, array $mandatory = []): void
    {
        $mandatory = [
            'BillingInitials',
            'BillingLastName',
            'BillingBirthDate',
            'BillingStreet',
            'BillingHouseNumber',
            'BillingPostalCode',
            'BillingCity',
            'BillingCountry',
            'BillingEmail',
            'BillingPhoneNumber',
            'BillingLanguage',
            'CustomerIPAddress'
        ];

        if (isset($parameters['AddressesDiffer']) && $parameters['AddressesDiffer'] === true) {
            $mandatory += [
                'ShippingInitials',
                'ShippingLastName',
                'ShippingBirthDate',
                'ShippingStreet',
                'ShippingHouseNumber',
                'ShippingPostalCode',
                'ShippingCity',
                'ShippingCountry',
                'ShippingEmail',
                'ShippingPhoneNumber',
                'ShippingLanguage'
            ];
        }

        if (isset($parameters['B2B']) && $parameters['B2B'] === true) {
            $mandatory += ['CompanyCOCRegistration', 'CompanyName'];
        } else {
            $mandatory[] = 'BillingGender';
        }

        if (empty($this->articles)) {
            throw new NoArticlesProvidedException('No articles have been provided');
        }

        parent::validateParameters($parameters, $mandatory);
    }

    /**
     * Give the possibility to add extra information to the parameters list
     * @param array $parameters
     * @return array
     */
    public function complementParameterList(array $parameters): array
    {
        foreach ($this->articles as $articleCount => $articleData) {
            foreach ($articleData as $name => $value) {
                $parameters[] = [
                    'Name' => $name,
                    'GroupType' => 'Article',
                    'GroupID' => $articleCount,
                    'Value' => $value
                ];
            }
        }

        return $parameters;
    }

    /**
     * Add an article.
     * At least one article is required.
     * @param string $id
     * @param string $description
     * @param int $quantity
     * @param decimal $unitPrice
     * @param int $vatCategory
     * @return AfterpayDigiAccept
     */
    public function addArticle(
        string $id,
        string $description,
        int $quantity,
        $unitPrice,
        int $vatCategory = VatCategory::HIGH
    ): AfterpayDigiAccept {
        $this->articles[] = [
            'ArticleId' => $id,
            'ArticleDescription' => $description,
            'ArticleQuantity' => $quantity,
            'ArticleUnitprice' => $unitPrice,
            'ArticleVatcategory' => $vatCategory
        ];

        return $this;
    }

    /**
     * Shipping costs
     * @param decimal $value
     * @return AfterpayDigiAccept
     */
    public function setShippingCosts($value): AfterpayDigiAccept
    {
        $this->set('ShippingCosts', $value);
        return $this;
    }

    /**
     * Discount
     * @param decimal $value
     * @return AfterpayDigiAccept
     */
    public function setDiscount($value): AfterpayDigiAccept
    {
        $this->set('Discount', $value);
        return $this;
    }

    /**
     * Were the license agreements accepted by the customer?
     * Make sure you link to the following page when you are having the customer accept the agreement on
     * your own checkout: https://www.afterpay.nl/nl/algemeen/betalen-met-afterpay/betalingsvoorwaarden
     * @param bool $value
     * @return AfterpayDigiAccept
     */
    public function setAccept(bool $value): AfterpayDigiAccept
    {
        $this->set('Accept', $value);
        return $this;
    }

    /**
     * Title of the billing customer
     * @param string $value
     * @return AfterpayDigiAccept
     */
    public function setBillingTitle(string $value): AfterpayDigiAccept
    {
        $this->set('BillingTitle', $value);
        return $this;
    }

    /**
     * Gender of the billing customer
     * Male (1) Female (2)
     * Not required in case of B2B
     * @param int $value
     * @return AfterpayDigiAccept
     * @see Gender
     */
    public function setBillingGender(int $value): AfterpayDigiAccept
    {
        $this->set('BillingGender', $value);
        return $this;
    }

    /**
     * Initials of the billing customer
     * @param string $value
     * @return AfterpayDigiAccept
     */
    public function setBillingInitials(string $value): AfterpayDigiAccept
    {
        $this->set('BillingInitials', $value);
        return $this;
    }

    /**
     * Last name prefix of the billing customer
     * @param string $value
     * @return AfterpayDigiAccept
     */
    public function setBillingLastNamePrefix(string $value): AfterpayDigiAccept
    {
        $this->set('BillingLastNamePrefix', $value);
        return $this;
    }

    /**
     * Last name of the billing customer
     * @param string $value
     * @return AfterpayDigiAccept
     */
    public function setBillingLastName(string $value): AfterpayDigiAccept
    {
        $this->set('BillingLastName', $value);
        return $this;
    }

    /**
     * Birthdate of the billing customer. In case of B2B a dummy value is allowed.
     * @param \DateTime $value
     * @return AfterpayDigiAccept
     */
    public function setBillingBirthDate(\DateTime $value): AfterpayDigiAccept
    {
        $this->set('BillingBirthDate', $value->format('d-m-Y'));
        return $this;
    }

    /**
     * Street of the billing customer
     * @param string $value
     * @return AfterpayDigiAccept
     */
    public function setBillingStreet(string $value): AfterpayDigiAccept
    {
        $this->set('BillingStreet', $value);
        return $this;
    }

    /**
     * House number of the billing customer
     * @param int $value
     * @return AfterpayDigiAccept
     */
    public function setBillingHouseNumber(int $value): AfterpayDigiAccept
    {
        $this->set('BillingHouseNumber', $value);
        return $this;
    }

    /**
     * House number suffix of the billing customer
     * @param string $value
     * @return AfterpayDigiAccept
     */
    public function setBillingHouseNumberSuffix(string $value): AfterpayDigiAccept
    {
        $this->set('BillingHouseNumberSuffix', $value);
        return $this;
    }

    /**
     * Postal code of the billing customer
     * @param string $value
     * @return AfterpayDigiAccept
     */
    public function setBillingPostalCode(string $value): AfterpayDigiAccept
    {
        $this->set('BillingPostalCode', $value);
        return $this;
    }

    /**
     * City of the billing customer
     * @param string $value
     * @return AfterpayDigiAccept
     */
    public function setBillingCity(string $value): AfterpayDigiAccept
    {
        $this->set('BillingCity', $value);
        return $this;
    }

    /**
     * Country code of the billing customer, e.g. NL
     * @param string $value
     * @return AfterpayDigiAccept
     */
    public function setBillingCountry(string $value): AfterpayDigiAccept
    {
        $this->set('BillingCountry', $value);
        return $this;
    }

    /**
     * E-mail of the billing customer
     * @param string $value
     * @return AfterpayDigiAccept
     */
    public function setBillingEmail(string $value): AfterpayDigiAccept
    {
        $this->set('BillingEmail', $value);
        return $this;
    }

    /**
     * Phone number of the billing customer.
     * Prefix such as +31, 31 and 0031 allowed.
     * Without prefix has to be 10 characters, so no spaces ( ) or dashes (-).
     * @param string $value
     * @return AfterpayDigiAccept
     */
    public function setBillingPhoneNumber(string $value): AfterpayDigiAccept
    {
        $this->set('BillingPhoneNumber', $value);
        return $this;
    }

    /**
     * Language code of the billing customer, e.g. nl
     * @param string $value
     * @return AfterpayDigiAccept
     */
    public function setBillingLanguage(string $value): AfterpayDigiAccept
    {
        $this->set('BillingLanguage', $value);
        return $this;
    }

    /**
     * Title of the shipping customer
     * @param string $value
     * @return AfterpayDigiAccept
     */
    public function setShippingTitle(string $value): AfterpayDigiAccept
    {
        $this->set('ShippingTitle', $value);
        return $this;
    }

    /**
     * Gender of the billing customer
     * Male (1) Female (2)
     * Not required in case of B2B
     * @param int $value
     * @return AfterpayDigiAccept
     * @see Gender
     */
    public function setShippingGender(int $value): AfterpayDigiAccept
    {
        $this->set('ShippingGender', $value);
        return $this;
    }

    /**
     * Initials of the shipping customer
     * @param string $value
     * @return AfterpayDigiAccept
     */
    public function setShippingInitials(string $value): AfterpayDigiAccept
    {
        $this->set('ShippingInitials', $value);
        return $this;
    }

    /**
     * Last name prefix of the shipping customer
     * @param string $value
     * @return AfterpayDigiAccept
     */
    public function setShippingLastNamePrefix(string $value): AfterpayDigiAccept
    {
        $this->set('ShippingLastNamePrefix', $value);
        return $this;
    }

    /**
     * Last name of the shipping customer
     * @param string $value
     * @return AfterpayDigiAccept
     */
    public function setShippingLastName(string $value): AfterpayDigiAccept
    {
        $this->set('ShippingLastName', $value);
        return $this;
    }

    /**
     * Birthdate of the shipping customer
     * @param \DateTime $value
     * @return AfterpayDigiAccept
     */
    public function setShippingBirthDate(\DateTime $value): AfterpayDigiAccept
    {
        $this->set('ShippingBirthDate', $value->format('d-m-Y'));
        return $this;
    }

    /**
     * Street of the shipping customer
     * @param string $value
     * @return AfterpayDigiAccept
     */
    public function setShippingStreet(string $value): AfterpayDigiAccept
    {
        $this->set('ShippingStreet', $value);
        return $this;
    }

    /**
     * House number of the shipping customer.
     * @param int $value
     * @return AfterpayDigiAccept
     */
    public function setShippingHouseNumber(int $value): AfterpayDigiAccept
    {
        $this->set('ShippingHouseNumber', $value);
        return $this;
    }

    /**
     * House number suffix of the shipping customer
     * @param string $value
     * @return AfterpayDigiAccept
     */
    public function setShippingHouseNumberSuffix(string $value): AfterpayDigiAccept
    {
        $this->set('ShippingHouseNumberSuffix', $value);
        return $this;
    }

    /**
     * Postal code of the shipping customer
     * @param string $value
     * @return AfterpayDigiAccept
     */
    public function setShippingPostalCode(string $value): AfterpayDigiAccept
    {
        $this->set('ShippingPostalCode', $value);
        return $this;
    }

    /**
     * City of the shipping customer
     * @param string $value
     * @return AfterpayDigiAccept
     */
    public function setShippingCity(string $value): AfterpayDigiAccept
    {
        $this->set('ShippingCity', $value);
        return $this;
    }

    /**
     * Country code of the shipping customer
     * @param string $value
     * @return AfterpayDigiAccept
     */
    public function setShippingCountryCode(string $value): AfterpayDigiAccept
    {
        $this->set('ShippingCountryCode', $value);
        return $this;
    }

    /**
     * E-mail of the shipping customer
     * @param string $value
     * @return AfterpayDigiAccept
     */
    public function setShippingEmail(string $value): AfterpayDigiAccept
    {
        $this->set('ShippingEmail', $value);
        return $this;
    }

    /**
     * Phone number of the shipping customer
     * @param string $value
     * @return AfterpayDigiAccept
     */
    public function setShippingPhoneNumber(string $value): AfterpayDigiAccept
    {
        $this->set('ShippingPhoneNumber', $value);
        return $this;
    }

    /**
     * Language code of the shipping customer
     * @param string $value
     * @return AfterpayDigiAccept
     */
    public function setShippingLanguage(string $value): AfterpayDigiAccept
    {
        $this->set('ShippingLanguage', $value);
        return $this;
    }

    /**
     * Set to true if the shipping address is different from the billing address
     * @param bool $value
     * @return AfterpayDigiAccept
     */
    public function setAddressesDiffer(bool $value): AfterpayDigiAccept
    {
        $this->set('AddressesDiffer', $value);
        return $this;
    }

    /**
     * Whether the customer is a consumer or a company.
     * Set to true if the order is billed to a company (only available in the Netherlands).
     * Allowing B2B requires separate credentials from AfterPay.
     * @param bool $value
     * @return AfterpayDigiAccept
     */
    public function setB2B(bool $value): AfterpayDigiAccept
    {
        $this->set('B2B', $value);
        return $this;
    }

    /**
     * Bank account number of the customer
     * @param string $value
     * @return AfterpayDigiAccept
     */
    public function setCustomerAccountNumber(string $value): AfterpayDigiAccept
    {
        $this->set('CustomerAccountNumber', $value);
        return $this;
    }

    /**
     * COC (KvK) number. Required if B2B is set to true.
     * @param string $value
     * @return AfterpayDigiAccept
     */
    public function setCompanyCOCRegistration(string $value): AfterpayDigiAccept
    {
        $this->set('CompanyCOCRegistration', $value);
        return $this;
    }

    /**
     * Name of the organization.
     * Required if B2B is set to true.
     * @param string $value
     * @return AfterpayDigiAccept
     */
    public function setCompanyName(string $value): AfterpayDigiAccept
    {
        $this->set('CompanyName', $value);
        return $this;
    }

    /**
     * Cost centre of the order
     * @param string $value
     * @return AfterpayDigiAccept
     */
    public function setCostCentre(string $value): AfterpayDigiAccept
    {
        $this->set('CostCentre', $value);
        return $this;
    }

    /**
     * Name of the department
     * @param string $value
     * @return AfterpayDigiAccept
     */
    public function setDepartment(string $value): AfterpayDigiAccept
    {
        $this->set('Department', $value);
        return $this;
    }

    /**
     * Number of the establishment
     * @param string $value
     * @return AfterpayDigiAccept
     */
    public function setEstablishmentNumber(string $value): AfterpayDigiAccept
    {
        $this->set('EstablishmentNumber', $value);
        return $this;
    }

    /**
     * IP address of the customer
     * @param string $value
     * @return AfterpayDigiAccept
     */
    public function setCustomerIPAddress(string $value): AfterpayDigiAccept
    {
        $this->set('CustomerIPAddress', $value);
        return $this;
    }

    /**
     * VAT (BTW) number
     * @param string $value
     * @return AfterpayDigiAccept
     */
    public function setVatNumber(string $value): AfterpayDigiAccept
    {
        $this->set('VatNumber', $value);
        return $this;
    }

    /**
     * Set default values
     */
    private function setDefaults(): void
    {
        $this->setAccept(false);
        $this->setCustomerIPAddress('0.0.0.0');
    }
}
