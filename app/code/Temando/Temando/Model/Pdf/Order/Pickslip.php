<?php

namespace Temando\Temando\Model\Pdf\Order;

use Magento\Sales\Model\Order\Pdf\AbstractPdf;

class Pickslip extends AbstractPdf
{
    const ENCODING_TYPE = 'UTF-8';
    
    /**
     * Address Renderer
     *
     * @var \Magento\Sales\Model\Order\Address\Renderer
     */
    protected $_addressRenderer;
    
    /**
     * Total number of items contained in the master pickslip
     *
     * @var int
     */
    protected $totalItems = 0;
    protected $_productFactory;
    protected $_boxCollection;
    protected $_directoryList;
    protected $_helper;
    protected $_scopeConfig;
    protected $_orderType;
    protected $_storeManager;
    /**
     * The barcode TT Font file path relative to current skin folder
     * ie: /skin/adminhtml/default/default/ + {path}
     */
    const TEMANDO_FONT = 'barcode-fonts/FRE3OF9X.TTF';

    public function __construct(
        \Magento\Payment\Helper\Data $paymentData,
        \Magento\Framework\Stdlib\StringUtils $string,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Framework\Filesystem $filesystem,
        \Magento\Sales\Model\Order\Pdf\Config $pdfConfig,
        \Magento\Sales\Model\Order\Pdf\Total\Factory $pdfTotalFactory,
        \Magento\Sales\Model\Order\Pdf\ItemsFactory $pdfItemsFactory,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $localeDate,
        \Magento\Framework\Translate\Inline\StateInterface $inlineTranslation,
        \Magento\Sales\Model\Order\Address\Renderer $addressRenderer,
        \Magento\Catalog\Model\ProductFactory $productFactory,
        \Magento\Framework\App\Filesystem\DirectoryList $directoryList,
        \Temando\Temando\Helper\Data $helper,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Temando\Temando\Model\ResourceModel\Box\Collection $boxCollection,
        array $data = []
    ) {
        $this->_directoryList = $directoryList;
        $this->_productFactory = $productFactory;
        $this->_boxCollection = $boxCollection;
        $this->_addressRenderer = $addressRenderer;
        $this->_helper = $helper;
        $this->_scopeConfig = $scopeConfig;
        $this->_storeManager = $storeManager;
        parent::__construct(
            $paymentData,
            $string,
            $scopeConfig,
            $filesystem,
            $pdfConfig,
            $pdfTotalFactory,
            $pdfItemsFactory,
            $localeDate,
            $inlineTranslation,
            $addressRenderer,
            $data
        );
    }
    
    /**
     * Draw table header for product items
     *
     * @param \Zend_Pdf_Page $page
     *
     * @return void
     */
    protected function _drawHeader(\Zend_Pdf_Page $page)
    {
        /* Add table head */
        $this->_setFontRegular($page, 10);
        $page->setFillColor(new \Zend_Pdf_Color_Rgb(0.93, 0.92, 0.92));
        $page->setLineColor(new \Zend_Pdf_Color_GrayScale(0.5));
        $page->setLineWidth(0.5);
        $page->drawRectangle(25, $this->y, 570, $this->y - 15);
        $this->y -= 10;
        $page->setFillColor(new \Zend_Pdf_Color_Rgb(0, 0, 0));

        //columns headers
        $lines[0][] = array(
            'text' => 'Products',
            'feed' => 65,
        );

        $lines[0][] = array(
            'text' => 'Qty',
            'feed' => 35
        );

        $lines[0][] = array(
            'text' => 'Image',
            'feed' => 270
        );

        $lines[0][] = array(
            'text' => 'SKU',
            'feed' => 310
        );

        $lines[0][] = array(
            'text' => 'Brand',
            'feed' => 400
        );

        $lines[0][] = array(
            'text' => 'Part No',
            'feed' => 560,
            'align' => 'right'
        );

        $lineBlock = array(
            'lines' => $lines,
            'height' => 10
        );

        $this->drawLineBlocks($page, array($lineBlock), array('table_header' => true));
        $page->setFillColor(new \Zend_Pdf_Color_GrayScale(0));
        $this->y -= 20;
    }

