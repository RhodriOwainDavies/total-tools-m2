<?php
namespace Temando\Temando\Controller\Adminhtml\Shipment;

use Magento\Backend\App\Action;
use Magento\TestFramework\ErrorLog\Logger;

class Save extends \Magento\Backend\App\Action
{

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
        return $this->_authorization->isAllowed('Temando_Temando::temando_shipments_edit_save');
    }

    /**
     * Save action.
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        //$this->_logger = $this->_objectManager->create('Psr\Log\LoggerInterface');
        $data = $this->getRequest()->getPostValue();
        $resultRedirect = $this->resultRedirectFactory->create();
        if ($data) {
            $model = $this->_objectManager->create('Temando\Temando\Model\Shipment');
            $this->_helper = $this->_objectManager->create('Temando\Temando\Helper\Data');
            $this->_emailHelper = $this->_objectManager->create('Temando\Temando\Helper\Email');

            $id = $this->getRequest()->getParam('shipment_id');
            $originalOriginId = null;
            if ($id) {
                $model->load($id);
                $originalOriginId = $model->getOriginId();
            }

            if (!$this->_helper->checkShipmentPermission($model)) {
                $this->messageManager->addErrorMessage(__('You do not have permission to save this shipment'));
                return $resultRedirect->setPath('*/*/');
            }
            $model->setData(array_merge($model->getData(), $data));

            if (empty($data['destination_type'])) {
                $model->setData(
                    'destination_type',
                    \Temando\Temando\Model\System\Config\Source\Origin\Type::RESIDENTIAL
                );
            }

            if (empty($data['destination_authority_to_leave'])) {
                $model->setData('destination_authority_to_leave', 0);
            }

            $this->_eventManager->dispatch(
                'temando_shipment_prepare_save',
                ['shipment' => $model, 'request' => $this->getRequest()]
            );

            try {
                $model->save();
                $response = null;
                if ($model->getStatus()<\Temando\Temando\Model\System\Config\Source\Shipment\Status::BOOKED) {
                    $response = $model->fetchQuotes();
                    if ($response instanceof \SoapFault) {
                        $errorMessage = $response->getMessage().' ('.$response->getCode().')';
                        $this->messageManager->addErrorMessage(__($errorMessage));
                    }
                }
                if ($originalOriginId != $data['origin_id']) {
                    $this->_emailHelper->sendOrderAllocationEmailToMerchant($model->getOrder(), $model->getOrigin());
                }
                $this->messageManager->addSuccessMessage(__('You saved this Shipment.'));
                $this->_objectManager->get('Magento\Backend\Model\Session')->setFormData(false);

                if ($response instanceof \SoapFault) {
                    $errorMessage = $response->getMessage().' ('.$response->getCode().')';
                    $this->messageManager->addErrorMessage(__($errorMessage));
                }

                if ($this->getRequest()->getParam('back')) {
                    return $resultRedirect->setPath('*/*/edit', ['shipment_id' => $model->getId(), '_current' => true]);
                }
                return $resultRedirect->setPath('*/*/view', ['shipment_id' => $model->getId(), '_current' => true]);
                //return $resultRedirect->setPath('*/*/');
            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            } catch (\RuntimeException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            }

            $this->_getSession()->setFormData($data);
            return $resultRedirect->setPath(
                '*/*/edit',
                [
                    'shipment_id' => $this->getRequest()->getParam('shipment_id')
                ]
            );
        }
        return $resultRedirect->setPath('*/*/');
    }
}
