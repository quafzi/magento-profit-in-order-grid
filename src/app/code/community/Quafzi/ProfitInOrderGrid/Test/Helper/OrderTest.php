<?php
/**
 * Testing Order Item Helper
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

use PHPUnit\Framework\TestCase;
require_once dirname(__FILE__) . '/../../Helper/Order.php';

/**
 * Quafzi_ProfitInOrderGrid_Test_Helper_Order
 *
 * @category Mage_Sales
 * @package  Quafzi_ProfitInOrderGrid
 * @author   Thomas Birke <magento@netextreme.de>
 * @license  http://opensource.org/licenses/osl-3.0.php OSL 3.0
 * @link     https://github.com/quafzi/magento-profit-in-order-grid
 */
class Quafzi_ProfitInOrderGrid_Test_Helper_Order extends TestCase
{
    /**
     * Test cost calculation for simple products
     *
     * @return void
     */
    public function testGetCostOfOrder()
    {
        $helper = new Quafzi_ProfitInOrderGrid_Helper_Order();

        // prepare an order
        $items = [];
        $costs = [123, 0, 5, -12.3];
        foreach ($costs as $itemCost) {
            $item = $this->getMockBuilder('Mage_Sales_Model_Order')
                ->setMethods(['getProfitAmount', 'getCost'])->getMock();
            $item->expects($this->any())
                ->method('getCost')
                ->will($this->returnValue($itemCost));
            $item->expects($this->any())
                ->method('getProfitAmount')
                ->will($this->returnValue($itemCost/3));
            $items[] = $item;
        }
        $order = $this->getMockBuilder('Mage_Sales_Model_Order')
            ->setMethods(['getId', 'getItemsCollection'])->getMock();
        $order->expects($this->any())
            ->method('getId')
            ->will($this->returnValue(23890));
        $order->expects($this->any())
            ->method('getItemsCollection')
            ->will($this->returnValue($items));

        $this->assertEquals(array_sum($costs), $helper->getCost($order));
    }
    /**
     * Test profit calculation for simple products
     *
     * @return void
     */
    public function testGetProfitOfOrder()
    {
        $helper = new Quafzi_ProfitInOrderGrid_Helper_Order();

        // prepare an order
        $items = [];
        $profits = [123, 0, 5, -12.3];
        foreach ($profits as $itemProfit) {
            $item = $this->getMockBuilder('Mage_Sales_Model_Order')
                ->setMethods(['getProfitAmount', 'getCost'])->getMock();
            $item->expects($this->any())
                ->method('getCost')
                ->will($this->returnValue($itemProfit*3));
            $item->expects($this->any())
                ->method('getProfitAmount')
                ->will($this->returnValue($itemProfit));
            $items[] = $item;
        }
        $order = $this->getMockBuilder('Mage_Sales_Model_Order')
            ->setMethods(['getId', 'getItemsCollection'])->getMock();
        $order->expects($this->any())
            ->method('getId')
            ->will($this->returnValue(23890));
        $order->expects($this->any())
            ->method('getItemsCollection')
            ->will($this->returnValue($items));

        $this->assertEquals(array_sum($profits), $helper->getProfitAmount($order));
    }
}
