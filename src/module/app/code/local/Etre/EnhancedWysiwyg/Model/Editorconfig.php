<?php


class Etre_EnhancedWysiwyg_Model_Editorconfig extends Mage_Cms_Model_Wysiwyg_Config
{

    public function magentoDefaultWysiwygSettings()
    {
        return array(
            'enabled' => $this->isEnabled(),
            'hidden' => $this->isHidden(),
            'use_container' => false,
            'add_variables' => true,
            'add_widgets' => true,
            'no_display' => false,
            'translator' => Mage::helper('cms'),
            'encode_directives' => true,
            'directives_url' => Mage::getSingleton('adminhtml/url')->getUrl('*/cms_wysiwyg/directive'),
            'popup_css' =>
                Mage::getBaseUrl('js') . 'mage/adminhtml/wysiwyg/tiny_mce/themes/advanced/skins/default/dialog.css',
            'content_css' =>
                Mage::getBaseUrl('js') . 'mage/adminhtml/wysiwyg/tiny_mce/themes/advanced/skins/default/content.css',
            'width' => '100%',
            'plugins' => array()
        );
    }

    public function newWysiwygSettings()
    {
        $originalSettings = $this->magentoDefaultWysiwygSettings();
        $modifiedSettings = $originalSettings;
        $frontendCssUrl = Mage::helper("etre_enhancedwysiwyg")->getCssUrl();
        $modifiedSettings['content_css'] = "{$originalSettings['content_css']},{$frontendCssUrl}";
        $modifiedSettings['visualblocks_default_state'] = true;
        $modifiedSettings["element_format"] ="xhtml";
        // Schema is HTML5 instead of default HTML4
        $modifiedSettings["schema"] = "html5";
        $modifiedSettings["verify_html"] = false;
        $modifiedSettings["visual"] = true;
        $modifiedSettings["apply_source_formatting"] = true;
        // End container block element when pressing enter inside an empty block
        $modifiedSettings['end_container_on_empty_block'] = true;

        // HTML5 formats
        $modifiedSettings['style_formats'] = [
            ['title' => 'h1', 'block' => 'h1'],
            ['title' => 'h2', 'block' => 'h2'],
            ['title' => 'h3', 'block' => 'h3'],
            ['title' => 'h4', 'block' => 'h4'],
            ['title' => 'h5', 'block' => 'h5'],
            ['title' => 'h6', 'block' => 'h6'],
            ['title' => 'p', 'block' => 'p'],
            ['title' => 'div', 'block' => 'div'],
            ['title' => 'pre', 'block' => 'pre'],
            ['title' => 'section', 'block' => 'section', 'wrapper' => true, 'merge_siblings' => false],
            ['title' => 'article', 'block' => 'article', 'wrapper' => true, 'merge_siblings' => false],
            ['title' => 'blockquote', 'block' => 'blockquote', 'wrapper' => true],
            ['title' => 'hgroup', 'block' => 'hgroup', 'wrapper' => true],
            ['title' => 'aside', 'block' => 'aside', 'wrapper' => true],
            ['title' => 'figure', 'block' => 'figure', 'wrapper' => true]
        ];
        return $modifiedSettings;
    }

    /**
     * Return Wysiwyg config as Varien_Object
     *
     * Config options description:
     *
     * enabled:                 Enabled Visual Editor or not
     * hidden:                  Show Visual Editor on page load or not
     * use_container:           Wrap Editor contents into div or not
     * no_display:              Hide Editor container or not (related to use_container)
     * translator:              Helper to translate phrases in lib
     * files_browser_*:         Files Browser (media, images) settings
     * encode_directives:       Encode template directives with JS or not
     *
     * @param $data array       constructor params to override default config values
     * @return Varien_Object
     */
    public function getConfig($data = array())
    {
        $config = new Varien_Object();

        $config->setData($this->newWysiwygSettings());

        $config->setData('directives_url_quoted', preg_quote($config->getData('directives_url')));

        if (Mage::getSingleton('admin/session')->isAllowed('cms/media_gallery')) {
            $config->addData(array(
                'add_images' => true,
                'files_browser_window_url' => Mage::getSingleton('adminhtml/url')->getUrl('*/cms_wysiwyg_images/index'),
                'files_browser_window_width'
                => (int)Mage::getConfig()->getNode('adminhtml/cms/browser/window_width'),
                'files_browser_window_height'
                => (int)Mage::getConfig()->getNode('adminhtml/cms/browser/window_height'),
            ));
        }

        if (is_array($data)) {
            $config->addData($data);
        }

        Mage::dispatchEvent('cms_wysiwyg_config_prepare', array('config' => $config));

        return $config;
    }

}