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
WHERE afterbuyorderdata.bezahlt !=2 AND afterbuyorderdata.aid!="" AND sales_flat_order_payment.method="banktransfer" AND sales_flat_order.state != "canceled"';

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
    $result = $ab_class->updateMagentoOrderStatus($single_result['aid'], $single_result['shoporderid']);

    if($result==false){
        echo "Update Status:<pre>";
        var_dump($result);
        echo '</pre><br/>';
    }
}

//set afterbuy orders to compleate if compleate in Magento
$ab_class->setOrderPaymentStatusComplete();

echo "End of AB Magento sync Cron<br>";
?>