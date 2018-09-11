<?php

namespace Temando\Temando\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

class InstallSchema implements InstallSchemaInterface
{
    /**
     * {@inheritdoc}
     *
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;

        $installer->startSetup();

        /**
         * Create table 'temando_carrier'
         */
        $temandoCarrierTable = $installer->getConnection()->newTable(
            $installer->getTable('temando_carrier')
        )->addColumn(
            'carrier_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
            'Carrier Id'
        )->addColumn(
            'temando_carrier_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_BIGINT,
            20,
            ['nullable' => false],
            'Temando Carrier Id'
        )->addColumn(
            'company_name',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            250,
            ['nullable' => false],
            'Company Name'
        )->addColumn(
            'company_contact',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            null,
            ['nullable' => false],
            'Company Contact'
        )->addColumn(
            'street_address',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            null,
            ['nullable' => false],
            'Street Address'
        )->addColumn(
            'street_suburb',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['nullable' => false],
            'Street Suburb'
        )->addColumn(
            'street_city',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['nullable' => false],
            'Street City'
        )->addColumn(
            'street_state',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['nullable' => false],
            'Street State'
        )->addColumn(
            'street_postcode',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['nullable' => false],
            'Street Postcode'
        )->addColumn(
            'street_country',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['nullable' => false],
            'Street Country'
        )->addColumn(
            'postal_address',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            null,
            ['nullable' => false],
            'Postal Address'
        )->addColumn(
            'postal_suburb',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['nullable' => false],
            'Postal Suburb'
        )->addColumn(
            'postal_city',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['nullable' => false],
            'Postal City'
        )->addColumn(
            'postal_state',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['nullable' => false],
            'Postal State'
        )->addColumn(
            'postal_postcode',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['nullable' => false],
            'Postal Postcode'
        )->addColumn(
            'postal_country',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['nullable' => false],
            'Postal Country'
        )->addColumn(
            'phone',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['nullable' => false],
            'Phone'
        )->addColumn(
            'email',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['nullable' => false],
            'Email'
        )->addColumn(
            'website',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['nullable' => false],
            'Website'
        )->setComment(
            'Temando Carrier'
        );

        /**
         * Create table 'temando_quote'
         */
        $temandoQuoteTable = $installer->getConnection()->newTable(
            $installer->getTable('temando_quote')
        )->addColumn(
            'quote_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
            'Id'
        )->addColumn(
            'magento_quote_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_BIGINT,
            20,
            ['unsigned' => true, 'nullable' => false],
            'Magento Quote Id'
        )->addColumn(
            'carrier_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_BIGINT,
            20,
            ['unsigned' => true, 'nullable' => false],
            'Carrier Id'
        )->addColumn(
            'accepted',
            \Magento\Framework\DB\Ddl\Table::TYPE_BOOLEAN,
            null,
            ['nullable' => false, 'default' => 0],
            'Accepted'
        )->addColumn(
            'total_price',
            \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
            '12,2',
            ['nullable' => false],
            'Total Price'
        )->addColumn(
            'base_price',
            \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
            '12,2',
            ['nullable' => false],
            'Base Price'
        )->addColumn(
            'tax',
            \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
            '12,2',
            ['nullable' => false],
            'Tax'
        )->addColumn(
            'insurance_total_price',
            \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
            '12,2',
            ['nullable' => false],
            'Insurance Total Price'
        )->addColumn(
            'carbon_total_price',
            \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
            '12,2',
            ['nullable' => false],
            'Carbon Total Price'
        )->addColumn(
            'footprints_total_price',
            \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
            '12,2',
            ['nullable' => false],
            'Footprints Total Price'
        )->addColumn(
            'currency',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            10,
            ['nullable' => false],
            'Currency'
        )->addColumn(
            'delivery_method',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            null,
            ['nullable' => false],
            'Delivery Method'
        )->addColumn(
            'eta_from',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            10,
            ['unsigned' => true, 'nullable' => false, 'default' => 0],
            'Eta From'
        )->addColumn(
            'eta_to',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            10,
            ['unsigned' => true, 'nullable' => false, 'default' => 0],
            'Eta To'
        )->addColumn(
            'guaranteed_eta',
            \Magento\Framework\DB\Ddl\Table::TYPE_BOOLEAN,
            null,
            ['nullable' => false, 'default' => 0],
            'Guaranteed Eta'
        )->addColumn(
            'extras',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            null,
            ['nullable' => false],
            'Extras'
        )->setComment(
            'Temando Quote'
        );

