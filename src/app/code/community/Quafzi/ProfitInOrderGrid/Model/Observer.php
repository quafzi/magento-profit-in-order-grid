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
        $item = $observer->getEvent()->getItem();
        $helper = Mage::helper('quafzi_profitinordergrid/order_item');
        $item->setCost($helper->getCost($item))
            ->setProfitAmount($helper->getProfitAmount($item))
            ->setProfitPercent($helper->getProfitPercentage($item));
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
        $order = $observer->getEvent()->getOrder();
        $helper = Mage::helper('quafzi_profitinordergrid/order');
        $order->setCost($helper->getCost($order))
            ->setProfitAmount($helper->getProfitAmount($order))
            ->setProfitPercent($helper->getProfitPercentage($order));
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
}
