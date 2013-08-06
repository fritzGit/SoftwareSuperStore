<?php

$installer = $this;

$installer->startSetup();

$installer->run("

DROP TABLE IF EXISTS {$this->getTable('afterbuycheckout')};
CREATE TABLE {$this->getTable('afterbuycheckout')} (
  `afterbuycheckout_id` int(11) unsigned NOT NULL auto_increment,
  `user_name` varchar(255) NOT NULL default '',
  `user_pass` varchar(255) NOT NULL default '',
  `partner_id` varchar(255) NOT NULL default '',
  `partner_pass` varchar(255) NOT NULL default '',
  `status` smallint(6) NOT NULL default '0',
  `created_time` datetime NULL,
  `update_time` datetime NULL,
  PRIMARY KEY (`afterbuycheckout_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

    ");

$installer->endSetup(); 