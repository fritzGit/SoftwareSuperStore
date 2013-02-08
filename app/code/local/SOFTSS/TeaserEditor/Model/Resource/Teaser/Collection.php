<?php
/**
 * Created by
 * User: jgalvez
 * @author juan galvez
 * @package SOFTSS
 * @subpackage SOFTSS_TeaserEditor
 */
class SOFTSS_TeaserEditor_Model_Resource_Teaser_Collection extends Mage_Core_Model_Resource_Db_Collection_Abstract
{
    /**
     * Resource initialization
     */
    protected function _construct()
    {
        $this->_init('softssteasereditor/teaser');
    }

    /**
     *
     * @param int $iTeaserGroupId
     * @return SOFTSS_Teaser_Model_Resource_Teaser_Collection
     */
    public function addTeaserGroupFilter($iTeaserGroupId)
    {
        $oSelect  = $this->getSelect();
        $oAdapter = $oSelect->getAdapter();
        $storeId = (int) Mage::app()->getStore()->getStoreId();

        $aJoinCondition = array(
            'group.teaser_group_id = main_table.teaser_group_id',
            $oAdapter->quoteInto('group.teaser_group_id = ?', $iTeaserGroupId)
        );
        $oSelect->join(
            array('group' => $this->getTable('softssteasereditor/teaser_group')),
            implode(' AND ', $aJoinCondition),
            array('teaser_group_id','teaser_group_name')
        );
        $oSelect->where('store_id = ?', $storeId)->order('sort', 'ASC');
        return $this;
    }

    /**
     *
     * @return SOFTSS_Teaser_Model_Resource_Teaser_Collection
     */
    public function addTeaserGroupName()
    {
        $oSelect  = $this->getSelect();
        $aJoinCondition = array(
            'group.teaser_group_id = main_table.teaser_group_id',
        );
        $oSelect->join(
            array('group' => $this->getTable('softssteasereditor/teaser_group')),
            implode(' AND ', $aJoinCondition),
            array('teaser_group_id','teaser_group_name')
        );
        $oSelect->order('sort', 'ASC');
        return $this;

    }

    /**
     *
     * @return SOFTSS_Teaser_Model_Resource_Teaser_Collection
     */
    public function addCategoryName()
    {
        $oSelect  = $this->getSelect()->group('id');
        $aJoinCondition = array(
            'main_table.category_id = catalog.entity_id',
        );
        $oSelect->joinLeft(
            array('catalog' => 'catalog_category_entity_varchar'),
            implode(' AND ', $aJoinCondition),
            array('value as category_name')
        );
        $oSelect->limit(3);
        $oSelect->order('sort', 'ASC');
        return $this;
    }


}