<?php
namespace wcf\data\menu\item;
use wcf\data\DatabaseObject;

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
class MenuItem extends DatabaseObject {
	/**
	 * @see	\wcf\data\DatabaseObject::$databaseTableIndexName
	 */
	protected static $databaseTableIndexName = 'menuItemID';
	
	/**
	 * @see	\wcf\data\DatabaseObject::$databaseTableIndexName
	 */
	protected static $databaseTableName = 'menu_item';
	
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
}
