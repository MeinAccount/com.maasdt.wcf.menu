Code Examples
=============

Menu Package Installation Plugin
--------------------------------

```xml
<?xml version="1.0" encoding="UTF-8"?>
<data xmlns="http://www.maasdt.com" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://www.maasdt.com http://www.maasdt.com/XSD/maelstrom/menu.xsd">
	<import>
		<menu name="com.maasdt.wcf.menu.staticMenu" />
		
		<menu name="com.maasdt.wcf.menu.objectMenu">
			<classname><![CDATA[wcf\data\foo\Foo]]></classname>
			<defaultmenuitemprovider><![CDATA[wcf\system\menu\foo\DefaultFooMenuItemProvider]]></defaultmenuitemprovider>
			<someadditionaldata>value</someadditionaldata>
		</menu>
	</import>
</data>
```

**Attributes:**

- `name` [required]: globally unique name of the menu

**Elements:**

- `classname` [required for object-bound menus]: class name of the objects the menu belongs to
- `defaultmenuitemprovider` [optional]: name of the default menu item provider class for all menu items of the menu
- `someadditionaldata` [optional]: additional data which is stored in the menu object's `additionalData` array


Menu Item Package Installation Plugin
-------------------------------------

```xml
<?xml version="1.0" encoding="UTF-8"?>
<data xmlns="http://www.maasdt.com" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://www.maasdt.com http://www.maasdt.com/XSD/maelstrom/menu.xsd">
	<import>
		<!-- com.maasdt.wcf.menu.staticMenu -->
		<menuitem name="com.maasdt.wcf.menu.staticMenu.menuItemOne">
			<controller><![CDATA[wcf\page\StaticOnePage]]></controller>
			<menuname>com.maasdt.wcf.menu.staticMenu</menuname>
		</menuitem>
		<menuitem name="com.maasdt.wcf.menu.staticMenu.menuItemTwo">
			<controller><![CDATA[wcf\page\StaticTwoPage]]></controller>
			<menuname>com.maasdt.wcf.menu.staticMenu</menuname>
		</menuitem>
		<!-- /com.maasdt.wcf.menu.staticMenu -->
		
		<!-- com.maasdt.wcf.menu.objectMenu -->
		<menuitem name="com.maasdt.wcf.menu.objectMenu.index">
			<controller><![CDATA[wcf\page\FooIndexPage]]></controller>
			<menuname>com.maasdt.wcf.menu.objectMenu</menuname>
		</menuitem>
		<menuitem name="com.maasdt.wcf.menu.objectMenu.someObjectList">
			<classname><![CDATA[wcf\system\menu\foo\FooSomeObjectListMenuItemProvider]]></classname>
			<controller><![CDATA[wcf\page\FooSomeObjectListPage]]></controller>
			<menuname>com.maasdt.wcf.menu.objectMenu</menuname>
		</menuitem>
		<menuitem name="com.maasdt.wcf.menu.objectMenu.management">
			<controller><![CDATA[wcf\page\FooManagementPage]]></controller>
			<hidechildless>1</hidechildless>
			<menuname>com.maasdt.wcf.menu.objectMenu</menuname>
			<objectpermissions>user.canManage</objectpermissions>
		</menuitem>
		<menuitem name="com.maasdt.wcf.menu.objectMenu.management.bar">
			<controller><![CDATA[wcf\page\FooBarManagementPage]]></controller>
			<menuname>com.maasdt.wcf.menu.objectMenu</menuname>
			<parent>com.maasdt.wcf.menu.objectMenu.management</parent>
			<objectpermissions>user.canManageBar</objectpermissions>
		</menuitem>
		<menuitem name="com.maasdt.wcf.menu.objectMenu.management.baz">
			<controller><![CDATA[wcf\page\FooBazManagementPage]]></controller>
			<menuname>com.maasdt.wcf.menu.objectMenu</menuname>
			<parent>com.maasdt.wcf.menu.objectMenu.management</parent>
			<objectpermissions>user.canManageBaz</objectpermissions>
			<objectproperites>enableBaz</objectproperites>
			<options>MODULE_BAZ</options>
		</menuitem>
		<!-- /com.maasdt.wcf.menu.objectMenu -->
	</import>
</data>
```

