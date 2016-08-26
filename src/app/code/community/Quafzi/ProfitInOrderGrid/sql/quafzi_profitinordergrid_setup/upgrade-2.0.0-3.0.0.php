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

// add custom cost
$tables = [
    $this->getTable('sales_flat_order_item'),
    $this->getTable('sales_flat_order'),
    $this->getTable('sales_flat_order_grid')
];
foreach ($tables as $table) {
    $field = 'custom_cost';
    try {
        $installer->run("ALTER TABLE $table ADD `$field` $decimal(12,4) NULL;");
    } catch (Exception $e) {
        // Column seems to exist already
    }
}

$installer->endSetup();
