<?php

namespace Temando\Temando\Model\System\Config\Source;

/**
 * System Config Source Labeltype
 */
class Labeltype extends \Temando\Temando\Model\System\Config\Source
{
    /**
     * {@inheritdoc}
     *
     * @codeCoverageIgnore
     */
    const THERMAL  = 'Thermal';

    protected function _setupOptions()
    {
        $this->_options = [
            self::THERMAL  => __('Thermal'),
        ];
    }
}
