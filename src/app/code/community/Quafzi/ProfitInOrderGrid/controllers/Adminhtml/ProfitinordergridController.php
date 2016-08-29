<?php
/**
 * Adminhtml controller
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
 * Quafzi_ProfitInOrderGrid_Adminhtml_ProfitinordergridController
 *
 * @category Mage_Sales
 * @package  Quafzi_ProfitInOrderGrid
 * @author   Thomas Birke <magento@netextreme.de>
 * @license  http://opensource.org/licenses/osl-3.0.php OSL 3.0
 * @link     https://github.com/quafzi/magento-profit-in-order-grid
 */
class Quafzi_ProfitInOrderGrid_Adminhtml_ProfitinordergridController
    extends Mage_Adminhtml_Controller_Action
{
    public function updateCostAction ()
    {
        $itemId = (int)$this->getRequest()->getPost('item_id');
        $value = (float)$this->getRequest()->getPost('value');
        $item = Mage::getModel('sales/order_item')->load($itemId);
        if ($item->getId()) {
            if ($item->getParentItemId()) {
                $item = $item->getParentItem();
            }
            $item->setCustomCost($value)->save();
            $item->getOrder()->setCost(null)->save();
            $item = Mage::getModel('sales/order_item')->load($item->getId());

            echo Mage::getSingleton('core/layout')->createBlock(
                'quafzi_profitinordergrid/sales_order_item_profit',
                'order_item_profit_column',
                ['template' => 'quafzi/profitinordergrid/sales/order/item/profit.phtml']
            )->setItem($item)->toHtml();
        }
    }
}
