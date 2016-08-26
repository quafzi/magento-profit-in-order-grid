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
    protected $productResourceModel;

    protected function getRawProductCostValue($productId, $storeId)
    {
        if (!$this->productResourceModel) {
            $this->productResourceModel = Mage::getResourceModel('catalog/product');
        }
        $cost = $this->productResourceModel->getAttributeRawValue($productId, 'cost', $storeId);
        if (!$cost) {
            $cost = $this->productResourceModel->getAttributeRawValue($productId, 'cost', 0);
        }
        return $cost;
    }

    /**
     * for testing purposes, only
     */
    public function setProductResourceModel ($resourceModel)
    {
        $this->productResourceModel = $resourceModel;
        return $this;
    }

    protected function getProductCostForItem(Mage_Sales_Model_Order_Item $item)
    {
        return $this->getRawProductCostValue($item->getProductId(), $item->getStoreId())
            ?: $item->getProduct()->getCost();
    }

    /**
     * Get cost for an order item
     *
     * @param Mage_Sales_Model_Order_Item $item Order item
     *
     * @return float
     */
    public function getCost(Mage_Sales_Model_Order_Item $item)
    {
        $cost = $item->getCost();
        if (!$cost) {
            $cost = $this->getProductCostForItem($item) * $item->getQtyOrdered();
        }
        if (is_null($cost)) {
            $cost = 0;
            foreach ($item->getChildrenItems() as $child) {
                $cost += $this->getProductCostForItem($child);
            }
            $cost *= $item->getQtyOrdered();
        }
        return $cost;
    }

    /**
     * Get profit amount for an order item
     *
     * @param Mage_Sales_Model_Order_Item $item Order item
     *
     * @return float
     */
    public function getProfitAmount(Mage_Sales_Model_Order_Item $item)
    {
        return $item->getQtyOrdered() * $item->getPrice()
            - $this->getCost($item)
            - $item->getDiscountAmount();
    }

    /**
     * Get profit percentage for an order item
     *
     * @param Mage_Sales_Model_Order_Item $item Order item
     *
     * @return float
     */
    public function getProfitPercentage(Mage_Sales_Model_Order_Item $item)
    {
        $qty = $item->getQtyOrdered();
        $finalPrice = $qty * $item->getPrice() - $item->getDiscountAmount();
        if ($finalPrice < 0.01) {
            return 0;
        }
        $profit = $this->getProfitAmount($item) / $finalPrice * 100;
        return $profit;
    }
}
