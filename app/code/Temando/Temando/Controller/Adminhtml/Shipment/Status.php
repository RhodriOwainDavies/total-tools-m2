<?php
namespace Temando\Temando\Controller\Adminhtml\Shipment;

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
     * Constructor.
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
        return $this->_authorization->isAllowed('Temando_Temando::temando_shipments_process');
    }

    /**
     * Save action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();

        $model = $this->_objectManager->create('Temando\Temando\Model\Shipment');
        $this->_helper = $this->_objectManager->create('Temando\Temando\Helper\Data');
        $id = $this->getRequest()->getParam('id');
        if ($id) {
            $model->load($id);
        }

        $status = $this->getRequest()->getParam('status');
        if (!$status) {
            $this->messageManager->addErrorMessage(__('Failed to find a new status for this Shipment'));
            return $resultRedirect->setPath(
                '*/*/view',
                [
                    'shipment_id' => $id
                ]
            );
        }

        if (!$this->_helper->checkShipmentPermission($model)) {
            $this->messageManager->addErrorMessage(__('You do not have permission to save this Shipment'));
            return $resultRedirect->setPath(
                '*/*/view',
                [
                    'shipment_id' => $id
                ]
            );
        }

        $model->setData('status', $status);

        $this->_eventManager->dispatch(
            'temando_shipment_prepare_save',
            [
                'shipment' => $model,
                'request' => $this->getRequest()
            ]
        );

        if ($status == \Temando\Temando\Model\System\Config\Source\Shipment\Status::PICKING) {
            //render PDF
            $order = $model->getOrder();
            if ($order->getId()) {
                $pdf = $this->_objectManager->create('Temando\Temando\Model\Pdf\Order\Pickslip')
                    ->getPdf($order, $id);

                $this->_fileFactory = $this->_objectManager->create('Magento\Framework\App\Response\Http\FileFactory');

                file_put_contents(
                    $this->_helper->getPickslipDir(true) . DIRECTORY_SEPARATOR . $model->getPickslipFilename(),
                    $pdf->render()
                );
            }
        }

        try {
            $model->save();
            $this->messageManager->addSuccessMessage(__('You saved this Shipment.'));
            $this->_objectManager->get('Magento\Backend\Model\Session')->setFormData(false);

            return $resultRedirect->setPath('*/*/view', ['shipment_id' => $model->getId(), '_current' => true]);
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
        } catch (\RuntimeException $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
        }

        return $resultRedirect->setPath('*/*/view', ['shipment_id' => $id]);
    }
}