**Attributes:**

- `name` [required]: name of the menu item which has to be unique for the related menu

**Elements:**

- `classname` [optional]: name of the menu item provider class
- `controller` [optional]: name of the link controller
- `hidechildless` [optional]: if the menu has no child menu items that are visible for the active user, the menu item will also not be visible for the active user
- `menuname` [required]: name of the menu the menu item belongs to
- `link` [optional]: link of the menu item or link parameters if a controller is given
- `objectpermissions` [optional, only for object-bound menus]: list of object-bound acl permissions the active user needs to have to see the menu item (if multiple permissions are given, it is sufficient if the active user has at least one of them); the class of the objects, the menu belongs to, needs to implement `wcf\data\IPermissionObject`
- `objectproperties` [optional, only for object-bound menus]: list of boolean-like object properties of which at least one needs to be true to display to menu item
- `parent` [optional]: name of the parent menu item
- `permissions` [optional]: list of global user group permissions the active user needs to have to see the menu item (if multiple permissions are given, it is sufficient if the active user has at least one of them)
- `showorder` [optional]: position of the menu item in the menu relative to its siblings

You can also add additional elements which will be stored in the menu item's additional data array.


Installing Menus and Menu Items
-------------------------------

Like for any other package installation plugin, to install menus and menu items, add the following lines to your installation instructions:

```xml
<instruction type="menu">menu.xml</instruction>
<instruction type="menuItem">menuItem.xml</instruction>
```


Using Object-Bound Menus
------------------------

```php
$foo = new \wcf\data\foo\Foo(1);
$menu = \wcf\system\menu\MenuHandler::getInstance()->getMenu('com.maasdt.wcf.menu.objectMenu', $foo);
$menu->setActiveMenuItem('com.maasdt.wcf.menu.objectMenu.index');
```

It might also be a good idea, to add a `getMenu()` method to the `\wcf\data\foo\Foo` class:

```php
namespace wcf\data\foo;
use wcf\data\DatabaseObject;
use wcf\system\menu\MenuHandler;

class Foo extends DatabaseObject {
	protected $menu = null;
	
	public function getMenu() {
		if ($this->menu === null) {
			$this->menu = MenuHandler::getInstance()->getMenu('com.maasdt.wcf.menu.objectMenu', $this);
		}
		
		return $this->menu;
	}
}
```

Or, if your object should support/have multiple menus:

```php
namespace wcf\data\foo;
use wcf\data\DatabaseObject;
use wcf\system\menu\MenuHandler;

class Foo extends DatabaseObject {
	protected $menus = array();
	
	public function getMenu($menuName) {
		if (!isset($this->menu[$menuName])) {
			$this->menu[$menuName] = MenuHandler::getInstance()->getMenu($menuName, $this);
		}
		
		return $this->menu;
	}
}
```


Using Static Menus
------------------

Using static menus works like using object-bound menus, except for the object parameter which has to be omitted for static menus:

```php
$staticMenu = \wcf\system\menu\MenuHandler::getInstance()->getMenu('com.maasdt.wcf.menu.staticMenu');
$staticMenu->setActiveMenuItem('com.maasdt.wcf.menu.staticMenu.menuItemOne');
```


Displaying menus
----------------

The packages supports two default ways of displaying menus:

- sidebar menus using `menu.tpl` which behaves similar to the user menu
- static tab menus using `tabMenu.tpl` and `subTabMenu.tpl` which look like dynamic tab menus where each tab has a **static** link

Both default display types only supported one level of nesting (but the package installation plugin doesn't limit nesting).

Code example for static tab menus:

```smarty
<section class="marginTop tabMenuContainer staticTabMenuContainer">
	{include file='tabMenu' menu=$theMenuObject}
	
	<div class="container tabMenuContent tabMenuContainer staticTabMenuContainer">
		{include file='subTabMenu' menu=$theMenuObject}
	</div>
</section>
```

You have to use the additional `staticTabMenuContainer` CSS class to avoid issues with dynamic tab menus.

Code example for a global sidebar menu:

```smarty
{include file='menu' assign='sidebar'}

{include file='header' sidebarOrientation='left'}
```
