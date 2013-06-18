<?php

class SOFTSS_Softd_Block_Adminhtml_Softd_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
  public function __construct()
  {
      parent::__construct();
      $this->setId('softdGrid');
      $this->setDefaultSort('id');
      $this->setDefaultDir('ASC');
      $this->setSaveParametersInSession(true);
  }

  protected function _prepareCollection()
  {
      $collection = Mage::getModel('softd/softd')->getCollection();
      $this->setCollection($collection);
      return parent::_prepareCollection();
  }

  protected function _prepareColumns()
  {
      $this->addColumn('id', array(
          'header'    => Mage::helper('softd')->__('ID'),
          'align'     =>'right',
          'width'     => '50px',
          'index'     => 'id',
      ));

      $this->addColumn('productpid', array(
            'header' => Mage::helper('softd')->__('Product ID'),
            'align' => 'left',
            'index' => 'productpid',
            'width'     => '120px',
        ));

        $this->addColumn('itemid', array(
            'header' => Mage::helper('softd')->__('Item ID'),
            'align' => 'left',
            'index' => 'itemid',
            'width'     => '120px',

        ));

        $this->addColumn('downloadlink', array(
            'header' => Mage::helper('softd')->__('Download Link'),
            'align' => 'left',
            'index' => 'downloadlink',
        ));

        $this->addColumn('transactionid', array(
            'header' => Mage::helper('softd')->__('Transaction ID'),
            'align' => 'left',
            'index' => 'transactionid',
        ));

        $this->addColumn('resellertransid', array(
            'header' => Mage::helper('softd')->__('Reseller Transaction ID'),
            'align' => 'left',
            'index' => 'resellertransid',
        ));

        $this->addColumn('orderref', array(
            'header' => Mage::helper('softd')->__('Order ID'),
            'align' => 'left',
            'index' => 'orderref',
            'width'     => '120px',

        ));
        
        $this->addColumn('customerref', array(
            'header' => Mage::helper('softd')->__('Customer Ref'),
            'align' => 'left',
            'index' => 'customerref',
            'width'     => '120px',

        ));

        $this->addColumn('serialnumber', array(
            'header' => Mage::helper('softd')->__('Serian Number'),
            'align' => 'left',
            'index' => 'serialnumber',
        ));
        
        $this->addColumn('additionalinfo', array(
            'header' => Mage::helper('softd')->__('Additional Info'),
            'align' => 'left',
            'index' => 'additionalinfo',
        ));

        $this->addColumn('created_time', array(
            'header' => Mage::helper('softd')->__('Created'),
            'align'     => 'left',
            'width'     => '120px',
            'type'      => 'date',
            'default'   => '--',
            'index' => 'created_time',
        ));

		
	$this->addExportType('*/*/exportCsv', Mage::helper('softd')->__('CSV'));
	$this->addExportType('*/*/exportXml', Mage::helper('softd')->__('XML'));
	  
      return parent::_prepareColumns();
  }

    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('id');
        $this->getMassactionBlock()->setFormFieldName('softd');

        $this->getMassactionBlock()->addItem('delete', array(
             'label'    => Mage::helper('softd')->__('Delete'),
             'url'      => $this->getUrl('*/*/massDelete'),
             'confirm'  => Mage::helper('softd')->__('Are you sure?')
        ));

        return $this;
    }

  public function getRowUrl($row)
  {
      return;
      //return $this->getUrl('*/*/edit', array('id' => $row->getId()));
  }

}