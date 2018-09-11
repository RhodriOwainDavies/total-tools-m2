<?php

namespace Temando\Temando\Block\Adminhtml\Origin\Edit\Tab;

class ScheduleTab extends \Magento\Backend\Block\Widget\Form\Generic implements
    \Magento\Backend\Block\Widget\Tab\TabInterface
{
    protected $_scheduleOption;

    /**
     * ScheduleTab constructor.
     *
     * @param \Magento\Backend\Block\Template\Context             $context
     * @param \Magento\Framework\Registry                         $registry
     * @param \Magento\Framework\Data\FormFactory                 $formFactory
     * @param array                                               $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Magestore\Storepickup\Model\Store\Option\Schedule $scheduleOption,
        array $data = []
    ) {
        parent::__construct($context, $registry, $formFactory, $data);
        $this->_scheduleOption = $scheduleOption;
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

        $fieldset = $form->addFieldset(
            'scheudle_fieldset',
            [
                'legend' => __('Time Schedule'),
            ]
        );

        $fieldset->addField(
            'schedule_id',
            'select',
            [
                'name' => 'schedule_id',
                'label' => __('Schedule'),
                'title' => __('Schedule'),
                'values' => array_merge(
                    [
                        ['value' => '', 'label' => __('-------- Please select a Schedule --------')],
                    ],
                    $this->_scheduleOption->toOptionArray()
                ),
                'note' => $this->_getNoteCreateSchedule(),
            ]
        );

        $scheduleTableBlock = $this->getLayout()
            ->createBlock('Temando\Temando\Block\Adminhtml\Origin\Edit\Tab\ScheduleTab\Renderer\ScheduleTable');

        $fieldset->addField(
            'schedule_table',
            'text',
            [
                'name' => 'schedule_table',
                'label' => __('Schedule Table'),
            ]
        )->setRenderer($scheduleTableBlock);

        $form->setValues($model->getData());
        $this->setForm($form);

        return parent::_prepareForm();
    }

    /**
     * Get note create new schedule.
     *
     * @return mixed
     */
    protected function _getNoteCreateSchedule()
    {
        return sprintf(
            '<a href="%s" target="_blank">%s</a> %s',
            $this->getUrl('storepickupadmin/schedule/new'),
            __('Click here'),
            __('to go to page create new schedule.')
        );
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
        return __('Store\'s Schedule');
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
        return __('Store\'s Schedule');
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
