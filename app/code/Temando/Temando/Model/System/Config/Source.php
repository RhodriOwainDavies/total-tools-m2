<?php

namespace Temando\Temando\Model\System\Config;

/**
 * System Config Source
 */
abstract class Source
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
        $this->_setupOptions();
    }

    /**
     * Sets up the $_options array with the correct values.
     *
     * This function is called in the constructor.
     *
     * @return \Temando\Temando\Model\System\Config\Source
     */
    protected abstract function _setupOptions();

    /**
     * Gets all the options in the key => value type array.
     *
     * @return array
     */
    public function getOptions($please_select = false)
    {
        $options = $this->_options;
        if ($please_select) {
            $options = [null => __('--Please Select--')] + $options;
        }
        return $options;
    }

    /**
     * Converts the options into a format suitable for use in the admin area.
     *
     * @return array
     */
    public function toOptionArray()
    {
        return $this->_toOptionArray($this->_options);
    }

    protected function _toOptionArray($input)
    {
        $array = [];

        foreach ($input as $key => $value) {
            $array[] = [
                'value' => $key,
                'label' => $value,
            ];
        }

        return $array;
    }

    /**
     * Looks up an option by key and gets the label.
     *
     * @param mixed $value
     *
     * @return mixed
     */
    public function getOptionLabel($value)
    {
        if (array_key_exists($value, $this->_options)) {
            return $this->_options[$value];
        }
        return null;
    }
}
