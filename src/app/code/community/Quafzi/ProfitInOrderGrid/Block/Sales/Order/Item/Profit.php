<?php
/**
 * Profit Column Renderer
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
 * Quafzi_ProfitInOrderGrid_Block_Grid_Column_Renderer_Percent
 *
 * @category Mage_Sales
 * @package  Quafzi_ProfitInOrderGrid
 * @author   Thomas Birke <magento@netextreme.de>
 * @license  http://opensource.org/licenses/osl-3.0.php OSL 3.0
 * @link     https://github.com/quafzi/magento-profit-in-order-grid
 */
class Quafzi_ProfitInOrderGrid_Block_Sales_Order_Item_Profit
    extends Mage_Adminhtml_Block_Template
{
    protected $_item;

    public function getValue($field)
    {
        return $this->_item->getData($field);
    }

    public function setItem(Mage_Sales_Model_Order_Item $item)
    {
        $this->_item = $item;
        return $this;
    }

    public function getId()
    {
        return $this->_item->getId();
    }

    public function getFormattedValue($field)
    {
        $value = $this->_item->getData($field);
        if ('profit_percent' === $field) {
            return Mage::app()->getStore()->getCurrentCurrency()->formatTxt(
                $value,
                ['display' => Zend_Currency::NO_SYMBOL]
            ) . '&nbsp;%';
        }
        return Mage::helper('core')->formatPrice($value, false);
    }

    public function getUpdateCostUrl()
    {
        return $this->getUrl('adminhtml/profitinordergrid/updateCost');
    }
}
