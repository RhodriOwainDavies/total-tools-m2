<?php

namespace Temando\Temando\Model\System\Config\Source\Shipment;

/**
 * System Config Source Shipment Status
 */
class Status extends \Temando\Temando\Model\System\Config\Source implements
    \Magento\Framework\Data\OptionSourceInterface
{

    const BACK_ORDER        = '1';
    const PENDING           = '3';
    const PICKING           = '5';
    const PACKED            = '10';
    const BOOKED            = '20';
    const BOOKED_EXTERNALLY = '25';
    const COMPLETE          = '50';
    const CANCELLED         = '99';

    protected function _setupOptions()
    {
        $this->_options = array(
            self::PENDING           => __('Pending'),
            self::BACK_ORDER        => __('Back Order'),
            self::PICKING           => __('Picking'),
            self::PACKED            => __('Packed'),
            self::BOOKED            => __('Booked'),
            self::BOOKED_EXTERNALLY => __('Booked Externally'),
            self::COMPLETE          => __('Complete'),
            self::CANCELLED         => __('Cancelled')
        );
    }
}
