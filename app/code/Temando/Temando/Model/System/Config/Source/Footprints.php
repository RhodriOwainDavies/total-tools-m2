<?php

namespace Temando\Temando\Model\System\Config\Source;

class Footprints extends \Temando\Temando\Model\System\Config\Source
{
    /**
     * {@inheritdoc}
     *
     * @codeCoverageIgnore
     */
    const DISABLED  = 'disabled';
    const OPTIONAL  = 'optional';
    const MANDATORY = 'mandatory';

    protected function _setupOptions()
    {
        $this->_options = [
            self::DISABLED  => __('Disabled'),
            self::OPTIONAL  => __('Optional'),
            self::MANDATORY => __('Mandatory'),
        ];
    }
}
