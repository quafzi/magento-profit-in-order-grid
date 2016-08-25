<?php
/**
 * Data Helper
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
 * @package    Quafzi_ProfitInOrderGrid
 * @copyright  Copyright (c) 2015 Thomas Birke
 * @author     Thomas Birke <tbirke@netextreme.de>
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Quafzi_ProfitInOrderGrid_Helper_Data extends Mage_Core_Helper_Data
{
    public function getItemProfit(Mage_Sales_Model_Order_Item $item)
    {
        $out = '<table class="qty-table" cellspacing="0">';
        foreach (['cost', 'profit_amount', 'profit_percent'] as $field) {
            $value = $item->getData($field);
            $profitStyle = ((0 < $value) ? '' : 'color:red;') . 'font-weight:bold';
            $value = 'profit_percent' !== $field
                ? Mage::helper('core')->formatPrice($value, false)
                : Mage::app()->getStore()->getCurrentCurrency()->formatTxt(
                        $value,
                        ['display' => Zend_Currency::NO_SYMBOL]
                    ) . '&nbsp;%';
            $out .= '<tr><td>' . $this->__($field) . '</td>'
                . '<td style="' . $profitStyle . '">'
                . $value
                . '</td></tr>';
        }
        $out .= '</table>';
        return $out;
    }
}
