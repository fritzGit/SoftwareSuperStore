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
  `feedback` SMALLINT( 6 ) NOT NULL DEFAULT '0',
  `artikelerkennung` SMALLINT( 6 ) NOT NULL DEFAULT '0',
  `kundenerkennung` SMALLINT( 6 ) NOT NULL DEFAULT '1',
  `versand` SMALLINT( 6 ) NOT NULL DEFAULT '1',
  `created_time` datetime NULL,
  `update_time` datetime NULL,
  PRIMARY KEY (`afterbuycheckout_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
DROP TABLE IF EXISTS {$this->getTable('afterbuyorderdata')};
CREATE TABLE {$this->getTable('afterbuyorderdata')} (
  `afterbuyorderdata_id` int(11) unsigned NOT NULL auto_increment,
  `shoporderid` varchar(255) NOT NULL default '',
  `kundennr` varchar(255) NOT NULL default '',
  `aid` varchar(255) NOT NULL default '',
  `uid` varchar(255) NOT NULL default '',
  `update_time` datetime NULL,
  PRIMARY KEY (`afterbuyorderdata_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
    ");

$installer->endSetup(); 