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

    public function afterBlockToHtml(Varien_Event_Observer $observer) {
        $block = $observer->getEvent()->getBlock();
        if ($block instanceof Mage_Adminhtml_Block_Sales_Order_View_Items_Renderer_Default) {
            $item = $block->getItem();
            $this->_insertItemGridProfitColumn($observer->getEvent()->getTransport(), $item);
        } elseif ($block instanceof Mage_Adminhtml_Block_Sales_Order_View_Items) {
            $item = $block->getItem();
            $this->_insertItemGridProfitColumnHeader($observer->getEvent()->getTransport());
        }
    }

    protected function _insertItemGridProfitColumnHeader($transport)
    {
        $html = $transport->getHtml();
        $html = str_replace('<thead>', '<col width="1" /><thead>', $html);
        $title = Mage::helper('quafzi_profitinordergrid')->__('Profit');
        $transport->setHtml(
            str_replace(
                '<th class="last">',
                '<th class="a-center">' . $title . '</th><th class="last">',
                $html
            )
        );
    }
    protected function _insertItemGridProfitColumn($transport, $item)
    {
        $html = $transport->getHtml();
        $profit = Mage::helper('quafzi_profitinordergrid')->getItemProfit($item);
        $html = str_replace(
            '<td class="a-right last">',
            '<td class="a-right"><!-- profit --></td><td class="a-right last">',
            $html
        );
        $transport->setHtml(preg_replace('/<!-- profit -->/', $profit, $html, 1));
        $this->_renderedItems[] = $item->getId();
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
