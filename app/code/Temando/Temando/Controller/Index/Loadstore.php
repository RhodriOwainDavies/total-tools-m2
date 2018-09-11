<?php

namespace Temando\Temando\Controller\Index;

use Magento\Framework\Controller\ResultFactory;
use Magestore\Storepickup\Model\Config\Source\OrderTypeStore;

class Loadstore extends \Magestore\Storepickup\Controller\Index
{
    /**
     * Default current page.
     */
    const DEFAULT_CURRENT_PAGINATION = 1;

    /**
     * Default range pagination.
     */
    const DEFAULT_RANGE_PAGINATION = 5;

    protected $_objectManager;

    protected $_collectionFactory;

    public function execute()
    {
        $this->_objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $originCollection = $this->_objectManager->create('Temando\Temando\Model\ResourceModel\Origin\Collection');
        $collection = $this->_filterStoreCollection($originCollection);

        $resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);

        $response = $this->resultFactory->create(ResultFactory::TYPE_RAW);
        $response->setHeader('Content-type', 'text/plain');

        $response->setContents(
            $this->_jsonHelper->jsonEncode(
                [
                    'storesjson' => $collection,
                    'num_store' => count($collection),
                ]
            )
        );

        return $response;
    }

    /**
     * Filter store.
     *
     * @param \Temando\Temando\Model\ResourceModel\Origin\Collection $collection
     *
     * @return \Temando\Temando\Model\ResourceModel\Origin\Collection
     */
    protected function _filterStoreCollection(
        \Temando\Temando\Model\ResourceModel\Origin\Collection $collection
    ) {
        $collection->addFieldToSelect([
            'name',
            'contact_phone_1',
            'street',
            'latitude',
            'longitude',
            'zoom_level',
            'erp_id'
        ]);

        $curPage = $this->getRequest()->getParam('curPage', self::DEFAULT_CURRENT_PAGINATION);

        /*
         * Filter store enabled
         */
        $collection->addFieldToFilter('is_active', \Temando\Temando\Model\Origin::STATUS_ENABLED);
        $collection->addFieldToFilter('allow_store_collection', '1');
        //return $collection;
        /*
         * filter by radius
         */
        if ($radius = $this->getRequest()->getParam('radius')) {
            $latitude = $this->getRequest()->getParam('latitude');
            $longitude = $this->getRequest()->getParam('longitude');
            $collection->addLatLngToFilterDistance($latitude, $longitude, $radius);
        }

        /*
         * filter by store information
         */

        if ($countryId = $this->getRequest()->getParam('countryId')) {
            $collection->addFieldToFilter('country_id', $countryId);
        }

        if ($storeName = $this->getRequest()->getParam('storeName')) {
            $collection->addFieldToFilter('name', ['like' => "%$storeName%"]);
        }

        if ($state = $this->getRequest()->getParam('state')) {
            $collection->addFieldToFilter('state', ['like' => "%$state%"]);
        }

        if ($city = $this->getRequest()->getParam('city')) {
            $collection->addFieldToFilter('city', ['like' => "%$city%"]);
        }

        if ($zipcode = $this->getRequest()->getParam('zipcode')) {
            $collection->addFieldToFilter('postcode', ['like' => "%$zipcode%"]);
        }

        // Set sort type for list store
        switch ($this->_systemConfig->getSortStoreType()) {
            case OrderTypeStore::SORT_BY_ALPHABETICAL:
                $collection->setOrder('name', \Magento\Framework\Data\Collection\AbstractDb::SORT_ORDER_ASC);
                break;

            case OrderTypeStore::SORT_BY_DISTANCE:
                if ($radius) {
                    $collection->setOrder('distance', \Magento\Framework\Data\Collection\AbstractDb::SORT_ORDER_ASC);
                }
                break;
            default:
        }
        
        $helper = $this->_objectManager->create('Temando\Temando\Helper\Data');
        $cart = $this->_objectManager->create('Magento\Checkout\Model\Cart');
        // get all items in cart and their quantity
        $items = $cart->getQuote()->getAllItems();
        $products = array();
        foreach ($items as $item) {
            $products[$item->getSku()] = $item->getQty();
        }
        // update origins with stock_level
        $origins = $helper->getOriginsStockLevelMessage($collection->getData(), $products);
        return $origins;
    }
}
