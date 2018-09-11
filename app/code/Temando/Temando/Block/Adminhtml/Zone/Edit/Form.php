<?php
namespace Temando\Temando\Block\Adminhtml\Zone\Edit;

class Form extends \Magento\Backend\Block\Widget\Form\Generic
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
     * Form constructor.
     *
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Data\FormFactory $formFactory
     * @param \Magento\Store\Model\System\Store $systemStore
     * @param \Magento\Directory\Model\Config\Source\Country $countryFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Magento\Store\Model\System\Store $systemStore,
        \Magento\Directory\Model\Config\Source\Country $countryFactory,
        array $data = []
    ) {
        $this->_systemStore = $systemStore;
        $this->_countryFactory = $countryFactory;
        parent::__construct($context, $registry, $formFactory, $data);
    }

    /**
     * Init form
     *
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('zone_form');
        $this->setTitle(__('Zone Information'));
    }

    /**
     * Prepare form
     *
     * @return $this
     */
    protected function _prepareForm()
    {
        $model = $this->_coreRegistry->registry('temando_zone');

        $form = $this->_formFactory->create(
            ['data' => ['id' => 'edit_form', 'action' => $this->getData('action'), 'method' => 'post']]
        );

        $form->setHtmlIdPrefix('zone_');

        $fieldset = $form->addFieldset(
            'base_fieldset',
            ['legend' => __('General Information'), 'class' => 'fieldset-wide']
        );

        if ($model->getZoneId()) {
            $fieldset->addField('zone_id', 'hidden', ['name' => 'zone_id']);
        }

        $fieldset->addField(
            'name',
            'text',
            ['name' => 'name', 'label' => __('Name'), 'title' => __('Name'), 'required' => true]
        );

        $country_options = $this->_countryFactory->toOptionArray();
        $fieldset->addField(
            'country_code',
            'select',
            [
                'name' => 'country_code',
                'label' => __('Country'),
                'title' => __('Country'),
                'values' => $country_options,
            ]
        );
        
        if (!$model->getId()) {
            $model->setData('is_active', '1');
        }

        $fieldset->addField(
            'ranges',
            'editor',
            [
                'name' => 'ranges',
                'label' => __('Ranges'),
                'title' => __('Ranges'),
                'style' => 'height:10em',
                'required' => false,
                'note' => __('Use colon to specify range, comma to separate ranges.')
            ]
        );

        $form->setValues($model->getData());
        $form->setUseContainer(true);
        $this->setForm($form);

        return parent::_prepareForm();
    }
}
