-- phpMyAdmin SQL Dump
-- version 2.11.9
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Apr 28, 2009 at 07:35 AM
-- Server version: 5.0.67
-- PHP Version: 5.2.9

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";

--
-- Database: 'quickbooks_import'
--

-- --------------------------------------------------------

--
-- Table structure for table 'qb_example_customer'
--

CREATE TABLE IF NOT EXISTS qb_example_customer (
  ListID                 VARCHAR(40)  NOT NULL,
  TimeCreated            datetime     NOT NULL,
  TimeModified           datetime     NOT NULL,
  `NAME ` VARCHAR(50) NOT NULL,
  FullName               VARCHAR(255) NOT NULL,
  FirstName              VARCHAR(40)  NOT NULL,
  MiddleName             VARCHAR(10)  NOT NULL,
  LastName               VARCHAR(40)  NOT NULL,
  Contact                VARCHAR(50)  NOT NULL,
  ShipAddress_Addr1      VARCHAR(50)  NOT NULL,
  ShipAddress_Addr2      VARCHAR(50)  NOT NULL,
  ShipAddress_City       VARCHAR(50)  NOT NULL,
  ShipAddress_State      VARCHAR(25)  NOT NULL,
  ShipAddress_Province   VARCHAR(25)  NOT NULL,
  ShipAddress_PostalCode VARCHAR(16)  NOT NULL,
  PRIMARY KEY (ListID)
) ENGINE = MyISAM DEFAULT CHARSET = latin1;
-- --------------------------------------------------------

--
-- Table structure for table 'qb_example_estimate'
--

CREATE TABLE IF NOT EXISTS qb_example_estimate (
  TxnID                  VARCHAR(40)  NOT NULL,
  TimeCreated            datetime     NOT NULL,
  TimeModified           datetime     NOT NULL,
  RefNumber              VARCHAR(16)  NOT NULL,
  Customer_ListID        VARCHAR(40)  NOT NULL,
  Customer_FullName      VARCHAR(255) NOT NULL,
  ShipAddress_Addr1      VARCHAR(50)  NOT NULL,
  ShipAddress_Addr2      VARCHAR(50)  NOT NULL,
  ShipAddress_City       VARCHAR(50)  NOT NULL,
  ShipAddress_State      VARCHAR(25)  NOT NULL,
  ShipAddress_Province   VARCHAR(25)  NOT NULL,
  ShipAddress_PostalCode VARCHAR(16)  NOT NULL,
  BalanceRemaining       FLOAT        NOT NULL,
  PRIMARY KEY (TxnID)
) ENGINE = MyISAM DEFAULT CHARSET = latin1;

-- --------------------------------------------------------

--
-- Table structure for table 'qb_example_estimate_lineitem'
--

CREATE TABLE IF NOT EXISTS qb_example_estimate_lineitem (
  TxnID         VARCHAR(40)  NOT NULL,
  TxnLineID     VARCHAR(40)  NOT NULL,
  Item_ListID   VARCHAR(40)  NOT NULL,
  Item_FullName VARCHAR(255) NOT NULL,
  Descrip       TEXT         NOT NULL,
  Quantity      INT (10) unsigned NOT NULL,
  Rate          FLOAT        NOT NULL,
  PRIMARY KEY (TxnID, TxnLineID)
) ENGINE = MyISAM DEFAULT CHARSET = latin1;

-- --------------------------------------------------------

--
-- Table structure for table 'qb_example_invoice'
--

CREATE TABLE IF NOT EXISTS qb_example_invoice (
  TxnID                  VARCHAR(40)  NOT NULL,
  TimeCreated            datetime     NOT NULL,
  TimeModified           datetime     NOT NULL,
  RefNumber              VARCHAR(16)  NOT NULL,
  Customer_ListID        VARCHAR(40)  NOT NULL,
  Customer_FullName      VARCHAR(255) NOT NULL,
  ShipAddress_Addr1      VARCHAR(50)  NOT NULL,
  ShipAddress_Addr2      VARCHAR(50)  NOT NULL,
  ShipAddress_City       VARCHAR(50)  NOT NULL,
  ShipAddress_State      VARCHAR(25)  NOT NULL,
  ShipAddress_Province   VARCHAR(25)  NOT NULL,
  ShipAddress_PostalCode VARCHAR(16)  NOT NULL,
  BalanceRemaining       FLOAT        NOT NULL,
  PRIMARY KEY (TxnID)
) ENGINE = MyISAM DEFAULT CHARSET = latin1;

