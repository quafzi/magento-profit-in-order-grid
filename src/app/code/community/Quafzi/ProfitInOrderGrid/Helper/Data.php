<?php
/**
 * @package    Quafzi_ProfitInOrderGrid
 * @copyright  Copyright (c) 2015 Thomas Birke
 * @author     Thomas Birke <tbirke@netextreme.de>
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class Quafzi_ProfitInOrderGrid_Helper_Data
    extends Mage_Core_Helper_Data
{
    /**
     * get profit of an order
     * 
     * @param Mage_Sales_Model_Order|Varien_Object $order 
     * @return Varien_Object
     */
    public function getProfit(Varien_Object $order)
    {
        $orderProfit = new Varien_Object();
        $orderProfit->setContainsNegativeProfit(false);
        $orderProfit->setContainsWrongData(false);
        $orderProfit->setCost(0);
        $orderProfit->setProfit(0);
        $foo = [];
        foreach ($order->getAllItems() as $item) {
            $product = $item->getProduct();
            $qty = $item->getQtyOrdered();
            $rowCost = $product->getCost() * $qty;
            $rowPrice = $item->getRowTotalInclTax() - $item->getDiscountAmount();
            if ($rowPrice <= 0) {
                continue;
            }
            if (empty($rowCost) || 100*$rowCost/$rowPrice < 10) {
                // less than 10 percent cost => that's probably an error in data...
                $orderProfit->setContainsWrongData(true);
                $rowCost = $rowPrice;
            } elseif ($rowPrice < $rowCost) {
                $orderProfit->setContainsNegativeProfit(true);
            }
            $rowProfit = $rowPrice - $rowCost;
            $foo[] = [
                'sku' => $product->getSku(),
                'type' => $product->getTypeId(),
                'price' => $rowPrice,
                'cost' => $rowCost,
                'profit' => $rowProfit
            ];
            $orderProfit->setCost($orderProfit->getCost() + $rowCost);
            $orderProfit->setProfit($orderProfit->getProfit() + $rowProfit);
            $orderProfit->setIncome($orderProfit->getIncome() + $rowPrice);
        }
        return $orderProfit;
    }
}
