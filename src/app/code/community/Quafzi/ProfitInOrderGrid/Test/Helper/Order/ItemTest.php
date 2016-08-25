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
require_once dirname(__FILE__) . '/../../../Helper/Order/Item.php';

/**
 * Quafzi_ProfitInOrderGrid_Test_Helper_Order_Item
 *
 * @category Mage_Sales
 * @package  Quafzi_ProfitInOrderGrid
 * @author   Thomas Birke <magento@netextreme.de>
 * @license  http://opensource.org/licenses/osl-3.0.php OSL 3.0
 * @link     https://github.com/quafzi/magento-profit-in-order-grid
 */
class Quafzi_ProfitInOrderGrid_Test_Helper_Order_Item extends TestCase
{
    /**
     * Test profit amount calculation for simple products
     *
     * @return void
     */
    public function testGetProfitAmountOfSimpleProductItem()
    {
        $helper = new Quafzi_ProfitInOrderGrid_Helper_Order_Item();

        // prepare an order item
        $item = $this->getMockBuilder('Mage_Sales_Model_Order_Item')
            ->setMethods(
                ['getCost', 'getDiscountAmount', 'getPrice', 'getQtyOrdered']
            )->getMock();
        $item->expects($this->any())
            ->method('getCost')
            ->will($this->returnValue(14.83));
        $item->expects($this->any())
            ->method('getPrice')
            ->will($this->returnValue(19.99));
        $item->expects($this->any())
            ->method('getDiscountAmount')
            ->will($this->returnValue(1.00));
        $item->expects($this->any())
            ->method('getQtyOrdered')
            ->will($this->returnValue(2));

        $this->assertEquals(
            2 * (19.99 - 14.83) - 1.00,
            $helper->getProfitAmount($item)
        );
    }
}
