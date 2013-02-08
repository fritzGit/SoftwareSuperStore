<?php
/**
 * User: jgalvez
 */
class SOFTSS_TeaserEditor_Block_Adminhtml_Teasergrid_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    /**
     * Set grid params
     *
     */
    public function __construct()
    {
        parent::__construct();
        $this->setId('teaser_grid');
        $this->setDefaultSort('id');
        $this->setDefaultDir('asc');
        $this->setSaveParametersInSession(true);
    }

    /**
     * Prepare collection
     *
     * @return Mage_Adminhtml_Block_Teaser_Grid
     */
    protected function _prepareCollection()
    {
        $collection = Mage::getModel('softssteasereditor/teaser')->getCollection()->addTeaserGroupName()->addCategoryName();

        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        $this->addColumn('id', array(
            'header'    => Mage::helper('softssteasereditor')->__('ID'),
            'align'     =>'center',
            'width'     => '50px',
            'index'     => 'id',
            'filter_index' => 'main_table.store_id'

        ));

        $this->addColumn('store_id', array(
            'header'    => Mage::helper('softssteasereditor')->__('Store ID'),
            'align'     =>'center',
            'width'     => '50px',
            'index'     => 'store_id',
            'filter_index' => 'main_table.store_id'
        ));

        $this->addColumn('teaser_group_name', array(
            'header'    => Mage::helper('softssteasereditor')->__('Teaser Group'),
            'align'     =>'left',
            'index'     => 'teaser_group_name',
        ));

        $this->addColumn('category_name', array(
            'header'    => Mage::helper('softssteasereditor')->__('Category'),
            'align'     =>'left',
            'index'     => 'category_name',
            'filter_index' => 'main_table.category_name'
        ));
        /*
        $this->addColumn('image', array(
            'header'    => Mage::helper('softssteasereditor')->__('Image Name'),
            'align'     => 'left',
            'index'     => 'image',
        ));
*/
        $this->addColumn('image', array(
            'header'    => Mage::helper('softssteasereditor')->__('Image'),
            'align'     =>'center',
            'width'     => '50px',
            'height'    => '100px',
            'filter'    => false,
            'sortable'  => false,
            'renderer'  => 'softssteasereditor/adminhtml_teasergrid_widget_renderer',
        ));


        $this->addColumn('link', array(
            'header'    => Mage::helper('softssteasereditor')->__('Link'),
            'align'     =>'left',
            'index'     => 'link',
        ));

        return parent::_prepareColumns();
    }

    public function getRowUrl($row)
    {
        return $this->getUrl('*/*/edit', array('id' => $row->getId()));
    }

}
