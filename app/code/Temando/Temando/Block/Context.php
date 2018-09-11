<?php

namespace Temando\Temando\Block;

class Context extends \Magento\Framework\View\Element\Template\Context
{
    /**
     * System Config.
     *
     * @var \Magestore\Storepickup\Model\SystemConfig
     */
    protected $_systemConfig;

    /**
     * Image Helper.
     *
     * @var \Magestore\Storepickup\Helper\Image
     */
    protected $_imageHelper;

    /**
     * Collection Factory.
     *
     * @var \Temando\Temando\Model\ResourceModel\Origin\CollectionFactory
     */
    protected $_originCollectionFactory;

    /**
     * Collection Factory.
     *
     * @var \Magestore\Storepickup\Model\ResourceModel\Tag\CollectionFactory
     */
    protected $_tagCollectionFactory;

    /**
     * Core registry.
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry = null;

    /**
     * Context constructor.
     *
     * @param \Magento\Framework\App\RequestInterface                 $request
     * @param \Magento\Framework\View\LayoutInterface                 $layout
     * @param \Magento\Framework\Event\ManagerInterface               $eventManager
     * @param \Magento\Framework\UrlInterface                         $urlBuilder
     * @param \Magento\Framework\App\CacheInterface                   $cache
     * @param \Magento\Framework\View\DesignInterface                 $design
     * @param \Magento\Framework\Session\SessionManagerInterface      $session
     * @param \Magento\Framework\Session\SidResolverInterface         $sidResolver
     * @param \Magento\Framework\App\Config\ScopeConfigInterface      $scopeConfig
     * @param \Magento\Framework\View\Asset\Repository                $assetRepo
     * @param \Magento\Framework\View\ConfigInterface                 $viewConfig
     * @param \Magento\Framework\App\Cache\StateInterface             $cacheState
     * @param \Psr\Log\LoggerInterface                                $logger
     * @param \Magento\Framework\Escaper                              $escaper
     * @param \Magento\Framework\Filter\FilterManager                 $filterManager
     * @param \Magento\Framework\Stdlib\DateTime\TimezoneInterface    $localeDate
     * @param \Magento\Framework\Translate\Inline\StateInterface      $inlineTranslation
     * @param \Magento\Framework\Filesystem                           $filesystem
     * @param \Magento\Framework\View\FileSystem                      $viewFileSystem
     * @param \Magento\Framework\View\TemplateEnginePool              $enginePool
     * @param \Magento\Framework\App\State                            $appState
     * @param \Magento\Store\Model\StoreManagerInterface              $storeManager
     * @param \Magento\Framework\View\Page\Config                     $pageConfig
     * @param \Magento\Framework\View\Element\Template\File\Resolver  $resolver
     * @param \Magento\Framework\View\Element\Template\File\Validator $validator
     */
    public function __construct(
        \Magento\Framework\App\RequestInterface $request,
        \Magento\Framework\View\LayoutInterface $layout,
        \Magento\Framework\Event\ManagerInterface $eventManager,
        \Magento\Framework\UrlInterface $urlBuilder,
        \Magento\Framework\App\CacheInterface $cache,
        \Magento\Framework\View\DesignInterface $design,
        \Magento\Framework\Session\SessionManagerInterface $session,
        \Magento\Framework\Session\SidResolverInterface $sidResolver,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Framework\View\Asset\Repository $assetRepo,
        \Magento\Framework\View\ConfigInterface $viewConfig,
        \Magento\Framework\App\Cache\StateInterface $cacheState,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\Escaper $escaper,
        \Magento\Framework\Filter\FilterManager $filterManager,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $localeDate,
        \Magento\Framework\Translate\Inline\StateInterface $inlineTranslation,
        \Magento\Framework\Filesystem $filesystem,
        \Magento\Framework\View\FileSystem $viewFileSystem,
        \Magento\Framework\View\TemplateEnginePool $enginePool,
        \Magento\Framework\App\State $appState,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\View\Page\Config $pageConfig,
        \Magento\Framework\View\Element\Template\File\Resolver $resolver,
        \Magento\Framework\View\Element\Template\File\Validator $validator,
        \Magestore\Storepickup\Model\SystemConfig $systemConfig,
        \Magestore\Storepickup\Helper\Image $imageHelper,
        \Temando\Temando\Model\ResourceModel\Origin\CollectionFactory $originCollectionFactory,
        \Magestore\Storepickup\Model\ResourceModel\Tag\CollectionFactory $tagCollectionFactory,
        \Magento\Framework\Registry $coreRegistry
    ) {
        parent::__construct(
            $request,
            $layout,
            $eventManager,
            $urlBuilder,
            $cache,
            $design,
            $session,
            $sidResolver,
            $scopeConfig,
            $assetRepo,
            $viewConfig,
            $cacheState,
            $logger,
            $escaper,
            $filterManager,
            $localeDate,
            $inlineTranslation,
            $filesystem,
            $viewFileSystem,
            $enginePool,
            $appState,
            $storeManager,
            $pageConfig,
            $resolver,
            $validator
        );

        $this->_systemConfig = $systemConfig;
        $this->_imageHelper = $imageHelper;
        $this->_originCollectionFactory = $originCollectionFactory;
        $this->_tagCollectionFactory = $tagCollectionFactory;
        $this->_coreRegistry = $coreRegistry;
    }

    /**
     * Get System Config.
     *
     * @return \Magestore\Storepickup\Model\SystemConfig
     */
    public function getSystemConfig()
    {
        return $this->_systemConfig;
    }

    /**
     * Get Image Helper.
     *
     * @return \Magestore\Storepickup\Helper\Image
     */
    public function getImageHelper()
    {
        return $this->_imageHelper;
    }

    /**
     * Get Origin Collection Factory.
     *
     * @return \Temando\Temando\Model\ResourceModel\Origin\CollectionFactory
     */
    public function getOriginCollectionFactory()
    {
        return $this->_originCollectionFactory;
    }

    /**
     * Get Tag Collection Factory.
     *
     * @return \Magestore\Storepickup\Model\ResourceModel\Tag\CollectionFactory
     */
    public function getTagCollectionFactory()
    {
        return $this->_tagCollectionFactory;
    }

    /**
     * Get Core Registry.
     *
     * @return \Magento\Framework\Registry
     */
    public function getCoreRegistry()
    {
        return $this->_coreRegistry;
    }
}
