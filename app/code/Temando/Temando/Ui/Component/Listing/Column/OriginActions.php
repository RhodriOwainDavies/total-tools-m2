<?php
namespace Temando\Temando\Ui\Component\Listing\Column;

use Magento\Ui\Component\Listing\Columns\Column;

class OriginActions extends Column
{
    /**
     * Edit Origin URL.
     */
    const TEMANDO_ORIGIN_URL_PATH_EDIT = 'temando/origin/edit';

    /**
     * Delete Origin URL.
     */
    const TEMANDO_ORIGIN_URL_PATH_DELETE = 'temando/origin/delete';

    /**
     * Context.
     *
     * @var \Magento\Framework\View\Element\UiComponent\ContextInterface
     */
    protected $_context;

    /**
     * Edit Origin URL.
     *
     * @var string
     */
    private $editUrl;

    /**
     * Temando Helper
     *
     * @var \Temando\Temando\Helper\Data
     */
    protected $_helper;

    /**
     * OriginActions constructor.
     *
     * @param \Magento\Framework\View\Element\UiComponent\ContextInterface $context
     * @param \Magento\Framework\View\Element\UiComponentFactory $uiComponentFactory
     * @param \Magento\Backend\Block\Template\Context $templateContext
     * @param \Temando\Temando\Helper\Data $helper
     * @param array $components
     * @param array $data
     * @param string $editUrl
     */
    public function __construct(
        \Magento\Framework\View\Element\UiComponent\ContextInterface $context,
        \Magento\Framework\View\Element\UiComponentFactory $uiComponentFactory,
        \Magento\Backend\Block\Template\Context $templateContext,
        \Temando\Temando\Helper\Data $helper,
        array $components = [],
        array $data = [],
        $editUrl = self::TEMANDO_ORIGIN_URL_PATH_EDIT
    ) {
        $this->_context = $context;
        $this->editUrl = $editUrl;
        $this->_helper = $helper;
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
                if (isset($item['origin_id'])) {
                    if ($this->_helper->_isAllowedAction('Temando_Temando::temando_locations_edit_origin')) {
                        $item[$name]['edit'] = [
                            'href' => $this->_context->getUrl($this->editUrl, ['origin_id' => $item['origin_id']]),
                            'label' => __('Edit')
                        ];
                    }
                    if ($this->_helper->_isAllowedAction('Temando_Temando::temando_locations_delete_origin')) {
                        $item[$name]['delete'] = [
                            'href' => $this->_context->getUrl(
                                self::TEMANDO_ORIGIN_URL_PATH_DELETE,
                                ['origin_id' => $item['origin_id']]
                            ),
                            'label' => __('Delete'),
                            'confirm' => [
                                'title' => __('Delete "${ $.$data.name }"'),
                                'message' => __('Are you sure you wan\'t to delete a "${ $.$data.name }" record?')
                            ]
                        ];
                    }
                }
            }
        }

        return $dataSource;
    }
}
