<?php
/**
 * Observer
 *
 * PHP version ^5.6
 *
 * @category  Mage_Sales
 * @package   Quafzi_ProfitInOrderGrid
 * @author    Thomas Birke <magento@netextreme.de>
 * @copyright 2015-2016 Thomas Birke
 * @license   http://opensource.org/licenses/osl-3.0.php OSL 3.0
 * @link      https://github.com/quafzi/magento-profit-in-order-grid
 */

/**
 * Quafzi_ProfitInOrderGrid_Model_Observer
 *
 * @category Mage_Sales
 * @package  Quafzi_ProfitInOrderGrid
 * @author   Thomas Birke <magento@netextreme.de>
 * @license  http://opensource.org/licenses/osl-3.0.php OSL 3.0
 * @link     https://github.com/quafzi/magento-profit-in-order-grid
 */
class Quafzi_ProfitInOrderGrid_Model_Observer
{
    /**
     * If item has no cost, we need to add it based on product cost
     * Update item profit
     *
     * @param Varien_Event_Observer $observer Observer
     *
     * @return void
     */
    public function updateOrderItemCostAndProfit(Varien_Event_Observer $observer)
    {
        $item = $observer->getEvent()->getModel();
        if (!$item->getCost()) {
            $item->setCost($item->getProduct()->getCost());
        }
        $item->setProfitAmount(
            Mage::helper('quafzi_profitinordergrid/order_item')->getProfitAmount($item)
        );
    }

    /**
     * Update cost and profit based on ordered items
     *
     * @param Varien_Event_Observer $observer Observer
     *
     * @return void
     */
    public function updateOrderCostAndProfit(Varien_Event_Observer $observer)
    {
        $order = $observer->getEvent()->getModel();
        $helper = Mage::helper('quafzi_profitinordergrid');
        $cost = 0;
        $profit = 0;
        foreach ($order->getItemsCollection() as $item) {
            $cost += $item->getCost() * $item->getQtyOrdered();
            $profit += $item->getCost() * $item->getQtyOrdered();
        }

    }

    /**
     * Add cost and profit to order grid
     *
     * @param Varien_Event_Observer $observer Observer
     *
     * @return void
     */
    public function beforeBlockToHtml(Varien_Event_Observer $observer)
    {
        $block = $observer->getEvent()->getBlock();
        if ($block instanceof Mage_Adminhtml_Block_Sales_Order_Grid) {
            $after = 'grand_total';
            $this->_addColumns($block, $after);
            // reinitialize column order
            $block->sortColumnsByOrder();
        }
    }

    /**
     * Inject cost and profit into order items in adminhtml order detail view
     *
     * @param Varien_Event_Observer $observer Observer
     *
     * @return void
     */
    public function afterBlockToHtml(Varien_Event_Observer $observer)
    {
        $block = $observer->getEvent()->getBlock();
        if ($block instanceof Mage_Adminhtml_Block_Sales_Order_View_Items_Renderer_Default) {
            $item = $block->getItem();
            $this->_insertItemGridProfitColumn($observer->getEvent()->getTransport(), $item);
        } elseif ($block instanceof Mage_Adminhtml_Block_Sales_Order_View_Items) {
            $item = $block->getItem();
            $this->_insertItemGridProfitColumnHeader($observer->getEvent()->getTransport());
        }
    }

    /**
     * Inject column headers into order items in adminhtml order detail view
     *
     * @param Varien_Object $transport Transport
     *
     * @return void
     */
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

    /**
     * Inject cost and profit data into order items in adminhtml order detail view
     *
     * @param Varien_Object               $transport Transport
     * @param Mage_Sales_Model_Order_Item $item      Order item
     *
     * @return void
     */
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

    /**
     * Add column to order grid
     *
     * @param Mage_Adminhtml_Block_Sales_Order_Grid $grid  Grid
     * @param string                                $after Preceding column name
     *
     * @return void
     */
    protected function _addColumns($grid, $after='grand_total')
    {
        $columns = ['cost', 'profit_amount'];
        $helper = Mage::helper('quafzi_profitinordergrid');
        foreach ($columns as $column) {
            $columnData = [
                'header'    => $helper->__($column),
                'align'     => 'right',
                'width'     => '80px',
                'index'     => $column
            ];
            $grid->addColumnAfter($column, $columnData, $after);
        }
    }
}
