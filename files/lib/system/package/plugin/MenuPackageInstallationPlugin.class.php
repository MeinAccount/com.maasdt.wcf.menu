<?php
namespace wcf\system\package\plugin;
use wcf\system\cache\builder\MenuCacheBuilder;
use wcf\system\WCF;

/**
 * Installs, deletes and updates menus.
 * 
 * @author	Matthias Schmidt
 * @copyright	2014 Maasdt
 * @license	GNU Lesser General Public License <http://www.gnu.org/licenses/lgpl.html>
 * @package	com.maasdt.wcf.menu
 * @subpackage	system.package.plugin
 * @category	Community Framework
 */
class MenuPackageInstallationPlugin extends AbstractXMLPackageInstallationPlugin {
	/**
	 * @see	\wcf\system\package\plugin\AbstractXMLPackageInstallationPlugin::$className
	 */
	public $className = 'wcf\data\menu\MenuEditor';
	
	/**
	 * @see	\wcf\system\package\plugin\AbstractPackageInstallationPlugin::$tagName
	 */
	public $tagName = 'menu';
	
	/**
	 * list of reserved tags
	 * @var	array<string>
	 */
	public static $reservedTags = array('additionaldata', 'classname', 'defaultmenuitemprovider');
	
	/**
	 * @see	\wcf\system\package\plugin\AbstractXMLPackageInstallationPlugin::cleanup()
	 */
	protected function cleanup() {
		MenuCacheBuilder::getInstance()->reset();
	}
	
	/**
	 * @see	\wcf\system\package\plugin\AbstractXMLPackageInstallationPlugin::findExistingItem()
	 */
	protected function findExistingItem(array $data) {
		$sql = "SELECT	*
			FROM	wcf".WCF_N."_".$this->tableName."
			WHERE	menuName = ?
				AND packageID = ?";
		$parameters = array(
			$data['menuName'],
			$this->installation->getPackageID()
		);
		
		return array(
			'parameters' => $parameters,
			'sql' => $sql
		);
	}
	
	/**
	 * @see	\wcf\system\package\plugin\AbstractXMLPackageInstallationPlugin::handleDelete()
	 */
	protected function handleDelete(array $items) {
		$sql = "DELETE FROM	wcf".WCF_N."_".$this->tableName."
			WHERE		menuName = ?
					AND packageID = ?";
		$statement = WCF::getDB()->prepareStatement($sql);
		foreach ($items as $item) {
			$statement->execute(array(
				$item['attributes']['name'],
				$this->installation->getPackageID()
			));
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
		
		return array(
			'additionalData' => serialize($additionalData),
			'className' => isset($data['elements']['classname']) ? $data['elements']['classname'] : '',
			'defaultMenuItemProvider' => isset($data['elements']['defaultmenuitemprovider']) ? $data['elements']['defaultmenuitemprovider'] : '',
			'menuName' => $data['attributes']['name']
		);
	}
}
