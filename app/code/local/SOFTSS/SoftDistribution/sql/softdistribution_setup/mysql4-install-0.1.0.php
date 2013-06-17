<?php

$installer = $this;

$installer->startSetup();

$installer->run("

-- DROP TABLE IF EXISTS {$this->getTable('softss_softdistribution')};
CREATE TABLE {$this->getTable('softss_softdistribution')} (
  `id` int(11) unsigned NOT NULL auto_increment,
  `productpid` varchar(255) NOT NULL default '',
  `itemid` varchar(255) NOT NULL default '',
  `downloadlink` varchar(255) NOT NULL default '',
  `transactionid` varchar(255) NOT NULL default '',
  `resellertransid` varchar(255) NOT NULL default '',
  `orderref` varchar(255) NOT NULL default '',
  `customerref` varchar(255) NOT NULL default '',
  `serialnumber` text NOT NULL default '',
  `additionalinfo` text NOT NULL default '',
  `created_time` datetime NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
");


$installer->endSetup();
