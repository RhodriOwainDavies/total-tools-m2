<?php
namespace Temando\Temando\Block\Adminhtml\Shipment;

class Edit extends \Magento\Backend\Block\Template
{
    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry = null;
    
    /**
     * Store Manager.
     *
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;
    
    /**
     * Helper
     *
     * @var \Temando\Temando\Helper\Data
     */
    protected $_helper = null;

    /**
     * Status
     *
     * @var \Temando\Temando\Model\System\Config\Source\Shipment\Status
     */
    protected $_status = null;

    /**
     * Edit constructor.
     *
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Temando\Temando\Helper\Data $helper
     * @param \Temando\Temando\Model\System\Config\Source\Shipment\Status $status
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Temando\Temando\Helper\Data $helper,
        \Temando\Temando\Model\System\Config\Source\Shipment\Status $status,
        array $data = []
    ) {
        $this->_coreRegistry = $registry;
        $this->_helper = $helper;
        $this->_status = $status;
        $this->__storeManager = $context->getStoreManager();
        parent::__construct($context, $data);
    }

    /**
     * Initialize zone edit block
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_objectId = 'shipment_id';
        $this->_blockGroup = 'Temando_Temando';
        $this->_controller = 'adminhtml_shipment';

        parent::_construct();
    }

    /**
     * Retrieve text for header element depending on loaded shipment
     *
     * @return \Magento\Framework\Phrase
     */
    public function getHeaderText()
    {
        if ($this->_coreRegistry->registry('temando_shipment')->getId()) {
            return __("Edit Shipment '%1'", $this->escapeHtml(
                $this->_coreRegistry->registry('temando_shipment')->getId()
            ));
        } else {
            return __('New Shipment');
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
        return $this->getUrl('shipment/*/save', ['_current' => true, 'back' => 'edit', 'active_tab' => '']);
    }

    /**
     * Get Helper
     *
     * @return Temando/Temando/Helper/Data
     */
    public function getHelper()
    {
        if (!$this->_helper) {
            $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
            $this->_helper = $objectManager->create('Temando\Temando\Helper\Data');
        }
        return $this->_helper;
    }

    /**
     * Retrieve current shipment model instance
     *
     * @return \Temando\Temando\Model\Shipment
     */
    public function getShipment()
    {
        return $this->_coreRegistry->registry('temando_shipment');
    }

    /**
     * Return form block HTML
     *
     * @return string
     */
    public function getForm()
    {
        return $this->getLayout()->createBlock('Temando\Temando\Block\Adminhtml\Shipment\Edit\Form')->toHtml();
    }

    /**
     * Preparing block layout.
     *
     * @return $this
     *
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    protected function _prepareLayout()
    {
        $shipment = $this->getShipment();

        $this->getToolbar()->addChild(
            'back_button',
            'Magento\Backend\Block\Widget\Button',
            [
                'label' => __('Shipments Grid'),
                'onclick' => "window.location.href = '" . $this->getUrl('*/*') . "'",
                'class' => 'action-back'
            ]
        );

        if ($this->_helper->_isAllowedAction('Temando_Temando::temando_shipments_edit_save')) {
            if ($shipment->getStatus()<\Temando\Temando\Model\System\Config\Source\Shipment\Status::BOOKED) {
                $this->getToolbar()->addChild(
                    'save_and_get_quotes_button',
                    'Magento\Backend\Block\Widget\Button',
                    [
                        'label' => __('Save &amp; Get Quotes'),
                        'data_attribute' => [
                            'role' => 'template-save-and-get-quotes',
                        ],
                        'class' => 'save-and-get-quotes primary',
                        'onclick' => 'jQuery(\'#edit_form\').submit();'
                    ]
                );
            } else {
                $this->getToolbar()->addChild(
                    'save_button',
                    'Magento\Backend\Block\Widget\Button',
                    [
                        'label' => __('Save'),
                        'data_attribute' => [
                            'role' => 'template-save',
                        ],
                        'class' => 'save primary',
                        'onclick' => 'jQuery(\'#edit_form\').submit();'
                    ]
                );
            }
        }

        return parent::_prepareLayout();
    }

    /**
     * Return the status of the shipment as text
     *
     * @return string
     */
    public function getShipmentStatusText()
    {
        return $this->_status->getOptionLabel($this->getShipment()->getStatus());
    }
    
    /**
     * Gets shipment currency code (by order store)
     *
     * @return string
     */

    public function getStoreCurrencyCode()
    {
        return $this->_storeManager->getStore()->getCurrentCurrency()->getCode();
    }
}
