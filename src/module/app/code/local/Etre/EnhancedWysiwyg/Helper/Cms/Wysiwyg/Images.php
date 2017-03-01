<?php

/**
 * Created by PhpStorm.
 * User: tyler
 * Date: 11/2/16
 * Time: 5:09 PM
 */
class Etre_EnhancedWysiwyg_Helper_Cms_Wysiwyg_Images extends Mage_Cms_Helper_Wysiwyg_Images
{

    /**
     * Prepare Image insertion declaration for Wysiwyg or textarea(as_is mode)
     *
     * @param string $filename Filename transferred via Ajax
     * @param bool $renderAsTag Leave image HTML as is or transform it to controller directive
     * @param integer $maxWidth (px) Scale image to this width if wider than this and cached (if enabled). Defaults to 0 - will not take action
     * @param integer $maxHeight (px) Scale image to this height if taller than this and cached (if enabled). Defaults to 0 - will not take action
     * @return string
     */
    public function getImageHtmlDeclaration($filename, $renderAsTag = false)
    {
        $fileurl = $this->getCurrentUrl() . $filename;
        $mediaPath = str_replace(Mage::getBaseUrl('media'), '', $fileurl);
        $directive = sprintf('{{media url="%s" maxWidth="%d" maxHeight="%d" quality="%d" }}', $mediaPath, 0, 0, 75);
        if($renderAsTag) {
            $html = sprintf('<img src="%s" alt="" />', $this->isUsingStaticUrlsAllowed() ? $fileurl : $directive);
        } else {
            if($this->isUsingStaticUrlsAllowed()) {
                $html = $fileurl; // $mediaPath;
            } else {
                $directive = Mage::helper('core')->urlEncode($directive);
                $html = Mage::helper('adminhtml')->getUrl('*/cms_wysiwyg/directive', ['___directive' => $directive]);
            }
        }
        return $html;
    }
}