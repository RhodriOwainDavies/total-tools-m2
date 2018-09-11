<?php

namespace Temando\Temando\Block\Adminhtml\Holiday\Edit;

class Tabs extends \Magestore\Storepickup\Block\Adminhtml\Holiday\Edit\Tabs
{
    protected function _prepareLayout()
    {
        parent::_prepareLayout();
        $this->removeTab('stores_section');
        return $this;
    }
}
