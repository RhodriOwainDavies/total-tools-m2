<?php
namespace Temando\Temando\Controller\Adminhtml\Pickup;

use Magento\Backend\App\Action;
use Magento\TestFramework\ErrorLog\Logger;

class Save extends \Magento\Backend\App\Action
{
    protected $_objectManager;

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
        return $this->_authorization->isAllowed('Temando_Temando::temando_pickups_edit_save');
    }

    /**
     * Save action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $data = $this->getRequest()->getPostValue();
        $resultRedirect = $this->resultRedirectFactory->create();
        if ($data) {
            $model = $this->_objectManager->create('Temando\Temando\Model\Pickup');
            
            $this->_helper = $this->_objectManager->create('Temando\Temando\Helper\Data');
            $this->_emailHelper = $this->_objectManager->create('Temando\Temando\Helper\Email');
            $id = $this->getRequest()->getParam('pickup_id');
            $originalOriginId = null;
            if ($id) {
                $model->load($id);
                $originalOriginId = $model->getOriginId();
            }

            if (!$this->_helper->checkPickupPermission($model)) {
                $this->messageManager->addErrorMessage(__('You do not have permission to save this Pickup'));
                return $resultRedirect->setPath('*/*/');
            }
            $model->setData(array_merge($model->getData(), $data));

            $this->_eventManager->dispatch(
                'temando_pickup_prepare_save',
                ['pickup' => $model, 'request' => $this->getRequest()]
            );

            try {
                $model->save();
                if ($originalOriginId != $data['origin_id']) {
                    $this->_emailHelper->sendOrderAllocationEmailToMerchant($model->getOrder(), $model->getOrigin());
                }
                $this->messageManager->addSuccessMessage(__('You saved this Pickup.'));
                $this->_objectManager->get('Magento\Backend\Model\Session')->setFormData(false);

                if ($this->getRequest()->getParam('back')) {
                    return $resultRedirect->setPath('*/*/edit', ['pickup_id' => $model->getId(), '_current' => true]);
                }
                return $resultRedirect->setPath('*/*/view', ['pickup_id' => $model->getId(), '_current' => true]);
                //return $resultRedirect->setPath('*/*/');
            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            } catch (\RuntimeException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            }

            $this->_getSession()->setFormData($data);
            return $resultRedirect->setPath('*/*/edit', ['pickup_id' => $this->getRequest()->getParam('pickup_id')]);
        }
        return $resultRedirect->setPath('*/*/');
    }
}
