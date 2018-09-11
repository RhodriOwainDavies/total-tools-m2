<?php
namespace Temando\Temando\Block\Adminhtml\Pickup;

class View extends \Magento\Backend\Block\Template
{
    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry = null;

    /**
     * Helper.
     *
     * @var \Temando\Temando\Helper\Data
     */
    protected $_helper = null;

    /**
     * Status.
     *
     * @var \Temando\Temando\Model\System\Config\Source\Pickup\Status
     */
    protected $_status = null;
    
    /**
     * Temando Pickup.
     *
     * @var \Temando\Temando\Model\Pickup
     */
    protected $_pickup;

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
     * @param \Temando\Temando\Model\System\Config\Source\Pickup\Status $status
     * @param \Temando\Temando\Model\Pickup $pickup
     * @param \Magento\Catalog\Model\ProductFactory $productFactory
     * @param \Magento\Backend\Model\Auth\Session $authSession
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Widget\Context $context,
        \Magento\Framework\Registry $registry,
        \Temando\Temando\Helper\Data $helper,
        \Temando\Temando\Model\System\Config\Source\Pickup\Status $status,
        \Temando\Temando\Model\Pickup $pickup,
        \Magento\Catalog\Model\ProductFactory $productFactory,
        \Magento\Backend\Model\Auth\Session $authSession,
        array $data = []
    ) {
        $this->_coreRegistry = $registry;
        $this->_helper = $helper;
        $this->_status = $status;
        $this->_pickup = $pickup;
        $this->_productFactory = $productFactory;
        $this->_authSession = $authSession;
        parent::__construct($context, $data);
    }

    /**
     * Initialize pickup view block
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
     * Retrieve text for header element depending on loaded zone
     *
     * @return \Magento\Framework\Phrase
     */
    public function getHeaderText()
    {
        if ($this->_coreRegistry->registry('temando_pickup')->getId()) {
            return __("View Pickup '%1'", $this->escapeHtml($this->_coreRegistry->registry('temando_pickup')->getId()));
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
     * Retrieve current pickup model instance
     *
     * @return \Temando\Temando\Model\Pickup
     */
    public function getPickup()
    {
        return $this->_coreRegistry->registry('temando_pickup');
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
     * Return the status of the pickup as text
     *
     * @return string
     */
    public function getPickupStatusText()
    {
        return $this->_status->getOptionLabel($this->getPickup()->getStatus());
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
        $user = $this->_authSession->getUser();
        
        $this->getToolbar()->addChild(
            'back_button',
            'Magento\Backend\Block\Widget\Button',
            [
                'label' => __('Pickup Grid'),
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
                $this->_helper->_isAllowedAction('Temando_Temando::temando_pickups_process')
                &&
                $pickup->getStatus() < \Temando\Temando\Model\System\Config\Source\Pickup\Status::COLLECTED
            )
        ) {
            $editUrl = $this->getUrl(
                'temando/pickup/edit',
                array(
                    'pickup_id'   => $pickupId
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
        if ($this->_helper->checkPickupActionPermission(
            $pickup,
            \Temando\Temando\Model\System\Config\Source\Pickup\Status::CANCELLED
        )) {
            $cancelledUrl = $this->getUrl(
                'temando/pickup/status',
                array(
                    'id' => $pickupId,
                    'status' => \Temando\Temando\Model\System\Config\Source\Pickup\Status::CANCELLED
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
         * Pending button
         */
        if ($this->_helper->checkPickupActionPermission(
            $pickup,
            \Temando\Temando\Model\System\Config\Source\Pickup\Status::PENDING
        )) {
            $pendingUrl = $this->getUrl(
                'temando/pickup/status',
                array(
                    'id' => $pickupId,
                    'status' => \Temando\Temando\Model\System\Config\Source\Pickup\Status::PENDING
                )
            );
            $this->getToolbar()->addChild(
                'update_status_pending_button',
                'Magento\Backend\Block\Widget\Button',
                [
                    'label' => __('Stock Received'),
                    'class' => 'action-pending primary',
                    'onclick' => "window.location.href = '" . $pendingUrl . "';return false;",
                ]
            );
        }

        /**
         * Pickslip button
         */
        if ($this->_helper->checkPickupActionPermission(
            $pickup,
            \Temando\Temando\Model\System\Config\Source\Pickup\Status::PICKING
        )) {
            $pickingUrl = $this->getUrl(
                'temando/pickup/status',
                array(
                    'id' => $pickupId,
                    'status' => \Temando\Temando\Model\System\Config\Source\Pickup\Status::PICKING
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
        if ($this->_helper->checkPickupActionPermission(
            $pickup,
            \Temando\Temando\Model\System\Config\Source\Pickup\Status::PACKED
        )) {
            $packedUrl = $this->getUrl(
                'temando/pickup/status',
                array(
                    'id' => $pickupId,
                    'status' => \Temando\Temando\Model\System\Config\Source\Pickup\Status::PACKED
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
         * Ready for Collection button
         */
        if ($this->_helper->checkPickupActionPermission(
            $pickup,
            \Temando\Temando\Model\System\Config\Source\Pickup\Status::AWAITING
        )) {
            $readyUrl = $this->getUrl('temando/pickup/status', array(
                'id' => $pickupId,
                'status' => \Temando\Temando\Model\System\Config\Source\Pickup\Status::AWAITING
            ));
            $this->getToolbar()->addChild(
                'ready_to_collect_button',
                'Magento\Backend\Block\Widget\Button',
                [
                    'label' => __('Ready to collect'),
                    'onclick' => "window.location.href = '" . $readyUrl . "'",
                    'class' => 'action-ready-to-collect primary'
                ]
            );
        }

        /**
         * Collected button
         */
        if ($this->_helper->checkPickupActionPermission(
            $pickup,
            \Temando\Temando\Model\System\Config\Source\Pickup\Status::COLLECTED
        )) {
            $collectedUrl = $this->getUrl('temando/pickup/status', array(
                'id' => $pickupId,
                'status' => \Temando\Temando\Model\System\Config\Source\Pickup\Status::COLLECTED
            ));
            $this->getToolbar()->addChild(
                'ready_to_collect_button',
                'Magento\Backend\Block\Widget\Button',
                [
                    'label' => __('Collected'),
                    'onclick' => "window.location.href = '" . $collectedUrl . "'",
                    'class' => 'action-ready-to-collect primary'
                ]
            );
        }
    }

    /**
     * Get Product
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
