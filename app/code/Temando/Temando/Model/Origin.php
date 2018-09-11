<?php

namespace Temando\Temando\Model;

use Temando\Temando\Api\Data\OriginInterface;
use Magento\Framework\DataObject\IdentityInterface;
use Magento\Framework\Exception\LocalizedException as CoreException;
use Magestore\Storepickup\Model\Schedule\Option\WeekdayStatus;

class Origin extends \Magento\Framework\Model\AbstractModel implements OriginInterface, IdentityInterface
{

    /**
     * Number of seconds in a day.
     */
    const TIME_DAY = 86400;
    
    /**
     * List of countries using non-numerical postal codes
     *
     * @var array
     */
    protected $_nonNumericPcodes = array('GB', 'CA');

    /**
     * Origin's Statuses
     */
    const STATUS_ENABLED = 1;

    const STATUS_DISABLED = 0;

    /**
     * Method id getter.
     */
    const METHOD_GET_HOLIDAY_ID = 2;

    const METHOD_GET_SPECIALDAY_ID = 3;

    /* When an item is out of stock
     * the customer explicitly wants to say that the item is avaialble to order
     */
    const OUT_OF_STOCK_MESSAGE_PRODUCT = 'Available to order';
    const OUT_OF_STOCK_MESSAGE = 'Available for collection in 7 days';

    /**
    * If item quantity <= low stock count, then it is marked up as low stock
    */
    const LOW_STOCK_MESSAGE_PRODUCT = 'Low Stock - Please call to confirm';
    const LOW_STOCK_MESSAGE = 'Low Stock';

    /**
     * If item quantity > low stock count, then it is marked up as in stock
     */
    const IN_STOCK_MESSAGE_PRODUCT = 'In Stock';

    /**
    * Define shipping additional message
    */
    const ON_DEMAND_ERROR_MESSAGE = 'At least one of your items is out of stock'.
    ' and will be despatched within 3-5 Business days';
    const DANGEROUS_ERROR_MESSAGE = 'One or more of your items in your cart is not available for delivery';

    /**
    * Define stock_availability_code
    */
    const STOCK_ON_DEMAND = 'OD';
    const STOCK_ON_CATALOG = 'OC';
    const STOCK_ONLINE = 'OL';
    const STOCK_SPECIAL_PRODUCT = 'SP';

    /**
     * Mapping method builder.
     *
     * @var array
     */
    protected $_methodGetters = [
        self::METHOD_GET_HOLIDAY_ID    => 'getHolidayIds',
        self::METHOD_GET_SPECIALDAY_ID => 'getSpecialdayIds'
    ];
    
    /**
     * Holiday Collection Factory.
     *
     * @var \Magestore\Storepickup\Model\ResourceModel\Holiday\CollectionFactory
     */
    protected $_holidayCollectionFactory;

    /**
     * Special Dat Collection Factory.
     *
     * @var \Magestore\Storepickup\Model\ResourceModel\Specialday\CollectionFactory
     */
    protected $_specialdayCollectionFactory;
    
    
    /**
     * System Config.
     *
     * @var \Magestore\Storepickup\Model\SystemConfig
     */
    protected $_systemConfig;
    
    /**
     * Origin cache tag
     */
    const CACHE_TAG = 'temando_origin';

    /**
     * Temando Origin.
     *
     * @var string
     */
    protected $_cacheTag = 'temando_origin';
    protected $_client;
    protected $_scopeConfig;
    protected $_jsonDecoder;
    protected $_schedule;

    /**
     * Prefix of model events names
     *
     * @var string
     */
    protected $_eventPrefix = 'temando_origin';

    /**
     * Logger.
     *
     * @var \Psr\Log\LoggerInterface
     */
    protected $_logger;

    /**
     * Resource Connection.
     *
     * @var \Magento\Framework\App\ResourceConnection
     */
    protected $_resourceConnection;

