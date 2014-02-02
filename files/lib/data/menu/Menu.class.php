<?php
namespace wcf\data\menu;
use wcf\data\menu\item\MenuItem;
use wcf\data\DatabaseObject;
use wcf\data\DatabaseObjectDecorator;
use wcf\data\IPermissionObject;
use wcf\system\event\EventHandler;
use wcf\system\exception\SystemException;
use wcf\system\menu\MenuHandler;
use wcf\system\WCF;

/**
 * Represents a menu.
 * 
 * @author	Matthias Schmidt
 * @copyright	2014 Maasdt
 * @license	GNU Lesser General Public License <http://www.gnu.org/licenses/lgpl.html>
 * @package	com.maasdt.wcf.menu
 * @subpackage	data.menu
 * @category	Community Framework
 */
class Menu extends DatabaseObject {
	/**
	 * names of the active menu items
	 * @var	array<string>
	 */
	public $activeMenuItems = array();
	
	/**
	 * visible menu items of the menu
	 * @var	array
	 */
	public $menuItemList = null;
	
	/**
	 * all menu items of the menu
	 * @var	array
	 */
	public $menuItems = null;
	
	/**
	 * object the menu belongs to
	 * @var	\wcf\data\DatabaseObject
	 */
	protected $object = null;
	
	/**
	 * @see	\wcf\data\DatabaseObject::$databaseTableIndexName
	 */
	protected static $databaseTableIndexName = 'menuID';
	
	/**
	 * @see	\wcf\data\DatabaseObject::$databaseTableIndexName
	 */
	protected static $databaseTableName = 'menu';
	
	/**
	 * @see	\wcf\data\IStorableObject::__get()
	 */
	public function __get($name) {
		$value = parent::__get($name);
		
		// check additional data
		if ($value === null && isset($this->data['additionalData'][$name])) {
			$value = $this->data['additionalData'][$name];
		}
		
		return $value;
	}
	
	/**
	 * Builds the menu structure.
	 */
	protected function buildMenu() {
		$this->menuItems = array();
		$this->menuItemList = array();
		
		foreach (MenuHandler::getInstance()->getMenuItems($this) as $menuItem) {
			if (!isset($this->menuItems[$menuItem->parentMenuItem])) {
				$this->menuItems[$menuItem->parentMenuItem] = array();
			}
			
			$this->menuItems[$menuItem->parentMenuItem][] = clone $menuItem;
		}
		
		// call 'didInit' event
		EventHandler::getInstance()->fireAction($this, 'didInit');
		
		$this->checkMenuItems();
		$this->removeEmptyMenuItems();
		$this->buildMenuItemList();
		
		// call 'didBuild' event
		EventHandler::getInstance()->fireAction($this, 'didBuild');
	}
	
	/**
	 * Builds one level of the menu item list.
	 * 
	 * @param	string		$parentMenuItem
	 */
	protected function buildMenuItemList($parentMenuItem = '') {
		if (!isset($this->menuItems[$parentMenuItem])) {
			return;
		}
		
		foreach ($this->menuItems[$parentMenuItem] as $menuItem) {
			$this->menuItemList[$menuItem->menuItem] = $menuItem;
			$this->buildMenuItemList($menuItem->menuItem);
		}
	}
	
	/**
	 * Returns true if the given menu item is visible for the active user.
	 * 
	 * @param	\wcf\data\menu\item\MenuItem	$menuItem
	 * @return	boolean
	 */
	protected function checkMenuItem(MenuItem $menuItem) {
		// check options
		if (!empty($menuItem->options)) {
			$optionsCheck = false;
			
			foreach (explode(',', strtoupper($menuItem->options)) as $option) {
				if (defined($option) && constant($option)) {
					$optionsCheck = true;
					break;
				}
			}
			
			if (!$optionsCheck) {
				return false;
			}
		}
		
		// check permissions
		if (!empty($menuItem->permissions)) {
			$permissionsCheck = false;
			
			foreach (explode(',', $menuItem->permissions) as $permission) {
				if (WCF::getSession()->getPermission($permission)) {
					$permissionsCheck = true;
					break;
				}
			}
			
			if (!$permissionsCheck) {
				return false;
			}
		}
		
		// check object properties
		if ($this->object && !empty($menuItem->objectProperties)) {
			$propertiesCheck = false;
			
			foreach (explode(',', $menuItem->objectProperties) as $objectProperty) {
				if ($this->object->__get($objectProperty)) {
					$propertiesCheck = true;
					break;
				}
			}
			
			if (!$propertiesCheck) {
				return false;
			}
		}
		
		// check object permissions
		if ($this->object && !empty($menuItem->objectPermissions) && $this->object instanceof IPermissionObject) {
			$objectPermissionsCheck = false;
			
			foreach (explode(',', $menuItem->objectPermissions) as $permission) {
				if ($this->object->getPermission($permission)) {
					$objectPermissionsCheck = true;
					break;
				}
			}
			
			if (!$objectPermissionsCheck) {
				return false;
			}
		}
		
		return $menuItem->getProcessor()->isVisible();
	}
	
