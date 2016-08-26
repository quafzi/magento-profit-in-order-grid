<?php
/**
 * Order Helper
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
 * Quafzi_ProfitInOrderGrid_Helper_Order
 *
 * @category Mage_Sales
 * @package  Quafzi_ProfitInOrderGrid
 * @author   Thomas Birke <magento@netextreme.de>
 * @license  http://opensource.org/licenses/osl-3.0.php OSL 3.0
 * @link     https://github.com/quafzi/magento-profit-in-order-grid
 */
class Quafzi_ProfitInOrderGrid_Helper_Order
{
    protected $costs = [];
    protected $finalPrices = [];
    protected $profitAmounts = [];

    /**
     * Get cost for an order
     *
     * @param Mage_Sales_Model_Order $order Order
     *
     * @return float
     */
    public function getCost(Mage_Sales_Model_Order $order)
    {
        if (!isset($this->costs[$order->getId()])) {
            $this->collectCostAndProfit($order);
        }
        return $this->costs[$order->getId()];
    }

    /**
     * Get profit amount for an order
     *
     * @param Mage_Sales_Model_Order $order Order
     *
     * @return float
     */
    public function getProfitAmount(Mage_Sales_Model_Order $order)
    {
        if (!isset($this->profitAmounts[$order->getId()])) {
            $this->collectCostAndProfit($order);
        }
        return $this->profitAmounts[$order->getId()];
    }

    /**
     * Get profit percentage for an order
     *
     * @param Mage_Sales_Model_Order $order Order
     *
     * @return float
     */
    public function getProfitPercentage(Mage_Sales_Model_Order $order)
    {
        return $this->getProfitAmount($order) / $this->finalPrices[$order->getId()] * 100;
    }

    /**
     * Get items generator
     *
     * @param Mage_Sales_Model_Resource_Order_Item_Collection|array $items Order items collection
     */
    protected function getItem($items)
    {
        if (is_array($items)) {
            foreach ($items as $item) {
                yield $item;
            }
            return;
        }
        $ids = $items->getAllIds();
        foreach ($ids as $id) {
            yield $items->getItemById($id);
        }
    }

    /**
     * Collect cost profit for an order
     *
     * @param Mage_Sales_Model_Order $order Order
     *
     * @return void
     */
    protected function collectCostAndProfit(Mage_Sales_Model_Order $order)
    {
        $cost = 0;
        $finalPrice = 0;
        $profitAmount = 0;
        $items = $order->getItemsCollection();
        foreach ($this->getItem($items) as $item) {
            $cost += $item->getCost();
            $finalPrice += $item->getQtyOrdered() * $item->getPrice() - $item->getDiscountAmount();
            $profitAmount += $item->getProfitAmount();
        }
        unset($items);
        $this->costs[$order->getId()] = $cost;
        $this->finalPrices[$order->getId()] = $finalPrice;
        $this->profitAmounts[$order->getId()] = $profitAmount;
    }
}