    /**
     * Origin constructor.
     *
     * @param Api\Client $client
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magestore\Storepickup\Model\Schedule $schedule
     * @param \Magento\Framework\Json\Decoder $decoder
     * @param \Magento\Framework\App\ResourceConnection $resourceConnection
     * @param \Magestore\Storepickup\Model\ResourceModel\Holiday\CollectionFactory $holidayCollectionFactory
     * @param \Magestore\Storepickup\Model\SystemConfig $systemConfig
     * @param \Magestore\Storepickup\Model\ResourceModel\Specialday\CollectionFactory $specialdayCollectionFactory
     */
    public function __construct(
        \Temando\Temando\Model\Api\Client $client,
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magestore\Storepickup\Model\Schedule $schedule,
        \Magento\Framework\Json\Decoder $decoder,
        \Magento\Framework\App\ResourceConnection $resourceConnection,
        \Magestore\Storepickup\Model\ResourceModel\Holiday\CollectionFactory $holidayCollectionFactory,
        \Magestore\Storepickup\Model\SystemConfig $systemConfig,
        \Magestore\Storepickup\Model\ResourceModel\Specialday\CollectionFactory $specialdayCollectionFactory
    ) {
        $this->_logger = $context->getLogger();
        $this->_client = $client;
        $this->_scopeConfig = $scopeConfig;
        $this->_jsonDecoder = $decoder;
        $this->_schedule = $schedule;
        $this->_resourceConnection = $resourceConnection;
        $this->_systemConfig = $systemConfig;
        $this->_holidayCollectionFactory = $holidayCollectionFactory;
        $this->_specialdayCollectionFactory = $specialdayCollectionFactory;
        parent::__construct($context, $registry);
    }


    /**
     * Initialize resource model.
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Temando\Temando\Model\ResourceModel\Origin');
    }

    /**
     * Prepare origin's statuses.
     * Available event temando_origin_get_available_statuses to customize statuses.
     *
     * @return array
     */
    public function getAvailableStatuses()
    {
        return [self::STATUS_ENABLED => __('Enabled'), self::STATUS_DISABLED => __('Disabled')];
    }

    /**
     * Return unique ID(s) for each object in system.
     *
     * @return array
     */
    public function getIdentities()
    {
        return [self::CACHE_TAG . '_' . $this->getId()];
    }

    /**
     * Get ID.
     *
     * @return int|null
     */
    public function getId()
    {
        return $this->getData(self::ORIGIN_ID);
    }

    /**
     * Get name.
     *
     * @return string|null
     */
    public function getName()
    {
        return $this->getData(self::NAME);
    }

    /**
     * Is active.
     *
     * @return bool|null
     */
    public function isActive()
    {
        return (bool) $this->getData(self::IS_ACTIVE);
    }

    /**
     * Get company name.
     *
     * @return string|null
     */
    public function getCompanyName()
    {
        return $this->getData(self::COMPANY_NAME);
    }

    /**
     * Get ERP ID.
     *
     * @return string|null
     */
    public function getErpId()
    {
        return $this->getData(self::ERP_ID);
    }

    /**
     * Get street.
     *
     * @return string|null
     */
    public function getStreet()
    {
        return $this->getData(self::STREET);
    }

    /**
     * Get city.
     *
     * @return string|null
     */
    public function getCity()
    {
        return $this->getData(self::CITY);
    }

    /**
     * Get region.
     *
     * @return string|null
     */
    public function getRegion()
    {
        return $this->getData(self::REGION);
    }

    /**
     * Get postcode.
     *
     * @return string|null
     */
    public function getPostcode()
    {
        return $this->getData(self::POSTCODE);
    }

    /**
     * Get country.
     *
     * @return string|null
     */
    public function getCountry()
    {
        return $this->getData(self::COUNTRY);
    }

    /**
     * Get contact name.
     *
     * @return string|null
     */
    public function getContactName()
    {
        return $this->getData(self::CONTACT_NAME);
    }

    /**
     * Get contact email.
     *
     * @return string|null
     */
    public function getContactEmail()
    {
        return $this->getData(self::CONTACT_EMAIL);
    }

    /**
     * Get contact phone 1.
     *
     * @return string|null
     */
    public function getContactPhone1()
    {
        return $this->getData(self::CONTACT_PHONE_1);
    }

