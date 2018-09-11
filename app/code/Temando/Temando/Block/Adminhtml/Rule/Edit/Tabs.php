<?php

namespace Temando\Temando\Block\Adminhtml\Rule\Edit;

/**
 * Class Tabs.
 *
 * @category Temando
 * @package  Temando_Temando
 */
class Tabs extends \Magento\Backend\Block\Widget\Tabs
{
    /**
     * Construct.
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('rule_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(__('Rule Information'));
        parent::_prepareLayout();
    }
    /**
     * Preparing global layout.
     *
     * You can redefine this method in child classes for changing layout
     *
     * @return $this
     */
    protected function _prepareLayout()
    {

        parent::_prepareLayout();
        $this->addTab('temando_rule_general', 'rule_edit_tab_general');
        $this->addTab('temando_rule_conditions', 'rule_edit_tab_conditions');
        $this->addTab('temando_rule_action', 'rule_edit_tab_action');

        return $this;
    }
}
