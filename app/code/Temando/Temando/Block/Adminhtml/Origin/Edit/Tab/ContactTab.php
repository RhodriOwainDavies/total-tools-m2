<?php

namespace Temando\Temando\Block\Adminhtml\Origin\Edit\Tab;

class ContactTab extends \Magento\Backend\Block\Widget\Form\Generic implements
    \Magento\Backend\Block\Widget\Tab\TabInterface
{
    /**
     * Prepare form.
     *
     * @return $this
     */
    protected function _prepareForm()
    {
        $model = $this->getRegistryModel();
        $form = $this->_formFactory->create();
        $form->setHtmlIdPrefix('origin_');
        
        $fieldset = $form->addFieldset(
            'contact_fieldset',
            [
                'legend' => __('Contact Information'),
                'class' => 'fieldset-wide',
                'collapsable' => true
            ]
        );
        $fieldset->addField(
            'contact_name',
            'text',
            [
                'name' => 'contact_name',
                'label' => __('Contact Name'),
                'title' => __('Contact Name'),
                'required' => true
            ]
        );
        $fieldset->addField(
            'contact_email',
            'text',
            [
                'name' => 'contact_email',
                'label' => __('Contact Email'),
                'title' => __('Contact Email'),
                'required' => true,
                'class' => 'validate-email'
            ]
        );
        $fieldset->addField(
            'contact_phone_1',
            'text',
            [
                'name' => 'contact_phone_1',
                'label' => __('Phone 1'),
                'title' => __('Phone 2'),
                'required' => true,
                'class' => 'validate-number'
            ]
        );
        $fieldset->addField(
            'contact_phone_2',
            'text',
            [
                'name' => 'contact_phone_2',
                'label' => __('Phone 2'),
                'title' => __('Phone 2'),
                'required' => false,
                'class' => 'validate-number'
            ]
        );
        $fieldset->addField(
            'contact_fax',
            'text',
            [
                'name' => 'contact_fax',
                'label' => __('Fax'),
                'title' => __('Fax'),
                'required' => false,
                'class' => 'validate-number'
            ]
        );
        
        $form->setValues($model->getData());
        $this->setForm($form);

        return parent::_prepareForm();
    }
     /**
     * Get registry model.
     *
     * @return \Temando\Temamdo\Model\Store
     */
    public function getRegistryModel()
    {
        return $this->_coreRegistry->registry('temando_origin');
    }

    /**
     * Return Tab label.
     *
     * @return string
     *
     * @api
     */
    public function getTabLabel()
    {
        return __('Contact Information');
    }

    /**
     * Return Tab title.
     *
     * @return string
     *
     * @api
     */
    public function getTabTitle()
    {
        return __('Contact Information');
    }

    /**
     * Can show tab in tabs.
     *
     * @return bool
     *
     * @api
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * Tab is hidden.
     *
     * @return bool
     *
     * @api
     */
    public function isHidden()
    {
        return false;
    }
}
