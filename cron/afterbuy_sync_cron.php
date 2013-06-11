<?php
echo "Start of AB to Magento sync Cron<br>";
ini_set('display_errors',1);
require_once '/var/www/vhosts/softwaresuperstore.co.uk/htdocs/app/Mage.php';

Mage::app()->setCurrentStore(Mage_Core_Model_App::ADMIN_STORE_ID);

/**
* Get the resource model
*/
$resource = Mage::getSingleton('core/resource');

/**
* Retrieve the read connection
*/
$readConnection = $resource->getConnection('core_read');

$query = 'SELECT afterbuyorderdata.aid, afterbuyorderdata.shoporderid,sales_flat_order.entity_id,sales_flat_order_payment.method FROM afterbuyorderdata RIGHT JOIN sales_flat_order
ON afterbuyorderdata.shoporderid=sales_flat_order.increment_id
RIGHT JOIN sales_flat_order_payment ON sales_flat_order.entity_id=sales_flat_order_payment.parent_id
WHERE afterbuyorderdata.bezahlt !=2 AND afterbuyorderdata.aid!="" AND sales_flat_order_payment.method="banktransfer"';

/**
 * Execute the query and store the results in $results
 */
$results = $readConnection->fetchAll($query);

/**
* Print out the errors
*/


$ab_class = Mage::getModel('afterbuycheckout/orderafterbuy');
$ab_class->handler = 'cron';

foreach($results as $single_result)
{
    $ab_class->setCheckStatusOrderID($single_result['shoporderid']);
    $sXML = $ab_class->createAfterbuyCallString($single_result['aid']);
    $result = $ab_class->checkAndUpdateStatus($sXML, $single_result['aid']);

    if($result==false){
        echo "Update Status:<pre>";
        var_dump($sXML);
        echo '</pre><br/>';
    }
}

echo "End of AB Magento sync Cron<br>";
?>