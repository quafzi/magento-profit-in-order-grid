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
     * Add profit columns
     *
     * @param Varien_Event_Observer $observer Observer
     *
     * @return void
     */
    public function addColumnsAfterBlockCreate(Varien_Event_Observer $observer) {
        $block = $observer->getEvent()->getBlock();
        if ($block instanceof Mage_Adminhtml_Block_Sales_Order_Grid) {
            $helper = Mage::helper('quafzi_profitinordergrid');
            $block->addColumnAfter('cost', [
                'header' => $helper->__('Cost'),
                'index' => 'cost',
                'filter_index' => 'cost',
                'type' => 'currency',
                'currency' => 'order_currency_code'
            ], 'grand_total');
            $block->addColumnAfter('profit_amount', [
                'header' => $helper->__('Margin'),
                'index' => 'profit_amount',
                'filter_index' => 'profit_amount',
                'type' => 'currency',
                'currency' => 'order_currency_code'
            ], 'cost');
            $block->addColumnAfter('profit_percent', [
                'header' => $helper->__('Markdown Margin'),
                'index' => 'profit_percent',
                'filter_index' => 'profit_percent',
                'type' => 'number',
                'renderer' => 'quafzi_profitinordergrid/grid_column_renderer_percent'
            ], 'profit_amount');
            // reinitialize column order
            $block->sortColumnsByOrder();
        }
    }

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
        try {
            $item = $observer->getEvent()->getItem();
            $helper = Mage::helper('quafzi_profitinordergrid/order_item');
            $item->setCost($helper->getCost($item))
                ->setProfitAmount($helper->getProfitAmount($item))
                ->setProfitPercent($helper->getProfitPercentage($item));
        } catch (Exception $e) {
            Mage::logException($e);
        }
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
        try {
            $order = $observer->getEvent()->getOrder();
            $helper = Mage::helper('quafzi_profitinordergrid/order');
            $order->setCost($helper->getCost($order))
                ->setProfitAmount($helper->getProfitAmount($order))
                ->setProfitPercent($helper->getProfitPercentage($order));
        } catch (Exception $e) {
            Mage::logException($e);
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
        $profitOutput = Mage::getSingleton('core/layout')->createBlock(
            'quafzi_profitinordergrid/sales_order_item_profit',
            'order_item_profit_column',
            ['template' => 'quafzi/profitinordergrid/sales/order/item/profit.phtml']
        )->setItem($item)->toHtml();
        $html = str_replace(
            '<td class="a-right last">',
            '<td class="a-right"><!-- profit --></td><td class="a-right last">',
            $html
        );
        $transport->setHtml(preg_replace('/<!-- profit -->/', $profitOutput, $html, 1));
        $this->_renderedItems[] = $item->getId();
    }
}
