<?php

namespace Temando\Temando\Block\Adminhtml\Schedule\Edit;

class Tabs extends \Magestore\Storepickup\Block\Adminhtml\Schedule\Edit\Tabs
{
    protected function _prepareLayout()
    {
        parent::_prepareLayout();
        $this->removeTab('stores_section');
        return $this;
    }
}
