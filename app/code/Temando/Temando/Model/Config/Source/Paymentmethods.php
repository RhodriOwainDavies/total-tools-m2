<?php
namespace Temando\Temando\Model\Config\Source;

class Paymentmethods extends \Magestore\Storepickup\Model\Config\Source\Paymentmethods
{
    public function toOptionArray()
    {
        $storeCollection = $this->_collectionFactory->getActiveMethods();
        if (!count($storeCollection)) {
            return;
        }
        
        $options = array() ;
        foreach ($storeCollection as $item) {
            $title = $item->getTitle() ? $item->getTitle() : $item->getCode();
            $options[] = array('value'=> $item->getCode(), 'label' => $title);
        }

        return $options;
    }
}
