<?php

namespace Temando\Temando\Model\System\Config\Source\Unit;

class Measure extends \Temando\Temando\Model\System\Config\Source\Unit
{
    /**
     * {@inheritdoc}
     *
     * @codeCoverageIgnore
     */
    const CENTIMETRES = 'Centimetres';
    const METRES      = 'Metres';
    const INCHES      = 'Inches';
    const FEET        = 'Feet';

    protected function _setupOptions()
    {
        $this->_options = [
            self::CENTIMETRES => 'Centimetres',
            self::METRES      => 'Metres',
            self::INCHES      => 'Inches',
            self::FEET        => 'Feet',
        ];
    }
    
    protected function _setupBriefOptions()
    {
        $this->_brief_options = [
            self::CENTIMETRES => 'cm',
            self::METRES      => 'm',
            self::INCHES      => 'in.',
            self::FEET        => 'ft.',
        ];
    }
}
