<?php

namespace Temando\Temando\Setup;

use Magento\Eav\Setup\EavSetup;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Store\Model\System\Store;

/**
 * Install data.
 *
 * @codeCoverageIgnore
 */
class InstallData implements InstallDataInterface
{
    /**
     * EAV setup factory
     *
     * @var EavSetupFactory
     */
    private $eavSetupFactory;

    /**
     * Magento StoreManagerInterface
     *
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * Attribute Repository.
     *
     * @var \Magento\Eav\Api\AttributeRepositoryInterface
     */
    private $_attributeRepository;
    /**
     * Init
     *
     * @param EavSetupFactory $eavSetupFactory
     */
    public function __construct(
        EavSetupFactory $eavSetupFactory,
        Store $store,
        \Magento\Eav\Api\AttributeRepositoryInterface $attributeRepositoryInterface
    ) {
        $this->eavSetupFactory = $eavSetupFactory;
        $this->store = $store;
        $this->_attributeRepository = $attributeRepositoryInterface;
    }

    /**
     * {@inheritdoc}
     */
    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        /**
         * EAV Setup.
         *
         * @var EavSetup $eavSetup
         */
        $eavSetup = $this->eavSetupFactory->create(['setup' => $setup]);
        $eavSetup->addAttributeGroup(\Magento\Catalog\Model\Category::ENTITY, 'Default', 'Temando', 90);

        $entityTypeId = $eavSetup->getEntityTypeId(\Magento\Catalog\Model\Product::ENTITY);
        $attributeSetId = $eavSetup->getAttributeSetId($entityTypeId, 'Default');
        $groupId = $eavSetup->getAttributeGroupId($entityTypeId, $attributeSetId, 'Temando');

        $shipping_dimensions = array();
        $shipping_dimensions['shipping_height'] = 'Height';
        $shipping_dimensions['shipping_width'] = 'Width';
        $shipping_dimensions['shipping_length'] = 'Length';

