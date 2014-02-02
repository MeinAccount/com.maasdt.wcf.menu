<?php
namespace wcf\system\menu;
use wcf\data\DatabaseObjectDecorator;

/**
 * Default implementation of a menu item provider.
 * 
 * @author	Matthias Schmidt
 * @copyright	2014 Maasdt
 * @license	GNU Lesser General Public License <http://www.gnu.org/licenses/lgpl.html>
 * @package	com.maasdt.wcf.menu
 * @subpackage	system.menu
 * @category	Community Framework
 */
class DefaultMenuItemProvider extends DatabaseObjectDecorator implements IMenuItemProvider {
	/**
	 * @see	\wcf\data\DatabaseObjectDecorator::$baseClass
	 */
	protected static $baseClass = 'wcf\data\menu\item\MenuItem';
	
	/**
	 * @see	\wcf\system\menu\IMenuItemProvider::getLink()
	 */
	public function getLink() {
		return $this->getDecoratedObject()->getLink();
	}
	
	/**
	 * @see	\wcf\system\menu\IMenuItemProvider::getNotifications()
	 */
	public function getNotifications() {
		return 0;
	}
	
	/**
	 * @see	\wcf\system\menu\IMenuItemProvider::isVisible()
	 */
	public function isVisible() {
		return true;
	}
}
