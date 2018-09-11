<?php
namespace Temando\Temando\Controller\Adminhtml\Pickup;

use Magento\Backend\App\Action;
use Magento\TestFramework\ErrorLog\Logger;

class Status extends \Magento\Backend\App\Action
{
    /**
     * Object Manager.
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
     * Escaper.
     *
     * @var \Magento\Framework\Escaper
     */
    protected $_escaper;

    /**
     * Transport Builder.
     *
     * @var \Magento\Framework\Mail\Template\TransportBuilder
     */
    protected $_transportBuilder;

    /**
     * Scope Config.
     *
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $_scopeConfig;

    /**
     * Sales Order Factory.
     *
     * @var \Magento\Sales\Model\OrderFactory
     */
    protected $_salesOrderFactory;

    /**
     * Construct.
     *
     * @param Action\Context $context
     */
    public function __construct(Action\Context $context)
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $this->_objectManager = $objectManager;


        parent::__construct($context);
    }

    /**
     * {@inheritdoc}
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Temando_Temando::temando_pickups_process');
    }

    /**
     * Save action.
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();

        $model = $this->_objectManager->create('Temando\Temando\Model\Pickup');
        $this->_helper = $this->_objectManager->create('\Temando\Temando\Helper\Data');
        $this->_emailHelper = $this->_objectManager->create('\Temando\Temando\Helper\Email');
        $this->_escaper = $this->_objectManager->create('\Magento\Framework\Escaper');
        $this->_transportBuilder = $this->_objectManager->create('\Magento\Framework\Mail\Template\TransportBuilder');
        $this->_scopeConfig = $this->_objectManager->create('\Magento\Framework\App\Config\ScopeConfigInterface');
        $this->_salesOrderFactory = $this->_objectManager->create('\Magento\Sales\Model\OrderFactory');

        $id = $this->getRequest()->getParam('id');
        if ($id) {
            $model->load($id);
        }

        $status = $this->getRequest()->getParam('status');
        if (!$status) {
            $this->messageManager->addErrorMessage(__('Failed to find a new status for this Pickup'));
            return $resultRedirect->setPath(
                '*/*/view',
                [
                    'pickup_id' => $id
                ]
            );
        }

        if (!$this->_helper->checkPickupPermission($model)) {
            $this->messageManager->addErrorMessage(__('You do not have permission to save this Pickup'));
            return $resultRedirect->setPath(
                '*/*/view',
                [
                    'pickup_id' => $id
                ]
            );
        }
        $model->setData('status', $status);
        if ($status == \Temando\Temando\Model\System\Config\Source\Pickup\Status::AWAITING) {
            $model->setData('ready_date', date('Y-m-d H:i:s'));
            //send email
            $this->_emailHelper->sendPickupReadyEmailToCustomer($model);
        } elseif ($status == \Temando\Temando\Model\System\Config\Source\Pickup\Status::COLLECTED) {
            $model->setData('collected_date', date('Y-m-d H:i:s'));
        }
        $this->_eventManager->dispatch(
            'temando_pickup_prepare_save',
            [
                'pickup' => $model,
                'request' => $this->getRequest()
            ]
        );

        if ($status == \Temando\Temando\Model\System\Config\Source\Pickup\Status::PICKING) {
            //render PDF
            $order = $model->getOrder();
            if ($order->getId()) {
                $pdf = $this->_objectManager->create('Temando\Temando\Model\Pdf\Order\Pickslip')
                    ->getPdf($order);

                $this->_fileFactory = $this->_objectManager->create('Magento\Framework\App\Response\Http\FileFactory');

                file_put_contents(
                    $this->_helper->getPickslipDir(true) . DIRECTORY_SEPARATOR . $model->getPickslipFilename(),
                    $pdf->render()
                );
            }
        }

        try {
            $model->save();

            $this->messageManager->addSuccessMessage(__('You saved this Pickup.'));
            $this->_objectManager->get('Magento\Backend\Model\Session')->setFormData(false);

            return $resultRedirect->setPath('*/*/view', ['pickup_id' => $model->getId(), '_current' => true]);
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
        } catch (\RuntimeException $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
        }

        return $resultRedirect->setPath('*/*/view', ['pickup_id' => $id]);
    }
}
