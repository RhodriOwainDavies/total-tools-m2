<?php

namespace Temando\Temando\Model\System\Config\Source;

/**
 * System Config Source
 */
abstract class Unit extends \Temando\Temando\Model\System\Config\Source
{

    /**
     * The array of options in the configuration item.
     *
     * This array's keys are the values used in the database etc. and the
     * values of this array are used as labels on the frontend.
     *
     * @var array
     */
    protected $_options;

    public function __construct()
    {
        parent::__construct();
        $this->_setupBriefOptions();
    }

    /**
     * Sets up the $_brief_options array with the correct values.
     *
     * This function is called in the constructor.
     *
     * @return Temando_Temando_Model_System_Config_Source_Abstract
     */
    protected abstract function _setupBriefOptions();
    
    /**
     * Looks up an option by key and gets the label.
     *
     * @param mixed $value
     *
     * @return mixed
     */
    public function getBriefOptionLabel($value)
    {
        if (array_key_exists($value, $this->_brief_options)) {
            return $this->_brief_options[$value];
        }
        return null;
    }
    
    public function toBriefOptionArray()
    {
        return $this->_toOptionArray($this->_brief_options);
    }
    
    public function getOptionValue($value)
    {
        return array_search($value, array_flip($this->_brief_options));
    }
}
