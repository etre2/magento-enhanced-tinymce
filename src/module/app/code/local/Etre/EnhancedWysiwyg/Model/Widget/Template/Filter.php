<?php

/**
 * Created by PhpStorm.
 * User: tyler
 * Date: 11/2/16
 * Time: 5:13 PM
 */
class Etre_EnhancedWysiwyg_Model_Widget_Template_Filter extends Mage_Widget_Model_Template_Filter
{
    /**
     * Retrieve media file URL directive
     *
     * @param array $construction
     * @return string
     */
    public function mediaDirective($construction)
    {
        $params = $this->_getIncludeParameters($construction[2]);
        $mediaDir = Mage::getBaseDir(Mage_Core_Model_Store::URL_TYPE_MEDIA) . DS;
        $baseMediaUrl = Mage::getBaseUrl('media');
        $liveMediaUrl = $baseMediaUrl . $params['url'];
        $maxWidth = round(intval($params['maxWidth']));
        $maxHeight = round(intval($params['maxHeight']));
        $isUsableMaxWidth = $maxWidth > 0;
        $isUsableMaxHeight = $maxHeight > 0;
        $hasUsableMaxParam = ($isUsableMaxWidth || $isUsableMaxHeight);
        $ImagineDependencyInstalled = class_exists('Imagine\Gd\Imagine');
        if(!$hasUsableMaxParam || !($ImagineDependencyInstalled)):
            return $liveMediaUrl;
        endif;

        if(isset($params['quality'])):
            if((is_numeric($params['quality']))):
                $quality = round(intval($params['quality']));
                if($quality < 0):
                    $quality = 0;
                endif;
                if($quality > 100):
                    $quality = 100;
                endif;
            else:
                $quality = 100;
            endif;
        else:
            $quality = 100;
        endif;
        $cachedMediaDirName = 'wysiwyg_cache';
        $cachedMediaDir = $mediaDir . $cachedMediaDirName . DS;
        $baseMediaCachedUrl = $baseMediaUrl . $cachedMediaDirName . DS;
        $pathToImage = $mediaDir . $params['url'];
        $fileInfo = pathinfo($pathToImage);
        $cachedFileName = $fileInfo['filename'] . '-' . md5($params['url']) . '-' . $maxWidth . 'x' . $maxHeight . 'x' . $quality . '.' . $fileInfo['extension'];
        $pathToCachedImage = $cachedMediaDir . $cachedFileName;
        $cachedFileUrl = $baseMediaCachedUrl . $cachedFileName;
        if(file_exists($pathToCachedImage)) {
            return ($cachedFileUrl);
        } elseif(file_exists($pathToImage)) {
            if(!is_dir($cachedMediaDir)) {
                if(!mkdir($cachedMediaDir)):
                    Mage::log("Could not create directory $cachedMediaDir. Return non-cached media instead");
                    return $liveMediaUrl;
                endif;
            }
            try {
                $imagine = new \Imagine\Gd\Imagine();
                $_image = $imagine->open($pathToImage);
                //$_image = new Varien_Image($pathToImage);
                $imageWidthOriginal = $_image->getSize()->getWidth();
                $imageHeightOriginal = $_image->getSize()->getHeight();
                $shouldAdjustWidth = $isUsableMaxWidth && $this->isTooBig($imageWidthOriginal, $maxWidth);
                $shouldAdjustHeight = $isUsableMaxHeight && $this->isTooBig($imageHeightOriginal, $maxHeight);
                $doResize = $shouldAdjustHeight || $shouldAdjustWidth;
                if($doResize):
                    $currentWidth = $imageWidthOriginal;
                    $currentHeight = $imageHeightOriginal;
                    if($isUsableMaxWidth && $this->isTooBig($currentWidth, $maxWidth)):
                        $ratio = $maxWidth / $imageWidthOriginal;
                        $currentWidth = $maxWidth;
                        $currentHeight = $imageHeightOriginal * $ratio;
                    endif;
                    if($isUsableMaxHeight && $this->isTooBig($currentHeight, $maxHeight)):
                        $ratio = $maxHeight / $imageHeightOriginal;
                        $currentHeight = $maxHeight;
                        $currentWidth = $imageWidthOriginal * $ratio;
                    endif;

                    $undefinedFiler = \Imagine\Image\ImageInterface::FILTER_UNDEFINED;
                    $_image->resize(new \Imagine\Image\Box($currentWidth, $currentHeight), $undefinedFiler);
                    $_image->save($pathToCachedImage, ['jpeg_quality' => $quality, 'png_compression_level' => 9]);
                    return $cachedFileUrl;
                endif;
            } catch(Exception $e) {
                Mage::logException($e);
            }
            return $liveMediaUrl;
        }
        return $liveMediaUrl;
    }

    /**
     * @param $currentNumber
     * @param $maxNumber
     * @return bool
     */
    protected function isTooBig($currentNumber, $maxNumber)
    {
        return ($currentNumber > $maxNumber);
    }
}