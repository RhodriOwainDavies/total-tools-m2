<?php
namespace Temando\Temando\Block\Adminhtml\Shipment;

class View extends \Magento\Backend\Block\Template
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
     * Store Manager.
     *
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;
    
    /**
     * Status
     *
     * @var \Temando\Temando\Model\System\Config\Source\Shipment\Status
     */
    protected $_status = null;

    /**
     * Type
     *
     * @var \Temando\Temando\Model\System\Config\Source\Origin\Type
     */
    public $_type = null;

    /**
     * Auth Session.
     *
     * @var \Magento\Backend\Model\Auth\Session
     */
    public $_authSession;

    /**
     * Product Factory.
     *
     * @var \Magento\Catalog\Model\ProductFactory
     */
    protected $_productFactory;
    
    /**
     * View constructor.
     *
     * @param \Magento\Backend\Block\Widget\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Temando\Temando\Helper\Data $helper
     * @param \Temando\Temando\Model\System\Config\Source\Shipment\Status $status
     * @param \Temando\Temando\Model\Shipment $shipment
     * @param \Temando\Temando\Model\System\Config\Source\Origin\Type $type
     * @param \Magento\Backend\Model\Auth\Session $authSession
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Temando\Temando\Helper\Data $helper,
        \Temando\Temando\Model\System\Config\Source\Shipment\Status $status,
        \Temando\Temando\Model\Shipment $shipment,
        \Temando\Temando\Model\System\Config\Source\Origin\Type $type,
        \Magento\Catalog\Model\ProductFactory $productFactory,
        \Magento\Backend\Model\Auth\Session $authSession,
        array $data = []
    ) {
        $this->_coreRegistry = $registry;
        $this->_helper = $helper;
        $this->_status = $status;
        $this->_shipment = $shipment;
        $this->_type = $type;
        $this->_productFactory = $productFactory;
        $this->_authSession = $authSession;
        $this->_storeManager = $context->getStoreManager();
        parent::__construct($context, $data);
    }

    /**
     * Initialize shipment view block
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
     * Retrieve text for header element depending on loaded zone
     *
     * @return \Magento\Framework\Phrase
     */
    public function getHeaderText()
    {
        if ($this->_coreRegistry->registry('temando_shipment')->getId()) {
            return __("View Shipment '%1'", $this->escapeHtml(
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
     * Retrieve current shipment model instance
     *
     * @return \Temando\Temando\Model\Shipment
     */
    public function getShipment()
    {
        return $this->_coreRegistry->registry('temando_shipment');
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
     * Return the status of the shipment as text
     *
     * @return string
     */
    public function getShipmentStatusText()
    {
        return $this->_status->getOptionLabel($this->getShipment()->getStatus());
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

        $shipmentId = $this->getRequest()->getParam('shipment_id');
        $shipment = $this->_shipment->load($shipmentId);
        $user = $this->_authSession->getUser();

        $this->getToolbar()->addChild(
            'back_button',
            'Magento\Backend\Block\Widget\Button',
            [
                'label' => __('Shipments Grid'),
                'onclick' => "window.location.href = '" . $this->getUrl('*/*') . "'",
                'class' => 'action-back'
            ]
        );

        /**
         * Edit button (edit isn't an action so treated slightly differently)
         */
        if (($user->getAclRole()==1)
            ||
            (
                $this->_helper->_isAllowedAction('Temando_Temando::temando_shipments_edit_save')
                &&
                $shipment->getStatus() < \Temando\Temando\Model\System\Config\Source\Shipment\Status::BOOKED
            )
        ) {
            $editUrl = $this->getUrl(
                'temando/shipment/edit',
                array(
                    'shipment_id'   => $shipmentId
                )
            );
            $this->getToolbar()->addChild(
                'edit_button',
                'Magento\Backend\Block\Widget\Button',
                [
                    'label' => __('Edit'),
                    'class' => 'action-edit',
                    'onclick' => "window.location.href = '" . $editUrl . "';return false;",
                ]
            );
        }

        /**
         * Cancel button
         */
        if ($this->_helper->checkShipmentActionPermission(
            $shipment,
            \Temando\Temando\Model\System\Config\Source\Shipment\Status::CANCELLED
        )) {
            $cancelledUrl = $this->getUrl(
                'temando/shipment/status',
                array(
                    'id' => $shipmentId,
                    'status' => \Temando\Temando\Model\System\Config\Source\Shipment\Status::CANCELLED
                )
            );
            $this->getToolbar()->addChild(
                'update_status_cancelled_button',
                'Magento\Backend\Block\Widget\Button',
                [
                    'label' => __('Cancel'),
                    'class' => 'action-cancelled',
                    'onclick' => 'confirmSetLocation(\'' . __(
                        'Are you sure you wish to cancel?'
                    ) . '\', \'' . $cancelledUrl . '\')'
                ]
            );
        }

        /**
         * Book externally button
         */
        if ($this->_helper->checkShipmentActionPermission(
            $shipment,
            \Temando\Temando\Model\System\Config\Source\Shipment\Status::BOOKED_EXTERNALLY
        )) {
            $bookExternallyUrl = $this->getUrl(
                'temando/shipment/status',
                array(
                    'id'    => $shipmentId,
                    'status'=> \Temando\Temando\Model\System\Config\Source\Shipment\Status::BOOKED_EXTERNALLY
                )
            );
            $this->getToolbar()->addChild(
                'update_status_book_externally_button',
                'Magento\Backend\Block\Widget\Button',
                [
                    'label' => __('Book Externally'),
                    'class' => 'action-book-externally',
                    'onclick' => "window.location.href = '" . $bookExternallyUrl . "';return false;",
                ]
            );
        }

        /**
         * Pending button
         */
        if ($this->_helper->checkShipmentActionPermission(
            $shipment,
            \Temando\Temando\Model\System\Config\Source\Shipment\Status::PENDING
        )) {
            $pendingUrl = $this->getUrl(
                'temando/shipment/status',
                array(
                    'id' => $shipmentId,
                    'status' => \Temando\Temando\Model\System\Config\Source\Shipment\Status::PENDING
                )
            );
            $this->getToolbar()->addChild(
                'update_status_pending_button',
                'Magento\Backend\Block\Widget\Button',
                [
                    'label' => __('Stock Received'),
                    'class' => 'action-cancelled primary',
                    'onclick' => "window.location.href = '" . $pendingUrl . "';return false;",
                ]
            );
        }

        /**
         * Pickslip button
         */
        if ($this->_helper->checkShipmentActionPermission(
            $shipment,
            \Temando\Temando\Model\System\Config\Source\Shipment\Status::PICKING
        )) {
            $pickingUrl = $this->getUrl(
                'temando/shipment/status',
                array(
                    'id' => $shipmentId,
                    'status' => \Temando\Temando\Model\System\Config\Source\Shipment\Status::PICKING
                )
            );
            $this->getToolbar()->addChild(
                'update_status_picking_button',
                'Magento\Backend\Block\Widget\Button',
                [
                    'label' => __('Pickslip'),
                    'class' => 'action-picking primary',
                    'onclick' => "window.location.href = '" . $pickingUrl . "';return false;",
                ]
            );
        }

        /**
         * Packed button
         */
        if ($this->_helper->checkShipmentActionPermission(
            $shipment,
            \Temando\Temando\Model\System\Config\Source\Shipment\Status::PACKED
        )) {
            $packedUrl = $this->getUrl(
                'temando/shipment/status',
                array(
                    'id' => $shipmentId,
                    'status' => \Temando\Temando\Model\System\Config\Source\Shipment\Status::PACKED
                )
            );
            $this->getToolbar()->addChild(
                'update_status_packed_button',
                'Magento\Backend\Block\Widget\Button',
                [
                    'label' => __('Packed'),
                    'class' => 'action-packed primary',
                    'onclick' => "window.location.href = '" . $packedUrl . "';return false;",
                ]
            );
        }

        /**
         * Complete button
         */
        if ($this->_helper->checkShipmentActionPermission(
            $shipment,
            \Temando\Temando\Model\System\Config\Source\Shipment\Status::COMPLETE
        )) {
            $completeUrl = $this->getUrl(
                'temando/shipment/status',
                array(
                    'id' => $shipmentId,
                    'status' => \Temando\Temando\Model\System\Config\Source\Shipment\Status::COMPLETE
                )
            );
            $this->getToolbar()->addChild(
                'update_status_complete_button',
                'Magento\Backend\Block\Widget\Button',
                [
                    'label' => __('Complete'),
                    'class' => 'action-complete primary',
                    'onclick' => "window.location.href = '" . $completeUrl . "';return false;",
                ]
            );
        }

        return parent::_prepareLayout();
    }

    /**
     * Get Product.
     *
     * @param $productId
     *
     * @return $product
     */
    public function getProduct($productId)
    {
        return $this->_productFactory->create()->load($productId);
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
