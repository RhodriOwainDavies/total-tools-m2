<?php
namespace Temando\Temando\Controller\Adminhtml\Shipment;

use Magento\Backend\App\Action;
use Temando\Temando\Api\ShipmentManagementInterface;
use Temando\Temando\Api\ShipmentRepositoryInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\InputException;
use Psr\Log\LoggerInterface;

class View extends \Magento\Backend\App\Action //\Temando\Temando\Controller\Adminhtml\Shipment
{
    /**
     * Result Page Factory.
     *
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $_pageFactory;

    /**
     * Logger.
     *
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * Temando Shipment.
     *
     * @var \Temando\Temando\Model\Shipment
     */
    protected $_shipment;

    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'Temando_Temando::temando_shipments_view';

    /**
     * Constructor.
     *
     * @param Action\Context $context
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Magento\Framework\App\Response\Http\FileFactory $fileFactory
     * @param \Magento\Framework\Translate\InlineInterface $translateInline
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     * @param \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
     * @param \Magento\Framework\View\Result\LayoutFactory $resultLayoutFactory
     * @param \Magento\Framework\Controller\Result\RawFactory $resultRawFactory
     * @param LoggerInterface $logger
     *
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
     */
    public function __construct(
        Action\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Framework\App\Response\Http\FileFactory $fileFactory,
        \Magento\Framework\Translate\InlineInterface $translateInline,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
        \Magento\Framework\View\Result\LayoutFactory $resultLayoutFactory,
        \Magento\Framework\Controller\Result\RawFactory $resultRawFactory,
        \Magento\Framework\View\Result\PageFactory $pageFactory,
        \Temando\Temando\Model\Shipment $shipment,
        LoggerInterface $logger
    ) {
        $this->_coreRegistry = $coreRegistry;
        $this->_fileFactory = $fileFactory;
        $this->_translateInline = $translateInline;
        $this->resultPageFactory = $resultPageFactory;
        $this->resultJsonFactory = $resultJsonFactory;
        $this->resultLayoutFactory = $resultLayoutFactory;
        $this->resultRawFactory = $resultRawFactory;
//        $this->shipmentManagement = $shipmentManagement;
//        $this->shipmentRepository = $shipmentRepository;
        $this->_pageFactory = $pageFactory;
        $this->_shipment = $shipment;
        $this->logger = $logger;
        parent::__construct($context);
    }


    /**
     * View order detail
     *
     * @return \Magento\Backend\Model\View\Result\Page|\Magento\Backend\Model\View\Result\Redirect
     */
    public function execute()
    {
        $shipment = $this->_initShipment();

        $resultRedirect = $this->resultRedirectFactory->create();
        if ($shipment) {
            try {
                $resultPage = $this->_initAction();
                $resultPage->getConfig()->getTitle()->prepend(__('Shipment'));
            } catch (\Exception $e) {
                $this->logger->critical($e);
                $this->messageManager->addError(__('Exception occurred during shipment load')." ".$e->getMessage());
                $resultRedirect->setPath('temando/shipment/index');
                return $resultRedirect;
            }
            //$resultPage->getConfig()->getTitle()->prepend(sprintf("#%s", $shipment->getId()));
            return $resultPage;
        }
        $resultRedirect->setPath('shipment/*/');
        return $resultRedirect;
    }



    /**
     * {@inheritdoc}
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed($this::ADMIN_RESOURCE);
    }


    /**
     * Initialize order model instance
     *
     * @return \Magento\Sales\Api\Data\OrderInterface|false
     */
    protected function _initShipment()
    {
        $id = $this->getRequest()->getParam('shipment_id');
        try {
            $shipment = $this->_shipment->load($id);//$this->shipmentRepository->get($id);
        } catch (NoSuchEntityException $e) {
            $this->messageManager->addError(__('This shipment no longer exists.'));
            $this->_actionFlag->set('', self::FLAG_NO_DISPATCH, true);
            return false;
        } catch (InputException $e) {
            $this->messageManager->addError(__('This shipment no longer exists.'));
            $this->_actionFlag->set('', self::FLAG_NO_DISPATCH, true);
            return false;
        }
        $this->_coreRegistry->register('temando_shipment', $shipment);
        $this->_coreRegistry->register('current_shipment', $shipment);
        return $shipment;
    }
    /**
     * Init layout, menu and breadcrumb
     *
     * @return \Magento\Backend\Model\View\Result\Page
     */
    protected function _initAction()
    {
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('Temando_temando::temando');
        $resultPage->addBreadcrumb(__('Shipment'), __('Shipment'));
        $resultPage->addBreadcrumb(__('Manage Shipment'), __('Manage Shipment'));
        return $resultPage;
    }
}
