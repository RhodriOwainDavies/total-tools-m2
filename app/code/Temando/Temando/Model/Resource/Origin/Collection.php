<?php
namespace Temando\Temando\Model\Resource\Origin;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Temando\Temando\Model\Origin;

class Collection extends AbstractCollection
{
    /**
     * Temando Helper.
     *
     * @var \Temando\Temando\Helper\Data
     */
    protected $_helper;

    /**
     * Magento Catalog Product Factory
     *
     * @var \Magento\Catalog\Model\ProductFactory
     */
    protected $_productFactory;

    /**
     * Define model & resource model
     */
    const TEMANDO_ORIGIN_TABLE = 'temando_origin';

    /**
     * Define fallback store id
     */
    const FALLBACK_STORE_ID = 1;


    /**
     * Collection constructor.
     *
     * @param \Magento\Framework\Data\Collection\EntityFactoryInterface $entityFactory
     * @param \Psr\Log\LoggerInterface $logger
     * @param \Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetchStrategy
     * @param \Magento\Framework\Event\ManagerInterface $eventManager
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Temando\Temando\Helper\Data $helper
     * @param \Magento\Framework\DB\Adapter\AdapterInterface|null $connection
     * @param \Magento\Framework\Model\ResourceModel\Db\AbstractDb|null $resource
     */
    public function __construct(
        \Magento\Framework\Data\Collection\EntityFactoryInterface $entityFactory,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetchStrategy,
        \Magento\Framework\Event\ManagerInterface $eventManager,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Catalog\Model\ProductFactory $productFactory,
        \Temando\Temando\Helper\Data $helper,
        \Magento\Framework\DB\Adapter\AdapterInterface $connection = null,
        \Magento\Framework\Model\ResourceModel\Db\AbstractDb $resource = null
    ) {
        $this->_productFactory = $productFactory;
        $this->_helper = $helper;
        $this->_init(
            'Temando\Temando\Model\Origin',
            'Temando\Temando\Model\Resource\Origin'
        );
        parent::__construct(
            $entityFactory,
            $logger,
            $fetchStrategy,
            $eventManager,
            $connection,
            $resource
        );
        $this->storeManager = $storeManager;
    }

    /**
     * Get Origin By Postcode
     *
     * @param string $postcode
     * @param int null $countryId
     * @param int null $storeId
     *
     * @return \Temando\Temando\Model\Origin null
     */
    public function getOriginByPostcode($postcode, $countryId = null, $storeId = null)
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();

        $countryId = $countryId ? $countryId : $this->_helper->getDefaultCountryId();
        $this->addFieldToFilter('is_active', 1)->setOrder('origin_id', 'ASC')->load();
        $validOrigin = null;
        foreach ($this->_items as $warehouse) {
            $origin = $objectManager->create('Temando\Temando\Model\Origin');
            $origin = $origin->load($warehouse->getId());

            $store_ids = explode(',', $origin->getStoreIds());
            if ($storeId && !in_array($storeId, $store_ids)) {
                $this->_logger->debug(
                    date('Y/m/d H:i:s') . ' ' . get_class($this)
                    . ' does not serve this magento store'
                );
                continue;
            }

            if ($origin->servesArea($postcode, $countryId)) {
                $validOrigin = $origin;
                break;
            }
        }
        if (!$validOrigin) {
            return $origin->load(self::FALLBACK_STORE_ID);
        }
        return $validOrigin;
    }

    /**
     * Returns origin best suited to fulfill this order based on specific algorithm
     *
     * @param array $items
     * @param null $countryId
     * @param null $storeId
     *
     * @return null|\Temando\Temando\Model\Origin
     */
    public function getOriginByInventory($items, $postcode, $countryId = null, $storeId = null)
    {
        $this->_logger->debug(date('Y/m/d H:i:s') . ' ' . get_class($this) . ' getOriginByInventory()');
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $allowedProductTypes = array(
            Origin::STOCK_ONLINE,
            Origin::STOCK_ON_CATALOG
        );
        $skus = array();
        foreach ($items as $item) {
            $_product = $this->_productFactory->create();
            $_product = $_product->loadByAttribute('sku', $item->getSku());
            $stockAvailabilityCode = $_product->getAttributeText('stock_availability_code');

            //if any SP items in cart - cart is unshippable
            if ($stockAvailabilityCode==Origin::STOCK_SPECIAL_PRODUCT) {
                $this->_logger->debug(
                    date('Y/m/d H:i:s') . ' ' . get_class($this)
                    . ' The cart contains an unshippable product ('.$_product->getSku().')'
                );
                return;
            } elseif (in_array($stockAvailabilityCode, $allowedProductTypes)) {
                //build array of skus and quantites that need checking
                if ($item instanceof \Magento\Quote\Model\Quote\Item) {
                    $skus[$item->getSku()] = $item->getQty();
                } elseif ($item instanceof \Magento\Sales\Model\Order\Item) {
                    $skus[$item->getSku()] = $item->getQtyOrdered();
                }
            }
        }
        $postcodeOrigin = $this->getOriginByPostcode($postcode, $countryId, $storeId);

        //No OL or OD items, no stock check required, return postcodeOrigin
        if (count($skus)==0) {
            $this->_logger->debug(
                date('Y/m/d H:i:s') . ' ' . get_class($this)
                . ' The cart contains only OD products, no stock check'
            );
            return $postcodeOrigin;
        }
        $this->_logger->debug(
            date('Y/m/d H:i:s') . ' ' . get_class($this)
            . ' The cart contains OC/OL products, check stock'
        );

        //check if postcodeOrigin has stock
        if ($postcodeOrigin->hasStock($skus)) {
            $this->_logger->debug(
                date('Y/m/d H:i:s') . ' ' . get_class($this)
                . ' getOriginByInventory() returning postcodeOrigin'
            );

            return $postcodeOrigin;
        }

        $supportingOriginsStr = $postcodeOrigin->getSupportingOrigins();
        if (!is_null($supportingOriginsStr)) {
            $supportedOrigins = explode(',', $postcodeOrigin->getSupportingOrigins());

            $this->_logger->debug(
                date('Y/m/d H:i:s') . ' ' . get_class($this)
                . ' getOriginByInventory() check supporting warehouses'
            );

            foreach ($supportedOrigins as $key => $supportingOriginId) {
                if (is_numeric($supportingOriginId)) {
                    $origin = $objectManager->create('Temando\Temando\Model\Origin');
                    $supportingOrigin = $origin->load($supportingOriginId);
                    $this->_logger->debug(
                        date('Y/m/d H:i:s') . ' ' . get_class($this)
                        . ' loaded(' . $supportingOriginId . ') ' . $supportingOrigin->getName() . ' '
                        . get_class($supportingOrigin)
                    );

                    if ($supportingOrigin->hasStock($skus)) {
                        $this->_logger->debug(
                            date('Y/m/d H:i:s') . ' ' . get_class($this)
                            . ' getOriginByInventory returning(' . $supportingOriginId . ') '
                            . $supportingOrigin->getName()
                        );
                        return $supportingOrigin;
                    }
                }
            }
        } else {
            $this->_logger->debug(
                date('Y/m/d H:i:s') . ' ' . get_class($this)
                . ' this origin ('.$postcodeOrigin->getName().') does not have any supporting origins'
            );
        }

        $origin = $objectManager->create('Temando\Temando\Model\Origin');
        return $origin->load(self::FALLBACK_STORE_ID);
    }
}
