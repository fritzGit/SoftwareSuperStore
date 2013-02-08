<?php
/**
 * Created by jgalvez
 */
class SOFTSS_Topseller_Block_Adminhtml_Topsellergrid_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    /**
     * Set grid params
     */
    public function __construct()
    {
        parent::__construct();
        $this->setId('topsellergrid_grid');
        $this->setDefaultSort('Position');
        $this->setDefaultDir('asc');
        $this->setSaveParametersInSession(true);
    }

    /**
     * Prepare collection
     *
     * @return Mage_Adminhtml_Block_Topseller_Grid
     */
    protected function _prepareCollection()
    {
        $collection = Mage::getModel('softsstopseller/homepagetopseller')->getCollection();
        $this->setCollection($collection);

        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        $this->addColumn('id', array(
            'header'    => Mage::helper('softsstopseller')->__('ID'),
            'align'     =>'center',
            'width'     => '50px',
            'index'     => 'id',
            'filter_index' => 'main_table.id'
        ));

        $this->addColumn('store_id', array(
            'header'    => Mage::helper('softsstopseller')->__('Store ID'),
            'align'     =>'center',
            'width'     => '50px',
            'index'     => 'store_id',
            'filter_index' => 'main_table.store_id'
        ));

        $this->addColumn('position', array(
            'header'    => Mage::helper('softsstopseller')->__('Position'),
            'align'     =>'center',
            'width'     => '50px',
            'index'     => 'position',
            'filter_index' => 'main_table.position'
        ));

        $this->addColumn('product_id', array(
            'header'    => Mage::helper('softsstopseller')->__('Product ID'),
            'align'     =>'left',
            'index'     => 'product_id',
            'filter_index' => 'main_table.product_id'
        ));

        return parent::_prepareColumns();
    }

    public function getRowUrl($row)
    {
        return $this->getUrl('*/*/edit', array('id' => $row->getId()));
    }

}
