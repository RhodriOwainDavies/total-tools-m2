<?php

namespace Temando\Temando\Model\Api\Request;

use Magento\Framework\Model\AbstractModel;

/**
 * Api Request Anywhere
 *
 * @method Temando_Temando_Model_Api_Request_Anywhere setDestinationCountry()
 * @method Temando_Temando_Model_Api_Request_Anywhere setDestinationPostcode()
 * @method Temando_Temando_Model_Api_Request_Anywhere setDestinationCity()
 * @method Temando_Temando_Model_Api_Request_Anywhere setDestinationStreet()
 * @method Temando_Temando_Model_Api_Request_Anywhere setDestinationType()
 * @method Temando_Temando_Model_Api_Request_Anywhere setOriginDescription()
 * @method Temando_Temando_Model_Api_Request_Anywhere setOriginCountry()
 * @method Temando_Temando_Model_Api_Request_Anywhere setDeliveryOptions()
 *
 * @method string getDestinationCountry()
 * @method string getDestinationPostcode()
 * @method string getDestinationCity()
 * @method string getDestinationStreet()
 * @method string getDestinationType()
 * @method string getOriginDescription()
 * @method string getOriginCountry()
 * @method array  getDeliveryOptions()
 */
class Anywhere extends AbstractModel
{

    public function _construct()
    {
        parent::_construct();
        $this->setDeliveryOptions(array());
    }

    /**
     * Prepares the anywhere request array
     *
     * @param boolean $international
     *
     * @return array|boolean
     */
    public function toRequestArray($international = false)
    {

        $deliveryOptions = $this->getDeliveryOptions();

        switch ($this->getDestinationType()) {
            //case \Temando\Temando\Model\System\Config\Source\Origin::RESIDENTIAL:
            //case 'residence':
            default:
                $data = array(
                    'itemNature' => $international ? 'International' : 'Domestic',
                    'itemMethod' => 'Door to Door',
                    'destinationCountry' => $this->getDestinationCountry(),
                    'destinationCode' => $this->getDestinationPostcode(),
                    'destinationSuburb' => $this->getDestinationCity(),
                    'destinationIs' => ucfirst('residence'),
//                    'destinationResLimitedAccess' => 'N',
//                    'destinationResHeavyLift' => 'N',
//                    'destinationResTailgateLifter' => 'N',
                    'destinationResUnattended' => array_key_exists(
                        'unattended_delivery',
                        $deliveryOptions
                    ) ? 'Y' : 'N',
                    'originDescription' => $this->getOriginDescription()
                );
                break;

            case \Temando\Temando\Model\System\Config\Source\Origin\Type::BUSINESS:
                $data = array(
                    'itemNature' => $international ? 'International' : 'Domestic',
                    'itemMethod' => 'Door to Door',
                    'destinationCountry' => $this->getDestinationCountry(),
                    'destinationCode' => $this->getDestinationPostcode(),
                    'destinationSuburb' => $this->getDestinationCity(),
                    'destinationIs' => ucfirst(\Temando\Temando\Model\System\Config\Source\Origin\Type::BUSINESS),
//                    'destinationBusLimitedAccess' => array_key_exists(
//                        'limited_access',
//                        $this->getDeliveryOptions()
//                    ) ? 'Y' : 'N',
//                    'destinationBusHeavyLift' => array_key_exists(
//                        'heavy_lift',
//                        $this->getDeliveryOptions()
//                    ) ? 'Y' : 'N',
//                    'destinationBusTailgateLifter' => array_key_exists(
//                        'tailgate_lifter',
//                        $this->getDeliveryOptions()
//                    ) ? 'Y' : 'N',
                    'destinationBusUnattended' => array_key_exists(
                        'unattended_delivery',
                        $deliveryOptions
                    ) ? 'Y' : 'N',
//                    'destinationBusContainerSwingLifter' => array_key_exists(
//                        'swing_lifter',
//                        $this->getDeliveryOptions()
//                    ) ? 'Y' : 'N',
//                    'destinationBusForklift' => array_key_exists(
//                        'forklift',
//                        $this->getDeliveryOptions()
//                    ) ? 'Y' : 'N',
//                    'destinationBusDock' => array_key_exists(
//                        'loading_dock',
//                        $this->getDeliveryOptions()
//                    ) ? 'Y' : 'N',
                    'originDescription' => $this->getOriginDescription()
                );
//                if (Mage::helper('temando')->isStreetWithPO($this->getDestinationStreet())) {
//                    $data['destinationBusPostalBox'] = 'Y';
//                }
        }

        return $data;
    }
}
