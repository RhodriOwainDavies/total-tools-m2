<?php
namespace Temando\Temando\Block\Adminhtml\Rule;

class Edit extends \Magento\Backend\Block\Widget\Form\Container
{
    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry = null;

    /**
     * Temando Helper
     *
     * @var \Temando\Temando\Helper\Data
     */
    protected $_helper;

    /**
     * Edit constructor.
     *
     * @param \Magento\Backend\Block\Widget\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Temando\Temando\Helper\Data $helper
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Widget\Context $context,
        \Magento\Framework\Registry $registry,
        \Temando\Temando\Helper\Data $helper,
        array $data = []
    ) {
        $this->_coreRegistry = $registry;
        $this->_helper = $helper;
        parent::__construct($context, $data);
    }

    /**
     * Initialize zone edit block
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_objectId = 'rule_id';
        $this->_blockGroup = 'Temando_Temando';
        $this->_controller = 'adminhtml_rule';

        parent::_construct();

        if ($this->_helper->_isAllowedAction('Temando_Temando::temando_rules_save')) {
            $this->buttonList->update('save', 'label', __('Save Shipping Rule'));
            $this->buttonList->add(
                'saveandcontinue',
                [
                    'label' => __('Save and Continue Edit'),
                    'class' => 'save',
                    'data_attribute' => [
                        'mage-init' => [
                            'button' => ['event' => 'saveAndContinueEdit', 'target' => '#edit_form'],
                        ],
                    ]
                ],
                -100
            );
        } else {
            $this->buttonList->remove('save');
        }

        if ($this->_helper->_isAllowedAction('Temando_Temando::temando_rules_delete')) {
            $this->buttonList->update('delete', 'label', __('Delete Rule'));
        } else {
            $this->buttonList->remove('delete');
        }
    }

    /**
     * Retrieve text for header element depending on loaded post
     *
     * @return \Magento\Framework\Phrase
     */
    public function getHeaderText()
    {
        if ($this->_coreRegistry->registry('temando_rule')->getId()) {
            return __("Edit Rule '%1'", $this->escapeHtml($this->_coreRegistry->registry('temando_rule')->getName()));
        } else {
            return __('New Rule');
        }
    }

    /**
     * Getter of url for "Save and Continue" button
     * tab_id will be replaced by desired by JS later
     *
     * @return string
     */
    protected function _getSaveAndContinueUrl()
    {
        return $this->getUrl('rule/*/save', ['_current' => true, 'back' => 'edit', 'active_tab' => '']);
    }
}
