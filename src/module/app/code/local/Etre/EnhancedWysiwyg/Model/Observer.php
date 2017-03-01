<?php

class Etre_EnhancedWysiwyg_Model_Observer
{
    public function insertAdditional($observer)
    {
        /** @var $_block Mage_Core_Block_Abstract */
        $_block = $observer->getBlock();
        $_type = $_block->getType();
        if ($_type == 'adminhtml/page_footer') {
            $_child = clone $_block;
            $_child->setType('adminhtml/page_footer');
            $_child->setNameInLayout("footer");
            $_block->setChild('footer', $_child);
            $_block->setTemplate('etre_enhancedwysiwyg/footer.phtml');
        }
    }
}