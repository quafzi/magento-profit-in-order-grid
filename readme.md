Quafzi ProfitInOrderGrid Extension
==================================

[![Build Status](https://travis-ci.org/quafzi/magento-profit-in-order-grid.png)](https://travis-ci.org/quafzi/magento-profit-in-order-grid)

This extension shows your profit in order grid.

Facts
-----
- version: see ``config.xml``
- [extension on GitHub](https://github.com/quafzi/magento-profit-in-order-grid)
- Composer package name: quafzi/magento-profit-in-order-grid

Description
-----------
This extension adds 3 new columns to your order grid, containing cost, profit
amount and markdown margin of the order. In order view, the same information are
shown in the order items grid. In addition, you may specify a custom cost per
item. Changing that cost results in recalculation of profit.

All values are stored as separate fields in order tables and order item table.

Requirements
------------
- PHP >= 5.6.0
- Mage_Core
- Maintained cost attribute for every product

Compatibility
-------------
- Magento >= 1.4

Installation Instructions
-------------------------
1. Install the extension via Composer/Modman with the key shown above or copy all the files into your document root.
2. Clear the cache, logout from the admin panel and then login again.

Uninstallation
--------------
Remove all extension files from your Magento installation

Support
-------
If you have any issues with this extension, open an issue on [GitHub](https://github.com/quafzi/magento-profit-in-order-grid/issues).

Contribution
------------
Any contribution is highly appreciated. The best way to contribute code is to open a [pull request on GitHub](https://help.github.com/articles/using-pull-requests).

Developer
---------

Thomas Birke ([@quafzi](https://twitter.com/quafzi))

Licence
-------
[OSL - Open Software Licence 3.0](http://opensource.org/licenses/osl-3.0.php)

Copyright
---------
(c) 2015-2016 Thomas Birke

