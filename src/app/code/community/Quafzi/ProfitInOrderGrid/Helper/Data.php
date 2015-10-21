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
            /** @var $item Mage_Sales_Model_Order_Item */
            if ($item->getParentItemId()) {
                $this->_handleChildItem($item, $order);
            } else {
                $this->_handleParentItem($item, $order);
            }
        }

        foreach ($this->_items[$order->getId()] as $row) {
            $row->setProfit($row->getNetPrice() - $row->getTotalCost());
            if (0 == $row->getNetPrice()
                || 0 == $row->getTotalCost()
                || 100*$row->getTotalCost()/$row->getNetPrice() < 10
            ) {
                // less than 10 percent cost => that's probably an error in data...
                $orderProfit->setContainsWrongData(true);

                //$row->setCost($row->getNetPrice());
            } elseif ($row->getNetPrice() < $row->getTotalCost()) {
                $orderProfit->setContainsNegativeProfit(true);
            }
            $orderProfit
                ->setCost($orderProfit->getCost() + $row->getTotalCost())
                ->setProfit($orderProfit->getProfit() + $row->getProfit())
                ->setIncome($orderProfit->getIncome() + $row->getNetPrice());
        }
        return $orderProfit;
    }

    protected function _handleChildItem ($item, $order) {
        if (false === isset($this->_items[$order->getId()][$item->getParentItemId()])) {
            $this->_items[$order->getId()][$item->getParentItemId()] = new Varien_Object();
        }
        $row = $this->_items[$order->getId()][$item->getParentItemId()];

        // be careful: correct qty is given for parent item, child item may contain strange qtys
        $cost = $row->getCost() ?: $item->getProduct()->getCost();
        $qtyOrdered = $row->getQtyOrdered() ?: $item->getQtyOrdered();
        $netPrice = $row->getNetPrice() ?: $item->getRowTotal() - $item->getDiscountAmount();

        if ('bundle' == $row->getTypeId()) {
            $cost = $item->getProduct()->getCost();
            $row->setTotalCost($row->getTotalCost() + $cost * $qtyOrdered);
        } else {
            $row->setTotalCost($cost * $qtyOrdered);
        }
        $row->setCost($cost)
            ->setNetPrice($netPrice)
            ->setProfit($netPrice - $row->getTotalCost());
    }

    protected function _handleParentItem ($item, $order) {
        if (false === isset($this->_items[$order->getId()][$item->getId()])) {
            $this->_items[$order->getId()][$item->getId()] = new Varien_Object();
        }
        $row = $this->_items[$order->getId()][$item->getId()];
        $row->setQtyOrdered($item->getQtyOrdered());
        $row->setTypeId($item->getProduct()->getTypeId());
        if (false === in_array($row->getTypeId(), Mage_Catalog_Model_Product_Type::getCompositeTypes())) {
            /* there is no child item */
            $cost = $item->getProduct()->getCost();
            $row->setCost($cost);
            $row->setTotalCost($cost * $row->getQtyOrdered());
        }
        $row->setNetPrice($item->getRowTotal() - $item->getDiscountAmount());
        $row->setProfit($row->getNetPrice() - $row->getTotalCost());
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
            $this->__('Net Cost')  => $row->getTotalCost(),
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
