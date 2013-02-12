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

            if (!$this->_getSession()->getNoCartRedirect(true)) {
                if (!$cart->getQuote()->getHasError()){
                    $message = $this->__('%s was added to your shopping cart.', Mage::helper('core')->escapeHtml($product->getName()));
                    $response['message']=$message;

                    $this->getResponse()->setBody(Zend_Json::encode($response));
                }
            }
        } catch (Mage_Core_Exception $e) {

            if ($this->_getSession()->getUseNotice(true)) {
                $response['error']=Mage::helper('core')->escapeHtml($e->getMessage());
                $this->getResponse()->setBody(Zend_Json::encode($response));
            } else {
                $messages = array_unique(explode("\n", $e->getMessage()));
                foreach ($messages as $message) {
                    $error .= Mage::helper('core')->escapeHtml($message).'<br/>';
                }

                $response['error']=Mage::helper('core')->escapeHtml($e->getMessage());
                $this->getResponse()->setBody(Zend_Json::encode($response));
            }

            Mage::logException($e);
        } catch (Exception $e) {

            Mage::logException($e);

            $response['error']=$this->__('Cannot add the item to shopping cart.');
            $this->getResponse()->setBody(Zend_Json::encode($response));
        }
    }
}

?>
