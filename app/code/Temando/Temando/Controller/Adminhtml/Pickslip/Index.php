<?php
namespace Temando\Temando\Controller\Adminhtml\Pickslip;

class Index extends \Magento\Backend\App\Action
{
    /**
     * Object Manager
     *
     * @var \Magento\Framework\App\ObjectManager
     */
    protected $_objectManager;

    /**
     * Temando Helper.
     *
     * @var \Temando\Temando\Helper\Data
     */
    protected $_helper;

    /**
     * Temando Shipment.
     *
     * @var \Temando\Temando\Model\Shipment
     */
    protected $_shipment = null;

    /**
     * Temando Pickup.
     *
     * @var \Temando\Temando\Model\Pickup
     */
    protected $_pickup = null;

    /**
     * Index constructor.
     *
     * @param \Magento\Backend\App\Action\Context $context
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context
    ) {
        parent::__construct($context);
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $this->_objectManager = $objectManager;
        $this->_helper = $this->_objectManager->create('Temando\Temando\Helper\Data');
    }

    /**
     * Execute
     */
    public function execute()
    {
        $params = $this->getRequest()->getParams();
        if (array_key_exists("pickup", $params)) {
            //pickup
            $this->_pickup = $this->_objectManager->create('Temando\Temando\Model\Pickup');
            $this->_pickup->load($this->getRequest()->getParam('pickup'));
            $pickslipFile = $this->_helper->getPickslipDir() . DIRECTORY_SEPARATOR .
                $this->_pickup->getPickslipFilename();
        } else {
            //shipment
            $this->_shipment = $this->_objectManager->create('Temando\Temando\Model\Shipment');
            $this->_shipment->load($this->getRequest()->getParam('shipment'));
            $pickslipFile = $this->_helper->getPickslipDir() . DIRECTORY_SEPARATOR .
                $this->_shipment->getPickslipFilename();
        }
        $handle = fopen($pickslipFile, "r");
        $contents = fread($handle, filesize($pickslipFile));
        fclose($handle);

        $this->_helper->_prepareDownloadResponse($pickslipFile, $contents, false);
    }

    /**
     * Is the user allowed to view the blog post grid.
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        if ($this->_shipment) {
            return $this->_authorization->isAllowed('Temando_Temando::temando_shipments_view');
        } else {
            return $this->_authorization->isAllowed('Temando_Temando::temando_pickups_view');
        }
    }
}