        /**
         * Create table 'temando_shipment'
         */
        $temandoShipmentTable = $installer->getConnection()->newTable(
            $installer->getTable('temando_shipment')
        )->addColumn(
            'shipment_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
            'Shipment Id'
        )->addColumn(
            'order_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_BIGINT,
            20,
            ['unsigned' => true, 'nullable' => false],
            'Order Id'
        )->addColumn(
            'order_increment_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            50,
            ['unsigned' => true, 'nullable' => false],
            'Order Increment Id'
        )->addColumn(
            'order_status',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            32,
            ['unsigned' => true, 'nullable' => false],
            'Order Status'
        )->addColumn(
            'order_created_at',
            \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
            null,
            ['nullable' => false, 'default' => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_INIT],
            'Order Created At'
        )->addColumn(
            'order_shipment_amount',
            \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
            '12,2',
            ['nullable' => false, 'default' => '0.0000'],
            'Order Shipment Amount'
        )->addColumn(
            'origin_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_BIGINT,
            20,
            ['unsigned' => true, 'nullable' => false],
            'Origin Id'
        )->addColumn(
            'customer_selected_quote_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_BIGINT,
            20,
            ['unsigned' => true, 'nullable' => true, 'default' => null],
            'Customer Selected Quote Id'
        )->addColumn(
            'customer_selected_options',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            null,
            ['nullable' => false],
            'Customer Selected Options'
        )->addColumn(
            'customer_selected_delivery_options',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            null,
            ['nullable' => false],
            'Customer Selected Delivery Options'
        )->addColumn(
            'customer_selected_quote_description',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            null,
            ['nullable' => false],
            'Customer Selected Quote Description'
        )->addColumn(
            'admin_selected_quote_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_BIGINT,
            20,
            ['unsigned' => true, 'nullable' => true, 'default' => null],
            'Admin Selected Quote Id'
        )->addColumn(
            'anticipated_cost',
            \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
            '12,2',
            ['nullable' => false],
            'Anticipated Cost'
        )->addColumn(
            'status',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            10,
            ['nullable' => false, 'default' => 0],
            'Status'
        )->addColumn(
            'service_type',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            10,
            ['unsigned' => true, 'nullable' => false],
            'Service Type'
        )->addColumn(
            'destination_contact_name',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['nullable' => false],
            'Destination Contact Name'
        )->addColumn(
            'destination_company_name',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['nullable' => false],
            'Destination Company Name'
        )->addColumn(
            'destination_street',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['nullable' => false],
            'Destination Street'
        )->addColumn(
            'destination_city',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['nullable' => false],
            'Destination City'
        )->addColumn(
            'destination_postcode',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['nullable' => false],
            'Destination Postcode'
        )->addColumn(
            'destination_region',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['nullable' => false],
            'Destination Region'
        )->addColumn(
            'destination_country',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['nullable' => false],
            'Destination Country'
        )->addColumn(
            'destination_phone',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['nullable' => false],
            'Destination Phone'
        )->addColumn(
            'destination_email',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['nullable' => false],
            'Destination Email'
        )->addColumn(
            'destination_type',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            30,
            ['nullable' => false],
            'Destination Type'
        )->addColumn(
            'destination_authority_to_leave',
            \Magento\Framework\DB\Ddl\Table::TYPE_BOOLEAN,
            30,
            ['nullable' => false],
            'Destination authority to leave'
        )->addColumn(
            'ready_date',
            \Magento\Framework\DB\Ddl\Table::TYPE_DATE,
            null,
            ['nullable' => false],
            'Ready Date'
        )->addColumn(
            'ready_time',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            2,
            ['nullable' => false],
            'Ready Time'
        )->addColumn(
            'grid_display',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            1,
            ['unsigned' => true, 'nullable' => false],
            'Grid Display'
        )->addColumn(
            'customer_comment',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            5000,
            ['nullable' => false],
            'Customer Comment'
        )->addColumn(
            'shipping_instructions',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            30,
            ['nullable' => false],
            'Shipping Instructions'
        )->setComment(
            'Temando Shipment'
        );

