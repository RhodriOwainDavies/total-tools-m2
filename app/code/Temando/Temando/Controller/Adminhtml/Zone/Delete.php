<?php
namespace Temando\Temando\Controller\Adminhtml\Zone;

use Magento\Backend\App\Action;
use Magento\TestFramework\ErrorLog\Logger;

class Delete extends \Magento\Backend\App\Action
{

    /**
     * {@inheritdoc}
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Temando_Temando::temando_locations_delete_zone');
    }

    /**
     * Delete action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $id = $this->getRequest()->getParam('zone_id');
        $resultRedirect = $this->resultRedirectFactory->create();
        if ($id) {
            try {
                $model = $this->_objectManager->create('Temando\Temando\Model\Zone');
                $model->load($id);
                $model->delete();
                $this->messageManager->addSuccess(__('The zone has been deleted.'));
                return $resultRedirect->setPath('*/*/');
            } catch (\Exception $e) {
                $this->messageManager->addError($e->getMessage());
                return $resultRedirect->setPath('*/*/edit', ['zone_id' => $id]);
            }
        }
        $this->messageManager->addError(__('We can\'t find a zone to delete.'));
        return $resultRedirect->setPath('*/*/');
    }
}
