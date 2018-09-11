<?php
namespace Temando\Temando\Model\Origin\Source;

class IsActive implements \Magento\Framework\Data\OptionSourceInterface
{
    /**
     * Temando Origin.
     *
     * @var \Temando\Temando\Model\Origin
     */
    protected $origin;

    /**
     * Constructor
     *
     * @param \Temando\Temando\Model\Origin $origin
     */
    public function __construct(\Temando\Temando\Model\Origin $origin)
    {
        $this->origin = $origin;
    }

    /**
     * Get options
     *
     * @param bool
     *
     * @return array
     */
    public function toOptionArray($form = false)
    {
        $formOptions = array();
        $options[] = ['label' => '', 'value' => ''];
        $availableOptions = $this->origin->getAvailableStatuses();
        foreach ($availableOptions as $key => $value) {
            $options[] = [
                'label' => $value,
                'value' => $key,
            ];
            $formOptions[$key] = $value;
        }
        if ($form) {
            return $formOptions;
        }
        return $options;
    }
}
