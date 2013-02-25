<?php
/**
 * Description of RelationTypes
 *
 * @author j.galvez@pcfrits.de
 */
class SOFTSS_Checkout_Model_System_Config_Source_RelationTypes
{
    public function toOptionArray()
    {
        $options    = array(
            array(
                'value' => 'crossell',
                'label' => Mage::helper('checkout')->__('Cross-sell')
            ),
            array(
                'value' => 'related',
                'label' => Mage::helper('checkout')->__('Related')
            ),
            array(
                'value' => 'upsell',
                'label' => Mage::helper('checkout')->__('Upsell')
            ),
            array(
                'value' => 'promotional',
                'label' => Mage::helper('checkout')->__('Promotional')
            )
        );
        return $options;
    }
}

?>
