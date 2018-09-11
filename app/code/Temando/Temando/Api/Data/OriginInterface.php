<?php
namespace Temando\Temando\Api\Data;

interface OriginInterface
{
    /**
     * Constants for keys of data array. Identical to the name of the getter in snake case
     */
    const ORIGIN_ID         = 'origin_id';
    const NAME              = 'name';
    const IS_ACTIVE         = 'is_active';
    const COMPANY_NAME      = 'company_name';
    const ERP_ID            = 'erp_id';
    const STREET            = 'street';
    const CITY              = 'city';
    const REGION            = 'region';
    const POSTCODE          = 'postcode';
    const COUNTRY           = 'country';
    const CONTACT_NAME      = 'contact_name';
    const CONTACT_EMAIL     = 'contact_email';
    const CONTACT_PHONE_1   = 'contact_phone_1';
    const CONTACT_PHONE_2   = 'contact_phone_2';
    const CONTACT_FAX       = 'contact_fax';
    const ZONE_ID           = 'zone_id';
    const STORE_IDS         = 'store_ids';
    const USER_IDS          = 'user_ids';
    const ACCOUNT_MODE      = 'account_mode';
    const ACCOUNT_SANDBOX   = 'account_sandbox';
    const ACCOUNT_CLIENT_ID = 'account_clientid';
    const ACCOUNT_USERNAME  = 'account_username';
    const ACCOUNT_PASSWORD  = 'account_password';
     
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
     * Is active
     *
     * @return bool|null
     */
    public function isActive();

    /**
     * Get company name
     *
     * @return string|null
     */
    public function getCompanyName();


    /**
     * Set ID
     *
     * @param int $id
     *
     * @return \Temando\Temando\Api\Data\OriginInterface
     */
    public function setId($id);

    /**
     * Set name
     *
     * @param string $name
     *
     * @return \Temando\Temando\Api\Data\OriginInterface
     */
    public function setName($name);

    /**
     * Set is active
     *
     * @param int|bool $isActive
     *
     * @return \Temando\Temando\Api\Data\OriginInterface
     */
    public function setIsActive($isActive);

    /**
     * Set company name
     *
     * @param string $name
     *
     * @return \Temando\Temando\Api\Data\OriginInterface
     */
    public function setCompanyName($company_name);
}
