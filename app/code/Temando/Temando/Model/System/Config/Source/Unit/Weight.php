<?php

namespace Temando\Temando\Model\System\Config\Source\Unit;

class Weight extends \Temando\Temando\Model\System\Config\Source\Unit
{
    /**
     * {@inheritdoc}
     *
     * @codeCoverageIgnore
     */
    const GRAMS     = 'Grams';
    const KILOGRAMS = 'Kilograms';
    const OUNCES    = 'Ounces';
    const POUNDS    = 'Pounds';

    protected function _setupOptions()
    {
        $this->_options = [
            self::GRAMS     => 'Grams',
            self::KILOGRAMS => 'Kilograms',
            self::OUNCES    => 'Ounces',
            self::POUNDS    => 'Pounds',
        ];
    }
    
    protected function _setupBriefOptions()
    {
        $this->_brief_options = [
            self::GRAMS     => 'g',
            self::KILOGRAMS => 'kg',
            self::OUNCES    => 'oz.',
            self::POUNDS    => 'lb.',
        ];
    }
}
