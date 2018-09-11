<?php
namespace Temando\Temando\Ui\Component\Listing\Column;

use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;
use Magento\Framework\UrlInterface;

class ShipmentActions extends Column
{
    /**
     * Edit Shipment URL.
     */
    const TEMANDO_SHIPMENT_URL_PATH_VIEW = 'temando/shipment/view';

    /**
     * Delete Zone URL.
     */
    const TEMANDO_SHIPMENT_URL_PATH_EDIT = 'temando/shipment/edit';

    /**
     * URL Builder.
     *
     * @var UrlInterface
     */
    protected $urlBuilder;

    /**
     * Temando Helper
     *
     * @var \Temando\Temando\Helper\Data
     */
    protected $_helper;

    /**
     * Temando Shipment
     *
     * @var \Temando\Temando\Model\Shipment
     */
    protected $_shipment;

    /**
     * URL to view shipment.
     *
     * @var string
     */
    private $viewUrl;

    /**
     * URL to edit shipment.
     *
     * @var string
     */
    private $editUrl;

    /**
     * ShipmentActions constructor.
     *
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param UrlInterface $urlBuilder
     * @param \Temando\Temando\Helper\Data $helper
     * @param \Temando\Temando\Model\Shipment $shipment
     * @param array $components
     * @param array $data
     * @param string $viewUrl
     * @param string $editUrl
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        UrlInterface $urlBuilder,
        \Temando\Temando\Helper\Data $helper,
        \Temando\Temando\Model\Shipment $shipment,
        array $components = [],
        array $data = [],
        $viewUrl = self::TEMANDO_SHIPMENT_URL_PATH_VIEW,
        $editUrl = self::TEMANDO_SHIPMENT_URL_PATH_EDIT
    ) {
        $this->urlBuilder = $urlBuilder;
        $this->viewUrl = $viewUrl;
        $this->editUrl = $editUrl;
        $this->_helper = $helper;
        $this->_shipment = $shipment;
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }

    /**
     * Prepare Data Source
     *
     * @param array  $dataSource
     *
     * @return array
     */
    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as & $item) {
                $name = $this->getData('name');
                if (isset($item['shipment_id'])) {
                    $this->_shipment->load($item['shipment_id']);

                    $viewLabel = __('View');

                    if (($this->_shipment->getStatus() <
                        \Temando\Temando\Model\System\Config\Source\Shipment\Status::COMPLETE)
                        &&
                        ($this->_helper->_isAllowedAction('Temando_Temando::temando_shipments_process'))
                    ) {
                        $viewLabel .= __(' & Process');
                    }

                    if ($this->_helper->_isAllowedAction('Temando_Temando::temando_shipments_view')) {
                        $item[$name]['view'] = [
                            'href' => $this->urlBuilder->getUrl(
                                $this->viewUrl,
                                ['shipment_id' => $item['shipment_id']]
                            ),
                            'label' => __($viewLabel)
                        ];
                    }

                    if (($this->_shipment->getStatus() <
                            \Temando\Temando\Model\System\Config\Source\Shipment\Status::BOOKED)
                        &&
                        ($this->_helper->_isAllowedAction('Temando_Temando::temando_shipments_edit_save'))
                    ) {
                        $item[$name]['edit'] = [
                            'href' => $this->urlBuilder->getUrl(
                                $this->editUrl,
                                ['shipment_id' => $item['shipment_id']]
                            ),
                            'label' => __('Edit')
                        ];
                    }
                }
            }
        }
        return $dataSource;
    }
}
