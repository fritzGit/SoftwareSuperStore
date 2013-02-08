<?php
/**
 * Created by jgalvez
 */
class SOFTSS_Topseller_Adminhtml_IndexController extends Mage_Adminhtml_Controller_Action
{
    protected function _construct()
    {
        // Define module dependent translate
        $this->setUsedModuleName('SOFTSS_Topseller');
    }

    public function indexAction()
    {
        $this->loadLayout()->_addBreadcrumb(Mage::helper('softsstopseller')->__('Topseller'), Mage::helper('softsstopseller')->__('Topseller'));
        $this->renderLayout();
    }

    public function editAction()
    {
        $this->_title($this->__('Topseller'))->_title($this->__('Edit'));

        $id = $this->getRequest()->getParam('id', null);
        $oTopsellerModel = Mage::getModel('softsstopseller/homepagetopseller');
        if ($id) {
            $oTopseller = $oTopsellerModel->load((int) $id);
            if ($oTopsellerModel->getId()) {
                $data = $oTopseller->getData(); #Mage::getSingleton('adminhtml/session')->getFormData(true);
                if ($data) {
                    $oTopsellerModel->setData($data)->setId($id);
                }
            } else {
                Mage::getSingleton('adminhtml/session')->addError(Mage::helper('softsstopseller')->__('Example does not exist'));
                $this->_redirect('*/*/');
            }
        }
        Mage::register('topseller_data', $oTopsellerModel);

        $this->loadLayout();
        $this->renderLayout();
    }

    public function newAction()
    {
        $this->_forward('edit');
    }

    /**
     * Save homepage topseller data action
     */
    public function saveAction()
    {
        if ($data = $this->getRequest()->getPost())
        {
            $oTopseller = Mage::getModel('softsstopseller/homepagetopseller');
            $id = $this->getRequest()->getParam('id');
            if ($id) {
                $oTopseller->load($id);
            }
            Mage::getSingleton('adminhtml/session')->setFormData($data);
            try {
                if ($id) {
                    $data['id'] = $id;
                }

                $oTopseller->setData($data);
                $oTopseller->save();

                if (!$oTopseller->getId()) {
                    Mage::throwException(Mage::helper('softsstopseller')->__('Error saving'));
                }

                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('softsstopseller')->__('Homepage topseller has been saved.'));
                Mage::getSingleton('adminhtml/session')->setFormData(false);

                // The following line decides if it is a "save" or "save and continue"
                if ($this->getRequest()->getParam('back')) {
                    $this->_redirect('*/*/edit', array('id' => $oTopseller->getId()));
                } else {
                    $this->_redirect('*/*/');
                }

            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                if ($oTopseller && $oTopseller->getId()) {
                    $this->_redirect('*/*/edit', array('id' => $oTopseller->getId()));
                } else {
                    $this->_redirect('*/*/');
                }
            }

            return;
        }
        Mage::getSingleton('adminhtml/session')->addError(Mage::helper('softsstopseller')->__('No data found to save'));
        $this->_redirect('*/*/');
    }

    public function deleteAction()
    {

        if ($id = $this->getRequest()->getParam('id')) {
            try {
                $oTeaser = Mage::getModel('softsstopseller/homepagetopseller')->load($id);
                $oTeaser->delete();
                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('softsstopseller')->__('Topseller has been deleted.'));
                $this->_redirect('*/*/');
                return;
            }
            catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
                return;
            }
        }
        Mage::getSingleton('adminhtml/session')->addError(Mage::helper('softsstopseller')->__('Unable to find the teaser to delete.'));
        $this->_redirect('*/*/');
    }

}
