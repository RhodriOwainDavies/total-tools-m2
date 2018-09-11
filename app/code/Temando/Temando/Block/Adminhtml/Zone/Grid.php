<?php

namespace Temando\Temando\Block\Adminhtml\Zone;

class Grid extends \Magento\Backend\Block\Widget\Grid\Container
{
    protected function _construct()
    {
        $this->_blockGroup = 'Temando_Temando';
        $this->_controller = 'adminhtml_zone';
        $this->_headerText = __('Zones');
        $this->_addButtonLabel = __('Add New Zone');
        parent::_construct();
        $this->buttonList->add(
            'zone_add',
            [
                'label' => __('zone'),
                'onclick' => "location.href='" . $this->getUrl('temando/*/addZone') . "'",
                'class' => 'add'
            ]
        );
    }
}
