<?php
/**
 * Shopping cart controller
 * @author J.Galvez
 */
require_once 'Mage/Checkout/controllers/CartController.php';

class SOFTSS_Checkout_CartController extends Mage_Checkout_CartController {

    /*
     * Ajax add product to shopping cart action
     */
    public function addAjaxAction()
    {
        $cart   = $this->_getCart();
        $params = $this->getRequest()->getParams();
        try {
            if (isset($params['qty'])) {
                $filter = new Zend_Filter_LocalizedToNormalized(
                    array('locale' => Mage::app()->getLocale()->getLocaleCode())
                );
                $params['qty'] = $filter->filter($params['qty']);
            }

            $product = $this->_initProduct();
            $related = $this->getRequest()->getParam('related_product');

            /**
             * Check product availability
             */
            if (!$product) {
                $this->_goBack();
                return;
            }
            $cart->addProduct($product, $params);
            if (!empty($related)) {
                $cart->addProductsByIds(explode(',', $related));
            }

            $cart->save();

            $this->_getSession()->setCartWasUpdated(true);

            /**
             * @todo remove wishlist observer processAddToCart
             */
            Mage::dispatchEvent('checkout_cart_add_product_complete',
                array('product' => $product, 'request' => $this->getRequest(), 'response' => $this->getResponse())
            );

            //ajax response
            $response = array();
            if (!$cart->getQuote()->getHasError()){
                $cartmessage = $this->__('%s was added to your shopping cart.', Mage::helper('core')->escapeHtml($product->getName()));

                $ajaxSuccessBlockHTML = $this->getLayout()
                                        ->createBlock('softsscheckout/ajax_addtocart','addedToCart', array('cart_message'=>$cartmessage, 'product'=>$product, 'quantity'=>$params['qty']))
                                        ->setTemplate("checkout/ajax/addedtocart.phtml")
                                        ->toHtml();

                $toplink = $this->getLayout()->createBlock('checkout/cart_sidebar')
                                        ->setTemplate("checkout/cart/header.phtml")
                                        ->toHtml();

                $response['message']=$cartmessage;
                $response['additemhtml']=$ajaxSuccessBlockHTML;
                $response['toplink'] = $toplink;

                //json enconded response
                $this->getResponse()
                        ->clearHeaders()
                        ->setHeader('Content-Type', 'text/xml; charset=UTF-8')
                        ->setBody(Zend_Json::encode($response));
            }
        } catch (Mage_Core_Exception $e) {

            if ($this->_getSession()->getUseNotice(true)) {
                $response['error']=Mage::helper('core')->escapeHtml($e->getMessage());
                Mage::log($e->getMessage());
                $this->getResponse()->clearHeaders()->setHeader('Content-Type', 'text/xml; charset=UTF-8')->setBody(Zend_Json::encode($response));
            } else {
                $messages = array_unique(explode("\n", $e->getMessage()));
                foreach ($messages as $message) {
                    $error .= Mage::helper('core')->escapeHtml($message).'<br/>';
                }

                $response['error']=Mage::helper('core')->escapeHtml($error);
                Mage::log($error);
                $this->getResponse()->clearHeaders()->setHeader('Content-Type', 'text/xml; charset=UTF-8')->setbody(Zend_Json::encode($response));
            }

            Mage::logException($e);
        } catch (Exception $e) {
            Mage::logException($e);

            $response['error']=$this->__('Cannot add the item to shopping cart.');
            $this->getResponse()->clearHeaders()->setHeader('Content-Type', 'text/xml; charset=UTF-8')->setbody(Zend_Json::encode($response));
        }
    }

     /*
     * Add session messages and redirect to the cart
     */
    public function softDistributionResponseAction()
    {

        if($this->getRequest()->getParam('error')) {
           Mage::getSingleton('checkout/session')->addError("An error occured. Please try later.");
        } else {

            $products = $this->getRequest()->getParam('products');
            $aProducts = explode(",", $products);
            $productsStr = "";
            $cartHelper = Mage::helper('checkout/cart');
            $hasItems = false;

            $items = Mage::getSingleton('checkout/session')->getQuote()->getAllItems();

            foreach($items as $item) {

               if(in_array($item->getProduct()->getSoftssSupplierProductId(), $aProducts)) {

                   $productsStr .= $item->getProduct()->getName();
                   $cartHelper->getCart()->removeItem($item->getId())->save();

                   $product = Mage::getModel('catalog/product')->load($item->getProduct()->getId());
                   $stockData = $product->getStockData();
                   $stockData['is_in_stock'] = 0;
                   $product->setStockData($stockData);
                   $product->save();
                   $hasItems = true;
               }

            }
            if ($hasItems) {
                Mage::getSingleton('checkout/session')->addError("Some products are out of stock. The following products were removed from your cart: ".$productsStr);
            } else {
                Mage::getSingleton('checkout/session')->addError("An error occured. Please try later.");
            }

        }

        $this->_redirect('checkout/cart');
        return;
    }

    public function softDistributionProductAvailabilityAction()
    {

        //ajax response
        $response = array();

        try {

            $url = Mage::helper('softsscheckout')->getSoftDistributionUrl();

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

            $data = curl_exec($ch); // execute curl request
            curl_close($ch);

            if(empty($data)) {
               $response['error'] = true;
            } else {

                $xml = simplexml_load_string($data);

                $codes = array("N_A","OOS","PRE_SELL","PRE_SELL_ONLY","PRE_SELL_BONUS","DELISTED");
                $productIds = array();

                if($xml->status == 1) {
                    $response['products'] = false;
                } else {

                    foreach($xml->products->product as $product){

                      if(in_array($product->availability, $codes)) {
                          $productIds[] = $product->productversionid;
                      }
                    }
                    $response['products'] = implode(",", $productIds);
                }
            }
          } catch (Exception $e) {
            Mage::logException($e);
            $response['error'] = true;

          }

            $response['error'] = false;
            $this->getResponse()->setbody(Zend_Json::encode($response));

    }
}

?>