<?php

namespace Temando\Temando\Model\Api\Request;

use Magento\Framework\Model\AbstractModel;

/**
 * Api Request Anytime
 */
class Anytime extends AbstractModel
{

    /**
     * Ready Time.
     *
     * @var string
     */
    protected $_ready_time_of_day = null;

    /**
     * Temando Helper.
     *
     * @var \Temando\Temando\Helper\Data
     */
    protected $_helper;

    /**
     * Ready Date.
     *
     * @var string
     */
    protected $_ready_date = null;

    public function _construct()
    {
        parent::_construct();
    }

    public function __construct(
        \Temando\Temando\Helper\Data $helper,
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        $this->_helper = $helper;
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }

    /**
     * Set the date the product will be ready (in UTC).
     *
     * If setting the ready date to a weekend, the next week day will be used instead.
     *
     * Doesn't accept timestamps in the past.
     *
     * @param timestamp $timestamp The timestamp when the package will be ready
     * (only the date information is used, the time of day is set separately.
     */
    public function setReadyDate($timestamp = null)
    {
        $this->_ready_date = $this->_helper->getReadyDate($timestamp);
        return $this;
    }

    /**
     * Set the time of the day for the request.
     *
     * @param string $time_of_day
     *
     * @return \Temando\Temando\Model\Api\Request\Anytime
     */
    public function setReadyTimeOfDay($time_of_day = 'AM')
    {
        if (strtoupper($time_of_day) === 'AM' || strtoupper($time_of_day) === 'PM') {
            $this->_ready_time_of_day = strtoupper($time_of_day);
        }
        return $this;
    }

    /**
     * Validate the anytime.
     *
     * @return string
     */
    public function validate()
    {
        return
                ($this->_ready_time_of_day == 'AM' || $this->_ready_time_of_day == 'PM') &&
                is_numeric($this->_ready_date);
    }

    /**
     * Prepares the anytime request array.
     *
     * @return boolean|array
     */
    public function toRequestArray()
    {
        if (!$this->validate()) {
            return false;
        }

        return array(
            'readyDate' => date('Y-m-d', $this->_ready_date),
            'readyTime' => $this->_ready_time_of_day,
        );
    }
}
