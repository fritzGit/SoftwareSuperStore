<?php
echo "AB to Magento sync Cron<br>";
ini_set('display_errors',1);
require_once '/var/www/vhosts/softwaresuperstore.co.uk/htdocs/app/Mage.php';

Mage::app()->setCurrentStore(Mage_Core_Model_App::ADMIN_STORE_ID);
$tableNameAfterbuy = Mage::getSingleton('core/resource')->getTableName('afterbuyorderdata');

/**
* Get the resource model
*/
$resource = Mage::getSingleton('core/resource');

/**
* Retrieve the read connection
*/
$readConnection = $resource->getConnection('core_read');

$query = 'SELECT aid, increment_id FROM '.$tableNameAfterbuy.' WHERE bezahlt != 2';

/**
 * Execute the query and store the results in $results
 */
$results = $readConnection->fetchAll($query);

/**
* Print out the results
*/
echo "Update Status:<pre>";

$ab_class = Mage::getModel('afterbuycheckout/orderafterbuy');
$ab_class->handler = 'cron';

foreach($results as $single_result)
{
    $ab_class->setCheckStatusOrderID($single_result['increment_id']);
    $sXML = $ab_class->createAfterbuyCallString($single_result['aid']);
    $ab_class->checkAndUpdateStatus($sXML, $single_result['aid']);

    var_dump($sXML);
}
echo "</pre>";

?>