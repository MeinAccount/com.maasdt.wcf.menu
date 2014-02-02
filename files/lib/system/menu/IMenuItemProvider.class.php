<?php
namespace wcf\system\menu;
use wcf\data\IDatabaseObjectProcessor;

/**
 * Every menu item provider has to implement this interface.
 * 
 * @author	Matthias Schmidt
 * @copyright	2014 Maasdt
 * @license	GNU Lesser General Public License <http://www.gnu.org/licenses/lgpl.html>
 * @package	com.maasdt.wcf.menu
 * @subpackage	system.menu
 * @category	Community Framework
 */
interface IMenuItemProvider extends IDatabaseObjectProcessor {
	/**
	 * Returns the link of the menu item.
	 * 
	 * @return	string
	 */
	public function getLink();
	
	/**
	 * Returns the number of notifications for the menu item.
	 * 
	 * @return	integer
	 */
	public function getNotifications();
	
	/**
	 * Returns true if the menu item is visible for the active user.
	 * 
	 * @return	boolean
	 */
	public function isVisible();
}
