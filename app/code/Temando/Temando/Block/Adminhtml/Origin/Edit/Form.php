<?php
namespace Temando\Temando\Block\Adminhtml\Origin\Edit;

/**
 * Adminhtml blog post edit form
 */
class Form extends \Magento\Backend\Block\Widget\Form\Generic
{
    /**
     * Prepare form
     *
     * @return $this
     */
    protected function _prepareForm()
    {
        $form = $this->_formFactory->create(
            [
                'data' => [
                    'id' => 'edit_form',
                    'action' => $this->getData('action'),
                    'method' => 'post',
                    'enctype' => 'multipart/form-data'
                ]
            ]
        );
//        $country->setAfterElementHtml("
//            <script type=\"text/javascript\">
//                    require([
//                    'jquery',
//                    'mage/template',
//                    'jquery/ui',
//                    'mage/translate'
//                ],
//                function($, mageTemplate) {
//                   $('#edit_form').on('change', '#origin_country', function(event){
//                        $.ajax({
//                               url : '". $this->getUrl('temando/*/regionlist') . "country/' +  $('#country').val(),
//                                type: 'get',
//                                dataType: 'json',
//                               showLoader:true,
//                               success: function(data){
//                                    $('#origin_region').empty();
//                                    $('#origin_region').append(data.htmlconent);
//                               }
//                            });
//                   })
//                }
//
//            );
//            </script>"
//        );


        $form->setUseContainer(true);
        $this->setForm($form);

        return parent::_prepareForm();
    }
}
