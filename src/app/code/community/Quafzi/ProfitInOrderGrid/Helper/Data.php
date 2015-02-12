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
            if ($item->getParentItemId()) {
                $row = new Varien_Object();
                $row->setCost($item->getProduct()->getCost() * $item->getQtyOrdered())
                    ->setNetPrice($item->getRowTotal() - $item->getDiscountAmount());
                if (false === isset($this->_items[$order->getId()][$item->getParentItemId()])) {
                    $this->_items[$order->getId()][$item->getParentItemId()] = new Varien_Object($row->getData());
                } else {
                    $parentData = $this->_items[$order->getId()][$item->getParentItemId()];
                    if (0 == $parentData->getCost()) {
                        $parentData->setCost($row->getCost());
                    }
                    if (0 == $this->_items[$order->getId()][$item->getParentItemId()]->getNetPrice()) {
                        $parentData->setNetPrice($row->getNetPrice());
                    }
                    $parentData->setProfit($parentData->getNetPrice() - $parentData->getCost());
                }
                continue;
            }
            if (false === isset($this->_items[$order->getId()][$item->getId()])) {
                $this->_items[$order->getId()][$item->getId()] = new Varien_Object();
                $row = new Varien_Object();
            } else {
                $row = $this->_items[$order->getId()][$item->getId()];
            }
            if (0 == $row->getCost()) {
                $row->setCost($item->getProduct()->getCost() * $item->getQtyOrdered());
            }
            if (0 == $row->getNetPrice()) {
                $row->setNetPrice($item->getRowTotal() - $item->getDiscountAmount());
            }
            $row->setTypeId($item->getProduct()->getTypeId());
            $this->_items[$order->getId()][$item->getId()] = $row;
        }

        foreach ($this->_items[$order->getId()] as $row) {
            $row->setProfit($row->getNetPrice() - $row->getCost());
            if (0 == $row->getCost() || 100*$row->getCost()/$row->getNetPrice() < 10) {
                // less than 10 percent cost => that's probably an error in data...
                $orderProfit->setContainsWrongData(true);

                //$row->setCost($row->getNetPrice());
            } elseif ($row->getNetPrice() < $row->getCost()) {
                $orderProfit->setContainsNegativeProfit(true);
            }
            $orderProfit
                ->setCost($orderProfit->getCost() + $row->getCost())
                ->setProfit($orderProfit->getProfit() + $row->getProfit())
                ->setIncome($orderProfit->getIncome() + $row->getNetPrice());
        }
        return $orderProfit;
    }

    public function getItemProfit(Mage_Sales_Model_Order_Item $item)
    {
        if ($item->getParentItemId()) {
            return '';
        }
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
