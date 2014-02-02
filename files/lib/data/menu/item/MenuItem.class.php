<?php
namespace wcf\data\menu\item;
use wcf\data\menu\Menu;
use wcf\data\ILinkableObject;
use wcf\data\ProcessibleDatabaseObject;
use wcf\system\exception\SystemException;
use wcf\system\request\LinkHandler;
use wcf\system\Regex;
use wcf\system\WCF;

/**
 * Represents a menu item.
 * 
 * @author	Matthias Schmidt
 * @copyright	2014 Maasdt
 * @license	GNU Lesser General Public License <http://www.gnu.org/licenses/lgpl.html>
 * @package	com.maasdt.wcf.menu
 * @subpackage	data.menu.item
 * @category	Community Framework
 */
class MenuItem extends ProcessibleDatabaseObject implements ILinkableObject {
	/**
	 * abbreviation of the application the menu item belongs to
	 * @var	string
	 */
	protected $application = null;
	
	/**
	 * name of the menu item controller
	 * @var	string
	 */
	protected $controller = null;
	
	/**
	 * link of the menu item
	 * @var	string
	 */
	protected $link = null;
	
	/**
	 * menu the menu item belongs to
	 * @var	\wcf\data\menu\Menu
	 */
	protected $menu = null;
	
	/**
	 * @see	\wcf\data\DatabaseObject::$databaseTableIndexName
	 */
	protected static $databaseTableIndexName = 'menuItemID';
	
	/**
	 * @see	\wcf\data\DatabaseObject::$databaseTableIndexName
	 */
	protected static $databaseTableName = 'menu_item';
	
	/**
	 * @see	\wcf\data\ProcessibleDatabaseObject::$processorInterface
	 */
	protected static $processorInterface = 'wcf\system\menu\IMenuItemProvider';
	
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
	 * @see	\wcf\data\ILinkableObject::getLink()
	 */
	public function getLink() {
		if ($this->link === null) {
			$this->link = '';
			
			if (!empty($this->menuItemController) || !empty($this->menuItemLink)) {
				$this->parseController();
				
				if ($this->controller) {
					$this->link = LinkHandler::getInstance()->getLink($this->controller, array(
						'application' => $this->application,
						'object' => $this->getMenu()->getObject()
					), WCF::getLanguage()->get($this->menuItemLink));
				}
				else {
					$this->link = WCF::getLanguage()->get($this->menuItemLink);
				}
			}
		}
		
		return $this->link;
	}
	
	/**
	 * Returns the menu the menu item belongs to.
	 * 
	 * @return	\wcf\data\menu\Menu
	 */
	public function getMenu() {
		return $this->menu;
	}
	
	/**
	 * @see	\wcf\data\ProcessibleDatabaseObject::getProcessor()
	 */
	public function getProcessor() {
		if (parent::getProcessor() === null) {
			$className = 'wcf\system\menu\DefaultMenuItemProvider';
			if ($this->getMenu()->defaultMenuItemProvider) {
				$className = $this->getMenu()->defaultMenuItemProvider;
			}
			
			$this->processor = new $className($this);
		}
		
		return $this->processor;
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
	 * Parses the controller of the menu item and extracts the name of the real
	 * controller and the abbreviation of the application.
	 */
	protected function parseController() {
		if ($this->controller === null) {
			$this->controller = '';
			
			if ($this->menuItemController) {
				$parts = explode('\\', $this->menuItemController);
				$this->application = array_shift($parts);
				$this->controller = Regex::compile('(Action|Form|Page)$')->replace(array_pop($parts), '');
			}
		}
	}
	
	/**
	 * Sets the menu the menu item belongs to.
	 * 
	 * @param	\wcf\data\menu\Menu		$menu
	 */
	public function setMenu(Menu $menu) {
		if ($menu->menuName != $this->menuName) {
			throw new SystemException("Menu with name '".$this->menuName."' expected, menu with name '".$menu->menuName."' given");
		}
		
		$this->menu = $menu;
	}
}