    /**
     * Draw table header for product items
     *
     * @param Zend_Pdf_Page $page
     *
     * @return void
     */
    protected function _drawPackageHeader(\Zend_Pdf_Page $page)
    {
        /* Add table head */
        $this->_setFontRegular($page, 10);
        $page->setFillColor(new \Zend_Pdf_Color_RGB(0.93, 0.92, 0.92));
        $page->setLineColor(new \Zend_Pdf_Color_GrayScale(0.5));
        $page->setLineWidth(0.5);
        $page->drawRectangle(25, $this->y, 570, $this->y - 15);
        $this->y -= 10;
        $page->setFillColor(new \Zend_Pdf_Color_RGB(0, 0, 0));

        //columns headers
        $lines[0][] = array(
            'text' => 'Product',
            'feed' => 35,
            'align' => 'left',
        );
        $lines[0][] = array(
            'text' => 'Packaging',
            'feed' => 280,
        );

        $lines[0][] = array(
            'text' => 'Weight',
            'feed' => 340
        );

        $lines[0][] = array(
            'text' => 'Length',
            'feed' => 400,
        );

        $lines[0][] = array(
            'text' => 'Width',
            'feed' => 460,
        );

        $lines[0][] = array(
            'text' => 'Height',
            'feed' => 560,
            'align' => 'right',
        );

        $lineBlock = array(
            'lines' => $lines,
            'height' => 15
        );

        $this->drawLineBlocks($page, array($lineBlock), array('table_header' => true));
        $page->setFillColor(new \Zend_Pdf_Color_GrayScale(0));
        $this->y -= 5;
    }

    /**
     * Return PDF document
     *
     * @param array $orders
     * @param string $orderType
     * @param int $shipmentId
     *
     * @return Zend_Pdf
     */
    public function getPdf($order = null, $shipmentId = null)
    {
        //$this->_orderType = $orderType;
        $this->_beforeGetPdf();
        $this->_initRenderer('shipment');
        $pdf = new \Zend_Pdf();
        $this->_setPdf($pdf);
        $style = new \Zend_Pdf_Style();
        $this->_setFontBold($style, 10);
        $page = $this->newPage();
        /* Add image logo and ABN number */
        $this->insertLogoAndAbn($page, $order->getStore());
        /* Add head */
        $this->insertOrder($page, $order, true, $shipmentId);
        /* Add document text and number */
        $this->insertDocumentNumber($page, 'PICK SLIP');
        /* Add table */
        $this->_drawHeader($page);
        /* Add body */
        foreach ($order->getAllItems() as $item) {
            if ($item->getParentItem()) {
                continue;
            }
            /* Draw item */
            $this->_drawItem($item, $page, $order);
            $page = end($pdf->pages);
        }

        /* Add pickup in store checklist */
        if (is_null($shipmentId)) {
            /* Add Packages */
            foreach ($order->getAllItems() as $item) {
                if ($item->getParentItem()) {
                    continue;
                }
                $product = $this->_productFactory->create()->load($item->getProductId());
                if ($product->isVirtual()) {
                    continue;
                }
                /* Draw item */
                $this->_drawPackageHeader($page);
                $this->_drawPickupPackage($item, $page);
                $page = end($pdf->pages);
            }
            $this->y -= 50;

            $this->drawStoreChecklist($page, $order->getStore());
        } else {
            $packages = $this->_boxCollection
                ->addFieldToFilter('shipment_id', $shipmentId)
                ->getData();
            
            /* Draw item */
            $this->_drawPackageHeader($page);
            $this->_drawTemandoPackage($packages, $page, $order);
            $this->y -= 50;
        }
        
        $this->_afterGetPdf();
        return $pdf;
    }
    
