<?php
namespace Temando\Temando\Ui\Component\Listing\Column;

use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;
use Magento\Framework\UrlInterface;

class PickupActions extends Column
{
    /**
     * View Pickup URL.
     */
    const TEMANDO_PICKUP_URL_PATH_VIEW = 'temando/pickup/view';

    /**
     * Edit Pickup URL.
     */
    const TEMANDO_PICKUP_URL_PATH_EDIT = 'temando/pickup/edit';

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
     * Temando Pickup
     *
     * @var \Temando\Temando\Model\Pickup
     */
    protected $_pickup;

    /**
     * View Pickup URL.
     *
     * @var string
     */
    private $viewUrl;

    /**
     * Edit Pickup URL
     *
     * @var string
     */
    private $editUrl;

    /**
     * PickupActions constructor.
     *
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param UrlInterface $urlBuilder
     * @param \Temando\Temando\Helper\Data $helper
     * @param \Temando\Temando\Model\Pickup $pickup
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
        \Temando\Temando\Model\Pickup $pickup,
        array $components = [],
        array $data = [],
        $viewUrl = self::TEMANDO_PICKUP_URL_PATH_VIEW,
        $editUrl = self::TEMANDO_PICKUP_URL_PATH_EDIT
    ) {
        $this->urlBuilder = $urlBuilder;
        $this->viewUrl = $viewUrl;
        $this->editUrl = $editUrl;
        $this->_helper = $helper;
        $this->_pickup = $pickup;
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
                if (isset($item['pickup_id'])) {
                    $this->_pickup->load($item['pickup_id']);

                    $showEdit = false;
                    $viewLabel = __('View');
                    
                    if (($this->_pickup->getStatus()<
                        \Temando\Temando\Model\System\Config\Source\Pickup\Status::COLLECTED)
                    ) {
                        $showEdit = true;
                        if ($this->_helper->_isAllowedAction('Temando_Temando::temando_pickups_process')) {
                            $viewLabel .= __(' & Process');
                        }
                    }
                    if ($this->_helper->_isAllowedAction('Temando_Temando::temando_pickups_view')) {
                        $item[$name]['view'] = [
                            'href' => $this->urlBuilder->getUrl($this->viewUrl, ['pickup_id' => $item['pickup_id']]),
                            'label' => __($viewLabel)
                        ];
                    }
                    if ($showEdit) {
                        if ($this->_helper->_isAllowedAction('Temando_Temando::temando_pickups_edit_save')) {
                            $item[$name]['edit'] = [
                                'href' => $this->urlBuilder->getUrl(
                                    $this->editUrl,
                                    ['pickup_id' => $item['pickup_id']]
                                ),
                                'label' => __('Edit')
                            ];
                        }
                    }
                }
            }
        }
        return $dataSource;
    }
}
