<?php
namespace wcf\system\package\plugin;
use wcf\system\cache\builder\MenuItemCacheBuilder;
use wcf\system\database\util\PreparedStatementConditionBuilder;
use wcf\system\exception\SystemException;
use wcf\system\WCF;

/**
 * Installs, deletes and updates menu items.
 * 
 * @author	Matthias Schmidt
 * @copyright	2014 Maasdt
 * @license	GNU Lesser General Public License <http://www.gnu.org/licenses/lgpl.html>
 * @package	com.maasdt.wcf.menu
 * @subpackage	system.package.plugin
 * @category	Community Framework
 */
class MenuItemPackageInstallationPlugin extends AbstractMenuPackageInstallationPlugin {
	/**
	 * @see	\wcf\system\package\plugin\AbstractXMLPackageInstallationPlugin::$className
	 */
	public $className = 'wcf\data\menu\item\MenuItemEditor';
	
	/**
	 * name of the menu the currently installed menu item belongs to
	 * @var	string
	 */
	public $menuName = '';
	
	/**
	 * @see	\wcf\system\package\plugin\AbstractPackageInstallationPlugin::$tagName
	 */
	public $tagName = 'menuitem';
	
	/**
	 * list of reserved tags
	 * @var	array<string>
	 */
	public static $reservedTags = array('additionaldata', 'classname', 'controller', 'hidechildless', 'link', 'menuname', 'objectpermissions', 'objectproperties', 'options', 'parent', 'parentmenuitem', 'permissions');
	
	/**
	 * @see	\wcf\system\package\plugin\AbstractXMLPackageInstallationPlugin::cleanup()
	*/
	protected function cleanup() {
		MenuItemCacheBuilder::getInstance()->reset();
	}
	/**
	 * @see	\wcf\system\package\plugin\AbstractXMLPackageInstallationPlugin::findExistingItem()
	 */
	protected function findExistingItem(array $data) {
		$sql = "SELECT	*
			FROM	wcf".WCF_N."_".$this->tableName."
			WHERE	menuName = ?
				AND menuItem = ?
				AND packageID = ?";
		$parameters = array(
			$data['menuName'],
			$data['menuItem'],
			$this->installation->getPackageID()
		);
		
		return array(
			'parameters' => $parameters,
			'sql' => $sql
		);
	}
	
	/**
	 * @see	\wcf\system\package\plugin\AbstractXMLPackageInstallationPlugin::getShowOrder()
	 */
	protected function getShowOrder($showOrder, $parentName = null, $columnName = null, $tableNameExtension = '') {
		$conditionBuilder = new PreparedStatementConditionBuilder();
		$conditionBuilder->add('menuName = ?', array($this->menuName));
		if ($columnName !== null) {
			$conditionBuilder->add($columnName.' = ?', array(
				$parentName
			));
		}
		
		$sql = "SELECT	MAX(showOrder)
			FROM	wcf".WCF_N."_".$this->tableName.$tableNameExtension."
			".$conditionBuilder;
		$statement = WCF::getDB()->prepareStatement($sql);
		$statement->execute($conditionBuilder->getParameters());
		$maxShowOrder = $statement->fetchColumn() ?: 0;
		
		if ($showOrder === null) {
			return $maxShowOrder === 0 ? 1 : $maxShowOrder + 1;
		}
		else if ($showOrder > $maxShowOrder) {
			return $maxShowOrder + 1;
		}
		else {
			$sql = "UPDATE	wcf".WCF_N."_".$this->tableName.$tableNameExtension."
				SET	showOrder = showOrder + 1
				WHERE	menuName = ?
					AND showOrder >= ?
					".($columnName !== null ? "AND ".$columnName." = ?" : "");
			$statement = WCF::getDB()->prepareStatement($sql);
			
			$data = array(
				$this->menuName,
				$showOrder
			);
			if ($columnName !== null) {
				$data[] = $parentName;
			}
			
			$statement->execute($data);
			
			return $showOrder;
		}
	}
	
	/**
	 * @see	\wcf\system\package\plugin\AbstractXMLPackageInstallationPlugin::prepareImport()
	 */
	protected function prepareImport(array $data) {
		$additionalData = array();
		foreach ($data['elements'] as $tagName => $nodeValue) {
			if (!in_array(strtolower($tagName), self::$reservedTags)) {
				$additionalData[$tagName] = $nodeValue;
			}
		}
		
		$this->menuName = $data['elements']['menuname'];
		
		return array_merge(parent::prepareImport($data), array(
			'additionalData' => serialize($additionalData),
			'className' => isset($data['elements']['classname']) ? $data['elements']['classname'] : '',
			'hideChildless' => isset($data['elements']['hidechildless']) ? $data['elements']['hidechildless'] : 0,
			'menuName' => $this->menuName,
			'objectProperties' => isset($data['elements']['objectproperties']) ? $data['elements']['objectproperties'] : '',
			'objectPermissions' => isset($data['elements']['objectpermissions']) ? $data['elements']['objectpermissions'] : ''
		));
	}
}