        $temandoShipmentItemTable = $setup->getConnection()->newTable(
            $setup->getTable('temando_shipment_item')
        )->addColumn(
            'shipment_item_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
            'Id'
        )->addColumn(
            'store_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['nullable' => true, 'length' => 10],
            'Store ID'
        )->addColumn(
            'shipment_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['nullable' => true, 'length' => 20],
            'Shipment ID'
        )->addColumn(
            'order_item_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['nullable' => true, 'length' => 10],
            'Order Item ID'
        )->addColumn(
            'warehouse',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            null,
            ['nullable' => false, 'unique' => true, 'length' => 50],
            'Warehouse Name'
        )->addColumn(
            'sku',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            null,
            ['nullable' => false, 'unique' => true, 'length' => 64],
            'SKU'
        )->addColumn(
            'sku_type',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            null,
            ['nullable' => false, 'unique' => true, 'length' => 50],
            'SKU Type'
        )->addColumn(
            'qty_to_ship',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['nullable' => true, 'length' => 10],
            'Quantity To Ship'
        )->addColumn(
            'qty_ordered',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['nullable' => true, 'length' => 10],
            'Quantity Ordered'
        )->addColumn(
            'qty_shipped',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['nullable' => true, 'length' => 10],
            'Quantity Shipped'
        )->addColumn(
            'status',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['nullable' => true, 'length' => 10],
            'Status'
        )->addColumn(
            'user_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['nullable' => true, 'length' => 10],
            'User Id'
        )->addColumn(
            'fulfilled_at',
            \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
            null,
            ['nullable' => false, 'default' => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_INIT],
            'Fulfilled At'
        )->addColumn(
            'created_at',
            \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
            null,
            ['nullable' => false, 'default' => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_INIT],
            'Created At'
        );


        $temandoShipmentBoxTable = $setup->getConnection()->newTable(
            $setup->getTable('temando_box')
        )->addColumn(
            'id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
            'Id'
        )->addColumn(
            'shipment_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['nullable' => true, 'length' => 13],
            'Shipment ID'
        )->addColumn(
            'comment',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            null,
            ['nullable' => false, 'unique' => true, 'length' => 255],
            'Comment'
        )->addColumn(
            'qty',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['nullable' => true, 'length' => 11],
            'Quantity'
        )->addColumn(
            'value',
            \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
            '12,2',
            ['nullable' => false],
            'Value'
        )->addColumn(
            'length',
            \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
            '12,2',
            ['nullable' => false],
            'Length'
        )->addColumn(
            'width',
            \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
            '12,2',
            ['nullable' => false],
            'Width'
        )->addColumn(
            'height',
            \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
            '12,2',
            ['nullable' => false],
            'Height'
        )->addColumn(
            'measure_unit',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            null,
            ['nullable' => false, 'unique' => true, 'length' => 255],
            'Measure Unit'
        )->addColumn(
            'weight',
            \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
            '12,2',
            ['nullable' => false],
            'Weight'
        )->addColumn(
            'weight_unit',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            null,
            ['nullable' => false, 'unique' => true, 'length' => 255],
            'Weight Unit'
        )->addColumn(
            'fragile',
            \Magento\Framework\DB\Ddl\Table::TYPE_BOOLEAN,
            false,
            ['nullable' => false],
            'Fragile'
        )->addColumn(
            'dangerous',
            \Magento\Framework\DB\Ddl\Table::TYPE_BOOLEAN,
            false,
            ['nullable' => false],
            'Dangerous'
        )->addColumn(
            'packaging',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['nullable' => true, 'length' => 255],
            'Packaging'
        )->addColumn(
            'articles',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            null,
            ['nullable' => false, 'unique' => true],
            'Articles'
        );

