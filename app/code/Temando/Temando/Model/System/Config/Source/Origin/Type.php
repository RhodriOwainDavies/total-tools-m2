<?php

namespace Temando\Temando\Model\System\Config\Source\Origin;

class Type extends \Temando\Temando\Model\System\Config\Source
{
    /**
     * {@inheritdoc}
     *
     * @codeCoverageIgnore
     */
    const BUSINESS    = 'Business';
    const RESIDENTIAL = 'Residence';

    protected function _setupOptions()
    {
        $this->_options = [
            self::BUSINESS    => __('Business'),
            self::RESIDENTIAL => __('Residential'),
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