    /**
     * Create new page and assign to PDF object
     *
     * @param array $settings
     *
     * @return Zend_Pdf_Page
     */
    public function newPage(array $settings = array())
    {
        /* Add new table head */
        $page = $this->_getPdf()->newPage(\Zend_Pdf_Page::SIZE_A4);
        $this->_getPdf()->pages[] = $page;
        $this->y = 800;
        if (!empty($settings['table_header'])) {
            $this->_drawHeader($page);
        }
        return $page;
    }

    /**
     * Get product image url
     *
     * @param \Magento\Framework\DataObject $item
     *
     * @return string
     */
    private function _productImage(\Magento\Framework\DataObject $item)
    {
        $product = $this->_productFactory->create()->loadByAttribute('sku', $item->getSku());
        $imageUrl = $this->_directoryList->getPath('pub') . '/media/catalog/product' . $product->getThumbnail();
        return $imageUrl;
    }
    
    /**
     * Draw Item process
     *
     * @param \Varien_Object $item
     * @param \Zend_Pdf_Page $page
     * @param \Magento\Sales\Model\Order $order
     *
     * @return Zend_Pdf_Page
     */
    protected function _drawItem(
        \Magento\Framework\DataObject $item,
        \Zend_Pdf_Page $page,
        \Magento\Sales\Model\Order $order
    ) {
        $lines = array();
        $product = $item->getProduct();

        // draw Product name
        $lines[0] = array(array(
                'text' => str_split($item->getName(), 40),
                'feed' => 65,
        ));

        // draw QTY
        $lines[0][] = array(
            'text' => $item->getQtyOrdered() ? $item->getQtyOrdered() * 1 : $item->getQty(),
            'feed' => 35
        );
        
        // draw product image
        $image = $this->_productImage($item);
        if (is_file($image)) {
            $image       = \Zend_Pdf_Image::imageWithPath($image);
            // make image < 55 times compared to normal size
            $width       = $image->getPixelWidth() / 60;
            $height      = $image->getPixelHeight() / 60;

            // image padding top: 3
            $y1 = $this->y - 3;
            $y2 = $this->y + $height - 3;
            $x1 = 270;
            $x2 = $x1 + $width;

            //coordinates after transformation are rounded by Zend
            $page->drawImage($image, $x1, $y1, $x2, $y2);
        }
        
        // draw SKU
        $lines[0][] = array(
            'text' => str_split($this->getSku($item), 20),
            'feed' => 310
        );

        // draw Brand
        $optionText = '';
        if ($product->getBrand()) {
            $attr = $product->getResource()->getAttribute('brand');
            if ($attr->usesSource()) {
                $optionText = $attr->getSource()->getOptionText($product->getBrand());
            }
        }
        $lines[0][] = array(
            'text' => str_split($optionText, 20),
            'feed' => 400
        );

        // draw Part No
        $lines[0][] = array(
            'text' => $product->getPartNo() ? str_split($product->getPartNo(), 15) : '',
            'feed' => 560,
            'align' => 'right'
        );

        // Custom options
        $options = $this->getItemOptions($item);
        if ($options) {
            foreach ($options as $option) {
                // draw options value
                if ($option['value']) {
                    if (isset($option['print_value'])) {
                        $_printValue = $option['print_value'];
                    } else {
                        $_printValue = strip_tags($option['value']);
                    }
                    $values = explode(', ', $_printValue);
                    foreach ($values as $value) {
                        $lines[][] = array(
                            'text' => str_split(strip_tags($option['label'] . ': ' . $value), 70),
                            'feed' => 110
                        );
                    }
                }
            }
        }

        /*
        if ($item->getProductType() == 'bundle') {
            if ($item->getHasChildren()) {
                foreach ($item->getChildrenItems() as $child) {
                    $childQty  = $item->getQtyOrdered() ? $item->getQtyOrdered() * 1 : $item->getQty();
                    $lines[][] = array(
                        'text' => str_split(strip_tags($child->getName() . ' x ' . $childQty), 70),
                        'feed' => 110
                    );
                }
            }
        }
        */


        $lineBlock = array(
            'lines' => $lines,
            'height' => 23
        );

        $page = $this->drawLineBlocks($page, array($lineBlock), array('table_header' => true));
        return $page;
    }