	/**
	 * Checks if the menu items with the given parent menu item are visible
	 * for the active user.
	 * 
	 * @param	string		$parentMenuItem
	 */
	protected function checkMenuItems($parentMenuItem = '') {
		if (!isset($this->menuItems[$parentMenuItem])) {
			return;
		}
		
		foreach ($this->menuItems[$parentMenuItem] as $key => $menuItem) {
			$menuItem->setMenu($this);
			if ($this->checkMenuItem($menuItem)) {
				$this->checkMenuItems($menuItem->menuItem);
			}
			else {
				unset($this->menuItems[$parentMenuItem][$key]);
			}
		}
	}
	
	/**
	 * Returns the name of the active menu item at the given level. If no such
	 * active menu item exists, an empty string is returned.
	 * 
	 * @param	integer		$level
	 * @return	string
	 */
	public function getActiveMenuItem($level = 0) {
		if (isset($this->activeMenuItems[$level])) {
			return $this->activeMenuItems[$level];
		}
		
		return '';
	}
	
	/**
	 * Returns the names of the active menu items of the menu.
	 * 
	 * @return	array<string>
	 */
	public function getActiveMenuItems() {
		return $this->activeMenuItems;
	}
	
	/**
	 * Returns the menu items with the given parent menu item.
	 * 
	 * @param	string		$parentMenuItem
	 * @return	array<\wcf\data\menu\item\MenuItem>
	 */
	public function getMenuItems($parentMenuItem = '') {
		if ($this->menuItemList === null) {
			$this->buildMenu();
		}
		
		if (isset($this->menuItems[$parentMenuItem])) {
			return $this->menuItems[$parentMenuItem];
		}
		
		return array();
	}
	
	/**
	 * Returns the object the menu belongs to or null if the menu does not belong
	 * to any object.
	 * 
	 * @return	\wcf\data\DatabaseObject
	 */
	public function getObject() {
		return $this->object;
	}
	
	/**
	 * @see	\wcf\data\DatabaseObject::handleData()
	 */
	protected function handleData($data) {
		// handle additional data
		$data['additionalData'] = @unserialize($data['additionalData']);
		if (!is_array($data['additionalData'])) {
			$data['additionalData'] = array();
		}
		
		parent::handleData($data);
	}
	
	/**
	 * Removes the empty menu items with the given parent menu item.
	 * 
	 * @param	string		$parentMenuItem
	 */
	protected function removeEmptyMenuItems($parentMenuItem = '') {
		if (!isset($this->menuItems[$parentMenuItem])) {
			return;
		}
		
		foreach ($this->menuItems[$parentMenuItem] as $key => $menuItem) {
			$this->removeEmptyMenuItems($menuItem->menuItem);
			
			if ((!isset($this->menuItems[$menuItem->menuItem]) || empty($this->menuItems[$menuItem->menuItem])) && ((empty($menuItem->menuItemLink) && empty($menuItem->menuItemController)) || $menuItem->hideChildless)) {
				unset($this->menuItems[$parentMenuItem][$key]);
			}
		}
	}
	
	/**
	 * Sets the menu item with the given name as active menu item.
	 * 
	 * @param	string		$menuItem
	 */
	public function setActiveMenuItem($menuItem) {
		if ($this->menuItemList === null) {
			$this->buildMenu();
		}
		
		$this->activeMenuItems = array();
		while (isset($this->menuItemList[$menuItem])) {
			array_unshift($this->activeMenuItems, $menuItem);
			$menuItem = $this->menuItemList[$menuItem]->parentMenuItem;
		}
	}
	
	/**
	 * Sets the object the menu belongs to.
	 * 
	 * @param	\wcf\data\DatabaseObject	$object
	 */
	public function setObject(DatabaseObject $object) {
		if (!$this->className) {
			throw new SystemException("Menu does not support objects");
		}
		
		// check if object is of the expected class
		if (!($object instanceof $this->className)) {
			// check if object is a decorated object of the expected class
			if (!($object instanceof DatabaseObjectDecorator) || !($object->getDecoratedObject() instanceof $this->className)) {
				throw new SystemException("Object has to be instance of class '".$this->className."'.");
			}
		}
		
		$this->object = $object;
	}
}
