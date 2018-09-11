<?php

/**
 * Source for Origins
 *
 * @category Temando
 * @package  Temando_Temando
 */

namespace Temando\Temando\Model\System\Config\Source;

class Origin implements \Magento\Framework\Data\OptionSourceInterface
{
    /**
     * Origin Collection.
     *
     * @var \Temando\Temando\Model\ResourceModel\Origin\Collection
     */
    protected $_originCollection;

    public function __construct(
        \Temando\Temando\Model\ResourceModel\Origin\Collection $originCollection
    ) {
        $this->_originCollection = $originCollection;
    }

    /**
     * Return array of options as value-label pairs.
     *
     * @return array Format: array(array('value' => '<value>', 'label' => '<label>'), ...)
     */
    public function toOptionArray()
    {
        $option = [];
        $collection = $this->_originCollection->load();
        
        foreach ($collection as $origin) {
            $option[] = ['label' => $origin->getName(), 'value' => $origin->getOriginId()];
        }
        return $option;
    }
}
