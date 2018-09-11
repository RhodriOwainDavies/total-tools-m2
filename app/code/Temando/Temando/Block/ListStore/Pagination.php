<?php

namespace Temando\Temando\Block\ListStore;

class Pagination extends \Magestore\Storepickup\Block\AbstractBlock
{
    const FIRST_PAGE = 1;

    /**
     * Template.
     *
     * @var string
     */
    protected $_template = 'Magestore_Storepickup::liststore/pagination.phtml';

    /**
     * Min Page.
     *
     * @var int
     */
    protected $_minPage;

    /**
     * Max Page.
     *
     * @var int
     */
    protected $_maxPage;

    /**
     * Data Collection.
     *
     * @var \Magento\Framework\Data\Collection
     */
    protected $_collection;

    /**
     * Block constructor.
     *
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param array                                            $data
     */
    public function __construct(
        \Magestore\Storepickup\Block\Context $context,
        \Magento\Framework\Data\Collection $collection = null,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->_collection = $collection;
    }

    /**
     * Get collection.
     *
     * @return \Magento\Framework\Data\Collection
     */
    public function getCollection()
    {
        return $this->_collection;
    }

    /**
     * Set collection for pagination.
     *
     * @param \Magento\Framework\Data\Collection $collection
     */
    public function setCollection(\Magento\Framework\Data\Collection $collection)
    {
        $this->_collection = $collection;
    }

    /**
     * Internal constructor, that is called from real constructor.
     */
    protected function _construct()
    {
        parent::_construct();

        if (!$this->hasData('range')) {
            $this->setData('range', 5);
        }
    }

    /**
     * Get Min Page.
     *
     * @return mixed
     */
    public function getMinPage()
    {
        return $this->_minPage;
    }

    /**
     * Get Min Page.
     *
     * @param mixed $minPage
     */
    public function setMinPage($minPage)
    {
        $this->_minPage = $minPage;
    }

    /**
     * Get Max Page.
     *
     * @return mixed
     */
    public function getMaxPage()
    {
        return $this->_maxPage;
    }

    /**
     * Set Max Page Size.
     *
     * @param mixed $maxPage
     */
    public function setMaxPage($maxPage)
    {
        $this->_maxPage = $maxPage;
    }

    /**
     * Get Page Size.
     *
     * @return mixed
     */
    public function getPageSize()
    {
        return $this->getCollection()->getPageSize();
    }

    /**
     * Get Current Page.
     *
     * @return mixed
     */
    public function getCurPage()
    {
        return $this->getCollection()->getCurPage();
    }

    /**
     * Check has next page.
     *
     * @return bool
     */
    public function hasNextPage()
    {
        return $this->getCurPage() < $this->getTotalPage();
    }

    /**
     * Check has previous page.
     *
     * @return bool
     */
    public function hasPrevPage()
    {
        return $this->getCurPage() > self::FIRST_PAGE;
    }

    /**
     * Get Next Page.
     *
     * @return mixed
     */
    public function getNextPage()
    {
        return $this->hasNextPage() ? $this->getCurPage() + 1 : $this->getTotalPage();
    }

    public function getPrevPage()
    {
        return $this->hasPrevPage() ? $this->getCurPage() - 1 : $this->getTotalPage();
    }

    /**
     * Get Last Page Number.
     *
     * @return mixed
     */
    public function getTotalPage()
    {
        return $this->getCollection()->getLastPageNumber();
    }

    /**
     * Check current page is the first page.
     *
     * @param $page
     *
     * @return bool
     */
    public function currentIsFirstPage()
    {
        return $this->getCurPage() == self::FIRST_PAGE;
    }

    /**
     * Check current page is last page.
     *
     * @param $page
     *
     * @return bool
     */
    public function currentIsLastPage()
    {
        return $this->getCurPage() == $this->getTotalPage();
    }

    /**
     * Prepare Pagination.
     *
     * @return $this
     */
    protected function _preparePagination()
    {
        $middle = ceil($this->getRange() / 2);
        $totalPage = $this->getTotalPage();

        if ($totalPage < $this->getRange()) {
            $this->setMinPage(self::FIRST_PAGE);
            $this->setMaxPage($totalPage);
        } else {
            $this->setMinPage($this->getCurPage() - $middle + 1);
            $this->setMaxPage($this->getCurPage() + $middle - 1);

            if ($this->getMinPage() < self::FIRST_PAGE) {
                $this->setMinPage(self::FIRST_PAGE);
                $this->setMaxPage($this->getRange());
            } elseif ($this->getMaxPage() > $totalPage) {
                $this->setMinPage($totalPage - $this->getRange() + 1);
                $this->setMaxPage($totalPage);
            }
        }

        return $this;
    }

    /**
     * Prepare Layout.
     *
     * @return $this
     */
    protected function _prepareLayout()
    {
        $this->_preparePagination();

        return $this;
    }

    /**
     * Set collection page size.
     *
     * @param int $size
     *
     * @return $this
     */
    public function setPageSize($size)
    {
        $this->getCollection()->setPageSize($size);

        return $this;
    }

    /**
     * Set current page.
     *
     * @param int $page
     *
     * @return $this
     */
    public function setCurPage($page)
    {
        $this->getCollection()->setCurPage($page);

        return $this;
    }
}
