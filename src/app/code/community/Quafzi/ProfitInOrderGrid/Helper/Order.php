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
     * Get profit for an order
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
     * Collect cost profit for an order
     *
     * @param Mage_Sales_Model_Order $order Order
     *
     * @return void
     */
    protected function collectCostAndProfit(Mage_Sales_Model_Order $order)
    {
        $cost = 0;
        $profitAmount = 0;
        foreach ($order->getItemsCollection() as $item) {
            $cost += $item->getCost();
            $profitAmount += $item->getProfitAmount();
        }
        $this->costs[$order->getId()] = $cost;
        $this->profitAmounts[$order->getId()] = $profitAmount;
    }
}