    /**
     * Get contact phone 2.
     *
     * @return string|null
     */
    public function getContactPhone2()
    {
        return $this->getData(self::CONTACT_PHONE_2);
    }

    /**
     * Get contact fax.
     *
     * @return string|null
     */
    public function getContactFax()
    {
        return $this->getData(self::CONTACT_FAX);
    }

    /**
     * Get Store Ids.
     *
     * @return array|null
     */
    public function getStoreIds()
    {
        return $this->getData(self::STORE_IDS);
    }

    /**
     * Check store is open in a day.
     *
     * @param $day
     *
     * @return bool
     */
    public function isOpenday($day)
    {
        return $this->getScheduleId()
        && $this->isEnabled()
        && $this->getSchedule()->getData($day . '_status') == WeekdayStatus::WEEKDAY_STATUS_OPEN
        && $this->getSchedule()->getData($day . '_open') < $this->getSchedule()->getData($day . '_close');
    }
    
    public function getSchedule()
    {
        return $this->_schedule->load($this->getScheduleId());
    }
    /**
     * Has Break Time
     *
     * @param $day
     *
     * @return bool
     */
    public function hasBreakTime($day)
    {
        return $this->isOpenday($day)
        && $this->getData($day . '_open') < $this->getData($day . '_open_break')
        && $this->getData($day . '_open_break') < $this->getData($day . '_close_break')
        && $this->getData($day . '_close_break') < $this->getData($day . '_close');
    }
    
    /**
     * Check store is enabled.
     *
     * @return bool
     */
    public function isEnabled()
    {
        return $this->getIsActive() == \Magestore\Storepickup\Model\Status::STATUS_ENABLED ? true : false;
    }
    
    /**
     * Get User Ids.
     *
     * @return array|null
     */
    public function getUserIds()
    {
        return $this->getData(self::USER_IDS);
    }

    /**
     * Get Zone Ids.
     *
     * @return array|null
     */
    public function getZoneId()
    {
        return $this->getData(self::ZONE_ID);
    }

    /**
     * Get contact fax.
     *
     * @return string|null
     */
    public function getAccountMode()
    {
        return $this->getData(self::ACCOUNT_MODE);
    }

    /**
     * Get account sandbox.
     *
     * @return string|null
     */
    public function getAccountSandbox()
    {
        return $this->getData(self::ACCOUNT_SANDBOX);
    }

    /**
     * Get account username.
     *
     * @return string|null
     */
    public function getAccountUsername()
    {
        return $this->getData(self::ACCOUNT_USERNAME);
    }

    /**
     * Get account password.
     *
     * @return string|null
     */
    public function getAccountPassword()
    {
        return $this->getData(self::ACCOUNT_PASSWORD);
    }

    /**
     * Get account client id.
     *
     * @return string|null
     */
    public function getAccountClientId()
    {
        return $this->getData(self::ACCOUNT_CLIENT_ID);
    }

    /**
     * Set ID.
     *
     * @param int $id
     *
     * @return \Temando\Temando\Api\Data\OriginInterface
     */
    public function setId($id)
    {
        return $this->setData(self::ORIGIN_ID, $id);
    }

    /**
     * Set name.
     *
     * @param string $name
     *
     * @return \Temando\Temando\Api\Data\OriginInterface
     */
    public function setName($name)
    {
        return $this->setData(self::NAME, $name);
    }

    /**
     * Set is active.
     *
     * @param int|bool $is_active
     *
     * @return \Temando\Temando\Api\Data\OriginInterface
     */
    public function setIsActive($is_active)
    {
        return $this->setData(self::IS_ACTIVE, $is_active);
    }

    /**
     * Set company name.
     *
     * @param string $company_name
     *
     * @return \Temando\Temando\Api\Data\OriginInterface
     */
    public function setCompanyName($company_name)
    {
        return $this->setData(self::COMPANY_NAME, $company_name);
    }

    /**
     * Set erp id.
     *
     * @param string $erp_id
     *
     * @return $this
     */
    public function setErpId($erp_id)
    {
        return $this->setData(self::ERP_ID, $erp_id);
    }