        $eavSetup->addAttribute(
            \Magento\Catalog\Model\Product::ENTITY,
            'shipping_fragile',
            [
                'type' => 'int',
                'label' => 'Fragile',
                'group' => 'Temando',
                'global' => \Magento\Catalog\Model\ResourceModel\Eav\Attribute::SCOPE_GLOBAL,
                'source' => 'Magento\Eav\Model\Entity\Attribute\Source\Boolean',
                'input' => 'select',
                'default' => false,
                'visible' => true,
                'required' => false,
                'user_defined' => false,
                'searchable' => false,
                'filterable' => false,
                'comparable' => false,
                'visible_on_front' => false,
                'unique' => false
            ]
        );
        $eavSetup->addAttribute(
            \Magento\Catalog\Model\Product::ENTITY,
            'shipping_dangerous',
            [
                'type' => 'int',
                'label' => 'Dangerous',
                'group' => 'Temando',
                'global' => \Magento\Catalog\Model\ResourceModel\Eav\Attribute::SCOPE_GLOBAL,
                'source' => 'Magento\Eav\Model\Entity\Attribute\Source\Boolean',
                'input' => 'select',
                'default' => false,
                'visible' => true,
                'required' => false,
                'user_defined' => false,
                'searchable' => false,
                'filterable' => false,
                'comparable' => false,
                'visible_on_front' => false,
                'unique' => false
            ]
        );
        $eavSetup->addAttribute(
            \Magento\Catalog\Model\Product::ENTITY,
            'shipping_needs_packaging',
            [
                'type' => 'int',
                'label' => 'Needs Packaging',
                'group' => 'Temando',
                'global' => \Magento\Catalog\Model\ResourceModel\Eav\Attribute::SCOPE_GLOBAL,
                'source' => 'Magento\Eav\Model\Entity\Attribute\Source\Boolean',
                'input' => 'select',
                'default' => false,
                'visible' => true,
                'required' => false,
                'user_defined' => false,
                'searchable' => false,
                'filterable' => false,
                'comparable' => false,
                'visible_on_front' => false,
                'unique' => false
            ]
        );
/**
        $eavSetup->addAttribute(
            \Magento\Catalog\Model\Product::ENTITY,
            'temando_length',
                [
                    'type' => 'decimal',
                    'label' => 'Length',
                    'group' => 'Temando',
                    'global' => \Magento\Catalog\Model\ResourceModel\Eav\Attribute::SCOPE_GLOBAL,
                    'visible' => true,
                    'required' => false,
                    'user_defined' => false,
                    'searchable' => false,
                    'filterable' => false,
                    'comparable' => false,
                    'visible_on_front' => false,
                    'unique' => false
                ]
        );
        $eavSetup->addAttribute(
            \Magento\Catalog\Model\Product::ENTITY,
            'temando_width',
                [
                    'type' => 'decimal',
                    'label' => 'Width',
                    'group' => 'Temando',
                    'global' => \Magento\Catalog\Model\ResourceModel\Eav\Attribute::SCOPE_GLOBAL,
                    'visible' => true,
                    'required' => false,
                    'user_defined' => false,
                    'searchable' => false,
                    'filterable' => false,
                    'comparable' => false,
                    'visible_on_front' => false,
                    'unique' => false
                ]
        );
        $eavSetup->addAttribute(
            \Magento\Catalog\Model\Product::ENTITY,
            'temando_height',
                [
                    'type' => 'decimal',
                    'label' => 'Height',
                    'group' => 'Temando',
                    'global' => \Magento\Catalog\Model\ResourceModel\Eav\Attribute::SCOPE_GLOBAL,
                    'visible' => true,
                    'required' => false,
                    'user_defined' => false,
                    'searchable' => false,
                    'filterable' => false,
                    'comparable' => false,
                    'visible_on_front' => false,
                    'unique' => false
                ]
        );
**/
        $carriers = [
            ['carrier_id' => 1, 'temando_carrier_id' => 54381, 'company_name' => 'Allied Express'],
            ['carrier_id' => 2, 'temando_carrier_id' => 54426, 'company_name' => 'Allied Express (Bulk)'],
            ['carrier_id' => 3, 'temando_carrier_id' => 54359, 'company_name' => 'Startrack'],
            ['carrier_id' => 4, 'temando_carrier_id' => 54396, 'company_name' => 'Startrack - Auth To Leave'],
            ['carrier_id' => 5, 'temando_carrier_id' => 54360, 'company_name' => 'Bluestar Logistics'],
            ['carrier_id' => 6, 'temando_carrier_id' => 54429, 'company_name' => 'Bluestar Logistics Bulk'],
            ['carrier_id' => 7, 'temando_carrier_id' => 54433, 'company_name' => 'Capital Transport Courier'],
            ['carrier_id' => 8, 'temando_carrier_id' => 54432, 'company_name' => 'Capital Transport HDS'],
            ['carrier_id' => 9, 'temando_carrier_id' => 54425, 'company_name' => 'Couriers Please'],
            ['carrier_id' => 10, 'temando_carrier_id' => 54343, 'company_name' => 'DHL'],
            ['carrier_id' => 11, 'temando_carrier_id' => 54430, 'company_name' => 'DHL MultiZone'],
            ['carrier_id' => 12, 'temando_carrier_id' => 54431, 'company_name' => 'DHL SingleZone'],
            ['carrier_id' => 13, 'temando_carrier_id' => 54427, 'company_name' => 'Fastway Couriers Adhoc'],
            ['carrier_id' => 14, 'temando_carrier_id' => 54428, 'company_name' => 'Fastway Couriers Bulk'],
            ['carrier_id' => 15, 'temando_carrier_id' => 54344, 'company_name' => 'Hunter Express'],
            ['carrier_id' => 16, 'temando_carrier_id' => 54398, 'company_name' => 'Hunter Express (bulk)'],
            ['carrier_id' => 17, 'temando_carrier_id' => 54358, 'company_name' => 'Mainfreight'],
            ['carrier_id' => 18, 'temando_carrier_id' => 54410, 'company_name' => 'Northline'],
        ];

        $setup->getConnection()->insertArray(
            $setup->getTable('temando_carrier'),
            ['carrier_id', 'temando_carrier_id', 'company_name'],
            $carriers
        );

        $zones = [
            [
                'zone_id' => 1,
                'name' => 'No Zone (Head Office)',
                'country_code' => 'AU',
                'ranges' => '0000'
            ]
        ];

        $setup->getConnection()->insertArray(
            $setup->getTable('temando_zone'),
            ['zone_id', 'name', 'country_code', 'ranges'],
            $zones
        );

        $origins = [
            [
                'origin_id' => 1,
                'name' => 'Total Tools Head Office',
                'is_active' => true,
                'company_name' => 'Total Tools',
                'street' => '20 Thackray Rd',
                'city' => 'Port Melbourne',
                'region' => 'VIC',
                'erp_id' => 1,
                'postcode' => '3207',
                'country' => 'AU',
                'contact_name' => 'Alick Hyde',
                'contact_email' => 'a.hyde@totaltools.com.au',
                'contact_phone_1' => '0392611900',
                'store_ids' => implode(',', array_keys($this->store->getStoreCollection())),
                'zone_id' => 1,
                'account_mode' => '0',
                'latitude' => '-37.82865831',
                'longitude' => '144.92619306',
                'zoom_level' => '17',
                'allow_store_collection' => true
            ]
        ];

        $setup->getConnection()->insertArray(
            $setup->getTable('temando_origin'),
            [
                'origin_id', 'name', 'is_active', 'company_name', 'street', 'city', 'region','erp_id', 'postcode',
                'country', 'contact_name', 'contact_email', 'contact_phone_1', 'store_ids', 'zone_id', 'account_mode',
                'latitude','longitude','zoom_level','allow_store_collection'
            ],
            $origins
        );
    }
}
