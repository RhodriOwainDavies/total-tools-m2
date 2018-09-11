<?php

namespace Temando\Temando\Model\System\Config\Source\Shipment\Packaging;

/**
 * System Config Source Shipment Packaging Mode
 */
class Mode extends \Temando\Temando\Model\System\Config\Source
{
    /**
     * {@inheritdoc}
     *
     * @codeCoverageIgnore
     */
    const USE_DEFAULTS = 0;
    const AS_DEFINED = 1;

    protected function _setupOptions()
    {
        $this->_options = [
            self::USE_DEFAULTS => __('Use Defaults'),
            self::AS_DEFINED => __('As Defined'),
        ];
    }
}
