<?php
namespace wcf\system\cache\builder;
use wcf\data\menu\item\MenuItemList;

/**
 * Handles the menu item cache.
 * 
 * @author	Matthias Schmidt
 * @copyright	2014 Maasdt
 * @license	GNU Lesser General Public License <http://www.gnu.org/licenses/lgpl.html>
 * @package	com.maasdt.wcf.menu
 * @subpackage	system.cache.builder
 * @category	Community Framework
 */
class MenuItemCacheBuilder extends AbstractCacheBuilder {
	/**
	 * @see	\wcf\system\cache\builder\AbstractCacheBuilder::rebuild()
	 */
	protected function rebuild(array $parameters) {
		$menuItemList = new MenuItemList();
		$menuItemList->readObjects();
		
		return $menuItemList->getObjects();
	}
}
