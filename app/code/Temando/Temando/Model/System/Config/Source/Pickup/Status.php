<?php

namespace Temando\Temando\Model\System\Config\Source\Pickup;

/**
 * System Config Source Shipment Status
 */
class Status extends \Temando\Temando\Model\System\Config\Source implements
    \Magento\Framework\Data\OptionSourceInterface
{
    const BACK_ORDER    =   '1';
    const PENDING       =   '3';
    const PICKING       =   '5';
    const PACKED        =   '10';
    const AWAITING      =   '15';
    const COLLECTED     =   '50';
    const CANCELLED     =   '99';

    protected function _setupOptions()
    {
        $this->_options = array(
            self::PENDING       => __('Pending'),
            self::BACK_ORDER    => __('Back Order'),
            self::PICKING       => __('Picking'),
            self::PACKED        => __('Packed'),
            self::AWAITING      => __('Ready for collection'),
            self::COLLECTED     => __('Collected'),
            self::CANCELLED     => __('Cancelled'),
        );
    }
}
