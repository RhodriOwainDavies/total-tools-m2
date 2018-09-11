<?php
namespace Temando\Temando\Controller\Adminhtml\Rule;

use Magento\Backend\App\Action;
use Magento\TestFramework\ErrorLog\Logger;

class Delete extends \Magento\Backend\App\Action
{

    /**
     * {@inheritdoc}
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Temando_Temando::temando_shipping_rules_delete');
    }

    /**
     * Delete action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $id = $this->getRequest()->getParam('rule_id');
        $resultRedirect = $this->resultRedirectFactory->create();
        if ($id) {
            try {
                $model = $this->_objectManager->create('Temando\Temando\Model\Rule');
                $model->load($id);
                $model->delete();
                $this->messageManager->addSuccessMessage(__('The rule has been deleted'));
                return $resultRedirect->setPath('*/*/');
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
                return $resultRedirect->setPath('*/*/edit', ['rule_id' => $id]);
            }
        }
        $this->messageManager->addErrorMessage(__('We can\'t find a rule to delete.'));
        return $resultRedirect->setPath('*/*/');
    }
}
