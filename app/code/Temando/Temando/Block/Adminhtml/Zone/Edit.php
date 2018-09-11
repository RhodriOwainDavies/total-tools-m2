<?php
namespace Temando\Temando\Block\Adminhtml\Zone;

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
        $this->_objectId = 'zone_id';
        $this->_blockGroup = 'Temando_Temando';
        $this->_controller = 'adminhtml_zone';

        parent::_construct();

        if ($this->_helper->_isAllowedAction('Temando_Temando::temando_locations_save_zone')) {
            $this->buttonList->update('save', 'label', __('Save Shipping Zone'));
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

        if ($this->_helper->_isAllowedAction('Temando_Temando::temando_locations_delete_zone')) {
            $this->buttonList->update('delete', 'label', __('Delete Zone'));
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
        if ($this->_coreRegistry->registry('temando_zone')->getId()) {
            return __("Edit Zone '%1'", $this->escapeHtml($this->_coreRegistry->registry('temando_zone')->getName()));
        } else {
            return __('New Zone');
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
        return $this->getUrl('zone/*/save', ['_current' => true, 'back' => 'edit', 'active_tab' => '']);
    }
}
