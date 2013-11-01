<?php

QuickBooks_Loader::load('/QuickBooks/IPP/Object.php');

class QuickBooks_IPP_Object_Line extends QuickBooks_IPP_Object
{
	protected function _order()
	{
		return array(
			'Id' => true, 	
			'Desc' => true, 	
			'GroupMember' => true, 
			'Amount' => true, 
			'ClassId' => true, 
			'ClassName' => true, 
			'Taxable' => true, 
			'ItemId' => true, 
			'ItemName' => true, 
			'ItemType' => true, 
			'UnitPrice' => true, 
			'RatePercent' => true, 
			'PriceLevelId' => true, 
			'PriceLevelName' => true, 
			'Qty' => true, 
			'UOMId' => true, 
			'UOMAbbrv' => true, 
			'OverrideItemAccountId' => true, 
			'OverrideItemAccountName' => true, 
			'DiscountId' => true, 
			'DiscountName' => true, 
			'SalesTaxCodeId' => true, 
			'SalesTaxCodeName' => true, 
			'Custom1' => true, 
			'Custom2' => true, 
			'ServiceDate' => true, 
		);
	}
}
