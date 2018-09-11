<?php

namespace Temando\Temando\Model\System\Config\Source;

class Errorprocess extends \Temando\Temando\Model\System\Config\Source
{
    const VIEW  = 'view';
    const FLAT  = 'flat';

    protected function _setupOptions()
    {
        $this->_options = [
            self::FLAT  => __('Show flat rate'),
            self::VIEW  => __('Show error message'),
        ];
    }
}
