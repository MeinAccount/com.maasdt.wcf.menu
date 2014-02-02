DROP TABLE IF EXISTS wcf1_menu;
CREATE TABLE wcf1_menu (
	menuID INT(10) NOT NULL AUTO_INCREMENT PRIMARY KEY,
	packageID INT(10) NOT NULL,
	menuName VARCHAR(255) NOT NULL,
	className VARCHAR(255) NOT NULL,
	defaultMenuItemProvider VARCHAR(255) NOT NULL,
	additionalData MEDIUMTEXT,
	
	UNIQUE KEY (menuName) menuName
);

DROP TABLE IF EXISTS wcf1_menu_item;
CREATE TABLE wcf1_menu_item (
	menuItemID INT(10) NOT NULL AUTO_INCREMENT PRIMARY KEY,
	packageID INT(10) NOT NULL,
	menuName VARCHAR(255) NOT NULL,
	menuItem VARCHAR(255) NOT NULL,
	parentMenuItem VARCHAR(255) NOT NULL,
	menuItemController VARCHAR(255) NOT NULL,
	menuItemLink VARCHAR(255) NOT NULL,
	className VARCHAR(255) NOT NULL,
	showOrder INT(10) NOT NULL,
	permissions TEXT,
	options TEXT,
	objectPermissions TEXT,
	objectProperties TEXT,
	hideChildless TINYINT(1) NOT NULL,
	additionalData MEDIUMTEXT,
	
	UNIQUE KEY menuItem (menuItem, menuName)
);

ALTER TABLE wcf1_menu ADD FOREIGN KEY (packageID) REFERENCES wcf1_package (packageID) ON DELETE CASCADE;

ALTER TABLE wcf1_menu_item ADD FOREIGN KEY (packageID) REFERENCES wcf1_package (packageID) ON DELETE CASCADE;
