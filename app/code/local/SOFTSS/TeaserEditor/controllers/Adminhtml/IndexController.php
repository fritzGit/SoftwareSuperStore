<?php
/**
 * Created by
 * User: jgalvez
 * @author juan galvez
 * @pagage SOFTSS
 * @subpackage SOFTSS_TeaserEditor
 */

class SOFTSS_TeaserEditor_Adminhtml_IndexController extends Mage_Adminhtml_Controller_Action
{

    protected function _construct()
    {
        // Define module dependent translate
        $this->setUsedModuleName('SOFTSS_TeaserEditor');
    }

    public function indexAction()
    {
        $this->loadLayout()->_addBreadcrumb(Mage::helper('softssteasereditor')->__('Teasers'), Mage::helper('softssteasereditor')->__('Teasers'));
        $this->renderLayout();
    }

    public function editAction()
    {
        $this->_title($this->__('Teaser'))->_title($this->__('Edit'));

        $id = $this->getRequest()->getParam('id', null);
        $oTeaserModel = Mage::getModel('softssteasereditor/teaser');
        if ($id) {
            $oTeaser = $oTeaserModel->load((int) $id);
            if ($oTeaserModel->getId()) {
                $data = $oTeaser->getData();
                if ($data) {
                    $oTeaserModel->setData($data)->setId($id);
                }
            } else {
                Mage::getSingleton('adminhtml/session')->addError(Mage::helper('awesome')->__('Example does not exist'));
                $this->_redirect('*/*/');
            }
        }
        Mage::register('teaser_data', $oTeaserModel);

        $this->loadLayout();
        $this->renderLayout();
    }

    public function newAction()
    {
        $this->_forward('edit');
    }


    /**
     * Save teaser data action
     */
    public function saveAction()
    {
        if ($data = $this->getRequest()->getPost())
        {
            $oTeaser = Mage::getModel('softssteasereditor/teaser');
            $id = $this->getRequest()->getParam('id');
            if ($id) {
                $oTeaser->load($id);
            }
            Mage::getSingleton('adminhtml/session')->setFormData($data);
            try {
                if ($id) {
                    $data['id'] = $id;
                }

                if($data['teaserimage']['delete']){
                    $data['image'] = '';
                    @unlink(Mage::getBaseDir('media') . DS . Mage::helper('softssteasereditor')->getTeaserImageDir() . $oTeaser->getImage());
                }

                if(isset($_FILES['teaserimage']['name']) && $_FILES['teaserimage']['name'] != '') {

                    $sPath = Mage::getBaseDir('media') . DS . Mage::helper('softssteasereditor')->getTeaserImageDir() ;

                    // image handling
                    try
                    {
                        //check if directory exists.
                        if(!Mage::helper('softssbase')->checkExistDirectory($sPath)){
                            Mage::helper('softssbase')->createDirectory($sPath);
                        }
                        $uploader = new Mage_Core_Model_File_Uploader('teaserimage');
                        $uploader->setAllowedExtensions(array('jpg','jpeg','gif','png'));
                        $uploader->setAllowRenameFiles(true);

                        #$result = $uploader->save($sPath.'original/', $_FILES['image']['teaserimage']);
                        $result = $uploader->save($sPath, $_FILES['image']['teaserimage']);
                        $data['image'] = $result['file'];

                        $imageResize = $sPath.$result['file'];


                        /*
                         * to be compleated if we want to resize on upload
                         *
                         $imageUrl=$sPath.'original/'.$result['file'];
                        #if($data['teaser_group_id'] == 4){
                        if (!file_exists($imageResize)&&file_exists($imageUrl)) {
                            $imageObj = new Varien_Image($imageUrl);
                            $imageObj->constrainOnly(TRUE);
                            $imageObj->keepAspectRatio(TRUE);
                            $imageObj->keepFrame(FALSE);
                            $imageObj->resize(500, 250);
                            $imageObj->save($imageResize);
                        }*/

                    } catch (Exception $e)
                    {
                        if ($e->getCode() != Mage_Core_Model_File_Uploader::TMP_NAME_EMPTY)
                        {
                            Mage::logException($e);
                            Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                            $this->_redirect('*/*/edit', array('id' => $oTeaser->getId()));
                        }
                    }
                }

                $oTeaser->setData($data);
                $oTeaser->save();

                if (!$oTeaser->getId()) {
                    Mage::throwException(Mage::helper('softssteasereditor')->__('Error saving'));
                }

                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('softssteasereditor')->__('Teaser has been saved.'));
                Mage::getSingleton('adminhtml/session')->setFormData(false);

                // The following line decides if it is a "save" or "save and continue"
                if ($this->getRequest()->getParam('back')) {
                    $this->_redirect('*/*/edit', array('id' => $oTeaser->getId()));
                } else {
                    $this->_redirect('*/*/');
                }

            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                if ($oTeaser && $oTeaser->getId()) {
                    $this->_redirect('*/*/edit', array('id' => $oTeaser->getId()));
                } else {
                    $this->_redirect('*/*/');
                }
            }

            return;
        }
        Mage::getSingleton('adminhtml/session')->addError(Mage::helper('softssteasereditor')->__('No data found to save'));
        $this->_redirect('*/*/');
    }

    public function deleteAction()
    {
        if ($id = $this->getRequest()->getParam('id')) {
            try {
                $oTeaser = Mage::getModel('softssteasereditor/teaser')->load($id);
                $imageURL = Mage::getBaseDir('media') . DS . Mage::helper('softssteasereditor')->getTeaserImageDir() . $oTeaser->getImage();

                //remove image
                @unlink($imageURL);

                $oTeaser->delete();
                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('softssteasereditor')->__('Teaser has been deleted.'));
                $this->_redirect('*/*/');
                return;
            }
            catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
                return;
            }
        }
        Mage::getSingleton('adminhtml/session')->addError(Mage::helper('softssteasereditor')->__('Unable to find the teaser image to delete.'));
        $this->_redirect('*/*/');
    }
}