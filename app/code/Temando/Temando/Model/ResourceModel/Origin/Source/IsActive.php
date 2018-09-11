<?php
namespace Temando\Temando\Model\Post\Source;

class IsActive implements \Magento\Framework\Data\OptionSourceInterface
{
    /**
     * Origin.
     *
     * @var \Temando\Temando\Model\Origin
     */
    protected $origin;

    /**
     * Constructor
     *
     * @param \Temando\Temando\Model\Origin $post
     */
    public function __construct(\Temando\Temando\Model\Origin $origin)
    {
        $this->origin = $origin;
    }

    /**
     * Get options
     *
     * @return array
     */
    public function toOptionArray()
    {
        $options[] = ['label' => '', 'value' => ''];
        $availableOptions = $this->post->getAvailableStatuses();
        foreach ($availableOptions as $key => $value) {
            $options[] = [
                'label' => $value,
                'value' => $key,
            ];
        }
        return $options;
    }
}