        /**
         * Create table 'temando_zone'
         */
        $temandoZoneTable = $setup->getConnection()->newTable(
            $setup->getTable('temando_zone')
        )->addColumn(
            'zone_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
            'Id'
        )->addColumn(
            'name',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            null,
            ['nullable' => false, 'unique' => true, 'length' => 50],
            'Name'
        )->addColumn(
            'country_code',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            null,
            ['nullable' => false, 'length' => 2],
            'Country Code'
        )->addColumn(
            'ranges',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            null,
            ['nullable' => true],
            'ranges'
        )->setComment(
            'Temando Zone'
        );

        /**
         * Create table 'temando_origin'
         */
        $temandoOriginTable = $setup->getConnection()->newTable(
            $setup->getTable('temando_origin')
        )->addColumn(
            'origin_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
            'Origin Id'
        )->addColumn(
            'name',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            null,
            ['nullable' => false, 'unique' => true, 'length' => 255],
            'Name'
        )->addColumn(
            'is_active',
            \Magento\Framework\DB\Ddl\Table::TYPE_BOOLEAN,
            false,
            ['nullable' => false],
            'Active'
        )->addColumn(
            'company_name',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            null,
            ['nullable' => false, 'length' => 255],
            'Company'
        )->addColumn(
            'street',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            null,
            ['nullable' => false, 'length' => 255],
            'Street'
        )->addColumn(
            'city',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            null,
            ['nullable' => false, 'length' => 255],
            'City'
        )->addColumn(
            'region',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            null,
            ['nullable' => false, 'length' => 255],
            'Region'
        )->addColumn(
            'postcode',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            null,
            ['nullable' => true, 'length' => 50],
            'Postcode'
        )->addColumn(
            'country',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            null,
            ['nullable' => false, 'length' => 50],
            'Country'
        )->addColumn(
            'contact_name',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            null,
            ['nullable' => false, 'length' => 255],
            'Contact Name'
        )->addColumn(
            'contact_email',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            null,
            ['nullable' => false, 'length' => 255],
            'Contact Email'
        )->addColumn(
            'contact_phone_1',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            null,
            ['nullable' => false, 'length' => 255],
            'Phone 1'
        )->addColumn(
            'contact_phone_2',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            null,
            ['nullable' => true, 'length' => 255],
            'Phone 2'
        )->addColumn(
            'contact_fax',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            null,
            ['nullable' => true, 'length' => 255],
            'Fax'
        )->addColumn(
            'store_ids',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            null,
            ['nullable' => false, 'length' => 255],
            'Stores'
        )->addColumn(
            'erp_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            false,
            ['nullable' => true, 'length' => 15],
            'ERP ID'
        )->addColumn(
            'erp_code',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            false,
            ['nullable' => true, 'length' => 127],
            'ERP CODE'
        )->addColumn(
            'zone_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['nullable' => true, 'length' => 10],
            'Zone ID'
        )->addColumn(
            'loading_facilities',
            \Magento\Framework\DB\Ddl\Table::TYPE_BOOLEAN,
            false,
            ['nullable' => true],
            'Loading Facilities'
        )->addColumn(
            'dock',
            \Magento\Framework\DB\Ddl\Table::TYPE_BOOLEAN,
            false,
            ['nullable' => true, 'length' => 1],
            'Dock'
        )->addColumn(
            'forklift',
            \Magento\Framework\DB\Ddl\Table::TYPE_BOOLEAN,
            false,
            ['nullable' => true, 'length' => 1],
            'Forklift'
        )->addColumn(
            'limited_access',
            \Magento\Framework\DB\Ddl\Table::TYPE_BOOLEAN,
            false,
            ['nullable' => false],
            'Limited Access'
        )->addColumn(
            'postal_box',
            \Magento\Framework\DB\Ddl\Table::TYPE_BOOLEAN,
            false,
            ['nullable' => false],
            'Postal Box'
        )->addColumn(
            'label_type',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            null,
            ['nullable' => false, 'length' => 15],
            'Label Type'
        )->addColumn(
            'user_ids',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            null,
            ['nullable' => true],
            'Users IDs'
        )->addColumn(
            'account_mode',
            \Magento\Framework\DB\Ddl\Table::TYPE_BOOLEAN,
            false,
            ['nullable' => false, 'default' => false],
            'Account Mode'
        )->addColumn(
            'account_sandbox',
            \Magento\Framework\DB\Ddl\Table::TYPE_BOOLEAN,
            false,
            ['nullable' => false, 'default' => false],
            'Account Sandbox'
        )->addColumn(
            'account_username',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            false,
            ['nullable' => true, 'length' => 255],
            'Account Username'
        )->addColumn(
            'account_password',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            false,
            ['nullable' => true, 'length' => 255],
            'Account Password'
        )->addColumn(
            'account_clientid',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            false,
            ['nullable' => true, 'length' => 255],
            'Client ID'
        )->addColumn(
            'supporting_origins',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            null,
            ['nullable' => true],
            'Supporting Origins'
        )->addColumn(
            'description',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            null,
            ['nullable' => false, 'default' => ''],
            'Description'
        )->addColumn(
            'latitude',
            \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
            '12,8',
            ['nullable' => false, 'default' => '0.00000000'],
            'Latitude'
        )->addColumn(
            'longitude',
            \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
            '12,8',
            ['nullable' => false, 'default' => '0.00000000'],
            'Longitude'
        )->addColumn(
            'allow_store_collection',
            \Magento\Framework\DB\Ddl\Table::TYPE_BOOLEAN,
            '12,8',
            ['nullable' => false, 'default' => true],
            'Allow Store Pickup'
        )->addColumn(
            'schedule_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['unsigned' => true, 'nullable' => true],
            'Schedule Id'
        )->addColumn(
            'zoom_level',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['nullable' => false, 'default' => '4'],
            'Zoom Level of Store in Google Map'
        )->setComment(
            'Temando Origin'
        );

