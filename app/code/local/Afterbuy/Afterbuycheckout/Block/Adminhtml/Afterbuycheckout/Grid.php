<?php

class Afterbuy_Afterbuycheckout_Block_Adminhtml_Afterbuycheckout_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
  public function __construct()
  {
      parent::__construct();
      $this->setId('afterbuycheckoutGrid');
      $this->setDefaultSort('afterbuycheckout_id');
      $this->setDefaultDir('ASC');
      $this->setSaveParametersInSession(true);
  }

  protected function _prepareCollection()
  {
      $collection = Mage::getModel('afterbuycheckout/afterbuycheckout')->getCollection();
      $this->setCollection($collection);
      return parent::_prepareCollection();
  }

  protected function _prepareColumns()
  {
      $this->addColumn('afterbuycheckout_id', array(
          'header'    => Mage::helper('afterbuycheckout')->__('ID'),
          'align'     =>'right',
          'width'     => '5px',
          'index'     => 'afterbuycheckout_id',
      ));

      $this->addColumn('user_name', array(
          'header'    => Mage::helper('afterbuycheckout')->__('User Name'),
          'align'     =>'left',
      	  'width'     => '40px',
          'index'     => 'user_name',
      ));
           
      $this->addColumn('partner_id', array(
          'header'    => Mage::helper('afterbuycheckout')->__('Partner ID'),
          'align'     =>'left',
      	  'width'     => '30px',
          'index'     => 'partner_id',
      ));
      $this->addColumn('partner_pass', array(
          'header'    => Mage::helper('afterbuycheckout')->__('Partner Password'),
          'align'     =>'left',
      	  'width'     => '40px',
          'index'     => 'partner_pass',
      ));
	  
      $this->addColumn('status', array(
          'header'    => Mage::helper('afterbuycheckout')->__('Status'),
          'align'     => 'left',
          'width'     => '30px',
          'index'     => 'status',
          'type'      => 'options',
          'options'   => array(
              1 => 'Enabled',
              2 => 'Disabled',
          ),
      ));
	  
	  $this->addColumn('feedback', array(
          'header'    => Mage::helper('afterbuycheckout')->__('Feedback'),
          'align'     => 'left',
          'width'     => '120px',
          'index'     => 'feedback',
          'type'      => 'options',
          'options'   => array(
              0 => 'Feedbackdatum setzen, KEINE automatische Erstkontaktmail versenden',
              1 => 'Feedbackdatum NICHT setzen, automatische Erstkontaktmail versenden',
              2 => 'Feedbackdatum setzen, automatische Erstkontaktmail versenden',
          ),
      ));
	  
	  $this->addColumn('artikelerkennung', array(
          'header'    => Mage::helper('afterbuycheckout')->__('Artikelerkennung'),
          'align'     => 'left',
          'width'     => '40px',
          'index'     => 'artikelerkennung',
          'type'      => 'options',
          'options'   => array(
              0 => 'Product ID',
              1 => 'Artikelnummer',
              2 => 'EAN',
          ),
      ));

      $this->addColumn('versand', array(
          'header'    => Mage::helper('afterbuycheckout')->__('Versand'),
          'align'     => 'left',
          'width'     => '80px',
          'index'     => 'versand',
          'type'      => 'options',
          'options'   => array(
              0 => 'Versandermittlung durch Afterbuy',
              1 => 'Versandberechnung durch Magento',
              
          ),
      ));
	  
	  $this->addColumn('check_doppelbestellung', array(
          'header'    => Mage::helper('afterbuycheckout')->__('Check Doppelbestellung'),
          'align'     => 'left',
          'width'     => '50px',
          'width'     => '50px',
          'index'     => 'check_doppelbestellung',
          'type'      => 'options',
          'options'   => array(
              0 => 'deaktiviert',
              1 => 'aktiviert',
              
          ),
      ));
      $this->addColumn('kundenerkennung', array(
          'header'    => Mage::helper('afterbuycheckout')->__('Kundenerkennungen'),
          'align'     => 'left',
          'width'     => '80px',
          'index'     => 'kundenerkennung',
          'type'      => 'options',
          'options'   => array(
              0 => 'Standard EbayName',
              1 => 'Email',
              2 => 'EKNummer',
             
              
          ),
      ));
      $this->addColumn('action',
            array(
                'header'    =>  Mage::helper('afterbuycheckout')->__('Action'),
                'width'     => '80',
                'type'      => 'action',
                'getter'    => 'getId',
                'actions'   => array(
                    array(
                        'caption'   => Mage::helper('afterbuycheckout')->__('Edit'),
                        'url'       => array('base'=> '*/*/edit'),
                        'field'     => 'id'
                    )
                ),
                'filter'    => false,
                'sortable'  => false,
                'index'     => 'stores',
                'is_system' => true,
        ));
		

	  
      return parent::_prepareColumns();
  }

    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('afterbuycheckout_id');
        $this->getMassactionBlock()->setFormFieldName('afterbuycheckout');

        $statuses = Mage::getSingleton('afterbuycheckout/status')->getOptionArray();

        array_unshift($statuses, array('label'=>'', 'value'=>''));
        $this->getMassactionBlock()->addItem('status', array(
             'label'=> Mage::helper('afterbuycheckout')->__('Change status'),
             'url'  => $this->getUrl('*/*/massStatus', array('_current'=>true)),
             'additional' => array(
                    'visibility' => array(
                         'name' => 'status',
                         'type' => 'select',
                         'class' => 'required-entry',
                         'label' => Mage::helper('afterbuycheckout')->__('Status'),
                         'values' => $statuses
                     )
             )
        ));
        return $this;
    }

  public function getRowUrl($row)
  {
      return $this->getUrl('*/*/edit', array('id' => $row->getId()));
  }

}