<?php
namespace Temando\Temando\Block\Adminhtml\Shipment\Edit;

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
     * Temando Helper.
     *
     * @var \Temando\Temando\Helper\Data
     */
    protected $_helper;

    /**
     * Form constructor.
     *
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Data\FormFactory $formFactory
     * @param \Magento\Store\Model\System\Store $systemStore
     * @param \Magento\Directory\Model\Config\Source\Country $countryFactory
     * @param \Temando\Temando\Helper\Data $helper
     * @param \Temando\Temando\Model\System\Config\Source\Shipment\Status $status
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Magento\Store\Model\System\Store $systemStore,
        \Magento\Directory\Model\Config\Source\Country $countryFactory,
        \Temando\Temando\Helper\Data $helper,
        \Temando\Temando\Model\System\Config\Source\Shipment\Status $status,
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
        $this->setId('shipment_form');
        $this->setTitle(__('Shipment Information'));
    }

    /**
     * Prepare form
     *
     * @return $this
     */
    protected function _prepareForm()
    {

        $model = $this->_coreRegistry->registry('temando_shipment');

        $saveUrl = $this->getUrl('temando/shipment/save');

        $form = $this->_formFactory->create(
            ['data' => ['id' => 'edit_form', 'action' => $saveUrl, 'method' => 'post']]
        );

        $form->setHtmlIdPrefix('shipment_');

        $form->setValues($model->getData());

        $destinationFieldset = $form->addFieldset(
            'base_fieldset',
            ['legend' => __('Destination'), 'class' => 'fieldset-wide', 'collapsable' => true]
        );

        if ($model->getShipmentId()) {
            $destinationFieldset->addField('shipment_id', 'hidden', ['name' => 'shipment_id']);
        }

        $destinationFieldset->addField(
            'destination_contact_name',
            'text',
            [
                'name' => 'destination_contact_name',
                'label' => __('Contact Name'),
                'title' => __('Contact Name'),
                'required' => true
            ]
        );

        $destinationFieldset->addField(
            'destination_company_name',
            'text',
            [
                'name' => 'destination_company_name',
                'label' => __('Company Name'),
                'title' => __('Company Name'),
                'required' => true
            ]
        );

        $destinationFieldset->addField(
            'destination_street',
            'text',
            ['name' => 'destination_street', 'label' => __('Street'), 'title' => __('Street'), 'required' => true]
        );

        $destinationFieldset->addField(
            'destination_city',
            'text',
            ['name' => 'destination_city', 'label' => __('City'), 'title' => __('City'), 'required' => true]
        );

        $destinationFieldset->addField(
            'destination_postcode',
            'text',
            ['name' => 'destination_postcode', 'label' => __('Postcode'), 'title' => __('Postcode'), 'required' => true]
        );

        $destinationFieldset->addField(
            'destination_region',
            'text',
            ['name' => 'destination_region', 'label' => __('Region'), 'title' => __('Region'), 'required' => true]
        );

        $countryOptions = $this->_countryFactory->toOptionArray();
        $countryField = $destinationFieldset->addField(
            'destination_country',
            'select',
            [
                'name' => 'destination_country',
                'label' => __('Country'),
                'title' => __('Country'),
                // 'onchange' => 'getstate(this)',
                'values' => $countryOptions,
                'value' => $model->getData('destination_country')
            ]
        );

        $destinationFieldset->addField('destination_type', 'checkbox', array(
            'label'     => __('Is Business Address'),
            'name'      => 'destination_type',
            'onclick' => "this.value = this.checked ? '" .
                \Temando\Temando\Model\System\Config\Source\Origin\Type::BUSINESS."' : '" .
                \Temando\Temando\Model\System\Config\Source\Origin\Type::RESIDENTIAL."';"
        ));

        $destinationFieldset->addField('destination_authority_to_leave', 'checkbox', array(
            'label'     => __('Authority to Leave'),
            'name'      => 'destination_authority_to_leave',
            'onclick' => 'this.value = this.checked ? 1 : 0;'
        ));

        $destinationFieldset->addField(
            'destination_phone',
            'text',
            ['name' => 'destination_phone', 'label' => __('Phone'), 'title' => __('Phone'), 'required' => true]
        );

        $destinationFieldset->addField(
            'destination_email',
            'text',
            ['name' => 'destination_email', 'label' => __('Email'), 'title' => __('Email'), 'required' => true]
        );

        $otherFieldset = $form->addFieldset(
            'other_fieldset',
            ['legend' => __('Other'), 'class' => 'fieldset-wide', 'collapsable' => true]
        );

        $originOptions=$this->_helper->getOriginOptionArray();
        $originField = $otherFieldset->addField(
            'origin_id',
            'select',
            [
                'name' => 'origin_id',
                'label' => __('Store'),
                'title' => __('Store'),
                'values' => $originOptions,
                'value' => $model->getData('origin_id')
            ]
        );


        $statusOptions=$this->_status->toOptionArray();

        if ($model->getStatus() >= \Temando\Temando\Model\System\Config\Source\Shipment\Status::BOOKED) {
            foreach ($statusOptions as $key => $statusInfo) {
                if ($statusInfo['value'] < \Temando\Temando\Model\System\Config\Source\Shipment\Status::BOOKED) {
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
        $form->getElement('destination_authority_to_leave')
            ->setIsChecked(!empty($model->getData('destination_authority_to_leave')));
        $destinationTypeChecked = false;
        if ($model->getData('destination_type')==\Temando\Temando\Model\System\Config\Source\Origin\Type::BUSINESS) {
            $destinationTypeChecked = true;
        }

        $form->getElement('destination_type')->setIsChecked($destinationTypeChecked);
        $form->setUseContainer(true);
        $this->setForm($form);

        return parent::_prepareForm();
    }
}
