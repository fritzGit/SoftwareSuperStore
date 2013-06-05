<?php
echo "AB Cron<br>";
ini_set('display_errors',1);
require_once '/var/www/vhosts/softwaresuperstore.co.uk/htdocs/app/Mage.php';

$start_id = 0;
Mage::app()->setCurrentStore(Mage_Core_Model_App::ADMIN_STORE_ID);
$tableNameAfterbuy = Mage::getSingleton('core/resource')->getTableName('afterbuyorderdata');
$tableNameSalesFlat = Mage::getSingleton('core/resource')->getTableName('sales_flat_order');

	/**
	 * Get the resource model
	 */
	$resource = Mage::getSingleton('core/resource');

	/**
	 * Retrieve the read connection
	 */
	$readConnection = $resource->getConnection('core_read');

	//$query = 'SELECT * FROM ' . $resource->getTableName('catalog/product');
	$query = 'SELECT afterbuyorderdata_id, shoporderid, increment_id FROM '.$tableNameAfterbuy.' RIGHT JOIN '.$tableNameSalesFlat.' ON shoporderid = increment_id
		WHERE afterbuyorderdata_id IS NULL ';

	if ((int)$start_id > 0)
		$query .= ' AND increment_id > '.(int)$start_id ;
echo $query;
	/**
	 * Execute the query and store the results in $results
	 */
	$results = $readConnection->fetchAll($query);

	/**
	 * Print out the results
	 */
	 echo "Gefundene Bestellungen:<pre>";
	//echo sprintf('<pre>%s</pre>' print_r($results, true));

	$ab_class = Mage::getModel('afterbuycheckout/orderafterbuy');
	$ab_class->handler = 'cron';

	foreach($results as $single_result)
	{
		print_r($single_result);
		echo $single_result->increment_id;
		$ab_class->setOrderID($single_result['increment_id']);


		$ab_class->createAfterbuyString();
		var_dump( $ab_class->afterbuy_string);

		$ab_class->send();
	}
	echo "</pre>";
?>