    /**
     * Set street.
     *
     * @param string $street
     *
     * @return $this
     */
    public function setStreet($street)
    {
        return $this->setData(self::STREET, $street);
    }

    /**
     * Set city.
     *
     * @param string $city
     *
     * @return $this
     */
    public function setCity($city)
    {
        return $this->setData(self::CITY, $city);
    }

    /**
     * Set region.
     *
     * @param string $region
     *
     * @return $this
     */
    public function setRegion($region)
    {
        return $this->setData(self::REGION, $region);
    }

    /**
     * Set postcode.
     *
     * @param string $postcode
     *
     * @return $this
     */
    public function setPostcode($postcode)
    {
        return $this->setData(self::POSTCODE, $postcode);
    }

    /**
     * Set country.
     *
     * @param string $country
     *
     * @return $this
     */
    public function setCountry($country)
    {
        return $this->setData(self::COUNTRY, $country);
    }

    /**
     * Set contact name.
     *
     * @param string $contact_name
     *
     * @return $this
     */
    public function setContactName($contact_name)
    {
        return $this->setData(self::CONTACT_NAME, $contact_name);
    }

    /**
     * Set contact email.
     *
     * @param string $contact_email
     *
     * @return $this
     */
    public function setContactEmail($contact_email)
    {
        return $this->setData(self::CONTACT_EMAIL, $contact_email);
    }

    /**
     * Set contact phone 1.
     *
     * @param string $contact_phone_1
     *
     * @return $this
     */
    public function setContactPhone1($contact__phone_1)
    {
        return $this->setData(self::CONTACT_PHONE_1, $contact__phone_1);
    }

    /**
     * Set contact phone 2.
     *
     * @param string $contact_phone_2
     *
     * @return $this
     */
    public function setContactPhone2($contact_phone_2)
    {
        return $this->setData(self::CONTACT_PHONE_2, $contact_phone_2);
    }

    /**
     * Set contact fax.
     *
     * @param string $contact_fax
     *
     * @return $this
     */
    public function setContactFax($contact_fax)
    {
        return $this->setData(self::CONTACT_FAX, $contact_fax);
    }

    /**
     * Set store ids.
     *
     * @param array $store_ids
     *
     * @return $this
     */
    public function setStoreIds($store_ids)
    {
        return $this->setData(self::STORE_IDS, $store_ids);
    }

    /**
     * Set zone ids.
     *
     * @param array $zone_ids
     *
     * @return $this
     */
    public function setZoneId($zone_id)
    {
        return $this->setData(self::ZONE_ID, $zone_id);
    }

    /**
     * Set user ids.
     *
     * @param array $user_ids
     *
     * @return $this
     */
    public function setUserIds($user_ids)
    {
        return $this->setData(self::USER_IDS, $user_ids);
    }

    /**
     * Set account mode.
     *
     * @param string $account_mode
     *
     * @return $this
     */
    public function setAccountMode($account_mode)
    {
        return $this->setData(self::ACCOUNT_MODE, $account_mode);
    }

    /**
     * Set account sandbox.
     *
     * @param boolean $account_sandbox
     *
     * @return $this
     */
    public function setAccountSandbox($account_sandbox)
    {
        return $this->setData(self::ACCOUNT_SANDBOX, $account_sandbox);
    }

    /**
     * Set user ids.
     *
     * @param string $account_username
     *
     * @return $this
     */
    public function setAccountUsername($account_username)
    {
        return $this->setData(self::ACCOUNT_USERNAME, $account_username);
    }

    /**
     * Set account password.
     *
     * @param string $account_password
     *
     * @return $this
     */
    public function setAccountPassword($account_password)
    {
        return $this->setData(self::ACCOUNT_PASSWORD, $account_password);
    }

    /**
     * Set account client id.
     *
     * @param string $account_client_id
     *
     * @return $this
     */
    public function setAccountClientId($account_client_id)
    {
        return $this->setData(self::ACCOUNT_CLIENT_ID, $account_client_id);
    }
    
