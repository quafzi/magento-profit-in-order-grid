<?php
/**
 * Adding order and order item attributes to store cost and profit
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
$installer = $this;
$installer->startSetup();

$decimal = Varien_Db_Ddl_Table::TYPE_DECIMAL;
$fields = ['cost', 'profit_amount', 'profit_percent'];

// add new fields for cost and profit
$tables = [
    $this->getTable('sales_flat_order_item'),
    $this->getTable('sales_flat_order')
];
foreach ($tables as $table) {
    foreach ($fields as $field) {
        $installer->run("ALTER TABLE $orderItemTable ADD `$field` $decimal NULL;");
    }
}

$installer->endSetup();
