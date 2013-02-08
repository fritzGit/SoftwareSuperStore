<?php
/**
 * Created by
 * User: jgalvez
 * @author juan galvez
 * @pagage SOFTSS
 * @subpackage SOFTSS_TeaserEditor
 */
class SOFTSS_TeaserEditor_Helper_Data extends Mage_Core_Helper_Abstract
{
    /*
     * Get Image path for teasers
     */
    public function getTeaserImageDir() {
        return 'softss_teasers' . DS;
    }

    /**
     * Get Image URL for teaser
     *
     * @param SOFTSS_TeaserEditor_Model_Teaser $_oTeaser
     */
    public function getImageUrl(SOFTSS_TeaserEditor_Model_Teaser $_oTeaser) {
        return Mage::getBaseUrl('media').$this->getTeaserImageDir().$_oTeaser->getImage();
    }

    /*
     * Get the textual name of the teaser group
     *
     * @param teaser group ID
     */
    public function getTeaserGroupName($id){
        $teaserGroupNames = array(1=>'Homepage Left Sidebar', 2=>'Homepage Bottom', 3=>'Homepage Right Sidebar', 4=>'Homepage Slider', 5=>'Category');

        return $teaserGroupNames[$id];
    }
}