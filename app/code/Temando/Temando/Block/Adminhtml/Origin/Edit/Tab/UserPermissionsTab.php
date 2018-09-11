<?php

namespace Temando\Temando\Block\Adminhtml\Origin\Edit\Tab;

class UserPermissionsTab extends \Magento\Backend\Block\Widget\Form\Generic implements
    \Magento\Backend\Block\Widget\Tab\TabInterface
{
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
        \Temando\Temando\Helper\Data $helper,
        array $data = []
    ) {
        $this->_helper = $helper;
        parent::__construct($context, $registry, $formFactory, $data);
    }
    
    protected function _prepareForm()
    {
        $model = $this->getRegistryModel();
        $form = $this->_formFactory->create();
        $form->setHtmlIdPrefix('origin_');

        $fieldset = $form->addFieldset(
            'user_fieldset',
            [
                'legend' => __('User Permissions'),
                'class' => 'fieldset-wide'
            ]
        );

        $fieldset->addField(
            'user_ids',
            'multiselect',
            [
                'name' => 'user_ids[]',
                'label' => __('Users'),
                'title' => __('Users'),
                'required' => false,
                'style' => 'height:10em',
                'values' => $this->_helper->getUsersOptionArray(),
                'value' => $model->getUserIds()
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
        return __('User Permissions');
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
        return __('User Permissions');
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
