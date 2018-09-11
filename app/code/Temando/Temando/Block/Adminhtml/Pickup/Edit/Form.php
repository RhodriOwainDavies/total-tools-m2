<?php
namespace Temando\Temando\Block\Adminhtml\Pickup\Edit;

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
     * Helper.
     *
     * @var \Temando\Temando\Helper\Data
     */
    protected $_helper;

    /**
     * Status.
     *
     * @var \Temando\Temando\Model\System\Config\Source\Pickup\Status
     */
    protected $_status;

    /**
     * Form constructor.
     *
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Data\FormFactory $formFactory
     * @param \Magento\Store\Model\System\Store $systemStore
     * @param \Magento\Directory\Model\Config\Source\Country $countryFactory
     * @param \Temando\Temando\Helper\Data $helper
     * @param \Temando\Temando\Model\System\Config\Source\Pickup\Status $status
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Magento\Store\Model\System\Store $systemStore,
        \Magento\Directory\Model\Config\Source\Country $countryFactory,
        \Temando\Temando\Helper\Data $helper,
        \Temando\Temando\Model\System\Config\Source\Pickup\Status $status,
        array $data = []
    ) {
        $this->_systemStore = $systemStore;
        $this->_countryFactory = $countryFactory;
        $this->_helper = $helper;
        $this->_status = $status;
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
        $this->setId('pickup_form');
        $this->setTitle(__('Pickup Information'));
    }

    /**
     * Prepare form
     *
     * @return $this
     */
    protected function _prepareForm()
    {

        $model = $this->_coreRegistry->registry('temando_pickup');

        $saveUrl = $this->getUrl('temando/pickup/save');

        $form = $this->_formFactory->create(
            ['data' => ['id' => 'edit_form', 'action' => $saveUrl, 'method' => 'post']]
        );

        $form->setHtmlIdPrefix('pickup_');

        $form->setValues($model->getData());

        $destinationFieldset = $form->addFieldset(
            'base_fieldset',
            [
                'legend' => __('Customer Address'),
                'class' => 'fieldset-wide',
                'collapsable' => true
            ]
        );

        if ($model->getPickupId()) {
            $destinationFieldset->addField('pickup_id', 'hidden', ['name' => 'pickup_id']);
        }

        $destinationFieldset->addField(
            'billing_contact_name',
            'text',
            [
                'name' => 'billing_contact_name',
                'label' => __('Contact Name'),
                'title' => __('Contact Name'),
                'required' => true
            ]
        );

        $destinationFieldset->addField(
            'billing_company_name',
            'text',
            [
                'name' => 'billing_company_name',
                'label' => __('Company Name'),
                'title' => __('Company Name'),
                'required' => true
            ]
        );

        $destinationFieldset->addField(
            'billing_street',
            'text',
            ['name' => 'billing_street', 'label' => __('Street'), 'title' => __('Street'), 'required' => true]
        );

        $destinationFieldset->addField(
            'billing_city',
            'text',
            ['name' => 'billing_city', 'label' => __('City'), 'title' => __('City'), 'required' => true]
        );

        $destinationFieldset->addField(
            'billing_postcode',
            'text',
            ['name' => 'billing_postcode', 'label' => __('Postcode'), 'title' => __('Postcode'), 'required' => true]
        );

        $destinationFieldset->addField(
            'billing_region',
            'text',
            ['name' => 'billing_region', 'label' => __('Region'), 'title' => __('Region'), 'required' => true]
        );

        $countryOptions = $this->_countryFactory->toOptionArray();
        $countryField = $destinationFieldset->addField(
            'billing_country',
            'select',
            [
                'name' => 'billing_country',
                'label' => __('Country'),
                'title' => __('Country'),
                // 'onchange' => 'getstate(this)',
                'values' => $countryOptions,
                'value' => $model->getData('billing_country')
            ]
        );

        $destinationFieldset->addField(
            'billing_phone',
            'text',
            ['name' => 'billing_phone', 'label' => __('Phone'), 'title' => __('Phone'), 'required' => true]
        );

        $destinationFieldset->addField(
            'billing_email',
            'text',
            ['name' => 'billing_email', 'label' => __('Email'), 'title' => __('Email'), 'required' => true]
        );

        $otherFieldset = $form->addFieldset(
            'store_fieldset',
            [
                'legend' => __('Other Details'),
                'class' => 'fieldset-wide',
                'collapsable' => true
            ]
        );

        $originOptions=$this->_helper->getOriginOptionArray();
        $originField = $otherFieldset->addField(
            'origin_id',
            'select',
            [
                'name' => 'origin_id',
                'label' => __('Store'),
                'title' => __('Store'),
                // 'onchange' => 'getstate(this)',
                'values' => $originOptions,
                'value' => $model->getData('origin_id')
            ]
        );

        $statusOptions=$this->_status->toOptionArray();

        if ($model->getStatus() >= \Temando\Temando\Model\System\Config\Source\Pickup\Status::COLLECTED) {
            foreach ($statusOptions as $key => $statusInfo) {
                if ($statusInfo['value'] < \Temando\Temando\Model\System\Config\Source\Pickup\Status::COLLECTED) {
                    unset($statusOptions[$key]);
                }
            }
        }
        $statusField = $otherFieldset->addField(
            'status',
            'select',
            [
                'name' => 'status',
                'label' => __('Status'),
                'title' => __('Status'),
                'values' => $statusOptions,
                'value' => $model->getData('status')
            ]
        );


        $form->setValues($model->getData());

        $form->setUseContainer(true);
        $this->setForm($form);

        return parent::_prepareForm();
    }
}
