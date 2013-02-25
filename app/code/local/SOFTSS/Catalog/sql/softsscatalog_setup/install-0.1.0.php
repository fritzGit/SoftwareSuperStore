<?php

$this->startSetup();

$this->addAttribute(Mage_Catalog_Model_Product::ENTITY, 'service_pack', array(
	 'label' => 'Service Pack',
	 'note' => 'Service pack version',
	 'required' => 0,
	 'unique' => 0,
	 'is_configurable' => 0
));

$this->addAttribute(Mage_Catalog_Model_Product::ENTITY, 'language_version', array(
	 'label' => 'Language Version',
	 'required' => 0,
	 'unique' => 0,
	 'is_configurable' => 0
));

$this->addAttribute(Mage_Catalog_Model_Product::ENTITY, 'pc_type', array(
	 'label' => 'PC Type',
	 'default' => "All PC's / Notebooks",
	 'required' => 0,
	 'unique' => 0,
	 'is_configurable' => 0
));

$this->endSetup();