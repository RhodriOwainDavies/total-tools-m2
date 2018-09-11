<?php

namespace Temando\Temando\Model;

use Magento\Framework\Model\AbstractModel;
use Magento\Framework\DataObject\IdentityInterface;

class Quote extends AbstractModel implements IdentityInterface
{
    /**
     * Temando Carrier.
     *
     * @var \Temando\Temando\Model\Carrier
     */
    protected $_carrier;

    /**
     * Object Manager.
     *
     * @var \Magento\Framework\App\ObjectManager
     */
    protected $_objectManager;

    /**
     * Logger.
     *
     * @var \Psr\Log\LoggerInterface
     */
    protected $_logger;

    /**
     * Cache tag.
     */
    const CACHE_TAG = 'temando_quote';

    /**
     * Quote constructor.
     *
     * @param \Temando\Temando\Model\Carrier $carrier
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     */
    public function __construct(
        \Temando\Temando\Model\Carrier $carrier,
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry
    ) {
        $this->_carrier = $carrier;
        $this->_logger = $context->getLogger();
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $this->_objectManager = $objectManager;
        
        parent::__construct($context, $registry);
    }

    /**
     * Quote _construct
     */
    protected function _construct()
    {
        $this->_init('Temando\Temando\Model\ResourceModel\Quote');
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
     * Creates and saves a quote based on data returned from the API
     *
     * @param stdClass $response the SOAP response directly from the Temando API.
     */
    public function loadResponse(\stdClass $response)
    {
        if ($response instanceof \stdClass) {
            $carrier = $this->_objectManager->create('Temando\Temando\Model\ResourceModel\Carrier\Collection');
            $carrier = $carrier->addFieldToFilter('temando_carrier_id', $response->carrier->id)->getFirstItem();

            $carrier
                ->setTemandoCarrierId(isset($response->carrier->id) ? $response->carrier->id : '')
                ->setCompanyName(isset($response->carrier->companyName) ? $response->carrier->companyName : '')
                ->setCompanyContact(isset($response->carrier->companyContact) ? $response->carrier->companyContact : '')
                ->setStreetAddress(isset($response->carrier->streetAddress) ? $response->carrier->streetAddress : '')
                ->setStreetSuburb(isset($response->carrier->streetSuburb) ? $response->carrier->streetSuburb : '')
                ->setStreetCity(isset($response->carrier->streetCity) ? $response->carrier->streetCity : '')
                ->setStreetState(isset($response->carrier->streetState) ? $response->carrier->streetState : '')
                ->setStreetPostcode(isset($response->carrier->streetCode) ? $response->carrier->streetCode : '')
                ->setStreetCountry(isset($response->carrier->streetCountry) ? $response->carrier->streetCountry : '')
                ->setPostalAddress(isset($response->carrier->postalAddress) ? $response->carrier->postalAddress : '')
                ->setPostalSuburb(isset($response->carrier->postalSuburb) ? $response->carrier->postalSuburb : '')
                ->setPostalCity(isset($response->carrier->postalCity) ? $response->carrier->postalCity : '')
                ->setPostalState(isset($response->carrier->postalState) ? $response->carrier->postalState : '')
                ->setPostalPostcode(isset($response->carrier->postalCode) ? $response->carrier->postalCode : '')
                ->setPostalCountry(isset($response->carrier->postalCountry) ? $response->carrier->postalCountry : '')
                ->setPhone(isset($response->carrier->phone1) ? $response->carrier->phone1 : '')
                ->setEmail(isset($response->carrier->email) ? $response->carrier->email : '')
                ->setWebsite(isset($response->carrier->website) ? $response->carrier->website : '')
                ->save();

            $accepted = false;
            if (isset($response->accepted)) {
                if ($response->accepted == 'Y') {
                    $accepted = true;
                }
            }
            if (isset($response->etaFrom)) {
                $this->setEtaFrom($response->etaFrom);
            }
            if (isset($response->etaTo)) {
                $this->setEtaTo($response->etaTo);
            }
            $this
                ->setCarrierId($response->carrier->id)
                ->setAccepted($accepted)
                ->setTotalPrice($response->totalPrice)
                ->setBasePrice($response->basePrice)
                ->setTax($response->tax)
                ->setCurrency($response->currency)
                ->setGuaranteedEta($response->guaranteedEta == 'Y')
                ->setDeliveryMethod(isset($response->deliveryMethod) ? $response->deliveryMethod : '')
                ->setLoaded(true);
        } else {
            $this->_logger->debug(get_class($this) . ' loadResponse() response isn\'t stdClass '. get_class($response));
        }
        return $this;
    }

    /**
     * Returns this quote as an array used in makeBooking API call
     *
     * @param array $options
     *
     * @return array
     */
    public function toBookingRequestArray()
    {
        $request = array(
            'totalPrice'     => $this->getTotalPrice(),
            'basePrice'      => $this->getBasePrice(),
            'tax'            => $this->getTax(),
            'currency'       => $this->getCurrency(),
            'deliveryMethod' => $this->getDeliveryMethod(),
            'etaFrom'        => $this->getEtaFrom(),
            'etaTo'          => $this->getEtaTo(),
            'guaranteedEta'  => $this->getGuaranteedEta() ? 'Y' : 'N',
            'carrierId'      => $this->getCarrierId()
        );

        return $request;
    }
    
    /**
     * Returns all available extras
     *
     * @return array
     */
    public function getExtras()
    {
        if ($this->getData('extras')) {
            return unserialize($this->getData('extras'));
        }
        return null;
    }
    
    /**
     * Returns estimate delivery time description
     *
     * @return string
     */
    public function getEtaDescription()
    {
        $title = $this->getEtaFrom();

        if ($this->getEtaFrom() != $this->getEtaTo()) {
            $title .= ' - ' . $this->getEtaTo();
        }

        $title .= ' day';

        if ($this->getEtaTo() > 1) {
            $title .= 's';
        }

        return $title;
    }

    /**
     * Gets the carrier providing this quote.
     *
     * @return \Temando\Temando\Model\Carrier
     */
    public function getCarrier()
    {
        if (!$this->_carrier) {
            $this->setCarrier($this->getCarrierId());
        }
        return $this->_carrier;
    }

    /**
     * Sets the carrier providing this quote.
     *
     * @param int $carrier_id
     *
     * @return \Temando\Temando\Model\Quote
     */
    public function setCarrier($carrier_id)
    {
        $carrier = $this->_carrier->load($carrier_id);

        if ($carrier->getId() == $carrier_id) {
            // exists in the database
            $this->_carrier = $carrier;
            $this->setData('carrier_id', $carrier_id);
        }
        return $this;
    }

    /**
     * Get quote description (title of the shipping method)
     *
     * @param boolean $showCarrier
     * @param boolean $showMethod
     * @param boolean $showEta
     * @param string $carrierTitle
     *
     * @return string
     */
    public function getDescription($showCarrier = true, $showMethod = true, $showEta = true, $carrierTitle = '')
    {
        $title = $showCarrier ? $this->getCarrier()->getCompanyName() : trim($carrierTitle);
        if (strlen($title) && $showMethod) {
            $title .= ' - ';
        }
        if ($showMethod && $showEta) {
            $title .= $this->getDeliveryMethod(). ' [' . $this->getEtaDescription() . ']';
        } elseif ($showMethod) {
            $title .= $this->getDeliveryMethod();
        } elseif ($showEta) {
            $title .= ' [' . $this->getEtaDescription(). ']';
        }
        return $title . ' ' . $this->getExtraTitle();
    }
}