    /**
     * Try to sync the warehouse with the Temando API
     * If the location doesn't exist it will be created
     * otherwise it will be updated
     */
    public function syncWarehouse()
    {
        $request['location'] = $this->toCreateLocationRequestArray();
        try {
            $api = $this->_client->connect($this->getTemandoProfile());
            $result = $api
                ->getLocations(
                    array(
                        'type' => 'Origin',
                        'clientId' => $this->getAccountClientid(),
                        'description' => $this->getName()
                    )
                );
            if ($result && isset($result->locations->location)) {
                //location exists = update
                $result = $api->updateLocation($request);
            } else {
                $result = $api->createLocation($request);
            }
        } catch (Exception $e) {
            $this->_logger->critical($e);
        }
        return $result;
    }

    /**
     * Returns Temando Account details
     *
     * @return array
     */
    public function getTemandoProfile()
    {
        if ($this->getAccountMode()) {
            return array(
                'sandbox'   => $this->getAccountSandbox(),
                'clientid'  => $this->getAccountClientid(),
                'username'  => $this->getAccountUsername(),
                'password'  => $this->getAccountPassword(),
                'country'   => $this->getCountry(),
            );
        } else {
            return array(
                'sandbox'   => $this->_scopeConfig->getValue(
                    'temando/general/sandbox',
                    \Magento\Store\Model\ScopeInterface::SCOPE_STORE
                ),
                'clientid'  => $this->_scopeConfig->getValue(
                    'temando/general/client',
                    \Magento\Store\Model\ScopeInterface::SCOPE_STORE
                ),
                'username'  => $this->_scopeConfig->getValue(
                    'temando/general/username',
                    \Magento\Store\Model\ScopeInterface::SCOPE_STORE
                ),
                'password'  => $this->_scopeConfig->getValue(
                    'temando/general/password',
                    \Magento\Store\Model\ScopeInterface::SCOPE_STORE
                ),
                'country'   => $this->getCountry(),
            );
        }
    }
    
    /**
     * Returns request array used to create location via API
     *
     * @return array
     */
    public function toCreateLocationRequestArray()
    {
        return array(
            'description' => $this->getName(),
            'type' => 'Origin',
            'contactName' => $this->getContactName(),
            'companyName' => $this->getCompanyName(),
            'street' => $this->getStreet(),
            'suburb' => $this->getCity(),
            'state' => $this->getRegion(),
            'code' => $this->getPostcode(),
            'country' => $this->getCountry(),
            'phone1' => preg_replace('/\D/', '', $this->getData('contact_phone_1')),
            'phone2' => preg_replace('/\D/', '', $this->getData('contact_phone_2')),
            'fax' => $this->getContactFax(),
            'email' => $this->getContactEmail(),
            'loadingFacilities' => $this->getLoadingFacilities(),
            'forklift' => $this->getForklift(),
            'dock' => $this->getDock(),
            'limitedAccess' => $this->getLimitedAccess(),
        );
    }


    /**
     * Returns true if warehouse serves given postal code, false otherwise.
     *
     * @param string $countryId
     * @param string|int $postcode
     *
     * @return boolean
     */
    public function servesArea($postcode, $countryId)
    {
        $zones = explode(',', $this->getZoneId());
        if (is_array($zones) && !empty($zones)) {
            foreach ($zones as $zoneId) {
                $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
                $zone = $objectManager->create('Temando\Temando\Model\Zone');
                $zone = $zone->load($zoneId);

                /* @var $zone \Temando\Temando\Model\Zone */

                if ($zone->getCountryCode() != $countryId) {
                    continue;
                }
                $ranges = explode(',', $zone->getRanges());

                if (count($ranges) === 1) {
                    $range = trim($ranges[0]);
                    if (empty($range)) {
                        //range not specified - all country
                        return true;
                    }
                }

                /**
                 * Spaces in non-numerical postal codes are significant!
                 * 'M1 1A' != 'M11 A' - these can be two different zones
                 */
                if (in_array(strtoupper($countryId), $this->_nonNumericPcodes)) {
                    //non-numeric postal codes (wildcart * or exact match only)
                    foreach ($ranges as $range) {
                        $beginsWith = stristr($range, '*', true);
                        if ($beginsWith) {
                            //wildcart used
                            if (stripos($postcode, $beginsWith) === 0) {
                                return true;
                            }
                        } else {
                            //exact match
                            if (strtolower($postcode) == strtolower($range)) {
                                return true;
                            }
                        }
                    }
                } else {
                    //numeric postal codes
                    foreach ($ranges as $range) {
                        $minmax = explode(':', $range);
                        if (count($minmax) == 2) {
                            //range specified as a:b
                            if ($postcode >= $minmax[0] && $postcode <= $minmax[1]) {
                                return true;
                            }
                        } elseif (count($minmax) == 1) {
                            //single value
                            if ($postcode == $minmax[0]) {
                                return true;
                            }
                        }
                    }
                }
            }
        }
        return false;
    }

