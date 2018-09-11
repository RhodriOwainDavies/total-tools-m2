<?php
namespace Temando\Temando\Controller\Adminhtml\Origin;

use Magento\Backend\App\Action;
use Magento\TestFramework\ErrorLog\Logger;

class Delete extends \Magento\Backend\App\Action
{

    /**
     * {@inheritdoc}
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Temando_Temando::temando_locations_delete_origin');
    }

    /**
     * Delete action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */

    public function execute()
    {
        $id = $this->getRequest()->getParam('origin_id');
        $resultRedirect = $this->resultRedirectFactory->create();
        if ($id) {
            if ($id==\Temando\Temando\Model\Resource\Origin\Collection::FALLBACK_STORE_ID) {
                $this->messageManager->addErrorMessage(__('Can\'t delete store ID 1'));
                return $resultRedirect->setPath('*/*/');
            }
            try {
                $model = $this->_objectManager->create('Temando\Temando\Model\Origin');
                $model->load($id);
                $model->delete();
                $this->messageManager->addSuccessMessage(__('The store has been deleted'));
                return $resultRedirect->setPath('*/*/');
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
                return $resultRedirect->setPath('*/*/edit', ['origin_id' => $id]);
            }
        }
        $this->messageManager->addErrorMessage(__('We can\'t find a store to delete.'));
        return $resultRedirect->setPath('*/*/');
    }
}
