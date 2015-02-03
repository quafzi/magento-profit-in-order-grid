<?php
/**
 * @package    Quafzi_ProfitInOrderGrid
 * @copyright  Copyright (c) 2015 Thomas Birke
 * @author     Thomas Birke <tbirke@netextreme.de>
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class Quafzi_ProfitInOrderGrid_Model_Observer
{
    public function beforeBlockToHtml(Varien_Event_Observer $observer) {
        $block = $observer->getEvent()->getBlock();
        if ($block instanceof Mage_Adminhtml_Block_Sales_Order_Grid) {
            $after = 'grand_total';
            $this->_modifyGrid($block, $after);
        }
    }

    protected function _modifyGrid(Mage_Adminhtml_Block_Widget_Grid $grid, $after='grand_total')
    {
        $this->_addProfitColumn($grid, $after);
        // reinitialize column order
        $grid->sortColumnsByOrder();
    }

    protected function _addProfitColumn($grid, $after='grand_total')
    {
        $grid->addColumnAfter('profit', array(
            'header'    => Mage::helper('quafzi_profitinordergrid')->__('Profit'),
            'align'     => 'right',
            'width'     => '80px',
            'filter'    => false,
            'index'     => 'cost',
            'renderer'  => 'quafzi_profitinordergrid/renderer_profit'
        ), $after);
    }
}
