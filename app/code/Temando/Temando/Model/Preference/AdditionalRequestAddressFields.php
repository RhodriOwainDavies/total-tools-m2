<?php
namespace Temando\Temando\Model\Preference;

class AdditionalRequestAddressFields extends \Magento\Quote\Model\Quote\Address
{
    /**
     * Request shipping rates for entire address or specified address item
     * Returns true if current selected shipping method code corresponds to one of the found rates
     *
     * @param \Magento\Quote\Model\Quote\Item\AbstractItem $item
     *
     * @return bool
     *
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function requestShippingRates(\Magento\Quote\Model\Quote\Item\AbstractItem $item = null)
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $this->_logger = $objectManager->create('Psr\Log\LoggerInterface');

        $request = $this->_rateRequestFactory->create();
        $request->setAllItems($item ? [$item] : $this->getAllItems());
        $request->setDestCountryId($this->getCountryId());
        $request->setDestRegionId($this->getRegionId());
        $request->setDestRegionCode($this->getRegionCode());
        $request->setDestStreet($this->getStreetFull());
        $request->setDestCity($this->getCity());
        $request->setDestPostcode($this->getPostcode());
        $request->setPackageValue($item ? $item->getBaseRowTotal() : $this->getBaseSubtotal());
        $packageWithDiscount = $item ? $item->getBaseRowTotal() -
            $item->getBaseDiscountAmount() : $this->getBaseSubtotalWithDiscount();
        $request->setPackageValueWithDiscount($packageWithDiscount);
        $request->setPackageWeight($item ? $item->getRowWeight() : $this->getWeight());
        $request->setPackageQty($item ? $item->getQty() : $this->getItemQty());

        //Add checkout options
        $deliveryOptions = array();

        $customAttributes = $this->getCustomAttributes();
        if (is_array($customAttributes) && count($customAttributes)) {
            foreach ($customAttributes as $key => $customAttribute) {
                if ($customAttribute->getAttributeCode() == 'is_business_address') {
                    if ($customAttribute->getValue() == 1) {
                        $request->setDestType(\Temando\Temando\Model\System\Config\Source\Origin\Type::BUSINESS);
                    } else {
                        $request->setDestType(\Temando\Temando\Model\System\Config\Source\Origin\Type::RESIDENTIAL);
                    }
                } elseif ($customAttribute->getAttributeCode() == 'authority_to_leave') {
                    if ($customAttribute->getValue() == 1) {
                        $deliveryOptions['unattended_delivery'] = 1;
                    }
                }
            }
        }

        $request->setDeliveryOptions($deliveryOptions);

        /**
         * Need for shipping methods that use insurance based on price of physical products
         */
        $packagePhysicalValue = $item ? $item->getBaseRowTotal() : $this->getBaseSubtotal() -
            $this->getBaseVirtualAmount();
        $request->setPackagePhysicalValue($packagePhysicalValue);

        $request->setFreeMethodWeight($item ? 0 : $this->getFreeMethodWeight());

        /**
         * Store and website identifiers need specify from quote
         */
        $request->setStoreId($this->getQuote()->getStore()->getId());
        $request->setWebsiteId($this->getQuote()->getStore()->getWebsiteId());
        $request->setFreeShipping($this->getFreeShipping());
        /**
         * Currencies need to convert in free shipping
         */
        $request->setBaseCurrency($this->getQuote()->getStore()->getBaseCurrency());
        $request->setPackageCurrency($this->getQuote()->getStore()->getCurrentCurrency());
        $request->setLimitCarrier($this->getLimitCarrier());
        $baseSubtotalInclTax = $this->getBaseSubtotalTotalInclTax();
        $request->setBaseSubtotalInclTax($baseSubtotalInclTax);

        $result = $this->_rateCollector->create()->collectRates($request)->getResult();

        $found = false;
        if ($result) {
            $shippingRates = $result->getAllRates();

            foreach ($shippingRates as $shippingRate) {
                $rate = $this->_addressRateFactory->create()->importShippingRate($shippingRate);
                if (!$item) {
                    $this->addShippingRate($rate);
                }

                if ($this->getShippingMethod() == $rate->getCode()) {
                    if ($item) {
                        $item->setBaseShippingAmount($rate->getPrice());
                    } else {
                        $quote = $this->getQuote();
                        $amountPrice = $quote->getStore()
                            ->getBaseCurrency()
                            ->convert($rate->getPrice(), $quote->getQuoteCurrencyCode());
                        $this->setBaseShippingAmount($rate->getPrice());
                        $this->setShippingAmount($amountPrice);
                    }

                    $found = true;
                }
            }
        }

        return $found;
    }
}
