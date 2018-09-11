<?php

namespace Temando\Temando\Model\Carrier;

use Magento\Quote\Model\Quote\Address\RateRequest;

/**
 * Shipping model
 */
class Method extends \Magestore\Storepickup\Model\Carrier\Method
{
    /**
     * Collect Rates.
     *
     * @param RateRequest $request
     *
     * @return Result|bool
     *
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function collectRates(RateRequest $request)
    {
        if (!$this->getConfigFlag('active')) {
            return false;
        }

        $freeBoxes = 0;
        if ($request->getAllItems()) {
            foreach ($request->getAllItems() as $item) {
                // If cart contains a giftcard product, hide storepickup shipping option
                if ($item->getProduct()->getTypeId() == 'giftcard') {
                    return false;
                }
                
                if ($item->getProduct()->isVirtual() || $item->getParentItem()) {
                    continue;
                }

                if ($item->getHasChildren() && $item->isShipSeparately()) {
                    foreach ($item->getChildren() as $child) {
                        if ($child->getFreeShipping() && !$child->getProduct()->isVirtual()) {
                            $freeBoxes += $item->getQty() * $child->getQty();
                        }
                    }
                } elseif ($item->getFreeShipping()) {
                    $freeBoxes += $item->getQty();
                }
            }
        }
        $this->setFreeBoxes($freeBoxes);

        $result = $this->_rateResultFactory->create();
        if ($this->getConfigData('type') == 'O') {
            // per order
            $shippingPrice = $this->getConfigData('price');
        } elseif ($this->getConfigData('type') == 'I') {
            // per item
            $shippingPrice = $request->getPackageQty() * $this->getConfigData('price')
                - $this->getFreeBoxes() * $this->getConfigData('price');
        } else {
            $shippingPrice = false;
        }

        $shippingPrice = $this->getFinalPriceWithHandlingFee($shippingPrice);

        if ($shippingPrice !== false) {
            $method = $this->_rateMethodFactory->create();

            $method->setCarrier('storepickup');
            $method->setCarrierTitle($this->getConfigData('title'));

            $method->setMethod('storepickup');
            $method->setMethodTitle($this->getConfigData('name'));

            if ($request->getFreeShipping() === true || $request->getPackageQty() == $this->getFreeBoxes()) {
                $shippingPrice = '0.00';
            }

            $method->setPrice($shippingPrice);
            $method->setCost($shippingPrice);
            $result->append($method);
        }
        return $result;
    }
}
