<?php
namespace Temando\Temando\Controller\Adminhtml\Pickup;

use Magento\Backend\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;

class Index extends \Magento\Backend\App\Action
{

    /**
     * Result Page Factory.
     *
     * @var PageFactory
     */
    protected $resultPageFactory;

    /**
     * Construct.
     *
     * @param Context $context
     * @param PageFactory $resultPageFactory
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory
    ) {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
    }

    /**
     * Index action
     *
     * @return \Magento\Backend\Model\View\Result\Page
     */
    public function execute()
    {
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('Temando_Temando::pickup');
        //$resultPage->addBreadcrumb(__('Blog Posts'), __('Blog Posts'));
        //$resultPage->addBreadcrumb(__('Manage Origins'), __('Manage Shipping Origins'));
        $resultPage->getConfig()->getTitle()->prepend(__('Store Pickups'));
        return $resultPage;
    }

    /**
     * Is the user allowed to view the blog post grid.
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Temando_Temando::temando_pickups');
    }
}
