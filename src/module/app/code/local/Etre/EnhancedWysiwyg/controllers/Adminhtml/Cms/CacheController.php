<?php

/**
 * Created by PhpStorm.
 * User: tmills
 * Date: 12/3/2015
 * Time: 9:04 AM
 */
class Etre_EnhancedWysiwyg_Adminhtml_Cms_CacheController extends Mage_Adminhtml_Controller_Action
{
    public function FlushImagesAction()
    {
        $cachePath = Mage::helper("etre_enhancedwysiwyg")->cachePath();
        if (is_readable($cachePath) && is_writable($cachePath)):
            $cachedFiles = glob("{$cachePath}*");
            $failedToRemove = 0;
            foreach ($cachedFiles as $file) {
                if (is_file($file))
                    if (unlink($file) == false) $failedToRemove++;
            }
            if ($failedToRemove > 0) Mage::getSingleton('adminhtml/session')->addError($this->__("Could not remove %s cached CMS images.",$failedToRemove));
            Mage::getSingleton('adminhtml/session')->addSuccess($this->__("CMS Image Cache Flush process has completed."));
        else:
            Mage::getSingleton('adminhtml/session')->addError($this->__("Cannot read cache directory. Please ensure that your server has read/write permissions."));
            Mage::getSingleton('adminhtml/session')->addError($this->__($cachePath));
        endif;
        return $this->_redirectReferer();
    }
}