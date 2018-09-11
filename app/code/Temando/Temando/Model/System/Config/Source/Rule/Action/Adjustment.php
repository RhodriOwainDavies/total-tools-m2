<?php

namespace Temando\Temando\Model\System\Config\Source\Rule\Action;

class Adjustment extends \Temando\Temando\Model\System\Config\Source implements
    \Magento\Framework\Data\OptionSourceInterface
{
    const NONE      = 1;
    const OVERRIDE  = 2;
    const MARKUP    = 3;

    protected function _setupOptions()
    {
        $this->_options = array(
            self::NONE      => '--',
            self::OVERRIDE  => 'Override',
            self::MARKUP  => 'Markup'
        );
    }

    /**
     * Get options
     *
     * @param bool
     *
     * @return array
     */
    public function toOptionArray($form = false)
    {
        $formOptions = array();
        $options[] = ['label' => '', 'value' => ''];
        foreach ($this->_options as $key => $value) {
            $options[] = [
                'label' => $value,
                'value' => $key,
            ];
            $formOptions[$key] = $value;
        }
        if ($form) {
            return $formOptions;
        }
        return $options;
    }
}
