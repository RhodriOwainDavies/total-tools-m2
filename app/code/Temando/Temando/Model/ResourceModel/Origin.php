<?php
namespace Temando\Temando\Model\ResourceModel;

class Origin extends \Magestore\Storepickup\Model\ResourceModel\AbstractResource
{
    /**
     * \Psr\Log\LoggerInterface
     *
     * @var
     */
    protected $_logger;

    /**
     * \Magento\Backend\Model\Auth\Session
     *
     * @var
     */
    protected $_authSession;
    protected $_abstractResource;
    /**
     * Construct
     *
     * @param \Magento\Framework\Model\ResourceModel\Db\Context $context
     * @param \Magento\Backend\Model\Auth\Session $authSession
     * @param \Psr\Log\LoggerInterface $logger
     * @param string|null $resourcePrefix
     */
    public function __construct(
        \Magento\Framework\Model\ResourceModel\Db\Context $context,
        \Magento\Backend\Model\Auth\Session $authSession,
        \Psr\Log\LoggerInterface $logger,
        \Magestore\Storepickup\Model\ResourceModel\Store $abstractResource,
        $resourcePrefix = null
    ) {
        $this->_authSession = $authSession;
        $this->_logger = $logger;
        $this->_abstractResource = $abstractResource;
        parent::__construct($context, $resourcePrefix);
        $this->_init('temando_origin', 'origin_id');
    }

    /**
     * Initialize resource model
     *
     * @return void
     */

    protected function _construct()
    {
        $this->_init('temando_origin', 'origin_id');
    }

    /**
     * Retrieve select object for load object data
     *
     * @param string $field
     * @param mixed $value
     * @param \Temando\Temando\Model\Origin $object
     *
     * @return \Zend_Db_Select
     */
    protected function _getLoadSelect($field, $value, $object)
    {
        $select = parent::_getLoadSelect($field, $value, $object);

        $user = $this->_authSession->getUser();
        if ($user) {
            if ($user->getAclRole() != 1) {
                $select->where(
                    'user_ids = ' . $user->getId() .
                    ' OR user_ids LIKE \'%,'.$user->getId().',%\'' .
                    ' OR user_ids LIKE \'%,' . $user->getId() . '\'' .
                    ' OR user_ids LIKE \'' . $user->getId() . ',%\''
                );
            }
        }
        if ($object->getStoreId()) {
            $select->where(
                'is_active = ?',
                1
            )->limit(
                1
            );
        }

        return $select;
    }
    
    /**
     * Assign Holidays.
     *
     * @param \Magento\Framework\Model\AbstractModel $object
     * @param array                                  $holidayIds
     *
     * @return $this
     *
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function assignHolidays(\Magento\Framework\Model\AbstractModel $object, array $holidayIds = [])
    {
        
        $id = (int) $object->getId();
        $table = $this->getTable('temando_origin_holiday');
        
        $old = $this->getHolidayIds($object);
        
        $new = $holidayIds;

        /*
         * Remove stores from object
         */
        $this->deleteData(
            $table,
            [
                $this->getIdFieldName() . ' = ?' => $id,
                'holiday_id IN(?)' => array_values(array_diff($old, $new)),
            ]
        );

        /*
         * Add stores to object
         */
        $insert = [];
        foreach (array_values(array_diff($new, $old)) as $holidayId) {
            $insert[] = [$this->getIdFieldName() => $id, 'holiday_id' => (int) $holidayId];
        }
        $this->insertData($table, $insert);

        return $this;
    }
    
    /**
     * Assign special days.
     *
     * @param \Magento\Framework\Model\AbstractModel $object
     * @param array                                  $specialdayIds
     *
     * @return $this
     *
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function assignSpecialdays(\Magento\Framework\Model\AbstractModel $object, array $specialdayIds = [])
    {
        $id = (int) $object->getId();
        $table = $this->getTable('temando_origin_specialday');

        $old = $this->getSpecialdayIds($object);
        $new = $specialdayIds;

        /*
         * Remove stores from object
         */
        $this->deleteData(
            $table,
            [
                $this->getIdFieldName() . ' = ?' => $id,
                'specialday_id IN(?)' => array_values(array_diff($old, $new)),
            ]
        );

        /*
         * Add stores to object
         */
        $insert = [];
        foreach (array_values(array_diff($new, $old)) as $holidayId) {
            $insert[] = [$this->getIdFieldName() => $id, 'specialday_id' => (int) $holidayId];
        }
        $this->insertData($table, $insert);

        return $this;
    }
    
    /**
     * Get holiday ids of store.
     *
     * @param \Magento\Framework\Model\AbstractModel $object
     */
    public function getHolidayIds(\Magento\Framework\Model\AbstractModel $object)
    {
        $connection = $this->getConnection();
        $id = (int) $object->getId();

        $select = $connection->select()->from(
            $this->getTable('temando_origin_holiday'),
            'holiday_id'
        )->where(
            $this->getIdFieldName() . ' = :object_id'
        );

        return $connection->fetchCol($select, [':object_id' => $id]);
    }

    /**
     * Get holiday ids of store.
     *
     * @param \Magento\Framework\Model\AbstractModel $object
     */
    public function getSpecialdayIds(\Magento\Framework\Model\AbstractModel $object)
    {
        $connection = $this->getConnection();
        $id = (int) $object->getId();

        $select = $connection->select()->from(
            $this->getTable('temando_origin_specialday'),
            'specialday_id'
        )->where(
            $this->getIdFieldName() . ' = :object_id'
        );

        return $connection->fetchCol($select, [':object_id' => $id]);
    }
}
