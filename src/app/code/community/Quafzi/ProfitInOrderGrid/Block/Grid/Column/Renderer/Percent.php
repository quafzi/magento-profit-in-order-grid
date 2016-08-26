<?php
/**
 * Percent Renderer
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
class Quafzi_ProfitInOrderGrid_Block_Grid_Column_Renderer_Percent
    extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Number
{
    protected $_defaultWidth = 100;

    /**
     * Returns value of the row
     *
     * @param Varien_Object $row
     * @return mixed|string
     */
    protected function _getValue(Varien_Object $row)
    {
        return parent::_getValue($row) . ' %';
    }
}