    /**
     * Checks temando_origin_inventory for stock at this origin
     *
     * @param array $skus
     *
     * @return bool
     */
    public function hasStock(array $skus)
    {
        $erp_id = $this->getErpId();
        if (is_null($erp_id)) {
            $this->_logger->debug(
                date('Y/m/d H:i:s') . ' ' . get_class($this)
                . ' Origin does not have an ERP ID'
            );
            return false;
        }
        $skuKeys = array_keys($skus);
        // prevent product SKU contains single and double quote
        foreach ($skuKeys as $index => $sku) {
            $skuKeys[$index] = addslashes($sku);
        }
        $tableName = $this->_resourceConnection->getTableName('temando_origin_inventory');
        $select = "SELECT units, sku FROM " . $tableName . " WHERE erp_id='"
            . $this->getErpId()."' AND sku IN ('".implode("','", $skuKeys)."')";

        $connection = $this->_resourceConnection->getConnection();
        $rows = $connection->fetchAll($select);

        if (count($rows) < count($skus)) {
            //not every sku has a corresponding row - therefore at least on item does not have stock
            return false;
        }
        foreach ($rows as $index => $row) {
            if (!isset($skus[trim($row['sku'])])) {
                $this->_logger->debug(
                    date('Y/m/d H:i:s') . ' ' . get_class($this)
                    . ' There\'s a problem with the sku array - please check the SKUs match in the inventory import'
                    . ' and the Product details'
                );
                $this->_logger->debug(print_r($skus, 1));
                return false;
            } else {
                $this->_logger->debug(
                    date('Y/m/d H:i:s') . ' ' . get_class($this)
                    . ' Comparing ' . $row['units'] . ' & ' . $skus[trim($row['sku'])]
                );
            }
            if ($row['units']<$skus[trim($row['sku'])]) {
                return false;
            }
        }
        $this->_logger->debug(
            date('Y/m/d H:i:s') . ' ' . get_class($this)
            . ' Found stock in ' . $this->getName()
        );
        return true;
    }
    
    /**
     * {@inheritdoc}
     */
    public function afterSave()
    {
        parent::afterSave();
        $this->_saveSerializeData();
            
        return $this;
    }

    
    /**
     * Save serialize data.
     *
     * @return $this
     */
    protected function _saveSerializeData()
    {
        if ($this->hasData('in_holiday_ids')) {
            $this->assignHolidays($this->getData('in_holiday_ids'));
        }

        if ($this->hasData('in_specialday_ids')) {
            $this->assignSpecialdays($this->getData('in_specialday_ids'));
        }

        return $this;
    }
    
    /**
     * Run build method.
     *
     * @param $methodId
     */
    public function runGetterMethod($methodId)
    {
        if (!isset($this->_methodGetters[$methodId])) {
            throw new LocalizedException(__('Method of %1 is not exists !', get_class($this)));
        }

        $getterMethod = $this->_methodGetters[$methodId];

        return $this->$getterMethod();
    }
    
    /**
     * Assign Holidays to Store.
     *
     * @param array $holidayIds
     */
    public function assignHolidays(array $holidayIds = [])
    {
        $this->_getResource()->assignHolidays($this, $holidayIds);

        return $this;
    }
    
