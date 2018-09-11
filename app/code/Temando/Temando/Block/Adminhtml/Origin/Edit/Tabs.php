<?php

namespace Temando\Temando\Block\Adminhtml\Origin\Edit;

/**
 * Class Tabs.
 */
class Tabs extends \Magento\Backend\Block\Widget\Tabs
{
    /**
     * Construct.
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('origin_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(__('Store Information'));
        parent::_prepareLayout();
    }

    /**
     * Preparing global layout.
     *
     * You can redefine this method in child classes for changing layout
     *
     * @return $this
     */
    protected function _prepareLayout()
    {
        
        parent::_prepareLayout();

        // add general tab
        $this->addTab('general_section', 'origin_edit_tab_general');
        
        // add contact tab
        $this->addTab('contact_section', 'origin_edit_tab_contact');
        
        // add Google Map tab
        $this->addTab('gmap_section', 'origin_edit_tab_gmap');

        // add temando profile tab
        $this->addTab('temando_profile_section', 'origin_edit_tab_temando_profile');
        
        // add image gallery section
        //$this->addTab('imagegallery_section', 'origin_edit_tab_imagegallery');

        // add fallback stores & inventory tab
        $this->addTab('fallback_inventory_section', 'origin_edit_tab_fallback_inventory');

        // add user profiles tab
        $this->addTab('user_permissions_section', 'origin_edit_tab_user_permissions');
        
        // add schedule tab
        $this->addTab('schedule_section', 'origin_edit_tab_schedule');

        // add holiday tab
        $this->addTab(
            'holiday_section',
            [
                'label' => 'Store\'s Holidays',
                'title' => 'Store\'s Holidays',
                'class' => 'ajax',
                'url' => $this->getUrl(
                    'storepickupadmin/ajaxtabgrid_holiday',
                    [
                        'method_getter_id' => \Magestore\Storepickup\Model\Store::METHOD_GET_HOLIDAY_ID,
                        'serialized_name' => 'serialized_holidays',
                        '_current' => true,
                    ]
                ),
            ]
        );

        // add specialday tab
        $this->addTab(
            'specialday_section',
            [
                'label' => 'Store\'s Special days',
                'title' => 'Store\'s Special days',
                'class' => 'ajax',
                'url' => $this->getUrl(
                    'storepickupadmin/ajaxtabgrid_specialday',
                    [
                        'method_getter_id' => \Magestore\Storepickup\Model\Store::METHOD_GET_SPECIALDAY_ID,
                        'serialized_name' => 'serialized_specialdays',
                        '_current' => true,
                    ]
                ),
            ]
        );

        return $this;
    }
}