    /**
     * Draw Pickup package process
     *
     * @param \Varien_Object $item
     * @param \Zend_Pdf_Page $page
     *
     * @return Zend_Pdf_Page
     */
    protected function _drawPickupPackage(\Magento\Framework\DataObject $item, \Zend_Pdf_Page $page)
    {
        $lines = array();
        
        $packages = $this->_helper->getProductArticles($item);
        $weight_type = $this->_scopeConfig->getValue(
            'temando/units/weight',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
        
        $measure_type = $this->_scopeConfig->getValue(
            'temando/units/measure',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );

        for ($i = 0; $i < count($packages); $i++) {
            //draw index
            if ($item->getProductType() == 'bundle') {
                if ($item->getHasChildren()) {
                    $chilldItem = array();
                    foreach ($item->getChildrenItems() as $child) {
                        $childQty  = $child->getQtyOrdered() ? $child->getQtyOrdered() * 1 : $child->getQty();
                        $chilldItem[] = '    '.$childQty.' x '.substr($child->getName(), 0, 40);
                    }

                    $lines[$i] = array(array(
                        'text' => array_merge(str_split(strip_tags($packages[$i]['description']), 50), $chilldItem),
                        'feed' => 35,
                        'align' => 'left',
                    ));
                }
            } else {
                $lines[$i] = array(array(
                    'text' => str_split(strip_tags($packages[$i]['description']), 50),
                    'feed' => 35,
                    'align' => 'left',
                ));
            }

            // draw Product packaging
            $lines[$i][] = array(
                'text' => $packages[$i]['packaging'],
                'feed' => 280,
            );
            // draw weight
            $lines[$i][] = array(
                'text' => sprintf('%.2f %s', $packages[$i]['weight'], $this->_helper->getWeightUnitText()),
                'feed' => 340
            );
            // draw length
            $lines[$i][] = array(
                'text' => sprintf('%.2f %s', $packages[$i]['length'], $this->_helper->getMeasureUnitText()),
                'feed' => 400,
            );
            // draw width
            $lines[$i][] = array(
                'text' => sprintf('%.2f %s', $packages[$i]['width'], $this->_helper->getMeasureUnitText()),
                'feed' => 460,
            );
            // draw height
            $lines[$i][] = array(
                'text' => sprintf('%.2f %s', $packages[$i]['height'], $this->_helper->getMeasureUnitText()),
                'feed' => 560,
                'align' => 'right',
            );
        }

        $lineBlock = array(
            'lines' => $lines,
            'height' => 15
        );

        $page = $this->drawLineBlocks($page, array($lineBlock), array('table_header' => true));
        return $page;
    }

    /**
     * Draw Temando package process
     *
     * @param $package
     * @param \Zend_Pdf_Page $page
     *
     * @return Zend_Pdf_Page
     */
    protected function _drawTemandoPackage($packages, \Zend_Pdf_Page $page, $order = null)
    {
        $lines = array();
        $weight_type = $this->_scopeConfig->getValue(
            'temando/units/weight',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
                
        $measure_type = $this->_scopeConfig->getValue(
            'temando/units/measure',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );

        $defaultPackage = $this->_scopeConfig->getValue(
            'temando/defaults/packaging',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
                
        foreach ($packages as $index => $package) {
            //draw index
            $packageKey = preg_replace('#[^0-9a-z]+#i', '-', $packages[$index]['comment']);
            $chilldItem = array();
            if ($order != null) {
                foreach ($order->getAllVisibleItems() as $item) {
                    $productKey = preg_replace('#[^0-9a-z]+#i', '-', $item->getName());
                    if ($productKey == $packageKey && $item->getHasChildren() && $item->getProductType() == 'bundle') {
                        foreach ($item->getChildrenItems() as $child) {
                            $childQty  = $child->getQtyOrdered() ? $child->getQtyOrdered() * 1 : $child->getQty();
                            $chilldItem[] = '    '.$childQty.' x '.substr($child->getName(), 0, 40);
                        }
                        break;
                    }
                }
            }
            if (empty($chilldItem)) {
                $lines[$index] = array(array(
                    'text' => str_split(strip_tags($packages[$index]['comment']), 50),
                    'feed' => 35,
                    'align' => 'left',
                ));
            } else {
                $lines[$index] = array(array(
                    'text' => array_merge(str_split(strip_tags($packages[$index]['comment']), 50), $chilldItem),
                    'feed' => 35,
                    'align' => 'left',
                ));
            }
            
            // draw Product packaging
            $lines[$index][] = array(
                'text' => $packages[$index]['packaging'] ? $packages[$index]['packaging'] : 'box',
                'feed' => 280,
            );
            // draw weight
            $lines[$index][] = array(
                'text' => sprintf('%.2f %s', $packages[$index]['weight'], $this->_helper->getWeightUnitText()),
                'feed' => 340
            );
            // draw length
            $lines[$index][] = array(
                'text' => sprintf('%.2f %s', $packages[$index]['length'], $this->_helper->getMeasureUnitText()),
                'feed' => 400,
            );
            // draw width
            $lines[$index][] = array(
                'text' => sprintf('%.2f %s', $packages[$index]['width'], $this->_helper->getMeasureUnitText()),
                'feed' => 460,
            );
            // draw height
            $lines[$index][] = array(
                'text' => sprintf('%.2f %s', $packages[$index]['height'], $this->_helper->getMeasureUnitText()),
                'feed' => 560,
                'align' => 'right',
            );
        }

        $lineBlock = array(
            'lines' => $lines,
            'height' => 15
        );

        $page = $this->drawLineBlocks($page, array($lineBlock), array('table_header' => true));
        return $page;
    }
    
    /**
     * Return item Sku
     *
     * @param $item
     *
     * @return mixed
     */
    public function getSku($item)
    {
        if ($item->getProductOptionByCode('simple_sku')) {
            return $item->getProductOptionByCode('simple_sku');
        } else {
            return $item->getSku();
        }
    }

    /**
     * Retrieve item options
     *
     * @param \Magento\Framework\DataObject $item
     *
     * @return array
     */
    public function getItemOptions($item)
    {
        $result = array();
        if ($options = $item->getProductOptions()) {
            if (isset($options['options'])) {
                $result = array_merge($result, $options['options']);
            }
            if (isset($options['additional_options'])) {
                $result = array_merge($result, $options['additional_options']);
            }
            if (isset($options['attributes_info'])) {
                $result = array_merge($result, $options['attributes_info']);
            }
        }
        return $result;
    }

    /**
     * Insert logo to pdf page
     *
     * @param \Zend_Pdf_Page $page
     * @param null $store
     */
    protected function insertLogoAndAbn(&$page, $store = null)
    {
        $this->y = $this->y ? $this->y : 815;
        $image = $this->_directoryList->getPath('app')
            . '/code/Temando/Temando/view/adminhtml/web/images/totaltool-logo.png';
        
        if (is_file($image)) {
            $image       = \Zend_Pdf_Image::imageWithPath($image);
            $top         = 830; //top border of the page
            $widthLimit  = 270; //half of the page width
            $heightLimit = 270; //assuming the image is not a "skyscraper"
            $width       = $image->getPixelWidth();
            $height      = $image->getPixelHeight();

            //preserving aspect ratio (proportions)
            $ratio = $width / $height;
            if ($ratio > 1 && $width > $widthLimit) {
                $width  = $widthLimit;
                $height = $width / $ratio;
            } elseif ($ratio < 1 && $height > $heightLimit) {
                $height = $heightLimit;
                $width  = $height * $ratio;
            } elseif ($ratio == 1 && $height > $heightLimit) {
                $height = $heightLimit;
                $width  = $widthLimit;
            }

            $y1 = $top - $height;
            $y2 = $top;
            $x1 = 25;
            $x2 = $x1 + $width;

            //coordinates after transformation are rounded by Zend
            $page->drawImage($image, $x1, $y1, $x2, $y2);
            $this->y = $y1 - 10;
        }
        
        // add ABN
        $page->setFillColor(new \Zend_Pdf_Color_GrayScale(0));
        $font = $this->_setFontRegular($page, 10);
        $page->setLineWidth(0);

        $abn = 'ABN: ' . $this->_scopeConfig->getValue(
            'temando/pickslip/abn',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
        $page->drawText(trim($abn), 25, $this->y, self::ENCODING_TYPE);
        $this->y = $this->y - 10;
    }

    /**
     * Storepickup check list
     *
     * @param \Zend_Pdf_Page $page
     * @param null $store
    */

    protected function drawStoreChecklist(&$page, $store = null)
    {
        $lines = array();
        $this->y -= 10;
        
        //columns headers
        $lines[0][] = array(
            'text' => 'STORE USE ONLY - PICK UP IN STORE CHECKLIST',
            'feed' => 28,
            'font' => 'bold',
        );
        
        $lines[1][] = array(
            'text' => 'Photo ID Check:',
            'feed' => 28,
            'align' => 'left'
        );
        
        $lines[2][] = array(
            'text' => 'Customer name:',
            'feed' => 28,
            'align' => 'left'
        );
        
        $lines[3][] = array(
            'text' => 'Signature:',
            'feed' => 28,
            'align' => 'left'
        );
        
        $lines[4][] = array(
            'text' => 'Date of collection:',
            'feed' => 28,
            'align' => 'left'
        );
        
        $lines[5][] = array(
            'text' => 'TT staff name:',
            'feed' => 28,
            'align' => 'left'
        );
        
        $lineBlock = array(
            'lines' => $lines,
            'height' => 20
        );
        $page = $this->drawLineBlocks($page, array($lineBlock), array('table_header' => false));
    }
    
    /**
     * Insert order to pdf page
     *
     * @param \Zend_Pdf_Page $page
     * @param bool $putOrderId
     */
    protected function insertOrder(&$page, $obj, $putOrderId = true, $shipmentId = null)
    {
        $order = $obj;
        $this->y = $this->y ? $this->y : 815;
        $top = $this->y;

        $page->setFillColor(new \Zend_Pdf_Color_Html('#FFFFFF'));
        $page->setLineColor(new \Zend_Pdf_Color_Html('#000000'));
        $page->drawRectangle(25, $top, 570, $top - 55);
        $page->setFillColor(new \Zend_Pdf_Color_Html('#000000'));
        $this->setDocHeaderCoordinates(array(25, $top, 570, $top - 55));
        $this->_setFontRegular($page, 10);

        if ($putOrderId) {
            $page->drawText(
                'Order # ' . $order->getIncrementId(),
                35,
                ($top -= 30),
                self::ENCODING_TYPE
            );
        }
        
        $font = $this->_directoryList->getPath('app')
            . '/code/Temando/Temando/view/adminhtml/web/' . self::TEMANDO_FONT;
        $page->setFont(\Zend_Pdf_Font::fontWithPath($font), 48);
        $barcode = '*' . $order->getRealOrderId() . '*';
        $page->drawText($barcode, 200, $top - 15);
        $this->_setFontRegular($page, 10);

        $page->drawText(
            'Order Date: ' . date('d-m-Y', strtotime($order->getCreatedAt())),
            35,
            ($top -= 15),
            self::ENCODING_TYPE
        );

        $top -= 10;
        $page->setFillColor(new \Zend_Pdf_Color_Rgb(0.93, 0.92, 0.92));
        $page->setLineColor(new \Zend_Pdf_Color_GrayScale(0.5));
        $page->setLineWidth(0.5);
        $page->drawRectangle(25, $top, 275, ($top - 25));
        $page->drawRectangle(275, $top, 570, ($top - 25));

        /* Billing Address */
        $billingAddress = $this->_formatAddress($this->_addressRenderer->format($order->getBillingAddress(), 'pdf'));
        
        /* Shipping Address and Method */
        if (!$order->getIsVirtual()) {
            /* Shipping Address */
            $shippingAddress = $this->_formatAddress(
                $this->_addressRenderer->format($order->getShippingAddress(), 'pdf')
            );
            $shippingMethod = $order->getShippingDescription();
        }
        
        /* check If order is storepickup or temando order */
        if (is_null($shipmentId)) {
            $shippingMethod = 'Store Pickup';
        }

        $page->setFillColor(new \Zend_Pdf_Color_GrayScale(0));
        $this->_setFontBold($page, 12);
        $page->drawText('Sold to:', 35, ($top - 15), self::ENCODING_TYPE);

        if (!$order->getIsVirtual()) {
            if ($shipmentId) {
                $page->drawText('Ship to:', 285, ($top - 15), self::ENCODING_TYPE);
            } else {
                $page->drawText('Pickup location:', 285, ($top - 15), self::ENCODING_TYPE);
            }
        } else {
            $page->drawText('Payment Method:', 285, ($top - 15), self::ENCODING_TYPE);
        }

        $addressesHeight = $this->_calcAddressHeight($billingAddress)+15;
        if (isset($shippingAddress)) {
            $addressesHeight = max($addressesHeight, $this->_calcAddressHeight($shippingAddress));
        }

        $page->setFillColor(new \Zend_Pdf_Color_GrayScale(1));
        $page->drawRectangle(25, ($top - 25), 570, $top - 33 - $addressesHeight);
        $page->setFillColor(new \Zend_Pdf_Color_GrayScale(0));
        $this->_setFontRegular($page, 10);
        $this->y = $top - 40;
        $addressesStartY = $this->y;

        foreach ($billingAddress as $value) {
            if ($value !== '') {
                $text = array();
                foreach (str_split($value, 45) as $_value) {
                    $text[] = $_value;
                }
                foreach ($text as $part) {
                    $page->drawText(strip_tags(ltrim($part)), 35, $this->y, self::ENCODING_TYPE);
                    $this->y -= 15;
                }
            }
        }

        $customerEmail = $order->getCustomerEmail();
        $page->drawText(strip_tags(trim($customerEmail)), 35, $this->y, self::ENCODING_TYPE);
        $this->y -= 15;

        $addressesEndY = $this->y;

        if (!$order->getIsVirtual()) {
            $this->y = $addressesStartY;
            foreach ($shippingAddress as $value) {
                if ($value!=='') {
                    $text = array();
                    foreach (str_split($value, 45) as $_value) {
                        $text[] = $_value;
                    }
                    
                    foreach ($text as $part) {
                        $page->drawText(strip_tags(ltrim($part)), 285, $this->y, self::ENCODING_TYPE);
                        $this->y -= 15;
                    }
                }
            }
            $addressesEndY = min($addressesEndY, $this->y);
            $this->y = $addressesEndY;

            $page->setFillColor(new \Zend_Pdf_Color_Rgb(0.93, 0.92, 0.92));
            $page->setLineWidth(0.5);
            $page->drawRectangle(25, $this->y, 275, $this->y - 25);
            $page->drawRectangle(275, $this->y, 570, $this->y - 25);

            $this->y -= 15;
            $this->_setFontBold($page, 12);
            $page->setFillColor(new \Zend_Pdf_Color_GrayScale(0));
            $page->drawText('Payment Method', 35, $this->y, self::ENCODING_TYPE);
            $page->drawText('Shipping Method:', 285, $this->y, self::ENCODING_TYPE);

            $this->y -=10;
            $page->setFillColor(new \Zend_Pdf_Color_GrayScale(1));

            $this->_setFontRegular($page, 10);
            $page->setFillColor(new \Zend_Pdf_Color_GrayScale(0));

            $paymentLeft = 35;
            $yPayments = $this->y - 15;
        } else {
            $yPayments = $addressesStartY;
            $paymentLeft = 285;
        }

        $payment = $order->getPayment();
        $paymentInfo = $payment->getMethodInstance()->getTitle();
        $last4 = $payment->getCcLast4();
        if (trim($paymentInfo) != '') {
            //Printing "Payment Method" lines
            foreach (str_split($paymentInfo, 45) as $_value) {
                $page->drawText(strip_tags(trim($_value)), $paymentLeft, $yPayments, self::ENCODING_TYPE);
                $yPayments -= 15;
            }
        }

        if (trim($last4) != '') {
            $page->drawText('Credit Card Number: xxx-' . $last4, $paymentLeft, $yPayments, self::ENCODING_TYPE);
            $yPayments -= 15;
        }
        
        if ($order->getIsVirtual()) {
            // replacement of Shipments-Payments rectangle block
            $yPayments = min($addressesEndY, $yPayments);
            $page->drawLine(25, ($top - 25), 25, $yPayments);
            $page->drawLine(570, ($top - 25), 570, $yPayments);
            $page->drawLine(25, $yPayments, 570, $yPayments);

            $this->y = $yPayments - 15;
        } else {
            $topMargin = 15;
            $methodStartY = $this->y;
            $this->y -= 15;

            foreach (str_split($shippingMethod, 45) as $_value) {
                $page->drawText(strip_tags(trim($_value)), 285, $this->y, self::ENCODING_TYPE);
                $this->y -= 15;
            }

            $yShipments = $this->y;
            $totalShippingChargesText = "(" . 'Total Shipping Charges' . " "
                . $order->formatPriceTxt($order->getShippingAmount()) . ")";

            $page->drawText($totalShippingChargesText, 285, $yShipments - $topMargin, self::ENCODING_TYPE);
            $yShipments -= $topMargin + 10;
            $currentY = min($yPayments, $yShipments);

            // replacement of Shipments-Payments rectangle block
            $page->drawLine(25, $methodStartY, 25, $currentY); //left
            $page->drawLine(25, $currentY, 570, $currentY); //bottom
            $page->drawLine(570, $currentY, 570, $methodStartY); //right
            $this->y = $currentY;
            $this->y -= 15;
        }
    }

    /**
     * Insert title and number for concrete document type
     *
     * @param \Zend_Pdf_Page $page
     * @param string $text
     *
     * @return void
     */
    public function insertDocumentNumber(\Zend_Pdf_Page $page, $text)
    {
        $page->setFillColor(new \Zend_Pdf_Color_GrayScale(0));
        $this->_setFontRegular($page, 10);
        $docHeader = $this->getDocHeaderCoordinates();
        $page->drawText($text, 35, $docHeader[1] - 15, self::ENCODING_TYPE);
    }

    /**
     * Calculate address height.
     *
     * @param array $address
     *
     * @return int Height
     */
    protected function _calcAddressHeight($address)
    {
        $y = 0;
        foreach ($address as $value) {
         //   echo $value;
            if ($value !== '') {
                $text = array();
                foreach (str_split($value, 55) as $_value) {
                    $text[] = $_value;
                }
                foreach ($text as $part) {
                    $y += 15;
                }
            }
        }
        return $y;
    }
}
