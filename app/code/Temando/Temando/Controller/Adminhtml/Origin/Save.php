<?php
namespace Temando\Temando\Controller\Adminhtml\Origin;

use Magento\Backend\App\Action;
use Magento\TestFramework\ErrorLog\Logger;

class Save extends \Magento\Backend\App\Action
{

    private $_backendHelperJs;

    /**
     * Save constructor.
     *
     * @param Action\Context $context
     */
    public function __construct(Action\Context $context)
    {
        parent::__construct($context);
    }

    /**
     * {@inheritdoc}
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Temando_Temando::temando_locations_save_origin');
    }

    /**
     * Save action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $data = $this->getRequest()->getPostValue();
        //$data = $this->_helper->transformArrayToString($data, 'store_ids');
        $data['store_ids'] = implode(',', $data['store_ids']);
        if (isset($data['user_ids'])) {
            $data['user_ids'] = implode(',', $data['user_ids']);
        } else {
            $data['user_ids'] = '';
        }


        if (isset($data['supporting_origins'])) {
            $data['supporting_origins'] = implode(',', $data['supporting_origins']);
        } else {
            $data['supporting_origins'] = '';
        }

        $resultRedirect = $this->resultRedirectFactory->create();
        if ($data) {
            $model = $this->_objectManager->create('Temando\Temando\Model\Origin');

            $id = $this->getRequest()->getParam('origin_id');
            
            $helper = $this->_objectManager->create('Temando\Temando\Helper\Data');
            if ($id) {
                $model->load($id);
                $originId = $id;
            } else {
                $originId = $helper->getAutoIncrementNumber('temando_origin');
            }

            $model->setData($data);
            $this->_prepareSerializedData($model);

            $this->_eventManager->dispatch(
                'temando_origin_prepare_save',
                ['origin' => $model, 'request' => $this->getRequest()]
            );

            try {
                //save origin
                $model->save();
                
                $this->messageManager->addSuccess(__('You saved this Store.'));
                
                $response = $model->syncWarehouse();
                if (is_soap_fault($response)) {
                    $this->messageManager->addError(
                        __('Problem synching with Temando API').': \''.$response->faultstring.'\''
                    );
                }
                
                $this->_objectManager->get('Magento\Backend\Model\Session')->setFormData(false);

                if ($this->getRequest()->getParam('back')) {
                    return $resultRedirect->setPath('*/*/edit', ['origin_id' => $model->getId(), '_current' => true]);
                }
                return $resultRedirect->setPath('*/*/');
            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                $this->messageManager->addError($e->getMessage());
            } catch (\RuntimeException $e) {
                $this->messageManager->addError($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addError($e->getMessage());
                $this->_getSession()->setSerializedHolidays($model->getData('serialized_holidays'));
                $this->_getSession()->setSerializedSpecialdays($model->getData('serialized_specialdays'));
                //$this->messageManager->addException($e, __('Something went wrong while saving the origin.'));
            }

            $this->_getSession()->setFormData($data);
            return $resultRedirect->setPath('*/*/edit', ['origin_id' => $this->getRequest()->getParam('origin_id')]);
        }
        return $resultRedirect->setPath('*/*/');
    }
    
    /**
     * Prepare serialized data for model.
     *
     * @param \Temando\Temando\Model\Origin $model
     *
     * @return $this
     */
    protected function _prepareSerializedData(\Temando\Temando\Model\Origin $model)
    {
        $this->_backendHelperJs = $this->_objectManager->create('Magento\Backend\Helper\Js');

        if ($model->hasData('serialized_holidays')) {
            $model->setData(
                'in_holiday_ids',
                $this->_backendHelperJs->decodeGridSerializedInput($model->getData('serialized_holidays'))
            );
        }

        if ($model->hasData('serialized_specialdays')) {
            $model->setData(
                'in_specialday_ids',
                $this->_backendHelperJs->decodeGridSerializedInput($model->getData('serialized_specialdays'))
            );
        }
        
        return $this;
    }
}
