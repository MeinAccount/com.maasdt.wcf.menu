<?php
namespace wcf\system\menu;
use wcf\data\DatabaseObject;
use wcf\system\cache\builder\MenuCacheBuilder;
use wcf\system\cache\builder\MenuItemCacheBuilder;
use wcf\system\exception\SystemException;
use wcf\system\SingletonFactory;

/**
 * Handles the menus.
 * 
 * @author	Matthias Schmidt
 * @copyright	2014 Maasdt
 * @license	GNU Lesser General Public License <http://www.gnu.org/licenses/lgpl.html>
 * @package	com.maasdt.wcf.menu
 * @subpackage	system.menu
 * @category	Community Framework
 */
class MenuHandler extends SingletonFactory {
	/**
	 * list of cached menu items
	 * @var	array<\wcf\data\menu\item\MenuItem>
	 */
	protected $menuItems = array();
	
	/**
	 * list of cached menus
	 * @var	array<\wcf\data\menu\Menu>
	 */
	protected $menus = array();
	
	/**
	 * list of requested menus
	 * @var	array<\wcf\data\menu\Menu>
	 */
	protected $requestedMenus = array();
	
	/**
	 * Returns the menu with the given name for the given object.
	 * 
	 * @param	string				$menuName
	 * @param	\wcf\data\DatabaseObject	$object
	 * @return	\wcf\data\menu\Menu
	 */
	public function getMenu($menuName, DatabaseObject $object = null) {
		$menuIdentifier = sha1($menuName.($object->getObjectID() ? $object->getObjectID() : ''));
		
		if (!isset($this->requestedMenus[$menuIdentifier])) {
			$menu = $this->getMenuByName($menuName);
			if ($menu === null) {
				throw new SystemException("Unknown menu with name '".$menuName."'");
			}
			
			$this->requestedMenus[$menuIdentifier] = clone $menu;
			if ($object) {
				$this->requestedMenus[$menuIdentifier]->setObject($object);
			}
		}
		
		return $this->requestedMenus[$menuIdentifier];
	}
	
	/**
	 * Returns the menu with the given name. If no such menu exists, null is
	 * returned.
	 * 
	 * @param	string		$menuName
	 * @return	\wcf\data\menu\Menu
	 */
	public function getMenuByName($menuName) {
		foreach ($this->menus as $menu) {
			if ($menu->menuName == $menuName) {
				return $menu;
			}
		}
		
		return null;
	}
	
	/**
	 * Returns the menu items of the given menu.
	 * 
	 * @param	\wcf\data\menu\Menu		$menu
	 * @return	array<\wcf\data\menu\item\MenuItem>
	 */
	public function getMenuItems($menu) {
		if (isset($this->menuItems[$menu->menuName])) {
			return $this->menuItems[$menu->menuName];
		}
		
		return array();
	}
	
	/**
	 * @see	\wcf\system\SingletonFactory::init()
	 */
	protected function init() {
		$this->menus = MenuCacheBuilder::getInstance()->getData();
		$this->menuItems = MenuItemCacheBuilder::getInstance()->getData();
	}
}
