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
        $rowNetPrices = array();
        foreach ($order->getAllItems() as $item) {
            $product = $item->getProduct();
            $qty = $item->getQtyOrdered();
            $rowCost = $product->getCost() * $qty;
            $rowNetPrice = $item->getRowTotal() - $item->getDiscountAmount();
            if (empty($rowPrice) && $item->getParentItemId()) {
                $rowNetPrice = $rowNetPrices[$item->getParentItemId()];
            } else {
                $rowNetPrices[$item->getId()] = $rowNetPrice;
            }
            if ($rowNetPrice <= 0) {
                continue;
            }
            if (in_array($product->getTypeId(), array('configurable', 'grouped'))) {
                continue;
            }
            if (in_array($product->getTypeId(), array('bundle'))) {
                $orderProfit->setContainsWrongData(true);
            }
            if (empty($rowCost) || 100*$rowCost/$rowNetPrice < 10) {
                // less than 10 percent cost => that's probably an error in data...
                $orderProfit->setContainsWrongData(true);
                $rowCost = $rowNetPrice;
            } elseif ($rowNetPrice < $rowCost) {
                $orderProfit->setContainsNegativeProfit(true);
            }
            $rowProfit = $rowNetPrice - $rowCost;
            $orderProfit->setCost($orderProfit->getCost() + $rowCost);
            $orderProfit->setProfit($orderProfit->getProfit() + $rowProfit);
            $orderProfit->setIncome($orderProfit->getIncome() + $rowNetPrice);
        }
        return $orderProfit;
    }
}
