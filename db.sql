-- Adminer 4.2.0 MySQL dump
USE cjh3387;


SET NAMES utf8;
SET time_zone = '+00:00';
SET foreign_key_checks = 0;
SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';

DROP TABLE IF EXISTS `accounts`;
CREATE TABLE `accounts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(30) NOT NULL,
  `password` varchar(80) NOT NULL,
  `email` varchar(50) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

INSERT INTO `accounts` (`id`, `username`, `password`, `email`) VALUES
(13,	'dev',	'34c6fceca75e456f25e7e99531e2425c6c1de443',	'cjh3387@rit.edu'),
(12,	'Clyde Hull',	'ea2b93fe9b3449130db7c670298b800d6eb5da9c',	'chull@saunders.rit.edu');

DROP TABLE IF EXISTS `cartitem`;
CREATE TABLE `cartitem` (
  `CartItemID` int(11) NOT NULL AUTO_INCREMENT,
  `UserID` int(11) NOT NULL,
  `ProductID` int(11) NOT NULL,
  `CartItemQty` int(11) NOT NULL,
  `ProductAddedDate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`CartItemID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

INSERT INTO `cartitem` (`CartItemID`, `UserID`, `ProductID`, `CartItemQty`, `ProductAddedDate`) VALUES
(42,	1,	85,	4,	'2015-03-11 00:10:09'),
(17,	2,	83,	14,	'2015-03-06 05:38:08');

DROP TABLE IF EXISTS `people`;
CREATE TABLE `people` (
  `PersonID` mediumint(10) NOT NULL AUTO_INCREMENT,
  `LastName` varchar(20) DEFAULT NULL,
  `FirstName` varchar(20) DEFAULT NULL,
  `NickName` varchar(20) DEFAULT NULL,
  PRIMARY KEY (`PersonID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

INSERT INTO `people` (`PersonID`, `LastName`, `FirstName`, `NickName`) VALUES
(1,	'Stenson',	'Joeseph',	NULL),
(2,	'Janson',	'Phillip',	NULL),
(3,	'Smith',	'Henry',	'Smithers'),
(4,	'White',	'Walter',	'Heisenberg'),
(5,	'Pinkman',	'Jesse',	NULL);

