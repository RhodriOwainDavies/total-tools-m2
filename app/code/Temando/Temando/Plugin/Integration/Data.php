<?php
namespace Temando\Temando\Plugin\Integration;

class Data
{
    public function beforeMapResources(\Magento\Integration\Helper\Data $helper, array $resources)
    {
        $restricted = $this->getRestrictedIds();
        foreach ($resources as $key => $resource) {
            if (in_array($resource['id'], $restricted)) {
                unset($resources[$key]);
            }
        }
        return [$resources];
    }
    
    //list in this method all the ids of the acl's you don't want to show
    //if you don't want to had-code them you can read them from a config file - but you have to build that yourself.
    protected function getRestrictedIds()
    {
        return ['Magestore_Storepickup::store', 'Magestore_Storepickup::tag', 'Magestore_Storepickup::guide'];
    }
}
