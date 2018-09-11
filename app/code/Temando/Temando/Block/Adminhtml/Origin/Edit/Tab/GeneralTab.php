<?php

namespace Temando\Temando\Block\Adminhtml\Origin\Edit\Tab;

class GeneralTab extends \Magento\Backend\Block\Widget\Form\Generic implements
    \Magento\Backend\Block\Widget\Tab\TabInterface
{
    /**
     * System Store.
     *
     * @var \Magento\Store\Model\System\Store
     */
    protected $_systemStore;

    /**
     * Country Factory.
     *
     * @var \Magento\Directory\Model\Config\Source\Country
     */
    protected $_countryFactory;

    /**
     * Is Active Source.
     *
     * @var \Temando\Temando\Model\Origin\Source\IsActive
     */
    protected $_isActive;

    /**
     * Config Type.
     *
     * @var \Temando\Temando\Model\System\Config\Source\Origin\Type
     */
    protected $_type;

    /**
     * Temando Helper.
     *
     * @var \Temando\Temando\Helper\Data
     */
    protected $_helper;

    /**
     * GeneralTab constructor.
     *
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Data\FormFactory $formFactory
     * @param \Magento\Store\Model\System\Store $systemStore
     * @param \Temando\Temando\Model\Origin\Source\IsActive $isActive
     * @param \Temando\Temando\Model\System\Config\Source\Origin\Type $type
     * @param \Temando\Temando\Helper\Data $helper
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Magento\Store\Model\System\Store $systemStore,
        \Temando\Temando\Model\Origin\Source\IsActive $isActive,
        \Temando\Temando\Model\System\Config\Source\Origin\Type $type,
        \Temando\Temando\Helper\Data $helper,
        array $data = []
    ) {
        $this->_systemStore = $systemStore;
        $this->_isActive = $isActive;
        $this->_type = $type;
        $this->_helper = $helper;
        parent::__construct($context, $registry, $formFactory, $data);
    }

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

        /* General Field Set*/
        $fieldset = $form->addFieldset(
            'base_fieldset',
            [
                'legend' => __('General Information'),
                'class' => 'fieldset-wide',
                'collapsable' => true
            ]
        );
        if ($model->getOriginId()) {
            $fieldset->addField('origin_id', 'hidden', ['name' => 'origin_id']);
        }
        $fieldset->addField(
            'name',
            'text',
            [
                'name' => 'name',
                'label' => __('Name'),
                'title' => __('Name'),
                'required' => true
            ]
        );
        $fieldset->addField(
            'description',
            'editor',
            [
                'name' => 'description',
                'label' => __('Description'),
                'title' => __('Description'),
                'wysiwyg' => true,
            ]
        );
        $fieldset->addField(
            'is_active',
            'select',
            [
                'name' => 'is_active',
                'label' => __('Status'),
                'title' => __('Status'),
                'required' => true,
                'options' => $this->_isActive->toOptionArray(true)
            ]
        );
        
        if (!$model->getId()) {
            $model->setData('is_active', '1');
        }
        
        $fieldset->addField(
            'allow_store_collection',
            'select',
            [
                'name' => 'allow_store_collection',
                'label' => __('Allow Store Collection'),
                'title' => __('Allow Store Collection'),
                'required' => true,
                'options' => $this->_isActive->toOptionArray(true)
            ]
        );

        $fieldset->addField(
            'company_name',
            'text',
            [
                'name' => 'company_name',
                'label' => __('Company Name'),
                'title' => __('Company Name'),
                'required' => true
            ]
        );
        $fieldset->addField(
            'erp_id',
            'text',
            [
                'name' => 'erp_id',
                'label' => __('ERP ID'),
                'title' => __('ERP ID'),
                'required' => true,
                'note' => __('Used for synchronising inventory data from third party system.')
            ]
        );
        $fieldset->addField(
            'erp_code',
            'text',
            [
                'name' => 'erp_code',
                'label' => __('ERP Code'),
                'title' => __('ERP Code'),
                'required' => true,
                'note' => __('Passed into third party system.')
            ]
        );
        $fieldset->addField(
            'zone_id',
            'select',
            [
                'name' => 'zone_id',
                'label' => __('Zone'),
                'title' => __('Zone'),
                'required' => false,
                'options' => $this->_helper->getZonesOptionArray()
            ]
        );
        $fieldset->addField(
            'store_ids',
            'multiselect',
            [
                'name' => 'store_ids[]',
                'label' => __('Stores'),
                'title' => __('Stores'),
                'required' => true,
                'values' => $this->_systemStore->toOptionArray(),
                'value' => $model->getStoreIds()
            ]
        );
       
        $form->setValues($model->getData());
        $this->setForm($form);

        return parent::_prepareForm();
    }

    /**
     * Get registry model.
     *
     * @return \Temando\Temando\Model\Store
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
        return __('General Information');
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
        return __('General Information');
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
