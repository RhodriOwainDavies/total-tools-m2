<?php
namespace Temando\Temando\Block\Adminhtml\Pickup;

class Edit extends \Magento\Backend\Block\Template
{
    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry = null;

    /**
     * Helper
     *
     * @var \Temando\Temando\Helper\Data
     */
    protected $_helper = null;

    /**
     * Status
     *
     * @var \Temando\Temando\Model\System\Config\Source\Pickup\Status
     */
    protected $_status = null;

    protected $_pickup;
    
    protected $_productFactory;

    /**
     * Edit constructor.
     *
     * @param \Magento\Backend\Block\Widget\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Temando\Temando\Helper\Data $helper
     * @param \Magento\Catalog\Model\ProductFactory $productFactory
     * @param \Temando\Temando\Model\System\Config\Source\Pickup\Status $status
     * @param \Temando\Temando\Model\Pickup $pickup
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Widget\Context $context,
        \Magento\Framework\Registry $registry,
        \Temando\Temando\Helper\Data $helper,
        \Magento\Catalog\Model\ProductFactory $productFactory,
        \Temando\Temando\Model\System\Config\Source\Pickup\Status $status,
        \Temando\Temando\Model\Pickup $pickup,
        array $data = []
    ) {
        $this->_coreRegistry = $registry;
        $this->_helper = $helper;
        $this->_status = $status;
        $this->_pickup = $pickup;
        $this->_productFactory = $productFactory;
        parent::__construct($context, $data);
    }

    /**
     * Initialize zone edit block
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_objectId = 'pickup_id';
        $this->_blockGroup = 'Temando_Temando';
        $this->_controller = 'adminhtml_pickup';

        parent::_construct();
    }

    /**
     * Retrieve text for header element depending on loaded pickup
     *
     * @return \Magento\Framework\Phrase
     */
    public function getHeaderText()
    {
        if ($this->_coreRegistry->registry('temando_pickup')->getId()) {
            return __("Edit Pickup '%1'", $this->escapeHtml($this->_coreRegistry->registry('temando_pickup')->getId()));
        } else {
            return __('New Pickup');
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
        return $this->getUrl('pickup/*/save', ['_current' => true, 'back' => 'edit', 'active_tab' => '']);
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
     * Retrieve current pickup model instance
     *
     * @return \Temando\Temando\Model\Pickup
     */
    public function getPickup()
    {
        return $this->_coreRegistry->registry('temando_pickup');
    }

    /**
     * Return form block HTML
     *
     * @return string
     */
    public function getForm()
    {
        return $this->getLayout()->createBlock('Temando\Temando\Block\Adminhtml\Pickup\Edit\Form')->toHtml();
    }

    /**
     * Preparing block layout
     *
     * @return $this
     *
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    protected function _prepareLayout()
    {

        $pickupId = $this->getRequest()->getParam('pickup_id');
        $pickup = $this->_pickup->load($pickupId);
        
        $this->getToolbar()->addChild(
            'back_button',
            'Magento\Backend\Block\Widget\Button',
            [
                'label' => __('Back'),
                'onclick' => "window.location.href = '" . $this->getUrl('*/*') . "'",
                'class' => 'action-back'
            ]
        );
         
        if (($this->_helper->_isAllowedAction('Temando_Temando::temando_pickups_edit_save'))
            &&
            ($pickup->getStatus() != \Temando\Temando\Model\System\Config\Source\Pickup\Status::COLLECTED)) {
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

        return parent::_prepareLayout();
    }

    /**
     * Return the status of the pickup as text
     *
     * @return string
     */
    public function getPickupStatusText()
    {
        return $this->_status->getOptionLabel($this->getPickup()->getStatus());
    }
    
    /**
     * Get Customer Selected Origin
     *
     * @return \Temando\Temando\Model\Origin
     */
    
    public function getProduct($productId)
    {
        return $this->_productFactory->create()->load($productId);
    }
    
    /**
     * Get Product By Sku
     *
     * @param $productSku
     *
     * @return $product
     */
    public function getProductBySku($productSku)
    {
        return $this->_productFactory->create()->loadByAttribute('sku', $productSku);
    }
}
