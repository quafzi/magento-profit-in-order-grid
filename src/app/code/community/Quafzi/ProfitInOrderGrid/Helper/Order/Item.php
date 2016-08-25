<?php
/**
 * Order Item Helper
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
 * Quafzi_ProfitInOrderGrid_Helper_Order_Item
 *
 * @category Mage_Sales
 * @package  Quafzi_ProfitInOrderGrid
 * @author   Thomas Birke <magento@netextreme.de>
 * @license  http://opensource.org/licenses/osl-3.0.php OSL 3.0
 * @link     https://github.com/quafzi/magento-profit-in-order-grid
 */
class Quafzi_ProfitInOrderGrid_Helper_Order_Item
{
    /**
     * Get profit amount for an order item
     *
     * @param Mage_Sales_Model_Order_Item $item Order item
     *
     * @return float
     */
    public function getProfitAmount(Mage_Sales_Model_Order_Item $item)
    {
        return $item->getQtyOrdered() * ($item->getPrice() - $item->getCost())
            - $item->getDiscountAmount();
    }
}
