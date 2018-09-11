<?php
namespace Temando\Temando\Model;

use Magento\Framework\DataObject\IdentityInterface;
use Magento\Framework\Exception\LocalizedException as CoreException;
use Temando\Temando\Api\Data\ZoneInterface;

class Zone extends \Magento\Framework\Model\AbstractModel implements ZoneInterface, IdentityInterface
{
    /**
     * Origin's Statuses
     */
    const STATUS_ENABLED = 1;
    const STATUS_DISABLED = 0;

    /**
     * CMS page cache tag
     */
    const CACHE_TAG = 'temando_zone';

    /**
     * Cache tag.
     *
     * @var string
     */
    protected $_cacheTag = 'temando_zone';

    /**
     * Prefix of model events names
     *
     * @var string
     */
    protected $_eventPrefix = 'temando_zone';


    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Temando\Temando\Model\ResourceModel\Zone');
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
     * Return unique ID(s) for each object in system
     *
     * @return array
     */
    public function getIdentities()
    {
        return [self::CACHE_TAG . '_' . $this->getId()];
    }

    /**
     * Get ID
     *
     * @return int|null
     */
    public function getId()
    {
        return $this->getData(self::ZONE_ID);
    }

    /**
     * Get title
     *
     * @return string|null
     */
    public function getName()
    {
        return $this->getData(self::NAME);
    }

    /**
     * Get Country Code
     *
     * @return string
     */
    public function getCountryCode()
    {
        return $this->getData(self::COUNTRY_CODE);
    }

    /**
     * Get Ranges
     *
     * @return string
     */
    public function getRanges()
    {
        return $this->getData(self::RANGES);
    }

    /**
     * Set ID
     *
     * @param int $id
     *
     * @return \Temando\Temando\Api\Data\ZoneInterface
     */
    public function setId($id)
    {
        return $this->setData(self::ZONE_ID, $id);
    }

    /**
     * Set name.
     *
     * @param string $title
     *
     * @return \Temando\Temando\Api\Data\ZoneInterface
     */
    public function setName($title)
    {
        return $this->setData(self::NAME, $title);
    }

    /**
     * Set country code
     *
     * @param $country_code
     *
     * @return \Temando\Temando\Api\Data\ZoneInterface
     */
    public function setCountryCode($country_code)
    {
        return $this->setData(self::country_code, $country_code);
    }

    /**
     * Set Ranges.
     *
     * @param $ranges
     *
     * @return \Temando\Temando\Api\Data\ZoneInterface
     */
    public function setRanges($ranges)
    {
        return $this->setData(self::ranges, $ranges);
    }
}
