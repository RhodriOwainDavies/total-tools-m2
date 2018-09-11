<?php

namespace Temando\Temando\Model\Entity\Attribute\Source\Packaging;

/**
 * Entity Attribute Source Packaging Mode
 */
class Mode extends \Magento\Eav\Model\Entity\Attribute\Source\AbstractSource
{
    /**
     * {@inheritdoc}
     *
     * @codeCoverageIgnore
     */
    public function getAllOptions()
    {
        if (!$this->_options) {
            $mode = new \Temando\Temando\Model\System\Config\Source\Shipment\Packaging\Mode();
            $this->_options = $mode->toOptionArray();
        }
        return $this->_options;
    }
}
