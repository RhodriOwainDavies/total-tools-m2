<?php
namespace Temando\Temando\Block\Adminhtml\Rule\Edit\Tabs;

class Action extends \Magento\Backend\Block\Widget\Form\Generic implements
    \Magento\Backend\Block\Widget\Tab\TabInterface
{

    protected $_filter;

    protected $_adjustment;

    protected $_yesno;

    /**
     * Action constructor.
     *
     * @param \Temando\Temando\Model\System\Config\Source\Rule\Action\Filter $filter
     * @param \Temando\Temando\Model\System\Config\Source\Rule\Action\Adjustment $adjustment
     * @param \Temando\Temando\Model\System\Config\Source\Rule\Type $ruleType
     * @param \Magento\Config\Model\Config\Source\Yesno $yesno
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Data\FormFactory $formFactory
     * @param array $data
     */
    public function __construct(
        \Temando\Temando\Model\System\Config\Source\Rule\Action\Filter $filter,
        \Temando\Temando\Model\System\Config\Source\Rule\Action\Adjustment $adjustment,
        \Temando\Temando\Model\System\Config\Source\Rule\Type $ruleType,
        \Magento\Config\Model\Config\Source\Yesno $yesno,
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        array $data = []
    ) {
        $this->_filter = $filter;
        $this->_adjustment = $adjustment;
        $this->_ruleType = $ruleType;
        $this->_yesno = $yesno;
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

        $actionConfigFieldset = $form->addFieldset(
            'action_fieldset',
            [
                'legend' => __('Action'),
                'class' => 'fieldset-wide',
                'collapsable' => true
            ]
        );

        $actionConfigFieldset->addField(
            'action_rate_type',
            'select',
            [
                'name' => 'action_rate_type',
                'label' => __('Shipping Rate Type'),
                'title' => __('Shipping Rate Type'),
                'required' => true,
                'values' => $this->_ruleType->toOptionArray(true)
            ]
        );


        $staticRateConfigFieldset = $form->addFieldset(
            'static_rate_fieldset',
            [
                'legend' => __('Static Rate Configuration'),
                'class' => 'fieldset-wide',
                'collapsable' => true
            ]
        );

        $staticRateConfigFieldset->addField(
            'action_static_value',
            'text',
            [
                'name' => 'action_static_value',
                'label' => __('Static Rate Value'),
                'title' => __('Static Rate Value'),
                'required' => false,
                'note' => ''
            ]
        );

        $staticRateConfigFieldset->addField(
            'action_static_label',
            'text',
            [
                'name' => 'action_static_label',
                'label' => __('Static Rate Label'),
                'title' => __('Static Rate Label'),
                'required' => false
            ]
        );

        $dynamicRateConfigFieldset = $form->addFieldset(
            'dynamic_rate_fieldset',
            [
                'legend' => __('Dynamic Rate Configuration'),
                'class' => 'fieldset-wide',
                'collapsable' => true
            ]
        );

        $dynamicRateConfigFieldset->addField(
            'action_dynamic_filter',
            'select',
            [
                'name' => 'action_dynamic_filter',
                'label' => __('Display Filter'),
                'title' => __('Display Filter'),
                'required' => true,
                'values' => $this->_filter->toOptionArray(true)
            ]
        );

        $dynamicRateConfigFieldset->addField(
            'action_dynamic_adjustment_type',
            'select',
            [
                'name' => 'action_dynamic_adjustment_type',
                'label' => __('Rate Adjustment Type'),
                'title' => __('Rate Adjustment Type'),
                'required' => false,
                'values' => $this->_adjustment->toOptionArray(true)
            ]
        );

        $dynamicRateConfigFieldset->addField(
            'action_dynamic_adjustment_value',
            'text',
            [
                'name' => 'action_dynamic_adjustment_value',
                'label' => __('Rate Adjustment Value'),
                'title' => __('Rate Adjustment Value'),
                'required' => false,
                'note' => 'Override - use exact amount e.g. 12.95.  Markup - use multiplier e.g. 1.1'
            ]
        );

        $dynamicRateConfigFieldset->addField(
            'action_dynamic_adjustment_roundup',
            'select',
            [
                'name' => 'action_dynamic_adjustment_roundup',
                'label' => __('Rate adjustment round up'),
                'title' => __('Rate adjustment round up'),
                'required' => false,
                'note' => 'Round-up quote amount to nearest whole number (doesn\'t work with Override)',
                'values' => $this->_yesno->toOptionArray(),
            ]
        );

        $dynamicRateConfigFieldset->addField(
            'action_dynamic_label',
            'text',
            [
                'name' => 'action_dynamic_label',
                'label' => __('Alternative Rate Label'),
                'title' => __('Alternative Rate Label'),
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
     */
    public function getTabLabel()
    {
        return __('Action');
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
        return __('Action');
    }
    /**
     * Can show tab in tabs.
     *
     * @return bool
     */
    public function canShowTab()
    {
        return true;
    }
    /**
     * Tab is hidden.
     *
     * @return bool
     */
    public function isHidden()
    {
        return false;
    }
}
