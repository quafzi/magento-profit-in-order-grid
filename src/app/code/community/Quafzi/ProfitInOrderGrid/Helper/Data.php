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
    protected $_items = array();

    /**
     * get profit of an order
     * 
     * @param Mage_Sales_Model_Order|Varien_Object $order 
     * @return Varien_Object
     */
    public function getProfit(Varien_Object $order)
    {
        $orderProfit = new Varien_Object();
        $orderProfit
            ->setContainsNegativeProfit(false)
            ->setContainsWrongData(false)
            ->setCost(0)
            ->setProfit(0);
        $this->_items[$order->getId()] = array();
        $rowNetPrices = array();
        foreach ($order->getAllItems() as $item) {
            $product = $item->getProduct();
            $qty = $item->getQtyOrdered();
            $rowCost = $product->getCost() * $qty;
            $rowNetPrice = $item->getRowTotal() - $item->getDiscountAmount();
            if (empty($rowPrice) && $item->getParentItemId()) {
                $rowNetPrice = $rowNetPrices[$item->getParentItemId()];
                $this->_items[$order->getId()][$item->getId()] = $this->_items[$order->getId()][$item->getParentItemId()];
            } else {
                $rowNetPrices[$item->getId()] = $rowNetPrice;
                $this->_items[$order->getId()][$item->getId()] = new Varien_Object(array(
                    'cost'      => $rowCost,
                    'net_price' => $rowNetPrice,
                ));
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
            $orderProfit
                ->setCost($orderProfit->getCost() + $rowCost)
                ->setProfit($orderProfit->getProfit() + $rowProfit)
                ->setIncome($orderProfit->getIncome() + $rowNetPrice);
            if (!isset($this->_items[$order->getId()][$item->getId()])) {
                $this->_items[$order->getId()][$item->getId()] = new Varien_Object();
            }
            $this->_items[$order->getId()][$item->getId()]
                ->setCost($rowCost)
                ->setNetPrice($rowNetPrice)
                ->setProfit($rowProfit);
        }
        return $orderProfit;
    }

    public function getItemProfit(Mage_Sales_Model_Order_Item $item)
    {
        if (empty($this->_items)) {
            $this->getProfit($item->getOrder());
        }
        if (!isset($this->_items[$item->getOrderId()][$item->getId()])) {
            return '';
        }
        $row = $this->_items[$item->getOrderId()][$item->getId()];

        $subTotals = array(
            $this->__('Net Cost')  => $row->getCost(),
            $this->__('Net Price') => $row->getNetPrice(),
        );
        $out = '<table class="qty-table" cellspacing="0">';
        foreach ($subTotals as $label=>$value) {
            $out .= '<tr><td>' . $label . '</td>'
                . '<td>' . Mage::helper('core')->formatPrice($value) . '</td></tr>';
        }
        $profit = $row->getProfit();
        $profitStyle = ((0 < $profit) ? '' : 'color:red;') . 'font-weight:bold';
        $out .= '<tr><td>' . $this->__('Profit') . '</td>'
            . '<td style="' . $profitStyle . '">'
            . Mage::helper('core')->formatPrice($profit)
            . '</td></tr>';
        $out .= '</table>';
        return $out;
    }
}
