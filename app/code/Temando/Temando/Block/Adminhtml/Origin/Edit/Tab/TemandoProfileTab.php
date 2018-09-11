<?php

namespace Temando\Temando\Block\Adminhtml\Origin\Edit\Tab;

class TemandoProfileTab extends \Magento\Backend\Block\Widget\Form\Generic implements
    \Magento\Backend\Block\Widget\Tab\TabInterface
{
    /**
     * Yes/No Source.
     *
     * @var \Magento\Config\Model\Config\Source\Yesno
     */
    protected $_yesno;
    
    /**
     * Temando Helper.
     *
     * @var \Temando\Temando\Helper\Data
     */
    protected $_helper;
    
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Magento\Config\Model\Config\Source\Yesno $yesno,
        \Temando\Temando\Helper\Data $helper,
        array $data = []
    ) {
        $this->_yesno = $yesno;
        $this->_helper = $helper;
        parent::__construct($context, $registry, $formFactory, $data);
    }
    
    protected function _prepareForm()
    {
        $model = $this->getRegistryModel();
        $form = $this->_formFactory->create();
        $form->setHtmlIdPrefix('origin_');
        
        $fieldset = $form->addFieldset(
            'profile_fieldset',
            [
                'legend' => __('Temando Profile'),
                'class' => 'fieldset-wide'
            ]
        );
        $fieldset->addField(
            'account_mode',
            'select',
            [
                'name' => 'account_mode',
                'label' => __('Mode'),
                'title' => __('Mode'),
                'required' => true,
                'values' => [
                    '0' => __('*Use System Configuration'),
                    '1' => __('As Defined')
                ]
            ]
        );
        $fieldset->addField(
            'account_sandbox',
            'select',
            [
                'name' => 'account_sandbox',
                'label' => __('Sandbox'),
                'title' => __('Sandbox'),
                'required' => false,
                'values' => $this->_yesno->toOptionArray(),
                'note' => __(
                    'If set to "Yes", the sandbox (testing) service '
                    . 'will be used (usually set to "No" on a live site)'
                ),
            ]
        );
        $fieldset->addField(
            'account_clientid',
            'text',
            [
                'name' => 'account_clientid',
                'label' => __('Client ID'),
                'title' => __('Client ID'),
                'required' => false
            ]
        );
        $fieldset->addField(
            'account_username',
            'text',
            [
                'name' => 'account_username',
                'label' => __('Username'),
                'title' => __('Username'),
                'required' => false
            ]
        );

        $fieldset->addField(
            'account_password',
            'password',
            [
                'name' => 'account_password',
                'label' => __('Password'),
                'title' => __('Password'),
                'required' => false
            ]
        );
 
        $form->setValues($model->getData());
        $this->setForm($form);

        return parent::_prepareForm();
    }

    /**
     * Get registry model.
     *
     * @return \Magestore\Storepickup\Model\Store
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
        return __('Temando Profile');
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
        return __('Temando Profile');
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
