<?php

namespace Temando\Temando\Helper;

use Temando\Temando\Model\Origin;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * User Collection.
     *
     * @var \Magento\User\Model\ResourceModel\User\Collection
     */
    protected $_userCollection;

    /**
     * Zone Collection.
     *
     * @var \Temando\Temando\Model\ResourceModel\Zone\Collection
     */
    protected $_zoneCollection;

    /**
     * Origin Collection.
     *
     * @var \Temando\Temando\Model\ResourceModel\Origin\Collection
     */
    protected $_originCollection;

    /**
     * Origin Collection Factory.
     *
     * @var \Temando\Temando\Model\ResourceModel\Origin\Collection
     */
    protected $_originCollectionFactory;

    /**
     * Resource Origin Collection
     *
     * @var \Temando\Temando\Model\Resource\Origin\Collection
     */
    protected $_resourceOriginCollectionFactory;

    /**
     * Temando Zone.
     *
     * @var \Temando\Temando\Model\Zone
     */
    protected $_zone;

    /**
     * Magento Catalog Product.
     *
     * @var \Magento\Catalog\Model\Product
     */
    protected $_product;

    /**
     * Scope Config Interface.
     *
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $_scopeConfig;

    /**
     * Authorization Interface
     *
     * @var \Magento\Framework\AuthorizationInterface
     */
    protected $_authorization;

    /**
     * Country Helper.
     *
     * @var \Magento\Directory\Model\Config\Source\Country
     */
    protected $_countryHelper;

    /**
     * Logger.
     *
     * @var \Psr\Log\LoggerInterface
     */
    protected $_logger;

    /**
     * Price.
     *
     * @var \Magento\Framework\Pricing\PriceCurrencyInterface
     */
    protected $_priceCurrency;

    /**
     * Temando unit of weight measurement.
     *
     * @var \Temando\Temando\Model\System\Config\Source\Unit\Weight
     */
    protected $_weight;

    /**
     * Temando unit of distance measurement.
     *
     * @var \Temando\Temando\Model\System\Config\Source\Unit\Measure
     */
    protected $_measure;

    /**
     * Resource Connection.
     *
     * @var \Magento\Framework\App\ResourceConnection
     */
    protected $_resourceConnection;

    /**
     * Deployment Config.
     *
     * @var \Magento\Framework\App\DeploymentConfig
     */
    protected $_config;

    /**
     * Product Repository.
     *
     * @var \Magento\Catalog\Api\ProductRepositoryInterface
     */
    protected $_productRepositoryInterface;

    /**
     * Catalog Product Factory
     *
     * @var \Magento\Catalog\Model\ProductFactory
     */
    protected $_productFactory;

    /**
     * Directory List
     *
     * @var \Magento\Framework\App\Filesystem\DirectoryList
     */
    protected $_directoryList;

    /**
     * Object Manager
     *
     * @var \Magento\Framework\App\ObjectManager
     */
    protected $_objectManager;

    /**
     * Default currency code
     */
    const DEFAULT_CURRENCY_CODE = 'AUD';

    /**
     * Default country id
     */
    const DEFAULT_COUNTRY_ID = 'AU';

    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\User\Model\ResourceModel\User\Collection $userCollection,
        \Magento\Catalog\Model\Product $product,
        \Temando\Temando\Model\ResourceModel\Zone\Collection $zoneCollection,
        \Temando\Temando\Model\ResourceModel\Origin\Collection $originCollection,
        \Temando\Temando\Model\ResourceModel\Origin\CollectionFactory $originCollectionFactory,
        \Temando\Temando\Model\Resource\Origin\CollectionFactory $resourceOriginCollectionFactory,
        \Temando\Temando\Model\Zone $zone,
        \Magento\Backend\Block\Template\Context $templateContext,
        \Magento\Backend\Model\Auth\Session $authSession,
        \Magento\Directory\Model\Config\Source\Country $countryHelper,
        \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency,
        \Temando\Temando\Model\System\Config\Source\Unit\Weight $weight,
        \Temando\Temando\Model\System\Config\Source\Unit\Measure $measure,
        \Magento\Framework\App\ResourceConnection $resourceConnection,
        \Magento\Framework\App\DeploymentConfig $config,
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepositoryInterface,
        \Magento\Catalog\Model\ProductFactory $productFactory,
        \Magento\Framework\App\Filesystem\DirectoryList $directoryList
    ) {
        $this->_userCollection = $userCollection;
        $this->_product = $product;
        $this->_zoneCollection = $zoneCollection;
        $this->_originCollection = $originCollection;
        $this->_originCollectionFactory = $originCollectionFactory;
        $this->_resourceOriginCollectionFactory = $resourceOriginCollectionFactory;
        $this->_zone = $zone;
        $this->_authorization = $templateContext->getAuthorization();
        $this->_authSession = $authSession;
        $this->_countryHelper = $countryHelper;
        $this->_scopeConfig = $context->getScopeConfig();
        $this->_logger = $context->getLogger();
        $this->_priceCurrency = $priceCurrency;
        $this->_weight = $weight;
        $this->_measure = $measure;
        $this->_resourceConnection = $resourceConnection;
        $this->_config = $config;
        $this->_productRepositoryInterface = $productRepositoryInterface;
        $this->_productFactory = $productFactory;
        $this->_directoryList = $directoryList;
        $this->_objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        parent::__construct($context);
    }
    /**
     * Get zones Options Array
     *
     * @return array
     */
    public function getZonesOptionArray()
    {
        $zones = array();
        $zoneItems = $this->_zoneCollection->load();
        foreach ($zoneItems as $zone) {
            $zones[$zone->getId()] = $zone->getData('name');
        }
        return $zones;
    }

    /**
     * Get users Options Array
     *
     * @return array
     */
    public function getUsersOptionArray()
    {
        $users = array();
        $userItems = $this->_userCollection->load();
        foreach ($userItems as $user) {
            $users[] = [
                'value' => $user->getId(),
                'label' => $user->getData('username')
            ];
        }
        return $users;
    }

    /**
     * Get origins Options Array
     *
     * @return array
     */

    public function getOriginOptionArray()
    {
        $origins = array();
        $originItems = $this->_originCollection->load();

        $user = $this->_authSession->getUser();

        foreach ($originItems as $origin) {
            $originUserIds = explode(',', $origin->getUserIds());
            if (($user->getAclRole() == 1) || (in_array($user->getUserId(), $originUserIds))) {
                $origins[$origin->getId()] = $origin->getData('name');
            }
        }
        return $origins;
    }

    /**
     * Get catalog product from.
     *
     * @param \Temando\Temando\Model\Shipment\Item $item
     *
     * @return bool|\Magento\Catalog\Model\AbstractModel
     */
    public function getCatalogProduct(\Temando\Temando\Model\Shipment\Item $item)
    {
        return $this->_product->loadByAttribute('sku', $item->getSku());
    }

    /**
     * Check permission for passed action.
     *
     * @param string $resourceId
     *
     * @return bool
     */
    public function _isAllowedAction($resourceId)
    {
        return $this->_authorization->isAllowed($resourceId);
    }

    /**
     * Returns articles of an item
     * (supports scenario where 1 sku is shipped in up to 5 packages)
     *
     * @param \Magento\Sales\Model\Quote\Item $item
     *
     * @return array
     */
    public function getProductArticles($item, $international = false)
    {
        $articles = array();
        $this->_isInternational = $international;

        $product = $this->_product->loadByAttribute('sku', $item->getSku());

        $weight = $item->getWeight();
        $length = $product->getData("shipping_length");
        $width = $product->getData("shipping_width");
        $height = $product->getData("shipping_height");

        $articles[] = array(
            'description' => $this->cleanArticleDescription($item->getName()),
            'packaging' => 'Box',
            'value' => round($item->getPrice(), 2),
            'fragile' => $product->getData("shipping_fragile") ?
                $product->getData("shipping_fragile") : $this->_scopeConfig->getValue(
                    'temando/defaults/fragile',
                    \Magento\Store\Model\ScopeInterface::SCOPE_STORE
                ),
            'dangerous' => $product->getData("shipping_dangerous") ?
                $product->getData("shipping_dangerous") : $this->_scopeConfig->getValue(
                    'temando/defaults/dangerous',
                    \Magento\Store\Model\ScopeInterface::SCOPE_STORE
                ),
            'needs_packaging' => $product->getData("shipping_needs_packaging"),
            'weight' => $weight ? $weight : $this->_scopeConfig->getValue(
                'temando/defaults/weight',
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE
            ),
            'length' => $length ? $length : $this->_scopeConfig->getValue(
                'temando/defaults/length',
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE
            ),
            'width' => $width ? $width : $this->_scopeConfig->getValue(
                'temando/defaults/width',
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE
            ),
            'height' => $height ? $height : $this->_scopeConfig->getValue(
                'temando/defaults/height',
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE
            ),
        );
        return $articles;
    }

    /*
     * Clean the article description from unwanted characters before passing
     * the article array
     *
     * @param string $description
     * @return string
     */
    public function cleanArticleDescription($description)
    {
        $description = str_replace(',', '', $description);
        $description = str_replace('"', '', $description);
        $description = str_replace("'", '', $description);
        return $description;
    }

    /**
     * Format price.
     *
     * @param $price
     *
     * @return mixed
     */
    public function formatPrice($price)
    {
        return number_format($price, 2, '.', ',');
    }

    /**
     * Check shipment permission.
     *
     * @param $shipment
     *
     * @return bool
     */
    public function checkShipmentPermission(\Temando\Temando\Model\Shipment $shipment)
    {
        $origin = $shipment->getOrigin();
        $user = $this->_authSession->getUser();
        $originUserIds = explode(',', $origin->getUserIds());

        if (($user->getAclRole() == 1) || (in_array($user->getId(), $originUserIds))) {
            return true;
        }
        return false;
    }

    /**
     * Check pickup permission.
     *
     * @param $pickup
     *
     * @return bool
     */
    public function checkPickupPermission(\Temando\Temando\Model\Pickup $pickup)
    {
        $origin = $pickup->getOrigin();
        $user = $this->_authSession->getUser();
        $originUserIds = explode(',', $origin->getUserIds());

        if (($user->getAclRole() == 1) || (in_array($user->getId(), $originUserIds))) {
            return true;
        }
        return false;
    }

    /**
     * Get current currency symbol.
     *
     * @return string
     */
    public function getCurrentCurrencySymbol()
    {
        return $this->_priceCurrency->getCurrency()->getCurrencySymbol();
    }

    /**
     * Return the weight unit text.
     *
     * @param int $unit
     *
     * @return string
     */
    public function getWeightUnitText($unit = null)
    {
        if (!$unit) {
            $unit = $this->_scopeConfig->getValue(
                'temando/units/weight',
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE
            );
        }
        return $this->_weight->getBriefOptionLabel($unit);
    }

    /**
     * Return the measure unit text
     *
     * @param int $unit
     *
     * @return string
     */
    public function getMeasureUnitText($unit = null)
    {
        if (!$unit) {
            $unit = $this->_scopeConfig->getValue(
                'temando/units/measure',
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE
            );
        }
        return $this->_measure->getBriefOptionLabel($unit);
    }

    /**
     * Get Carrier object by specific temando carrier id
     *
     * @param int $temandoCarrierId
     *
     * @return \Temando\Temando\Model\Carrier
     */
    public function getCarrierByTemandoId($temandoCarrierId)
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $carrierCollection = $objectManager->create('Temando\Temando\Model\ResourceModel\Carrier\Collection');
        return $carrierCollection->addFieldToFilter('temando_carrier_id', $temandoCarrierId)->load()->getFirstItem();
    }


    /**
     * Get product weight
     *
     * @param \Magento\Catalog\Product $product
     *
     * @return float
     */
    public function getItemWeight($product)
    {
        return $product->getWeight() ? $product->getWeight() : $this->_scopeConfig->getValue(
            'temando/defaults/weight',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Get product width
     *
     * @param \Magento\Catalog\Product $product
     *
     * @return float
     */
    public function getItemWidth($product)
    {
        return $product->getShippingWidth() ? $product->getShippingWidth() : $this->_scopeConfig->getValue(
            'temando/defaults/width',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Get product length
     *
     * @param \Magento\Catalog\Product $product
     *
     * @return float
     */
    public function getItemLength($product)
    {
        return $product->getShippingLength() ? $product->getShippingLength() : $this->_scopeConfig->getValue(
            'temando/defaults/length',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Get product height
     *
     * @param \Magento\Catalog\Product $product
     *
     * @return float
     */
    public function getItemHeight($product)
    {
        return $product->getShippingHeight() ? $product->getShippingHeight() : $this->_scopeConfig->getValue(
            'temando/defaults/height',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }
    /**
     * Returns array of allowed countries based on Magento system configuration
     * and Temando plugin allowed countries.
     *
     * @param boolean $asJson
     *
     * @return array
     */
    public function getAllowedCountries()
    {
        $specific = $this->_scopeConfig->getValue(
            'carriers/temando/sallowspecific',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
        //check if all allowed and return selected
        if ($specific == 1) {
            $availableCountries = explode(',', $this->_scopeConfig->getValue(
                'carriers/temando/specificcountry',
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE
            ));
        } else {
            //return all allowed
            $availableCountries = explode(',', $this->_scopeConfig->getValue(
                'general/country/allow',
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE
            ));
        }
        return $availableCountries;
    }

    /**
     * Returns an array of all Magento countries
     *
     * @return array
     */
    public function getAllCountries()
    {
        //$countries = array();
        $countries = $this->_countryHelper->toOptionArray();
        //foreach ($countryCollection as $country) {
//        foreach ( $countries as $countryKey => $country ) {
//                /* @var $country Mage_Directory_Model_Country */
//                $countries[$country['value']] = $country->getName();
//            }
//        }
        return $countries;
    }

    /**
     * Gets the date when a package will be ready to ship. Adjusts dates so
     * that they always fall on a weekday.
     *
     * @param <type> $ready_time timestamp for when the package will be ready
     * to ship, defaults to 10 days from current date
     */
    public function getReadyDate($ready_time = null)
    {
        if (is_null($ready_time)) {
            $ready_time = strtotime('+1 days');
        }
        if (is_numeric($ready_time) && $ready_time >= strtotime(date('Y-m-d'))) {
            $weekend_days = array('6', '7');
            while (in_array(date('N', $ready_time), $weekend_days)) {
                $ready_time = strtotime('+1 day', $ready_time);
            }
            return $ready_time;
        }
    }

    /**
     * Converts given weight from configured unit to grams
     *
     * @param float $value Weight to convert
     *
     * @return float Converted weight in grams
     */
    public function getWeightInGrams($value, $currentUnit = null)
    {
        $value = floatval($value);
        $currentUnit = $currentUnit ? $currentUnit : $this->_scopeConfig->getValue(
            'temando/units/weight',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
        //from units as specified in configuration
        switch ($currentUnit) {
            case \Temando\Temando\Model\System\Config\Source\Unit\Weight::KILOGRAMS:
                return $value * 1000;
                break;

            case \Temando\Temando\Model\System\Config\Source\Unit\Weight::OUNCES:
                return $value * 28.3495;
                break;

            case \Temando\Temando\Model\System\Config\Source\Unit\Weight::POUNDS:
                return $value * 453.592;
                break;

            default:
                return $value;
                break;
        }
    }


    /**
     * Converts given distance from configured unit to centimetres
     *
     * @param float $value Distance to convert
     *
     * @return float Converted distance in centimetres
     */
    public function getDistanceInCentimetres($value, $currentUnit = null)
    {
        $value = floatval($value);
        $currentUnit = $currentUnit ? $currentUnit : $this->_scopeConfig->getValue(
            'temando/units/measure',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
        switch ($currentUnit) {
            case \Temando\Temando\Model\System\Config\Source\Unit\Measure::METRES:
                return $value * 100;
                break;

            case \Temando\Temando\Model\System\Config\Source\Unit\Measure::FEET:
                return $value * 30.48;
                break;

            case \Temando\Temando\Model\System\Config\Source\Unit\Measure::INCHES:
                return $value * 2.54;
                break;

            default:
                return $value;
                break;
        }
    }

    /**
     * Returns default currency code
     */
    public function getDefaultCurrencyCode()
    {
        return self::DEFAULT_CURRENCY_CODE;
    }

    /**
     * Returns default country id
     */
    public function getDefaultCountryId()
    {
        return self::DEFAULT_COUNTRY_ID;
    }

    /**
     * Packaging Logic Algorithm
     * Decide how best to package the cart items into boxes, consolidate items into boxes as per product attributes
     *
     * @param $Items and $currencyCode
     *
     * @return array
     */

    public function calculateBoxes($allItems, $currencyCode)
    {
        $articleCount = 0;
        $boxesRequired = array();
        $noPackingArticles = array();

        foreach ($allItems as $item) {
            if ($item->getParentItem() || $item->getIsVirtual()) {
                // do not add child products or virtual items
                continue;
            }

            if ($item->getProduct() && $item->getProduct()->isVirtual()) {
                // do not add virtual product
                continue;
            }

            if ($item->getFreeShipping()) {
                continue;
            }

            foreach ($this->getProductArticles($item) as $article) {
                if (!$article['needs_packaging']) {
                    //item does not need packaging
                    //each item is it's own package
                    $qty = $item->getQty() ? $item->getQty() : $item->getQtyOrdered();
                    for ($i=0; $i<=$qty; $i++) {
                        $qty = $item->getQty() ? $item->getQty() : $item->getQtyOrdered();
                        $articles = array();
                        for ($i = 1; $i <= $qty; $i++) {
                            //explicitly declare the single article in a box
                            $a = array();

                            $a['comment']   = $article['description'];
                            $a['qty']       = 1;
                            $a['value']     = $article['value'];
                            $a['length']    = $article['length'];
                            $a['width']     = $article['width'];
                            $a['height']    = $article['height'];
                            $a['weight']    = $article['weight'];
                            $a['fragile']   = $article['fragile'];
                            $a['dangerous'] = $article['dangerous'];
                            //populate articles
                            $articleItems = array(
                                'description'   => $article['description'],
                                'sku'           => $item->getSku(),
                                'goodsValue'    => $article['value'],
                                'goodsCurrency' => $currencyCode
                            );

                            $a['articles'][]  = $articleItems;

                            $boxesRequired[] = $a;
                        }
                    }
                } else {
                    //item needs packaging
                    $a['comment']   = $article['description'];
                    $a['qty']       = $item->getQty() ? $item->getQty() : $item->getQtyOrdered();
                    $a['value']     = $article['value'];
                    $a['length']    = $article['length'];
                    $a['width']     = $article['width'];
                    $a['height']    = $article['height'];
                    $a['weight']    = $article['weight'];
                    $a['fragile']   = $article['fragile'];
                    $a['dangerous'] = $article['dangerous'];
                    //populate articles
                    $articleItems = array(
                        'description'   => $article['description'],
                        'sku'           => $item->getSku(),
                        'goodsValue'    => $article['value'],
                        'goodsCurrency' => $currencyCode
                    );
                    $a['articles'][]= $articleItems;

                    $noPackingArticles[] = $a;
                }
                $articleCount++;
            }
        }

        //check if we can consolidate boxes

        if (count($boxesRequired)) {
            //at least one item needs a box
            $articleItems = array();
            if (count($noPackingArticles) > 0) {
                //stick the $noPackingArticles in the first box
                $firstBox           = $boxesRequired[0];
                $firstBoxComment    = $firstBox['comment'];
                $firstBoxValue      = $firstBox['value'];
                $firstBoxWeight     = $firstBox['weight'];
                $firstBoxArticles   = $firstBox['articles'][0];
                $articleNames       = array();

                $firstBoxLength     = $firstBox['length'];
                $firstBoxWidth      = $firstBox['width'];
                $firstBoxHeight     = $firstBox['height'];

                $sumLength  = $firstBox['length'];
                $sumWidth   = $firstBox['width'];
                $sumHeight= $firstBox['height'];

                $articleItems[]      = $firstBox['articles'][0];//array();
                foreach ($noPackingArticles as $noPackArticleIndex => $noPackArticle) {
                    //need qty for loop here
                    $qty = $noPackArticle['qty'];
                    for ($i=0; $i<$qty; $i++) {
                        $firstBoxComment    .= ', ' . $noPackArticle['comment'];
                        $firstBoxValue      = $firstBoxValue + $noPackArticle['value'];
                        $firstBoxWeight     = $firstBoxWeight + $noPackArticle['weight'];

                        //need to sum the length, width & height and use the minimum of those dimensions here TTMA-282
                        $sumLength  = $sumLength + $noPackArticle['length'];
                        $sumWidth   = $sumWidth + $noPackArticle['width'];
                        $sumHeight  = $sumHeight + $noPackArticle['height'];

                        //consolidate articles
                        $articleItems[] = $noPackArticle['articles'][0];
                    }

                    if ($noPackArticle['length'] > $firstBoxLength) {
                        $firstBoxLength = $noPackArticle['length'];
                    }
                    if ($noPackArticle['width'] > $firstBoxWidth) {
                        $firstBoxWidth = $noPackArticle['width'];
                    }
                    if ($noPackArticle['height'] > $firstBoxHeight) {
                        $firstBoxHeight = $noPackArticle['height'];
                    }

                    if ($noPackArticle['fragile']) {
                        $firstBox['fragile']    = 1;
                    }
                    if ($noPackArticle['dangerous']) {
                        $firstBox['dangerous']  = 1;
                    }
                }
                $firstBox['comment']    = $firstBoxComment;
                $firstBox['value']      = $firstBoxValue;
                $firstBox['weight']     = $firstBoxWeight;

                $firstBox['length']     = $firstBoxLength;
                $firstBox['width']      = $firstBoxWidth;
                $firstBox['height']     = $firstBoxHeight;
                //populate articles
                $firstBox['articles']   = $articleItems;

                //TTMA-282 use the minimum of the summed dimentions
                $dimensions = array(
                    'length'    => $sumLength,
                    'width'     => $sumWidth,
                    'height'    => $sumHeight
                );
                asort($dimensions);

                foreach ($dimensions as $dimension => $measurement) {
                    $firstBox[$dimension] = $measurement;
                    break;
                }

                $boxesRequired[0]       = $firstBox;
            }
        } else {
            $articleNames   = array();
            $articleItems   = array();
            //no items specifically need a box
            if (count($noPackingArticles)==1) {
                //single box required
                $noPackArticle = $noPackingArticles[0];
                $comment        = '';
                $value          = 0;
                $weight         = 0;

                $sumLength      = 0;
                $sumWidth       = 0;
                $sumHeight      = 0;

                $qty = $noPackArticle['qty'];
                for ($i=0; $i<$qty; $i++) {
                    $comment .= $noPackArticle['comment'];
                    if ($i<($qty-1)) {
                        $comment .= ', ';
                    }
                    $value = $value + $noPackArticle['value'];
                    $weight = $weight + $noPackArticle['weight'];

                    //need to sum distance measurements and use the minimum of the summed measurements
                    $sumLength = $sumLength + $noPackArticle['length'];
                    $sumWidth = $sumWidth + $noPackArticle['width'];
                    $sumHeight = $sumHeight + $noPackArticle['height'];

                    //consolidate articles
                    $articleItems[]   = $noPackArticle['articles'][0];
                }

                //TTMA-282 use the minimum of the summed dimentions
                $dimensions = array(
                    'length'    => $sumLength,
                    'width'     => $sumWidth,
                    'height'    => $sumHeight
                );
                asort($dimensions);

                foreach ($dimensions as $dimension => $measurement) {
                    $noPackArticle[$dimension] = $measurement;
                    break;
                }


                $noPackArticle['comment']   = $comment;
                $noPackArticle['value']     = $value;
                $noPackArticle['weight']    = $weight;
                //populate articles
                $noPackArticle['articles']  = $articleItems;
                $boxesRequired[0]           = $noPackArticle;
            } elseif (count($noPackingArticles) > 1) {
                //all articles in one package
                //the dimensions of the package are calculated as per TTMA-282
                $comment        = '';
                $value          = 0;
                $length         = 0;
                $width          = 0;
                $height         = 0;
                $weight         = 0;
                $fragile        = false;
                $dangerous      = false;

                $count = 0;

                $sumLength          = 0;
                $sumWidth           = 0;
                $sumHeight          = 0;

                foreach ($noPackingArticles as $noPackArticleIndex => $noPackArticle) {
                    $qty = $noPackArticle['qty'];
                    for ($i=0; $i<$qty; $i++) {
                        $comment .= $noPackArticle['comment'];
                        if ($i<($qty-1)) {
                            $comment .= ', ';
                        }
                        $value = $value + $noPackArticle['value'];
                        $weight = $weight + $noPackArticle['weight'];

                        //need to sum distance measurements and use the minimum of the summed measurements
                        $sumLength = $sumLength + $noPackArticle['length'];
                        $sumWidth = $sumWidth + $noPackArticle['width'];
                        $sumHeight = $sumHeight + $noPackArticle['height'];

                        //consolidate articles
                        $articleItems[]   = $noPackArticle['articles'];
                    }
                    if ($count < count($noPackingArticles)-1) {
                        $comment .= ', ';
                    }

                    if ($noPackArticle['length'] > $length) {
                        $length = $noPackArticle['length'];
                    }
                    if ($noPackArticle['width'] > $width) {
                        $width = $noPackArticle['width'];
                    }
                    if ($noPackArticle['height'] > $height) {
                        $height = $noPackArticle['height'];
                    }
                    if ($noPackArticle['fragile']) {
                        $fragile = true;
                    }
                    if ($noPackArticle['dangerous']) {
                        $dangerous = true;
                    }
                    $count++;
                }

                //TTMA-282 use the minimum of the summed dimentions
                $dimensions = array(
                    'length'    => $sumLength,
                    'width'     => $sumWidth,
                    'height'    => $sumHeight
                );
                asort($dimensions);

                foreach ($dimensions as $dimension => $measurement) {
                    ${$dimension} = $measurement;
                    break;
                }
                $firstBox               = array();
                $firstBox['comment']    = $comment;
                $firstBox['qty']        = 1;
                $firstBox['value']      = $value;
                $firstBox['length']     = $length;
                $firstBox['width']      = $width;
                $firstBox['height']     = $height;
                $firstBox['weight']     = $weight;
                $firstBox['fragile']    = $fragile;
                $firstBox['dangerous']  = $dangerous;
                //populate articles
                $firstBox['articles']   = $articleItems;

                $boxesRequired[] = $firstBox;
            }
        }
        return $boxesRequired;
    }

    /**
     * Get origins inventory
     *
     * @param array @origins
     * @param array @products
     *
     * @return array
     */

    public function getOriginsInventory($origins, $products = null)
    {
        foreach ($origins as $origin) {
            if (!isset($origin['erp_id'])) {
                return null;
            }
            if ($origin['erp_id'] == '') {
                $this->_logger->debug(
                    date('Y/m/d H:i:s') . ' ' .
                    get_class($this) .
                    ' this origin (' . $origin['name'] . ') does not have any ERP ID'
                );
                return null;
            }
        }
        $erpIds = array();
        foreach ($origins as $origin) {
            $erpIds[] = $origin['erp_id'];
        }
        //gives table name with prefix
        $connection = $this->_resourceConnection->getConnection();
        $tableTemandoOriginInventory = $this->_resourceConnection->getTableName('temando_origin_inventory');

        $select = "SELECT * FROM " . $tableTemandoOriginInventory . " WHERE erp_id IN ("
            . implode(",", $erpIds).")";

        if ($products != null) {
            // get all products sku in the cart
            $skus = array_keys($products);
            // prevent product SKU contains single and double quote
            foreach ($skus as $index => $sku) {
                $skus[$index] = addslashes($sku);
            }
            $select .=  "AND sku IN ('" . implode("','", $skus)."')";
        }
        $rows = $connection->fetchAll($select);

        return $rows;
    }

    /**
     * Add other key (stock_level_message) and value in origins
     *
     * @param array $origins
     * @param array $products
     *
     * @return string
     */
    public function getOriginsStockLevelMessage($origins, $products)
    {
        $stocklevel = '';
        $lowstock = $this->_scopeConfig->getValue(
            'storepickup/general/stock_level',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );

        $originInventory = $this->getOriginsInventory($origins, $products);

        $diff = array_diff(array_column($origins, 'erp_id'), array_column($originInventory, 'erp_id'));
        // If a product units is empty in temando_origin_inventory, return OUT_OF_STOCK_MESSAGE
        if (count($diff) > 0) {
            foreach ($diff as $index => $origin) {
                $origins[$index]['stock_level_message'] = Origin::OUT_OF_STOCK_MESSAGE;
            }
        }

        foreach ($origins as $index => $origin) {
            // if stock_level_message key is not exsit, then insert into origins
            if (!array_key_exists('stock_level_message', $origin)) {
                $origins[$index]['stock_level_message'] ='';
            }

            foreach ($originInventory as $result) {
                if ($origin['erp_id'] == $result['erp_id']) {
                    if (!array_key_exists($result['sku'], $products) || $result['units'] <= 0
                        || $result['units'] < $products[$result['sku']]) {
                        $origins[$index]['stock_level_message'] = Origin::OUT_OF_STOCK_MESSAGE;
                        break;
                    } elseif ($result['units'] <= $lowstock) {
                        $origins[$index]['stock_level_message'] = Origin::LOW_STOCK_MESSAGE;
                    } else {
                        $origins[$index]['stock_level_message'] = '';
                    }
                }
            }
        }
        return $origins;
    }

    /*
     * Return next auto increment number in temando_origin table
     */
    public function getAutoIncrementNumber($tableName)
    {
        $host = $this->_config->get('db')['connection']['default']['host'];
        $dbname = $this->_config->get('db')['connection']['default']['dbname'];
        $username = $this->_config->get('db')['connection']['default']['username'];
        $password = $this->_config->get('db')['connection']['default']['password'];
        $active = $this->_config->get('db')['connection']['default']['active'];
        if ($active) {
            $con = mysqli_connect($host, $username, $password, $dbname);
            $query = "SHOW TABLE STATUS LIKE '" . $tableName . "'";
            $result = mysqli_query($con, $query);
            $row = mysqli_fetch_assoc($result);
        }

        return $row['Auto_increment'];
    }

    /**
     * Check Shipment Action Permission.
     *
     * @param $shipment
     * @param $status
     *
     * @return bool
     */
    public function checkShipmentActionPermission($shipment, $status)
    {

        $user = $this->_authSession->getUser();

        if (!$this->_isAllowedAction('Temando_Temando::temando_shipments_process')) {
            return false;
        }

        if ($shipment->getStatus() == \Temando\Temando\Model\System\Config\Source\Shipment\Status::BACK_ORDER) {
            //Back Order Status : Pending and Cancelled
            if ($status == \Temando\Temando\Model\System\Config\Source\Shipment\Status::PENDING) {
                return true;
            }
            if ($status == \Temando\Temando\Model\System\Config\Source\Shipment\Status::CANCELLED
                &&
                $this->_isAllowedAction('Temando_Temando::temando_shipments_cancel')
            ) {
                return true;
            }
        } elseif ($shipment->getStatus() == \Temando\Temando\Model\System\Config\Source\Shipment\Status::PENDING) {
            //Pending Shipment : Pickslip and Cancelled
            if ($status == \Temando\Temando\Model\System\Config\Source\Shipment\Status::PICKING) {
                return true;
            }
            if ($status == \Temando\Temando\Model\System\Config\Source\Shipment\Status::CANCELLED
                &&
                $this->_isAllowedAction('Temando_Temando::temando_shipments_cancel')
            ) {
                return true;
            }
            if ($status == \Temando\Temando\Model\System\Config\Source\Shipment\Status::BOOKED_EXTERNALLY
                &&
                $this->_isAllowedAction('Temando_Temando::temando_shipments_book_externally')
            ) {
                return true;
            }
        } elseif ($shipment->getStatus() == \Temando\Temando\Model\System\Config\Source\Shipment\Status::PICKING) {
            //Picking Shipment : Packed and Cancelled
            if ($status == \Temando\Temando\Model\System\Config\Source\Shipment\Status::PACKED) {
                return true;
            }
            if ($status == \Temando\Temando\Model\System\Config\Source\Shipment\Status::CANCELLED
                &&
                $this->_isAllowedAction('Temando_Temando::temando_shipments_cancel')
            ) {
                return true;
            }
            if ($status == \Temando\Temando\Model\System\Config\Source\Shipment\Status::BOOKED_EXTERNALLY
                &&
                $this->_isAllowedAction('Temando_Temando::temando_shipments_book_externally')
            ) {
                return true;
            }
        } elseif ($shipment->getStatus() == \Temando\Temando\Model\System\Config\Source\Shipment\Status::PACKED) {
            //Packed Status : Book buttons next to quote(s), Cancelled
            if ($status == \Temando\Temando\Model\System\Config\Source\Shipment\Status::CANCELLED
                &&
                $this->_isAllowedAction('Temando_Temando::temando_shipments_cancel')
            ) {
                return true;
            }
            if ($status == \Temando\Temando\Model\System\Config\Source\Shipment\Status::BOOKED) {
                return true;
            }
            if ($status == \Temando\Temando\Model\System\Config\Source\Shipment\Status::BOOKED_EXTERNALLY
                &&
                $this->_isAllowedAction('Temando_Temando::temando_shipments_book_externally')
            ) {
                return true;
            }
        } elseif ($shipment->getStatus() == \Temando\Temando\Model\System\Config\Source\Shipment\Status::BOOKED) {
            //Booked Status : Complete & Cancelled
            if ($status == \Temando\Temando\Model\System\Config\Source\Shipment\Status::COMPLETE) {
                return true;
            }
            if ($status == \Temando\Temando\Model\System\Config\Source\Shipment\Status::CANCELLED
                &&
                $this->_isAllowedAction('Temando_Temando::temando_shipments_cancel')
            ) {
                return true;
            }
        } elseif ($shipment->getStatus()
            == \Temando\Temando\Model\System\Config\Source\Shipment\Status::BOOKED_EXTERNALLY
        ) {
            if ($status == \Temando\Temando\Model\System\Config\Source\Shipment\Status::COMPLETE) {
                return true;
            }
            if ($status == \Temando\Temando\Model\System\Config\Source\Shipment\Status::CANCELLED
                &&
                $this->_isAllowedAction('Temando_Temando::temando_shipments_cancel')
            ) {
                return true;
            }
        } elseif ($shipment->getStatus() == \Temando\Temando\Model\System\Config\Source\Shipment\Status::COMPLETE) {
        } elseif ($shipment->getStatus() == \Temando\Temando\Model\System\Config\Source\Shipment\Status::CANCELLED) {
        }
        return false;
    }

    /**
     * Check pickup Action Permission.
     *
     * @param $pickup
     * @param $status
     *
     * @return bool
     */
    public function checkPickupActionPermission($pickup, $status)
    {
        $user = $this->_authSession->getUser();

        if (!$this->_isAllowedAction('Temando_Temando::temando_pickups_process')) {
            return false;
        }

        if ($pickup->getStatus() == \Temando\Temando\Model\System\Config\Source\Pickup\Status::BACK_ORDER) {
            //Back Order Status : Pending and Cancelled
            if ($status == \Temando\Temando\Model\System\Config\Source\Pickup\Status::PENDING) {
                return true;
            }
            if ($status == \Temando\Temando\Model\System\Config\Source\Pickup\Status::CANCELLED
                &&
                $this->_isAllowedAction('Temando_Temando::temando_pickups_cancel')
            ) {
                return true;
            }
        } elseif ($pickup->getStatus() == \Temando\Temando\Model\System\Config\Source\Pickup\Status::PENDING) {
            //Pending Status : Pickslip and Cancelled
            if ($status == \Temando\Temando\Model\System\Config\Source\Pickup\Status::PICKING) {
                return true;
            }
            if ($status == \Temando\Temando\Model\System\Config\Source\Pickup\Status::CANCELLED
                &&
                $this->_isAllowedAction('Temando_Temando::temando_pickups_cancel')
            ) {
                return true;
            }
        } elseif ($pickup->getStatus() == \Temando\Temando\Model\System\Config\Source\Pickup\Status::PICKING) {
            //Picking Status : Packed and Cancelled
            if ($status == \Temando\Temando\Model\System\Config\Source\Pickup\Status::PACKED) {
                return true;
            }
            if ($status == \Temando\Temando\Model\System\Config\Source\Pickup\Status::CANCELLED
                &&
                $this->_isAllowedAction('Temando_Temando::temando_pickups_cancel')
            ) {
                return true;
            }
        } elseif ($pickup->getStatus() == \Temando\Temando\Model\System\Config\Source\Pickup\Status::PACKED) {
            //Packed Status : Ready for collection, Cancelled
            if ($status == \Temando\Temando\Model\System\Config\Source\Pickup\Status::CANCELLED
                &&
                $this->_isAllowedAction('Temando_Temando::temando_pickups_cancel')
            ) {
                return true;
            }
            if ($status == \Temando\Temando\Model\System\Config\Source\Pickup\Status::AWAITING) {
                return true;
            }
        } elseif ($pickup->getStatus() == \Temando\Temando\Model\System\Config\Source\Pickup\Status::AWAITING) {
            //Awaiting Status : Collected & Cancelled
            if ($status == \Temando\Temando\Model\System\Config\Source\Pickup\Status::COLLECTED) {
                return true;
            }
            if ($status == \Temando\Temando\Model\System\Config\Source\Pickup\Status::CANCELLED
                &&
                $this->_isAllowedAction('Temando_Temando::temando_pickups_cancel')
            ) {
                return true;
            }
        } elseif ($pickup->getStatus() == \Temando\Temando\Model\System\Config\Source\Pickup\Status::CANCELLED) {
        }
        return false;
    }

    /**
     * Order Contains Exclusively.
     *
     * @param $order
     *
     * @return bool
     */
    public function orderContainsExclusively($order, $stockAvailabilityCode)
    {
        foreach ($this->getOrderSkus($order) as $sku => $details) {
            if ($details['stock_availability_code'] != $stockAvailabilityCode) {
                return false;
            }
        }
        return true;
    }

    /**
     * Get Order Sku's.
     *
     * @param $order
     *
     * @return array
     */
    public function getOrderSkus($order)
    {
        $skus = array();
        foreach ($order->getAllVisibleItems() as $item) {
            $_product = $this->_productFactory->create();
            $_product = $_product->loadByAttribute('sku', $item->getSku());
            $stockAvailabilityCode = $_product->getAttributeText('stock_availability_code');
            $skus[$item->getSku()]['qty'] = $item->getQtyOrdered();
            $skus[$item->getSku()]['stock_availability_code'] = $stockAvailabilityCode;
        }
        return $skus;
    }

    /**
     * Get Back Order Sku's.
     *
     * @param $order
     * @param $origin
     *
     * @return array
     */

    public function getBackOrderSkus($order, $origin)
    {
        $skus = $this->getOrderSkus($order);
        $backOrderSkus = array();
        foreach ($skus as $sku => $details) {
            $qty = $details['qty'];
            if ($details['stock_availability_code'] == Origin::STOCK_ON_DEMAND) {
                if (!$origin->hasStock(array($sku => $qty))) {
                    $backOrderSkus[$sku] = $qty;
                }
            }
        }
        return $backOrderSkus;
    }

    /**
     * Get Pickslip Directory.
     *
     * @param bool $mkdir
     *
     * @return string
     */
    public function getPickslipDir($mkdir = false)
    {
        $varFolder = $this->_directoryList->getPath(
            \Magento\Framework\App\Filesystem\DirectoryList::VAR_DIR
        );
        if ($mkdir && !file_exists($varFolder)) {
            mkdir($varFolder);
        }

        $pdfDir = $varFolder . DIRECTORY_SEPARATOR . 'temando';
        if ($mkdir && !file_exists($pdfDir)) {
            mkdir($pdfDir);
        }

        $pdfDir .= DIRECTORY_SEPARATOR . 'pickslip';
        if ($mkdir && !file_exists($pdfDir)) {
            mkdir($pdfDir);
        }
        return $pdfDir;
    }

    /**
     * Prepare Download Response.
     *
     * @param $name
     * @param $content
     * @param bool $decode
     */
    public function _prepareDownloadResponse($name, $content, $decode = true)
    {
        $filePathComponents = explode('/', $name);
        $fileName = $filePathComponents[count($filePathComponents)-1];
        if ($decode) {
            $content = base64_decode($content);
        }
        header('Content-Type: application/pdf');
        header('Content-Length: '.strlen($content));
        header('Cache-Control', 'must-revalidate, post-check=0, pre-check=0');
        header('Last-Modified', date('r'));
        header('Content-Disposition: attachment; filename="' . $fileName . '"');
        echo $content;
        flush();
        exit;
    }

    /**
     * Get List of Stores for Pickup
     *
     * @return array
     */
    public function getListStore()
    {
        $collection = $this->_originCollectionFactory->create();
        $collection->addFieldToFilter('is_active', '1')
            ->addFieldToFilter('allow_store_collection', '1')
            ->addFieldToSelect([
                'origin_id',
                'longitude',
                'latitude',
                'street',
                'city',
                'region',
                'postcode',
                'contact_phone_1',
                'name'
            ]);
        return $collection->getData();
    }

    /**
     * Get List of Stores for Pickup in JSON.
     *
     * @return json
     */
    public function getListStoreJson()
    {
        return \Zend_Json::encode($this->getListStore());
    }

    public function generateTimes($mintime, $maxtime, $sys_min_time = '0:0')
    {
        //$sys_min_time = strtotime(date('H:i:s',$sys_min_time));
        $interval_time = $this->_scopeConfig->getValue(
            'carriers/storepickup/time_interval',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
        $timeHI = explode(':', $mintime);
        $mintime= mktime($timeHI[0], $timeHI[1], 0, '01', '01', '2000');
        $timeHI = explode(':', $maxtime);
        $maxtime= mktime($timeHI[0], $timeHI[1], 0, '01', '01', '2000');
        $timeHI = explode(':', $sys_min_time);
        $sys_min_time= mktime($timeHI[0], $timeHI[1], 0, '01', '01', '2000');
        $listTime = "";

        $i = $mintime;

        while ($i <= $maxtime) {
            if ($i >= $sys_min_time) {
                $time = date('H:i', $i);
                $listTime .= '<option value="' . $time . '">' . $time . '</option>';
                //$listTime[$time] = $time;
            }

            $i += $interval_time*60;
        }

        return $listTime;
    }

    /**
     * Is Admin.
     *
     * @return bool
     */
    public function isAdmin($appState)
    {
        if ($appState->getAreaCode() == \Magento\Backend\App\Area\FrontNameResolver::AREA_CODE) {
            return true;
        }
        return false;
    }

    /**
     * Get Stock Levels
     *
     * @param string $sku
     * @param string $postcode
     * @param string $country
     *
     * @return array
     */
    public function _getStockLevelsByPostcode($sku, $postcode, $country = null)
    {
        $connection = $this->_resourceConnection->getConnection();
        $country =  $country ? $country : $this->_scopeConfig->getValue(
            'general/country/default',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );

        //get nearest warehouse
        $resourceOriginCollection = $this->_resourceOriginCollectionFactory->create();
        $origin = $resourceOriginCollection->getOriginByPostcode($postcode, $country);
        $uniqueOriginKeys = array_unique(
            array_values(
                explode(',', $origin->getId().','.$origin->getSupportingOrigins())
            )
        );

        $stock = array();
        foreach ($uniqueOriginKeys as $index => $originId) {
            $origin = $this->_objectManager->create('Temando\Temando\Model\Origin');
            $origin = $origin->load($originId);
            $stock[$origin->getErpId()]['name'] = $origin->getName();
        }

        //populate the units values for the array
        $selectQuery = "SELECT * FROM temando_origin_inventory " .
            "WHERE sku='".$sku."' AND erp_id IN ('".implode("','", array_keys($stock))."')";
        $selectResult = $connection->query($selectQuery);

        $lowstock = $this->_scopeConfig->getValue(
            'storepickup/general/stock_level',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
        // determine stock level message based on item units
        foreach ($selectResult->fetchAll() as $result) {
            $stock[$result['erp_id']]['units'] = $result['units'];
            if ($result['units'] <= 0) {
                $stock[$result['erp_id']]['message'] = Origin::OUT_OF_STOCK_MESSAGE_PRODUCT;
            } elseif ($result['units'] > 0 && $result['units'] <= $lowstock) {
                $stock[$result['erp_id']]['message'] = Origin::LOW_STOCK_MESSAGE_PRODUCT;
            } elseif ($result['units'] > $lowstock) {
                $stock[$result['erp_id']]['message'] = Origin::IN_STOCK_MESSAGE_PRODUCT;
            }
        }

        //update array elements where the supporting origin does not have a corresponding row in table
        foreach ($stock as $erpId => $stockArray) {
            if (!isset($stockArray['units'])) {
                $stock[$erpId]['units'] = 0;
                $stock[$erpId]['message'] = Origin::OUT_OF_STOCK_MESSAGE_PRODUCT;
            }
        }
        return $stock;
    }
}
