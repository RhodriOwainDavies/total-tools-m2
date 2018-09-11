<?php

namespace Temando\Temando\Model\Entity\Attribute\Source;

/**
 * Entity Attribute Source Packaging
 */
class Packaging extends \Magento\Eav\Model\Entity\Attribute\Source\AbstractSource
{
    /**
     * {@inheritdoc}
     *
     * @codeCoverageIgnore
     */
    public function getAllOptions()
    {
        if (!$this->_options) {
            $packaging = new \Temando\Temando\Model\System\Config\Source\Shipment\Packaging();
            $this->_options = $packaging->toOptionArray();
        }
        return $this->_options;
    }
}