-- --------------------------------------------------------

--
-- Table structure for table 'qb_example_invoice_lineitem'
--

CREATE TABLE IF NOT EXISTS qb_example_invoice_lineitem (
  TxnID         VARCHAR(40)  NOT NULL,
  TxnLineID     VARCHAR(40)  NOT NULL,
  Item_ListID   VARCHAR(40)  NOT NULL,
  Item_FullName VARCHAR(255) NOT NULL,
  Descrip       TEXT         NOT NULL,
  Quantity      INT (10) unsigned NOT NULL,
  Rate          FLOAT        NOT NULL,
  PRIMARY KEY (TxnID, TxnLineID)
) ENGINE = MyISAM DEFAULT CHARSET = latin1;

-- --------------------------------------------------------


--
-- Table structure for table 'qb_example_item'
--

CREATE TABLE IF NOT EXISTS qb_example_item (
  ListID                 VARCHAR(40)  NOT NULL,
  TimeCreated            datetime     NOT NULL,
  TimeModified           datetime     NOT NULL,
  `NAME ` VARCHAR(50) NOT NULL,
  FullName               VARCHAR(255) NOT NULL,
  `TYPE ` VARCHAR(40) NOT NULL,
  Parent_ListID          VARCHAR(40)  NOT NULL,
  Parent_FullName        VARCHAR(255) NOT NULL,
  ManufacturerPartNumber VARCHAR(40)  NOT NULL,
  SalesTaxCode_ListID    VARCHAR(40)  NOT NULL,
  SalesTaxCode_FullName  VARCHAR(255) NOT NULL,
  BuildPoint             VARCHAR(40)  NOT NULL,
  ReorderPoint           VARCHAR(40)  NOT NULL,
  QuantityOnHand         INT (10) unsigned NOT NULL,
  AverageCost            FLOAT        NOT NULL,
  QuantityOnOrder        INT (10) unsigned NOT NULL,
  QuantityOnSalesOrder   INT (10) unsigned NOT NULL,
  TaxRate                VARCHAR(40)  NOT NULL,
  SalesPrice             FLOAT        NOT NULL,
  SalesDesc              TEXT         NOT NULL,
  PurchaseCost           FLOAT        NOT NULL,
  PurchaseDesc           TEXT         NOT NULL,
  PrefVendor_ListID      VARCHAR(40)  NOT NULL,
  PrefVendor_FullName    VARCHAR(255) NOT NULL,
  PRIMARY KEY (ListID)
) ENGINE = MyISAM DEFAULT CHARSET = latin1;

-- --------------------------------------------------------

--
-- Table structure for table 'qb_example_salesorder'
--

CREATE TABLE IF NOT EXISTS qb_example_salesorder (
  TxnID                  VARCHAR(40)  NOT NULL,
  TimeCreated            datetime     NOT NULL,
  TimeModified           datetime     NOT NULL,
  RefNumber              VARCHAR(16)  NOT NULL,
  Customer_ListID        VARCHAR(40)  NOT NULL,
  Customer_FullName      VARCHAR(255) NOT NULL,
  ShipAddress_Addr1      VARCHAR(50)  NOT NULL,
  ShipAddress_Addr2      VARCHAR(50)  NOT NULL,
  ShipAddress_City       VARCHAR(50)  NOT NULL,
  ShipAddress_State      VARCHAR(25)  NOT NULL,
  ShipAddress_Province   VARCHAR(25)  NOT NULL,
  ShipAddress_PostalCode VARCHAR(16)  NOT NULL,
  BalanceRemaining       FLOAT        NOT NULL,
  PRIMARY KEY (TxnID)
) ENGINE = MyISAM DEFAULT CHARSET = latin1;

-- --------------------------------------------------------

--
-- Table structure for table 'qb_example_salesorder_lineitem'
--

CREATE TABLE IF NOT EXISTS qb_example_salesorder_lineitem (
  TxnID         VARCHAR(40)  NOT NULL,
  TxnLineID     VARCHAR(40)  NOT NULL,
  Item_ListID   VARCHAR(40)  NOT NULL,
  Item_FullName VARCHAR(255) NOT NULL,
  Descrip       TEXT         NOT NULL,
  Quantity      INT (10) unsigned NOT NULL,
  Rate          FLOAT        NOT NULL,
  PRIMARY KEY (TxnID, TxnLineID)
) ENGINE = MyISAM DEFAULT CHARSET = latin1;
