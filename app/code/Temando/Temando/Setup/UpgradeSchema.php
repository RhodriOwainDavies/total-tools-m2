<?php

namespace Temando\Temando\Setup;

use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\DB\Adapter\AdapterInterface;

class UpgradeSchema implements UpgradeSchemaInterface
{
    /**
     * {@inheritdoc}
     */
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;
        $installer->startSetup();

        if (version_compare($context->getVersion(), '1.0.17', '<')) {
            $this->modifyColumnTextLength($setup);
        }

        $installer->endSetup();
    }

    /**
     * Modify length of text column
     *
     * @param SchemaSetupInterface $setup
     */
    public function modifyColumnTextLength(SchemaSetupInterface $setup)
    {
        $setup->getConnection()->modifyColumn(
            $setup->getTable('temando_booking'),
            'consignment_document',
            [
                'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                'length' => '2M',
                'comment' => 'Consignment Document'
            ]
        );
        $setup->getConnection()->modifyColumn(
            $setup->getTable('temando_booking'),
            'label_document',
            [
                'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                'length' => '2M',
                'comment' => 'Label Document'
            ]
        );
    }
}
