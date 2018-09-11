<?php

namespace Temando\Temando\Model\System\Config\Source\Rule\Action;

class Filter extends \Temando\Temando\Model\System\Config\Source implements
    \Magento\Framework\Data\OptionSourceInterface
{
    const DYNAMIC_ALL                  = 1;
    const DYNAMIC_FASTEST              = 2;
    const DYNAMIC_CHEAPEST             = 3;
    const DYNAMIC_FASTEST_AND_CHEAPEST = 4;

    protected function _setupOptions()
    {
        $this->_options = array(
            self::DYNAMIC_CHEAPEST             => 'Cheapest only',
            self::DYNAMIC_FASTEST              => 'Fastest only',
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
