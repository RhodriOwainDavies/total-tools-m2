<?php

namespace Temando\Temando\Model\System\Config\Source;

class Pricing extends \Temando\Temando\Model\System\Config\Source
{
    /**
     * {@inheritdoc}
     *
     * @codeCoverageIgnore
     */
    const FREE                         = 'free';
    const FLAT_RATE                    = 'flat';
    const DYNAMIC                      = 'dynamic';
    const DYNAMIC_FASTEST              = 'dynamicfast';
    const DYNAMIC_CHEAPEST             = 'dynamiccheap';
    const DYNAMIC_FASTEST_AND_CHEAPEST = 'dynamicfastcheap';

    protected function _setupOptions()
    {
        $this->_options = [
            self::FREE                         => __('Free Shipping'),
            self::FLAT_RATE                    => __('Fixed Price / Flat Rate'),
            self::DYNAMIC                      => __('Dynamic Pricing (All)'),
            self::DYNAMIC_CHEAPEST             => __('Dynamic Pricing (Cheapest only)'),
            self::DYNAMIC_FASTEST              => __('Dynamic Pricing (Fastest only)'),
            self::DYNAMIC_FASTEST_AND_CHEAPEST => __('Dynamic Pricing (Cheapest and Fastest only)'),
        ];
    }
}
