<?php

require_once Mage::getModuleDir('controllers', 'Mage_Cms') . DS . 'PageController.php';

/**
 * Created by PhpStorm.
 * User: tmills
 * Date: 1/14/2016
 * Time: 9:35 AM
 */
class Etre_EnhancedWysiwyg_CssController extends Mage_Core_Controller_Front_Action
{

    public function redirectAction()
    {
        $storeId = Mage::app()->getStore()->getId();
        $appEmulation = Mage::getSingleton('core/app_emulation');
        //Start environment emulation of the specified store
        $initialEnvironmentInfo = $appEmulation->startEnvironmentEmulation($storeId, Mage_Core_Model_App_Area::AREA_FRONTEND);
        $cmsPageId = 41;
        $cmsHelper = Mage::helper('cms/page');
        $request = new Mage_Core_Controller_Request_Http();
        $request->setRequestUri($cmsHelper->getPageUrl($cmsPageId));
        $cmsPageData = Mage::getModel("cms/page")->load($cmsPageId);
        $uriPath = strval($cmsPageData->getIdentifier());
        $baseUrl = rtrim(Mage::app()->getStore()->getDefaultBasePath(), '/');
        $pathToCmsPage = $baseUrl . '/' . $uriPath;
        $request->setRequestUri($pathToCmsPage);
        $request->setPathInfo($pathToCmsPage);
        $request->setParam('page_id',$cmsPageId);
        $request->setBaseUrl($baseUrl);
        $action = "view";
        $request->setModuleName("cms");
        $request->setControllerName("page");
        $request->setActionName($action);
        $request->setControllerModule("Mage_Cms");
        $request->setRouteName("cms");
        Mage::app()->setRequest($request);
        $cmsController = Mage::getControllerInstance("Mage_Cms_PageController", Mage::app()->getRequest(), Mage::app()->getResponse());

        if(!$cmsController->hasAction($action)) {
            return false;
        }
        $request->setDispatched(true);
        $cmsController->dispatch($action);
        $cmsLayoutXMLString = Mage::app()->getLayout()->getXmlString();
        $cssFilePathRegex = '/((?:[A-z]*(?:\/|\.|[0-9]|-))*[A-z]*)\.css/';
        preg_match_all($cssFilePathRegex, $cmsLayoutXMLString, $cssFileList);
        foreach($cssFileList as $cssList => $cssFiles):
            foreach($cssFiles as $fileKey => $filePath):
                $fileUrl = Mage::getModel("core/design_package")->getSkinUrl($filePath);
                $absoluteFilePath = str_replace(Mage::getBaseUrl(), Mage::getBaseDir() . DS, $fileUrl);
                $mergedCssList[] = $absoluteFilePath;
            endforeach;
        endforeach;
        $widgetCss = Mage::getModel("core/design_package")->getMergedCssUrl($mergedCssList);
        $appEmulation->stopEnvironmentEmulation($initialEnvironmentInfo);
        return $this->_redirectUrl($widgetCss);
    }
}