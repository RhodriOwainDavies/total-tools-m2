<?php
namespace Temando\Temando\Block\Adminhtml\Origin;

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
     * Initialize blog post edit block
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_objectId = 'origin_id';
        $this->_blockGroup = 'Temando_Temando';
        $this->_controller = 'adminhtml_origin';

        parent::_construct();

        if ($this->_helper->_isAllowedAction('Temando_Temando::temando_locations_save_origin')) {
            $this->buttonList->update('save', 'label', __('Save Store'));
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

        if ($this->_helper->_isAllowedAction('Temando_Temando::temando_locations_delete_origin')) {
            $this->buttonList->update('delete', 'label', __('Delete Store'));
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
        if ($this->_coreRegistry->registry('temando_origin')->getId()) {
            return __("Edit Store '%1'", $this->escapeHtml(
                $this->_coreRegistry->registry('temando_origin')->getName()
            ));
        } else {
            return __('New Store');
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
        return $this->getUrl('origin/*/save', ['_current' => true, 'back' => 'edit', 'active_tab' => '']);
    }
}
