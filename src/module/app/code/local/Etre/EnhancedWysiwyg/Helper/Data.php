<?php

/**
 * Created by PhpStorm.
 * User: tmills
 * Date: 1/14/2016
 * Time: 9:28 AM
 */
class Etre_EnhancedWysiwyg_Helper_Data extends Mage_Core_Helper_Abstract
{
    public function getCssUrl()
    {
       return Mage::getUrl("wysiwyg/css/redirect");
    }

    public function cachePath()
    {
        $mediaBasePath = Mage::getBaseDir('media') . "/";
        return $cachePath = "{$mediaBasePath}wysiwyg_cache/";
    }
}