DROP TABLE IF EXISTS `phonenumbers`;
CREATE TABLE `phonenumbers` (
  `PersonID` mediumint(10) NOT NULL,
  `PhoneType` varchar(10) DEFAULT NULL,
  `PhoneNum` varchar(8) DEFAULT NULL,
  `AreaCode` varchar(3) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

INSERT INTO `phonenumbers` (`PersonID`, `PhoneType`, `PhoneNum`, `AreaCode`) VALUES
(1,	'Home',	'2891111',	'585'),
(1,	'Cell',	'2892222',	'585'),
(1,	'Work',	'3452345',	'585'),
(1,	'Work 2',	'3456788',	'585'),
(2,	'Home',	'2349388',	'315'),
(2,	'Work',	'3439898',	'315'),
(2,	'Cell',	'1234567',	'315'),
(3,	'Home',	'2989823',	'345'),
(3,	'Cell',	'8998899',	'345'),
(4,	'Cell',	'2982988',	'567');

DROP TABLE IF EXISTS `product`;
CREATE TABLE `product` (
  `ProductID` int(11) NOT NULL AUTO_INCREMENT,
  `ProductName` varchar(60) NOT NULL,
  `ProductDescription` text NOT NULL,
  `ProductPrice` double NOT NULL,
  `ProductSalePrice` double NOT NULL,
  `ProductImgSrc` text NOT NULL,
  `ProductQty` int(11) NOT NULL,
  PRIMARY KEY (`ProductID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

INSERT INTO `product` (`ProductID`, `ProductName`, `ProductDescription`, `ProductPrice`, `ProductSalePrice`, `ProductImgSrc`, `ProductQty`) VALUES
(70,	'Eclat Dive Bars',	'The Eclat Dive bars are made from multi-butted 4130 chromoly tubing that\'s post-weld heat-treated and features 1.8mm thick tubing at the upper bends for maximum strength. ED coated to prevent rust.',	78.99,	0,	'file/large_362594.png',	3),
(55,	'Colony Sweet Tooth V4 Frame',	'\r\nThe Colony Sweet Tooth V4 frame is Alex Hiam\'s signature model made from 4130 chromoly tubing featuring a heat-treated integrated headtube drilled for gyro tabs, tapered toptube, tapered downtube with hydroformed gusset for strength, heat-treated Mid BB shell, integrated seat post clamp, tapered chain and seatstays with plate style gussets, extra wide tire clearance, seatstay located removable U-brake mounts and 5mm thick heat-treated dropouts.',	480.99,	0,	'file/large_211763_ED_Blue.png',	20),
(79,	'Eclat Control (Ctrl) Tire',	'The Eclat Control tire features a wide, deeply grooved directional tread pattern perfect for all round use and grooved sidewalls for additional traction. 120tpi casing to reduce weight',	38.99,	0,	'file/large_444187.png',	19),
(80,	'Demolition Axes Pivotal Seat',	'The Demolition Lodes \'Axes\' Pivotal seat is Connor Lodes\' signature model featuring a 3-pc canvas cover with custom Demolition axe logo on the top, hollow Pivotal bolt for weight saving, plastic bumpers under the nose and tail section, extra thick foam padding for comfort and a reinforced base to prevent bends or breaks. (11.5 oz)',	33.99,	25,	'file/large_470407.png',	24),
(81,	'Animal BPE PC Pedals',	'\r\nThe Animal BPE PC pedals are injection molded from a lightweight plastic composite featuring 10 molded traction pins per side and 2 polymer spindle bushings for long lasting durability. The pedals ride on a 17mm hollow chromoly spindle for maximum strength and require only a 6mm or 8mm Allen key for installation. Once the pedals start to wear out, simply slide the spindles out and replace with new pedal bodies. 9/16\" (15 oz)',	44.99,	32.99,	'file/large_465278.png',	1),
(83,	'Demolition Trooper Pedals',	'The Demolition Trooper pedals are low profile platform pedals CNC machined from 6061-T6 aluminum, featuring 10 removable traction pins per side, heat-treated chromoly spindles for increased strength, laser-etched Demolition logos and 2 polymer spindle bushings for durability. 9/16\" (17 oz)',	89.99,	0,	'file/large_465272_Flat_Black=9-16_(Pair).png',	21),
(84,	'Duo Resilite PC Pedals',	'The Duo Resilite PC pedals are lightweight platform pedals molded from Duo\'s Resilite composite featuring a micro-knurled concaved body with 12 hexagonal pins per side for maximum traction, unsealed bearings and chromoly spindle. 9/16\" (14.3 oz)',	15.99,	0,	'file/large_465002_Blue.png',	10),
(85,	'Gsport Van Peg (Van Homan)',	'The Gsport Van peg is BMX legend Van Homan\'s signature model, made from 4140 heat-treated chromoly for that traditional sound and feel you can only get with a metal peg. Each Van peg has reinforced ends for maximum strength and features 3 anti-rotation positions for extended peg life.',	16.99,	0,	'file/large_518845.png',	12),
(86,	'Odyssey Rifle 4130 Peg',	'The Odyssey Rifle 4140 peg features a smaller overall diameter and internal \"rifling\" that helps save weight without sacrificing strength or durability. It\'s made from heat-treated 4140 chromoly with 7 anti-spin positions to help maximize the life of the peg',	10.99,	9.99,	'file/large_518195.png',	34),
(87,	'Cinema 333 FA Cassette Wheel',	'The Cinema 333 FA cassette wheel is built using a Cinema alloy cassette hub featuring a 14mm chromoly center axle with 3/8\"x24tpi axle bolts, sealed cartridge bearings and 1-pc chromoly driver with 3 pawl engagement system laced 3x with 14G spokes and nipples to a Cinema 333 double-wall rim.',	69.99,	34,	'file/large_401029_Black-Blue-Blue.png',	1),
(88,	'Cult Match V2 Cassette Wheel',	'The Cult Match V2 cassette wheel is built using a Cult Match double-wall rim, laced 3X with black 14G spokes and nipples to a Cult Match V2 cassette hub. The Match V2 hub features a CNC machined aluminum hub shell with 14mm hollow heat-treated chromoly axle, precision sealed bearings, 1-pc chromoly driver with proven Primo Mix internals and 4 pawl engagement system for increased reliability.',	204.99,	0,	'file/large_401225.png',	3),
(89,	'Odyssey A Plus Cassette Wheel',	'The Odyssey A-Plus cassette wheel features an Odyssey Aerospace double-wall rim laced 3x with black 14G spokes and brass nipples to an Odyssey Antigram cassette hub with 2014 aluminum hub shell, 17mm hollow chromoly axle with 14mm axle bolts, sealed bearings, and a 1-pc chromoly driver with reversible 3 pawl engagement system for RHD/LHD use.',	264.99,	0,	'file/large_402217.png',	5),
(90,	'Dans Comp Rim Strips',	'Dans Comp Rim Strips are vinyl coated plastic rim strips that won\'t move or break like rubber strips can. Available in 2 widths to fit most 20x1.75\" rims available today. 24 or 28mm wide. Sold in pairs',	2.99,	0,	'file/large_449035.png',	34),
(91,	'Dans Schrader Tube',	'High quality BMX tube designed for racing, jumping and riding. 1.0 mm thick. Sold Individually (4.7oz).',	3.99,	0,	'file/large_446006.png',	49),
(92,	'Odyssey CS2 Freestyle Fork',	'\r\nMade from 41 thermal chromoly. Butted and tapered Race legs with U-brake mounts. Cable friendly alloy compression cap threaded into a 1-pc Race steerer tube with built in integrated bearing race. 5mm thick Dirt dropouts with 3/8\" axle slots. 20 x 1-1/8\"\r\n\r\nNote: Only compatible with Campy Spec Integrated headsets.',	84.99,	0,	'file/large_353246.png',	7),
(93,	'Demolition Vulcan V2 U-Brake',	'The Demolition Vulcan V2 U-brake features 6061 aluminum arms with machined surfaces to reduce weight, low profile spring adjusters, and sealed bearing pivots for a smooth operation. Includes Demolition brake pads, Vulcan alloy cable hanger and braided straddle cable. (5.8 oz)',	53.99,	0,	'file/large_480036_Red.png',	3),
(94,	'Odyssey Evolver 2 U-Brake',	'The Odyssey Evo 2 U-brake features forged and machined aluminum arms for strength and durabilty. Low stack height helps to clear small drivetrains. Includes hard and soft springs, A-brake pads, alloy cable hanger, pre-cut straddle cable and modular hardware for front or rear use. (6.7 oz)',	49.99,	0,	'file/large_480010.png',	3),
(117,	'alert(&quot;dumb&quot;)',	'essefsef',	23,	2,	'img/noimage.png',	23),
(115,	'Something',	'osenofsn',	90,	0,	'file/2948-burning-earth-1366x768-fantasy-wallpaper.jpg',	90);

DROP TABLE IF EXISTS `projects`;
CREATE TABLE `projects` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ProjectName` varchar(50) NOT NULL,
  `GroupName` varchar(30) NOT NULL,
  `GroupMembers` longtext NOT NULL,
  `ProjectDescription` longtext NOT NULL,
  `Class` varchar(30) NOT NULL,
  `Year` varchar(4) NOT NULL,
  `SiteImgSrc` varchar(30) NOT NULL,
  `URL` longtext NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

INSERT INTO `projects` (`id`, `ProjectName`, `GroupName`, `GroupMembers`, `ProjectDescription`, `Class`, `Year`, `SiteImgSrc`, `URL`) VALUES
(8,	'New Project',	'some grop',	'clay, andrea',	'a website development team...',	'MGMT 350',	'2015',	'1',	'http://flamingsquirreldesign.com/'),
(9,	'Thermos',	'A cool group',	'fred, todd, noah',	'Some project of stuff',	'MGMT 350',	'2015',	'2',	'http://www.google.com/');

DROP TABLE IF EXISTS `purchasedproducts`;
CREATE TABLE `purchasedproducts` (
  `PurchasedProductID` int(11) NOT NULL AUTO_INCREMENT,
  `UserID` int(11) NOT NULL,
  `ProductID` int(11) NOT NULL,
  `PurchasedDate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `PurchasedQty` int(11) NOT NULL,
  PRIMARY KEY (`PurchasedProductID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

INSERT INTO `purchasedproducts` (`PurchasedProductID`, `UserID`, `ProductID`, `PurchasedDate`, `PurchasedQty`) VALUES
(10,	1,	83,	'2015-03-10 23:11:57',	3),
(15,	1,	91,	'2015-03-10 23:37:29',	1),
(14,	1,	55,	'2015-03-10 23:34:35',	1),
(13,	1,	70,	'2015-03-10 23:32:41',	2),
(12,	1,	80,	'2015-03-10 23:32:41',	1),
(11,	1,	79,	'2015-03-10 23:11:57',	2),
(16,	1,	70,	'2015-03-10 23:42:24',	4),
(17,	1,	87,	'2015-03-10 23:42:25',	49);

DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `UserID` int(11) NOT NULL AUTO_INCREMENT,
  `UserAdminStatus` varchar(5) NOT NULL DEFAULT 'false',
  `UserFullName` varchar(20) NOT NULL,
  `UserEmail` varchar(40) NOT NULL,
  `UserPass` varchar(120) NOT NULL,
  `UserCity` varchar(20) NOT NULL,
  `UserState` varchar(2) NOT NULL,
  `UserZip` int(5) NOT NULL,
  `UserPhone` int(11) NOT NULL,
  `UserAddress` varchar(60) NOT NULL,
  `Username` varchar(20) NOT NULL,
  PRIMARY KEY (`UserID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

INSERT INTO `users` (`UserID`, `UserAdminStatus`, `UserFullName`, `UserEmail`, `UserPass`, `UserCity`, `UserState`, `UserZip`, `UserPhone`, `UserAddress`, `Username`) VALUES
(1,	'true',	'dev',	'cjh3387@rit.edu',	'd033e22ae348aeb5660fc2140aec35850c4da997',	'shortsville',	'NY',	14548,	2147483647,	'123 b st.',	'admin'),
(2,	'false',	'Ted',	'tedr@something.com',	'46ab578353b0478abc71fa54796a76c10bbe41a8',	'phili',	'PA',	12344,	2147483647,	'123 re. rd',	'ted');

-- 2015-03-11 22:14:50