    /**
     * Get holiday ids of store.
     *
     * @param \Magento\Framework\Model\AbstractModel $object
     */
    public function getHolidayIds()
    {
        return $this->_getResource()->getHolidayIds($this);
    }
    
     /**
     * Get holidays data.
     *
     * @return array
     */
    public function getHolidaysData($stockMessage)
    {
        $collection = $this->_filterDays($this->getHolidays());

        $days = [];
        $key = 0;
        $timeDay = self::TIME_DAY;

        foreach ($collection as $item) {
            $days[$key]['name'] = $item->getHolidayName();
            $dateFrom = strtotime($item->getDateFrom());
            $dateTo = strtotime($item->getDateTo());

            while ($dateFrom <= $dateTo) {
                $days[$key]['date'][] = date('Y-m-d', $dateFrom);
                $dateFrom += $timeDay;
            }

            ++$key;
        }
        // if item is out of stock, then block 7 days from now
        if ($stockMessage == self::OUT_OF_STOCK_MESSAGE) {
            $newIndex = count($days) + 1;
            $days[$newIndex]['name'] = 'OUTOFSTOCK';
            for ($i = 0; $i < 7; $i++) {
                $days[$newIndex]['date'][] = date('Y-m-d', strtotime('+ '.$i.' day'));
            }
        }
        return $days;
    }
    
    /**
     * Get Holiday collection of Store.
     *
     * @return \Magestore\Storepickup\Model\ResourceModel\Specialday\Collection
     */
    public function getHolidays()
    {
        $collection = $this->_holidayCollectionFactory->create();
        $collection->addFieldToFilter('holiday_id', ['in' => $this->getHolidayIds()]);

        return $collection;
    }
    
    /**
     * Assign Specialdays to Store.
     *
     * @param array $specialdayIds
     */
    public function assignSpecialdays(array $specialdayIds = [])
    {
        $this->_getResource()->assignSpecialdays($this, $specialdayIds);

        return $this;
    }
    
    /**
     * Get holiday ids of store.
     *
     * @param \Magento\Framework\Model\AbstractModel $object
     */
    public function getSpecialdayIds()
    {
        return $this->_getResource()->getSpecialdayIds($this);
    }
    
    /**
     * Get Specialday Collection  of Store.
     *
     * @return \Magestore\Storepickup\Model\ResourceModel\Specialday\Collection
     */
    public function getSpecialdays()
    {
        $collection = $this->_specialdayCollectionFactory->create();
        $collection->addFieldToFilter('specialday_id', ['in' => $this->getSpecialdayIds()]);

        return $collection;
    }
    
    /**
     * Get specialday data.
     *
     * @return array
     */
    public function getSpecialdaysData()
    {
        $collection = $this->_filterDays($this->getSpecialdays());

        $days = [];
        $key = 0;
        $timeDay = self::TIME_DAY;

        foreach ($collection as $item) {
            $days[$key]['name'] = $item->getSpecialdayName();
            $days[$key]['time_open'] = $item->getTimeOpen();
            $days[$key]['time_close'] = $item->getTimeClose();
            $dateFrom = strtotime($item->getDateFrom());
            $dateTo = strtotime($item->getDateTo());

            while ($dateFrom <= $dateTo) {
                $days[$key]['date'][] = date('Y-m-d', $dateFrom);
                $dateFrom += $timeDay;
            }

            ++$key;
        }

        return $days;
    }

    /**
     * Filter specialdays, holidays.
     *
     * @param \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection $collection
     */
    protected function _filterDays(\Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection $collection)
    {
        $dayShow = $this->_systemConfig->getLimitStoreDays();
        $dateStart = date('Y-m-d');
        $dateEnd = date('Y-m-d', strtotime(date('Y-m-d')) + $dayShow * self::TIME_DAY);

        $collection->getSelect()->where('date_from <= date_to');
        $collection->addFieldToFilter('date_to', ['gteq' => $dateStart])
            ->addFieldToFilter('date_from', ['lteq' => $dateEnd]);

        return $collection;
    }
}
