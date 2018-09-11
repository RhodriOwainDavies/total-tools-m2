<?php
namespace Temando\Temando\Block\Adminhtml\Rule\Edit\Tabs;

class Conditions extends \Magento\Backend\Block\Widget\Form\Generic implements
    \Magento\Backend\Block\Widget\Tab\TabInterface
{
    /**
     * Core registry
     *
     * @var \Magento\Backend\Block\Widget\Form\Renderer\Fieldset
     */
    protected $_rendererFieldset;

    /**
     * Product Attribute Set Options.
     *
     * @var \Magento\Catalog\Model\Product\AttributeSet\Options
     */
    protected $_options;

    /**
     * Conditions constructor.
     *
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Data\FormFactory $formFactory
     * @param \Magento\Backend\Block\Widget\Form\Renderer\Fieldset $rendererFieldset
     * @param \Magento\Catalog\Model\Product\AttributeSet\Options $options
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Magento\Backend\Block\Widget\Form\Renderer\Fieldset $rendererFieldset,
        \Magento\Catalog\Model\Product\AttributeSet\Options $options,
        array $data = []
    ) {
        $this->_rendererFieldset = $rendererFieldset;
        $this->_options = $options;
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

        $conditionsFieldset = $form->addFieldset(
            'conditions_fieldset',
            [
                'legend' => __(
                    'Conditions'
                )
            ]
        );

        $attributeSetOptions = array();
        $attributeSetOptions[''] = '--';
        foreach ($this->_options->toOptionArray() as $key => $index) {
            $attributeSetOptions[$index['value']] = $index['label'];
        }

        $conditionsFieldset->addField(
            'attribute_set_id',
            'select',
            [
                'name' => 'attribute_set_id',
                'label' => __('Attribute Set'),
                'title' => __('Attribute Set'),
                'required' => false,
                'options' => $attributeSetOptions,
                'note' => 'This rule will match only if the cart exclusively contains products with this attribute set'
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
     *
     * @api
     */
    public function getTabLabel()
    {
        return __('Conditions');
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
        return __('Conditions');
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
