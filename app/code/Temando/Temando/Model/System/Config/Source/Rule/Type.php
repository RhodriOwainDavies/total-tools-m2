<?php

namespace Temando\Temando\Model\System\Config\Source\Rule;

class Type extends \Temando\Temando\Model\System\Config\Source implements \Magento\Framework\Data\OptionSourceInterface
{
    /**
     * {@inheritdoc}
     *
     * @codeCoverageIgnore
     */
    const DYNAMIC       = 3;

    protected function _setupOptions()
    {
        $this->_options = [
            self::DYNAMIC    => __('Dynamic')
        ];
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