        /**
         * Create table 'temando_booking'
         */
        $temandoBookingTable = $installer->getConnection()->newTable(
            $installer->getTable('temando_booking')
        )->addColumn(
            'booking_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
            'Booking Id'
        )->addColumn(
            'shipment_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            20,
            ['unsigned' => true, 'nullable' => false],
            'Shipment Id'
        )->addColumn(
            'request_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            50,
            ['unsigned' => true, 'nullable' => false],
            'Booking Request Id'
        )->addColumn(
            'consignment_code',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['unsigned' => true, 'nullable' => false],
            'Consignment Code'
        )->addColumn(
            'consignment_document',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            null,
            ['nullable' => true],
            'Consignment Document'
        )->addColumn(
            'label_document',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            null,
            ['nullable' => true],
            'Label Document'
        )->addColumn(
            'created_at',
            \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
            null,
            ['nullable' => false, 'default' => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_INIT],
            'Created At'
        )->setComment(
            'Temando Booking'
        );



        /**
         * Create table 'temando_pickup'
         */
        $temandoPickupTable = $installer->getConnection()->newTable(
            $installer->getTable('temando_pickup')
        )->addColumn(
            'pickup_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
            'Pickup Id'
        )->addColumn(
            'order_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_BIGINT,
            20,
            ['unsigned' => true, 'nullable' => false],
            'Order Id'
        )->addColumn(
            'order_increment_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            50,
            ['unsigned' => true, 'nullable' => false],
            'Order Increment Id'
        )->addColumn(
            'order_status',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            32,
            ['unsigned' => true, 'nullable' => false],
            'Order Status'
        )->addColumn(
            'order_created_at',
            \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
            null,
            ['nullable' => false, 'default' => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_INIT],
            'Order Created At'
        )->addColumn(
            'order_shipment_amount',
            \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
            '12,2',
            ['nullable' => false, 'default' => '0.0000'],
            'Order Shipment Amount'
        )->addColumn(
            'origin_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_BIGINT,
            20,
            ['unsigned' => true, 'nullable' => false],
            'Origin Id'
        )->addColumn(
            'customer_selected_origin',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            null,
            ['nullable' => true, 'default' => null],
            'Customer Selected Origin'
        )->addColumn(
            'status',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            10,
            ['nullable' => false, 'default' => 0],
            'Status'
        )->addColumn(
            'billing_contact_name',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['nullable' => false],
            'Billing Contact Name'
        )->addColumn(
            'billing_company_name',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['nullable' => false],
            'Billing Company Name'
        )->addColumn(
            'billing_street',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['nullable' => false],
            'Billing Street'
        )->addColumn(
            'billing_city',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['nullable' => false],
            'Billing City'
        )->addColumn(
            'billing_postcode',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['nullable' => false],
            'Billing Postcode'
        )->addColumn(
            'billing_region',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['nullable' => false],
            'Billing Region'
        )->addColumn(
            'billing_country',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['nullable' => false],
            'Billing Country'
        )->addColumn(
            'billing_phone',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['nullable' => false],
            'Billing Phone'
        )->addColumn(
            'billing_email',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['nullable' => false],
            'Billing Email'
        )->addColumn(
            'collected_date',
            \Magento\Framework\DB\Ddl\Table::TYPE_DATE,
            null,
            ['nullable' => true],
            'Collected Date'
        )->addColumn(
            'ready_date',
            \Magento\Framework\DB\Ddl\Table::TYPE_DATE,
            null,
            ['nullable' => true],
            'Ready Date'
        )->setComment(
            'Temando Pickup'
        );
/**
        $installer->getConnection()->addColumn(
            $installer->getTable('quote_address'),
            'is_business_address',
            [
                'type' => \Magento\Framework\DB\Ddl\Table::TYPE_BOOLEAN,
                'nullable' => true,
                'default' => null,
                'comment' => 'Address is a business address',
            ]
        );

        $installer->getConnection()->addColumn(
            $installer->getTable('quote_address'),
            'authority_to_leave',
            [
                'type' => \Magento\Framework\DB\Ddl\Table::TYPE_BOOLEAN,
                'nullable' => true,
                'default' => null,
                'comment' => 'Authority to Leave',
            ]
        );
*/

