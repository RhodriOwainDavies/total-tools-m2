<?php
namespace Temando\Temando\Block\Adminhtml\Rule\Edit\Tabs;

class General extends \Magento\Backend\Block\Widget\Form\Generic implements
    \Magento\Backend\Block\Widget\Tab\TabInterface
{

    /**
     * System Store.
     *
     * @var \Magento\Store\Model\System\Store
     */
    protected $_systemStore;

    /**
     * Yes/No Source.
     *
     * @var \Magento\Config\Model\Config\Source\Yesno
     */
    protected $_yesno;

    /**
     * Is Active Source.
     *
     * @var \Temando\Temando\Model\Origin\Source\IsActive
     */
    protected $_isActive;

    /**
     * Origin Type.
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

    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Magento\Store\Model\System\Store $systemStore,
        \Magento\Config\Model\Config\Source\Yesno $yesno,
        \Temando\Temando\Model\Origin\Source\IsActive $isActive,
        //\Temando\Temando\Model\System\Config\Source\Origin\Type $type,
        \Temando\Temando\Helper\Data $helper,
        array $data = []
    ) {
        $this->_systemStore = $systemStore;
        $this->_yesno = $yesno;
        $this->_isActive = $isActive;
        //$this->_type = $type;
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
        $form->setHtmlIdPrefix('rule_');

        $generalFieldset = $form->addFieldset(
            'temando_rule_general_fieldset',
            [
                'legend' => __('General'),
                'class' => 'fieldset-wide'
            ]
        );

        $generalFieldset->addField(
            'name',
            'text',
            [
                'name' => 'name',
                'label' => __('Name'),
                'title' => __('Name'),
                'required' => true
            ]
        );

        $generalFieldset->addField(
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

        $generalFieldset->addField(
            'from_date',
            'date',
            [
                'name' => 'from_date',
                'label' => __('From Date'),
                'title' => __('From Date'),
                'date_format' => 'yyyy-MM-dd',
                'note' => 'Inclusive of this date'
            ]
        );

        $generalFieldset->addField(
            'to_date',
            'date',
            [
                'name' => 'to_date',
                'label' => __('End Date'),
                'title' => __('End Date'),
                'date_format' => 'yyyy-MM-dd',
                'note' => 'Inclusive of this date'
            ]
        );

        $generalFieldset->addField(
            'priority',
            'text',
            [
                'name' => 'priority',
                'label' => __('Priority'),
                'title' => __('Priority'),
                'required' => false,
                'note' => 'Rules with prioritiy 0 will be processed first'
            ]
        );

        $generalFieldset->addField(
            'stop_other',
            'select',
            [
                'name' => 'stop_other',
                'label' => __('Stop further rules processing'),
                'title' => __('Stop further rules processing'),
                'required' => false,
                'note' => __('Once this rule is matched, stop other rules from processing'),
                'values' => $this->_yesno->toOptionArray()
            ]
        );

        $generalFieldset->addField(
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

        if ($model->getRuleId()) {
            $generalFieldset->addField('rule_id', 'hidden', ['name' => 'rule_id']);
        }

        $form->setValues($model->getData());
        $this->setForm($form);
        return parent::_prepareForm();
    }

    /**
     * Get registry model.
     *
     * @return \Temando\Temando\Model\Rule
     */
    public function getRegistryModel()
    {
        return $this->_coreRegistry->registry('temando_rule');
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
        return __('General');
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
        return __('General');
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
