<?php
/**
 * @package    Quafzi_ProfitInOrderGrid
 * @copyright  Copyright (c) 2015 Thomas Birke
 * @author     Thomas Birke <tbirke@netextreme.de>
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class Quafzi_ProfitInOrderGrid_Block_Renderer_Profit
    extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    public function render(Varien_Object $order)
    {
        $helper = Mage::helper('quafzi_profitinordergrid');
        $orderProfit = $helper->getProfit($order);
        return '<span'
            . ($orderProfit->getContainsNegativeProfit() ? ' style="color:red;" title="' . $helper->__('At least one product was gifted!') . '"' : '')
            . ($orderProfit->getContainsWrongData() ? ' style="color:#aaa;" title="' . $helper->__('For at least one product it seems, that we do not know the real cost!') . '"' : '')
            . '>' . $helper->formatCurrency($orderProfit->getProfit())
//            . ' / ' . $helper->formatCurrency($orderProfit->getIncome())
            . '</span>';
    }
}
