<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Temando\Temando\Model\Shipping;

/**
 * Shipping method read service.
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class ShippingMethodManagement extends \Magento\Quote\Model\ShippingMethodManagement
{
    /**
     * {@inheritDoc}
     */
    public function __construct(
        \Magento\Quote\Api\CartRepositoryInterface $quoteRepository,
        \Magento\Quote\Model\Cart\ShippingMethodConverter $converter,
        \Magento\Customer\Api\AddressRepositoryInterface $addressRepository,
        \Magento\Quote\Model\Quote\TotalsCollector $totalsCollector
    ) {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $this->_logger = $objectManager->create('Psr\Log\LoggerInterface');
        parent::__construct($quoteRepository, $converter, $addressRepository, $totalsCollector);
    }

    /**
     * {@inheritDoc}
     */
    public function estimateByAddress($cartId, \Magento\Quote\Api\Data\EstimateAddressInterface $address)
    {
        $quote = $this->quoteRepository->getActive($cartId);

        // no methods applicable for empty carts or carts with virtual products
        if ($quote->isVirtual() || 0 == $quote->getItemsCount()) {
            return [];
        }

        return $this->getEstimatedRates(
            $quote,
            $address->getCountryId(),
            $address->getPostcode(),
            $address->getRegionId(),
            $address->getRegion(),
            $address->getExtensionAttributes(),
            $address->getCustomAttributes()
        );
    }

    /**
     * {@inheritDoc}
     */
    public function estimateByAddressId($cartId, $addressId)
    {
        $quote = $this->quoteRepository->getActive($cartId);

        // no methods applicable for empty carts or carts with virtual products
        if ($quote->isVirtual() || 0 == $quote->getItemsCount()) {
            return [];
        }
        $address = $this->addressRepository->getById($addressId);
        $quoteShippingAddress = $quote->getShippingAddress();

        $quoteShippingAddress->setCustomerAddressData($address);

        $quoteShippingAddress->setFirstname($address->getFirstname());
        $quoteShippingAddress->setLastname($address->getLastname());
        $quoteShippingAddress->setStreetFull($address->getStreet());
        $quoteShippingAddress->setCity($address->getCity());
        $quoteShippingAddress->setCountryId($address->getCountryId());
        $quoteShippingAddress->setRegion($address->getRegion());
        $quoteShippingAddress->setRegionId($address->getCountryId());
        $quoteShippingAddress->setPostcode($address->getPostcode());
        $quoteShippingAddress->setTelephone($address->getTelephone());
        $this->_logger->debug(date('Y/m/d H:i:s') . ' ' . get_class($this) . ' estimateByAddressId2 quote address');

        return $this->getEstimatedRates(
            $quote,
            $address->getCountryId(),
            $address->getPostcode(),
            $address->getRegionId(),
            $address->getRegion(),
            $address->getExtensionAttributes(),
            $address->getCustomAttributes()
        );
    }

    /**
     * Get estimated rates
     *
     * @param Quote $quote
     * @param int $country
     * @param string $postcode
     * @param int $regionId
     * @param string $region
     *
     * @return \Magento\Quote\Api\Data\ShippingMethodInterface[] An array of shipping methods.
     */
    protected function getEstimatedRates(
        \Magento\Quote\Model\Quote $quote,
        $country,
        $postcode,
        $regionId,
        $region,
        $extensionAttributes = null,
        $customAttributes = null
    ) {
        $output = [];
        $shippingAddress = $quote->getShippingAddress();
        $shippingAddress->setCountryId($country);
        $shippingAddress->setPostcode($postcode);
        $shippingAddress->setRegionId($regionId);
        $shippingAddress->setRegion($region);

        if ($extensionAttributes) {
            if ($extensionAttributes->getCity()) {
                $shippingAddress->setCity($extensionAttributes->getCity());
            }
            if ($extensionAttributes->getStreetAddressLine1()) {
                $shippingAddress->setStreet([$extensionAttributes->getStreetAddressLine1(),
                    $extensionAttributes->getStreetAddressLine2()]);
            }
            if ($extensionAttributes->getTelephone()) {
                $shippingAddress->setTelephone($extensionAttributes->getTelephone());
            }
            if ($extensionAttributes->getRegion()) {
                $shippingAddress->setRegion($extensionAttributes->getRegion());
            }
            if ($extensionAttributes->getFirstName()) {
                $shippingAddress->setFirstname($extensionAttributes->getFirstName());
            }
            if ($extensionAttributes->getLastName()) {
                $shippingAddress->setLastname($extensionAttributes->getLastName());
            }
            if ($extensionAttributes->getCompany()) {
                $shippingAddress->setCompany($extensionAttributes->getCompany());
            }
            if ($extensionAttributes->getEmail()) {
                $shippingAddress->setEmail($extensionAttributes->getEmail());
            }
        }

        if ($customAttributes) {
            if ($customAttributes['is_business_address']) {
                $shippingAddress->setIsBusinessAddress($customAttributes['is_business_address']->getValue());
            }

            if ($customAttributes['authority_to_leave']) {
                $shippingAddress->setAuthorityToLeave($customAttributes['authority_to_leave']->getValue());
            }
        }

        $shippingAddress->setCollectShippingRates(true);
        $this->totalsCollector->collectAddressTotals($quote, $shippingAddress);
        $shippingRates = $shippingAddress->getGroupedAllShippingRates();
        foreach ($shippingRates as $carrierRates) {
            foreach ($carrierRates as $rate) {
                $output[] = $this->converter->modelToDataObject($rate, $quote->getQuoteCurrencyCode());
            }
        }
        return $output;
    }
}
