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
		$menuItemList->sqlOrderBy = 'menu_item.showOrder ASC';
		$menuItemList->readObjects();
		
		$menuItems = array();
		foreach ($menuItemList as $menuItem) {
			if (!isset($menuItems[$menuItem->menuName])) {
				$menuItems[$menuItem->menuName] = array();
			}
			
			$menuItems[$menuItem->menuName][$menuItem->menuItemID] = $menuItem;
		}
		
		return $menuItems;
	}
}
