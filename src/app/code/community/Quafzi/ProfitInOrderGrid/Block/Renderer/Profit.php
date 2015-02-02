<?php
/**
 * @package    Quafzi_ProfitInOrderGrid
 * @copyright  Copyright (c) 2013 Thomas Birke
 * @author     Thomas Birke <tbirke@netextreme.de>
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class Quafzi_ProfitInOrderGrid_Block_Renderer_Profit
    extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    public function render(Varien_Object $order)
    {
        $cost   = 0;
        $profit = 0;
        $alertNegative = false;
        $alertWrongData = false;
        $helper = Mage::helper('quafzi_profitinordergrid');
        foreach ($order->getAllItems() as $item) {
            $product = $item->getProduct();
            $itemCost   = $product->getCost();

            $itemPrice  = $item->getPrice();
            if (0 == $itemPrice) {
                continue;
            }
            $itemProfit = $itemPrice - $itemCost;
            if (empty($itemCost) || 100*$itemCost/$itemPrice < 10) {
                // less than 10 percent cost => that's probably an error in data...
                $alertWrongData = true;
            } elseif ($itemPrice < $itemCost) {
                $alertNegative = true;
            }
            $cost += $itemCost;
            $profit += $itemProfit;
        }
        return '<span'
            . ($alertNegative ? ' style="color:red;" title="' . $helper->__('At least one product was gifted!') . '"' : '')
            . ($alertWrongData ? ' style="color:#aaa;" title="' . $helper->__('For at least one product it seems, that we do not know the real cost!') . '"' : '')
            . '>' . $profit . '</span>';
    }
}
