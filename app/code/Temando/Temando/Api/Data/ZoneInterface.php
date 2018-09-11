<?php
namespace Temando\Temando\Api\Data;

interface ZoneInterface
{
    /**
     * Constants for keys of data array. Identical to the name of the getter in snake case
     */
    const ZONE_ID     = 'zone_id';
    const NAME          = 'name';
    const COUNTRY_CODE  = 'country_code';
    const RANGES        = 'ranges';

    /**
     * Get ID
     *
     * @return int|null
     */
    public function getId();

    /**
     * Get name
     *
     * @return string|null
     */
    public function getName();

    /**
     * Get Country Code
     *
     * @return string
     */
    public function getCountryCode();

    /**
     * Get Ranges
     *
     * @return mixed
     */
    public function getRanges();

    /**
     * Set ID
     *
     * @param int $id
     *
     * @return \Temando\Temando\Api\Data\ZoneInterface
     */
    public function setId($id);

    /**
     * Set title.
     *
     * @param string $title
     *
     * @return \Temando\Temando\Api\Data\ZoneInterface
     */
    public function setName($title);

    /**
     * Set Country Code.
     *
     * @param string $country_code
     *
     * @return \Temando\Temando\Api\Data\ZoneInterface
     */
    public function setCountryCode($country_code);

    /**
     * Set Ranges.
     *
     * @param string $ranges
     *
     * @return \Temando\Temando\Api\Data\ZoneInterface
     */
    public function setRanges($ranges);
}