        /**
         * Create table 'temando_origin_inventory'
         */
        $temandoOriginInventoryTable = $setup->getConnection()->newTable(
            $setup->getTable('temando_origin_inventory')
        )->addColumn(
            'erp_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['unsigned' => true, 'nullable' => false, 'primary' => true],
            'Origin ERP Id'
        )->addColumn(
            'sku',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            64,
            ['unsigned' => true, 'nullable' => false, 'primary' => true, 'length' => 64],
            'Product SKU'
        )->addColumn(
            'units',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['nullable' => false, 'length' => 50],
            'Units'
        )->setComment(
            'Temando Origin Inventory'
        );

        /*
        * Create table temando_origin_specialday
        */
        $temandoOriginSpecialdayTable = $installer->getConnection()->newTable(
            $installer->getTable('temando_origin_specialday')
        )->addColumn(
            'origin_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['unsigned' => true, 'nullable' => false],
            'Storepickup Id'
        )->addColumn(
            'specialday_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['unsigned' => true, 'nullable' => false],
            'Holiday ID'
        )->setComment(
            'Store Special day Table'
        );

        /**
         * Create table 'temando_origin_holiday'
         */
        $temandoOriginHolidayTable = $setup->getConnection()->newTable(
            $setup->getTable('temando_origin_holiday')
        )->addColumn(
            'origin_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['unsigned' => true, 'nullable' => false],
            'Origin Id'
        )->addColumn(
            'holiday_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['unsigned' => true, 'nullable' => false],
            'Holiday Id'
        );

//        $setup->getConnection()->addColumn(
//            $setup->getTable('sales_order_address'),
//            'is_business_address',
//            [
//                'type' => Table::TYPE_BOOLEAN,
//                'nullable' => true,
//                'default' => null,
//                'comment' => 'Address is a business address',
//            ]
//        );
//
//        $setup->getConnection()->addColumn(
//            $setup->getTable('sales_order_address'),
//            'authority_to_leave',
//            [
//                'type' => Table::TYPE_BOOLEAN,
//                'nullable' => true,
//                'default' => null,
//                'comment' => 'Authority to leave',
//            ]
//        );

