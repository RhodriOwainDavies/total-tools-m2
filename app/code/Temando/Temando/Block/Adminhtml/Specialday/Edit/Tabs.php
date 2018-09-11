<?php

namespace Temando\Temando\Block\Adminhtml\Specialday\Edit;

class Tabs extends \Magestore\Storepickup\Block\Adminhtml\Specialday\Edit\Tabs
{
    protected function _prepareLayout()
    {
        parent::_prepareLayout();
        $this->removeTab('stores_section');
        return $this;
    }
}