        /**
         * Create table 'temando_rule'
         */
        $temandoRuleTable = $setup->getConnection()->newTable(
            $setup->getTable('temando_rule')
        )->addColumn(
            'rule_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
            'Id'
        )->addColumn(
            'name',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            null,
            ['nullable' => false, 'unique' => true, 'length' => 50],
            'Name'
        )->addColumn(
            'is_active',
            \Magento\Framework\DB\Ddl\Table::TYPE_BOOLEAN,
            false,
            ['nullable' => false],
            'Active'
        )->addColumn(
            'from_date',
            \Magento\Framework\DB\Ddl\Table::TYPE_DATE,
            null,
            ['nullable' => true],
            'From Date'
        )->addColumn(
            'to_date',
            \Magento\Framework\DB\Ddl\Table::TYPE_DATE,
            null,
            ['nullable' => true],
            'To Date'
        )->addColumn(
            'priority',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            10,
            ['unsigned' => true, 'nullable' => true, 'default' => null],
            'Priority'
        )->addColumn(
            'stop_other',
            \Magento\Framework\DB\Ddl\Table::TYPE_BOOLEAN,
            false,
            ['nullable' => false],
            'Stop other rules from processing'
        )->addColumn(
            'store_ids',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            null,
            ['nullable' => false, 'length' => 255],
            'Stores'
        )->addColumn(
            'category_ids',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            null,
            ['nullable' => false, 'length' => 255],
            'Categories'
        )->addColumn(
            'group_ids',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            null,
            ['nullable' => true, 'length' => 255],
            'Groups'
        )->addColumn(
            'condition_time_type',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            4,
            ['unsigned' => true, 'nullable' => true, 'default' => null],
            'Condition time type'
        )->addColumn(
            'condition_time_value',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            null,
            ['nullable' => true, 'default' => null, 'length' => 8],
            'Condition time value'
        )->addColumn(
            'condition_time_operator',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            4,
            ['unsigned' => true, 'nullable' => true, 'default' => null],
            'Condition time operator'
        )->addColumn(
            'secondary_condition_time_type',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            4,
            ['unsigned' => true, 'nullable' => true, 'default' => null],
            'Secondary condition time type'
        )->addColumn(
            'secondary_condition_time_value',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            null,
            ['nullable' => true, 'default' => null, 'length' => 8],
            'Secondary condition time value'
        )->addColumn(
            'condition_day',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            null,
            ['nullable' => true, 'default' => null, 'length' => 8],
            'Condition day'
        )->addColumn(
            'conditions_serialized',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            null,
            ['nullable' => true, 'default' => null, 'length' => 65535],
            'Conditions serialized'
        )->addColumn(
            'action_rate_type',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            4,
            ['unsigned' => true, 'nullable' => true, 'default' => null],
            'Action rate type'
        )->addColumn(
            'action_rate_fee',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            4,
            ['unsigned' => true, 'nullable' => true, 'default' => null],
            'Action rate fee'
        )->addColumn(
            'action_static_value',
            \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
            '12,2',
            ['nullable' => false],
            'Static value'
        )->addColumn(
            'action_static_label',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            null,
            ['nullable' => true, 'default' => null, 'length' => 255],
            'Action static label'
        )->addColumn(
            'action_dynamic_carriers',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            null,
            ['nullable' => true, 'default' => null, 'length' => 65535],
            'Action dynamic carriers'
        )->addColumn(
            'action_dynamic_filter',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            4,
            ['unsigned' => true, 'nullable' => true, 'default' => null],
            'Action dynamic filter'
        )->addColumn(
            'action_dynamic_filter_auspost',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            4,
            ['unsigned' => true, 'nullable' => true, 'default' => null],
            'Action dynamic filter AusPost'
        )->addColumn(
            'action_dynamic_adjustment_type',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            4,
            ['unsigned' => true, 'nullable' => true, 'default' => null],
            'Action dynamic adjustment type'
        )->addColumn(
            'action_dynamic_adjustment_value',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            null,
            ['nullable' => true, 'default' => null, 'length' => 15],
            'Action dynamic adjustment value'
        )->addColumn(
            'action_dynamic_adjustment_roundup',
            \Magento\Framework\DB\Ddl\Table::TYPE_BOOLEAN,
            true,
            ['nullable' => false, 'default' => false],
            'Action dynamic adjustment round up to nearest whole number'
        )->addColumn(
            'action_dynamic_show_carrier_name',
            \Magento\Framework\DB\Ddl\Table::TYPE_BOOLEAN,
            true,
            ['nullable' => false],
            'Action show dynamic carrier name'
        )->addColumn(
            'action_dynamic_show_carrier_method',
            \Magento\Framework\DB\Ddl\Table::TYPE_BOOLEAN,
            true,
            ['nullable' => false],
            'Action show dynamic carrier method'
        )->addColumn(
            'action_dynamic_show_carrier_time',
            \Magento\Framework\DB\Ddl\Table::TYPE_BOOLEAN,
            true,
            ['nullable' => false],
            'Action show dynamic carrier time'
        )->addColumn(
            'action_dynamic_label',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            null,
            ['nullable' => true, 'default' => null, 'length' => 255],
            'Action dynamic label'
        )->addColumn(
            'action_restrict_note',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            null,
            ['nullable' => true, 'default' => null, 'length' => 500],
            'Action restrict note'
        )->addColumn(
            'action_additional_charge_items',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            null,
            ['nullable' => true, 'default' => null, 'length' => 500],
            'Action additional charge items'
        )->addColumn(
            'servicetype',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            4,
            ['unsigned' => true, 'nullable' => false, 'default' => 3],
            'Service type'
        )->addColumn(
            'attribute_set_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            10,
            ['unsigned' => true, 'nullable' => true],
            'Attribute Set Id'
        )->setComment(
            'Temando Rule'
        );

        $installer->getConnection()->createTable($temandoCarrierTable);
        $installer->getConnection()->createTable($temandoQuoteTable);
        $installer->getConnection()->createTable($temandoShipmentTable);
        $installer->getConnection()->createTable($temandoShipmentItemTable);
        $installer->getConnection()->createTable($temandoShipmentBoxTable);
        $installer->getConnection()->createTable($temandoZoneTable);
        $installer->getConnection()->createTable($temandoOriginTable);
        $installer->getConnection()->createTable($temandoBookingTable);
        $installer->getConnection()->createTable($temandoPickupTable);
        $installer->getConnection()->createTable($temandoOriginInventoryTable);
        $installer->getConnection()->createTable($temandoOriginHolidayTable);
        $installer->getConnection()->createTable($temandoOriginSpecialdayTable);
        $installer->getConnection()->createTable($temandoRuleTable);

        $installer->endSetup();
    }
}
