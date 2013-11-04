<?php

/**
 * Callback methods for the server integrator components
 *
 * Copyright (c) 2010 Keith Palmer / ConsoliBYTE, LLC.
 * All rights reserved. This program and the accompanying materials
 * are made available under the terms of the Eclipse Public License v1.0
 * which accompanies this distribution, and is available at
 * http://www.opensource.org/licenses/eclipse-1.0.php
 *
 * @author     Keith Palmer <keith@consolibyte.com>
 * @license    LICENSE.txt
 *
 * @package    QuickBooks
 * @subpackage Server
 */

/**
 * QuickBooks API object-oriented classes
 */
QuickBooks_Loader::load('/QuickBooks/API.php');

/**
 * API singleton classes
 */
QuickBooks_Loader::load('/QuickBooks/API/Singleton.php');

/**
 * Server integrator class
 */
QuickBooks_Loader::load('/QuickBooks/Server/Integrator.php');

/**
 * Callback methods for the server integrator
 */
class QuickBooks_Callbacks_Integrator_Callbacks
{
    public static function onAuthenticate($requestID, $user, $hook, &$err, $hook_data, $callback_config)
    {
        // Get an API instance
        $API        = QuickBooks_API_Singleton::getInstance();
        $Integrator = QuickBooks_Integrator_Singleton::getInstance();

        $module = get_class($Integrator);

        // This is the *very first time the integration was run!*
        //	Orders *will not* be fetched from before this time!
        $type           = null;
        $opts           = null;
        $first_datetime = $API->configRead($module, 'initial', $type, $opts);

        $first = false;
        if (!$first_datetime) {
            $first_datetime = date('Y-m-d H:i:s');
            $API->configWrite($module, 'initial', $first_datetime);

            $first = true;
        }

        // The is the last time the integration was run!
        $type          = null;
        $opts          = null;
        $last_datetime = $API->configRead($module, 'datetime', $type, $opts);

        if (!$last_datetime) {
            $last_datetime = date('Y-m-d H:i:s');
        }

        $force = false;

        $this_datetime = date('Y-m-d H:i:s');
        $API->log('Integration handle() has started: [first: ' . $first_datetime . '], [last: ' . $last_datetime . '], [this: ' . $this_datetime . ']', QUICKBOOKS_LOG_VERBOSE);

        // Do some integration routines
        QuickBooks_Callbacks_Integrator_Callbacks::_integrate($last_datetime, $first_datetime, $first);

        $API->configWrite($module, 'datetime', $this_datetime);
    }

    /**
     * @param string  $last_datetime
     * @param         $first_datetime
     * @param boolean $first_time_running
     *
     * @return boolean
     */
    protected static function _integrate($last_datetime, $first_datetime, $first_time_running)
    {
        // Integrator instance
        $Integrator = QuickBooks_Integrator_Singleton::getInstance();

        // Generics which *must* be present
        if ($Shipping = $Integrator->getGenericShipping()) {
            QuickBooks_Callbacks_Integrator_Callbacks::_integrateShipping($Shipping);
        }

        if ($Discount = $Integrator->getGenericDiscount()) {
            QuickBooks_Callbacks_Integrator_Callbacks::_integrateDiscounts($Discount);
        }

        $orders = $Integrator->listNewOrdersSince($last_datetime, $first_datetime, $first_time_running);
        QuickBooks_Callbacks_Integrator_Callbacks::_integrateNewOrders($orders);

        $estimates = $Integrator->listNewEstimatesSince($last_datetime, $first_datetime, $first_time_running);
        QuickBooks_Callbacks_Integrator_Callbacks::_integrateNewEstimates($estimates);

        QuickBooks_Callbacks_Integrator_Callbacks::_pullNewAccounts($last_datetime, $first_datetime, $first_time_running);

        QuickBooks_Callbacks_Integrator_Callbacks::_pullNewClasses($last_datetime, $first_datetime, $first_time_running);

        QuickBooks_Callbacks_Integrator_Callbacks::_pullNewPaymentMethods($last_datetime, $first_datetime, $first_time_running);

        QuickBooks_Callbacks_Integrator_Callbacks::_pullNewCustomerTypes($last_datetime, $first_datetime, $first_time_running);

        QuickBooks_Callbacks_Integrator_Callbacks::_pullNewShipMethods($last_datetime, $first_datetime, $first_time_running);

        QuickBooks_Callbacks_Integrator_Callbacks::_pullNewSalesTaxItems($last_datetime, $first_datetime, $first_time_running);

        QuickBooks_Callbacks_Integrator_Callbacks::_pullNewSalesTaxGroupItems($last_datetime, $first_datetime, $first_time_running);

        QuickBooks_Callbacks_Integrator_Callbacks::_pullNewSalesTaxCodes($last_datetime, $first_datetime, $first_time_running);

        QuickBooks_Callbacks_Integrator_Callbacks::_pullNewUnitOfMeasureSets($last_datetime, $first_datetime, $first_time_running);
    }

    protected static function _pullNewAccounts($datetime, $first_datetime, $first_time_running)
    {
        // Get the API instance
        $API = QuickBooks_API_Singleton::getInstance();

        if (true) //$first_time_running)
        {
            // A really long time ago
            $datetime = '1983-01-02 00:00:01';
        }

        $API->log('Pulling in accounts modified since ' . $datetime, QUICKBOOKS_LOG_VERBOSE);

        return $API->listAccountsModifiedAfter($datetime, 'QuickBooks_Callbacks_Integrator_Callbacks::listAccountsModifiedAfter');
    }

    protected static function _pullNewClasses($datetime, $first_datetime, $first_time_running)
    {
        // Get the API instance
        $API = QuickBooks_API_Singleton::getInstance();

        if (true) // $first_time_running)
        {
            // A realllly long time ago
            $datetime = '1983-01-02 00:00:01';
        }

        $API->log('Pulling in classes modified since ' . $datetime, QUICKBOOKS_LOG_VERBOSE);

        return $API->listClassesModifiedAfter($datetime, 'QuickBooks_Callbacks_Integrator_Callbacks::listClassesModifiedAfter');
    }

    protected static function _pullNewPaymentMethods($datetime, $first_datetime, $first_time_running)
    {
        // Get the API instance
        $API = QuickBooks_API_Singleton::getInstance();

        if (true) // $first_time_running)
        {
            // A really long time ago
            $datetime = '1983-01-02 00:00:01';
        }

        $API->log('Pulling in payment methods modified since ' . $datetime, QUICKBOOKS_LOG_VERBOSE);

        return $API->listPaymentMethodsModifiedAfter($datetime, 'QuickBooks_Callbacks_Integrator_Callbacks::listPaymentMethodsModifiedAfter');
    }

    protected static function _pullNewSalesTaxItems($datetime, $first_datetime, $first_time_running)
    {
        // Get the API instance
        $API = QuickBooks_API_Singleton::getInstance();

        if (true) // $first_time_running)
        {
            // A really long time ago
            $datetime = '1983-01-02 00:00:01';
        }

        $API->log('Pulling in sales tax items modified since ' . $datetime, QUICKBOOKS_LOG_VERBOSE);

        return $API->listSalesTaxItemsModifiedAfter($datetime, 'QuickBooks_Callbacks_Integrator_Callbacks::listSalesTaxItemsModifiedAfter');
    }

    protected static function _pullNewSalesTaxGroupItems($datetime, $first_datetime, $first_time_running)
    {
        // Get the API instance
        $API = QuickBooks_API_Singleton::getInstance();

        if (true) // $first_time_running)
        {
            // A really long time ago
            $datetime = '1983-01-02 00:00:01';
        }

        $API->log('Pulling in sales tax *group* items modified since ' . $datetime, QUICKBOOKS_LOG_VERBOSE);

        return $API->listSalesTaxGroupItemsModifiedAfter($datetime, 'QuickBooks_Callbacks_Integrator_Callbacks::listSalesTaxGroupItemsModifiedAfter');
    }

    protected static function _pullNewShipMethods($datetime, $first_datetime, $first_time_running)
    {
        // Get the API instance
        $API = QuickBooks_API_Singleton::getInstance();

        if (true) // $first_time_running)
        {
            // A really long time ago
            $datetime = '1983-01-02 00:00:01';
        }

        $API->log('Pulling in ship methods modified since ' . $datetime, QUICKBOOKS_LOG_VERBOSE);

        return $API->listShipMethodsModifiedAfter($datetime, 'QuickBooks_Callbacks_Integrator_Callbacks::listShipMethodsModifiedAfter');
    }

    protected static function _pullNewCustomerTypes($datetime, $first_datetime, $first_time_running)
    {
        // Get the API instance
        $API = QuickBooks_API_Singleton::getInstance();

        if (true) // $first_time_running)
        {
            // A really long time ago
            $datetime = '1983-01-02 00:00:01';
        }

        $API->log('Pulling in customer types modified since ' . $datetime, QUICKBOOKS_LOG_VERBOSE);

        return $API->listCustomerTypesModifiedAfter($datetime, 'QuickBooks_Callbacks_Integrator_Callbacks::listCustomerTypesModifiedAfter');
    }

    protected static function _pullNewUnitOfMeasureSets($datetime, $first_datetime, $first_time_running)
    {
        // Get the API instance
        $API = QuickBooks_API_Singleton::getInstance();

        if (true) // $first_time_running)
        {
            // A really long time ago
            $datetime = '1983-01-02 00:00:01';
        }

        $API->log('Pulling in unit of measure sets modified since ' . $datetime, QUICKBOOKS_LOG_VERBOSE);

        return $API->listUnitOfMeasureSetsModifiedAfter($datetime, 'QuickBooks_Callbacks_Integrator_Callbacks::listUnitOfMeasureSetsModifiedAfter');
    }

    protected static function _pullNewSalesTaxCodes($datetime, $first_datetime, $first_time_running)
    {
        // Get the API instance
        $API = QuickBooks_API_Singleton::getInstance();

        if (true) // $first_time_running)
        {
            // A really long time ago
            $datetime = '1983-01-02 00:00:01';
        }

        $API->log('Pulling in sales tax codes modified since ' . $datetime, QUICKBOOKS_LOG_VERBOSE);

        return $API->listSalesTaxCodesModifiedAfter($datetime, 'QuickBooks_Callbacks_Integrator_Callbacks::listSalesTaxCodesModifiedAfter');
    }

    /**
     * @param string $datetime
     * @param        $first_datetime
     * @param        $first_time_running
     *
     * @return boolean
     * @todo Investigate why this method is empty
     */
    protected static function _pullNewEstimates($datetime, $first_datetime, $first_time_running)
    {
        /*
        if ($this->_integrator_config['pull_estimates'])
        {
            // Use the lookback value...
            $max = max(strtotime($datetime) - QUICKBOOKS_INTEGRATOR_LOOKBACK, strtotime($first_datetime));
            $datetime = date('Y-m-d H:i:s', $max);

            $API->log('Pulling in estimates modified since ' . $datetime, QUICKBOOKS_LOG_VERBOSE);

            return $API->listEstimatesModifiedAfter($datetime, 'QuickBooks_Callbacks_Integrator_Callbacks::listEstimatesModifiedAfter');
        }
        */

        return true;
    }

    /**
     * @param string  $datetime
     * @param string  $first_datetime
     * @param boolean $first_time_running
     *
     * @return bool
     * @todo Investigate why this method is empty
     */
    protected static function _pullNewOrders($datetime, $first_datetime, $first_time_running)
    {
        /*
        if ($this->_integrator_config['pull_orders'])
        {
            // Use the lookback value...
            $max = max(strtotime($datetime) - QUICKBOOKS_INTEGRATOR_LOOKBACK, strtotime($first_datetime));
            $datetime = date('Y-m-d H:i:s', $max);

            $API->log('Pulling in invoices modified since ' . $datetime, QUICKBOOKS_LOG_VERBOSE);

            return $API->listInvoicesModifiedAfter($datetime, 'QuickBooks_Callbacks_Integrator_Callbacks::listInvoicesModifiedAfter');
        }
        */

        return true;
    }

    protected static function _integrateNewCustomers($customers)
    {
        $API        = QuickBooks_API_Singleton::getInstance();
        $Integrator = QuickBooks_Integrator_Singleton::getInstance();

        foreach ($customers as $CustomerID) {
            if (true) {
                // Try to fetch the customer by name
                $API->getCustomerByName($Integrator->getCustomerNameForQuery($CustomerID), 'QuickBooks_Callbacks_Integrator_Callbacks::getCustomerByName', $CustomerID);
            }
            /*else
            {
                // Add the customer to QuickBooks

                QuickBooks_Callbacks_Integrator_Callbacks::integrateAddCustomer($CustomerID);
                //$extras = $Integrator->getCustomerExtras($CustomerID, __FILE__, __LINE__);
                //$Customer = $Integrator->getCustomer($CustomerID, __FILE__, __LINE__);
                //$this->_integrateCustomer($Customer, $CustomerID, $extras);
            }*/
        }
    }

    /**
     * @param array $estimates
     *
     * @return boolean
     */
    protected static function _integrateNewEstimates($estimates)
    {
        $API        = QuickBooks_API_Singleton::getInstance();
        $Integrator = QuickBooks_Integrator_Singleton::getInstance();

        // Let's start with new estimates
        foreach ($estimates as $EstimateID) {
            $API->log('Analyzing estimate #' . $EstimateID, QUICKBOOKS_LOG_VERBOSE);

            $Estimate = $Integrator->getEstimate($EstimateID);
            QuickBooks_Callbacks_Integrator_Callbacks::_integrateEstimate($Estimate, $EstimateID);

            // Customer
            $CustomerID = $Estimate->getCustomerApplicationID();
            if ($ListID = $Estimate->getCustomerListID()) {
                // xxx Do nothing, already in QuickBooks
                // Add it again, just in case!
                QuickBooks_Callbacks_Integrator_Callbacks::integrateAddCustomer($CustomerID);
            } else {
                if (true) {
                    // Try to fetch the customer by name
                    $API->getCustomerByName($Integrator->getCustomerNameForQuery($CustomerID), 'QuickBooks_Callbacks_Integrator_Callbacks::getCustomerByName', $CustomerID);
                }
            }
            /*else
            {
                // Add the customer to QuickBooks

                QuickBooks_Callbacks_Integrator_Callbacks::integrateAddCustomer($CustomerID);
                //$extras = $Integrator->getCustomerExtras($CustomerID, __FILE__, __LINE__);
                //$Customer = $Integrator->getCustomer($CustomerID, __FILE__, __LINE__);
                //$this->_integrateCustomer($Customer, $CustomerID, $extras);
            }*/

            $list = $Integrator->getEstimateItemsForEstimate($EstimateID);
            foreach ($list as $EstimateItem) {
                $ProductID = $EstimateItem->getItemApplicationID();

                if (!$EstimateID) {
                    continue;
                }

                //
                if ($ListID = $EstimateItem->getItemListID()) {
                    // Add it again anyway, just in case
                    $Product = $Integrator->getProduct($ProductID, __FILE__, __LINE__);
                    QuickBooks_Callbacks_Integrator_Callbacks::_integrateProduct($Product, $ProductID);
                } else {
                    if (true) {
                        // Queue a request *for each type* of item
                        $API->getItemByName($Integrator->getProductNameForQuery($ProductID), 'QuickBooks_Callbacks_Integrator_Callbacks::getProductByName', $ProductID);
                    }
                }
                /*
                else
                {
                    //print('else');
                    $Product = $Integrator->getProduct($ProductID, __FILE__, __LINE__);
                    $this->_integrateProduct($Product, $ProductID);
                }
                */
            }
        }
    }

    protected static function _integrateNewOrders($orders)
    {
        $API        = QuickBooks_API_Singleton::getInstance();
        $Integrator = QuickBooks_Integrator_Singleton::getInstance();

        // Let's start with new orders
        foreach ($orders as $OrderID) {
            $API->log('Analyzing order #' . $OrderID, QUICKBOOKS_LOG_VERBOSE);

            $Order = $Integrator->getOrder($OrderID);

            if ($Order) {
                // Customer
                $CustomerID = $Order->getCustomerApplicationID();
                if ($ListID = $Order->getCustomerListID()) {
                    // Add it again, just in case!
                    QuickBooks_Callbacks_Integrator_Callbacks::integrateAddCustomer($CustomerID);
                } else {
                    // Try to fetch the customer by name
                    $API->getCustomerByName($Integrator->getCustomerNameForQuery($CustomerID), 'QuickBooks_Callbacks_Integrator_Callbacks::getCustomerByName', $CustomerID);
                }

                // ShipMethod
                /*
                $ShipMethodID = $Order->getShipMethodApplicationID();
                if (
                    (is_numeric($ShipMethodID) and (int) $ShipMethodID) or
                    (!is_numeric($ShipMethodID) and strlen($ShipMethodID)))
                {
                    if ($ListID = $Order->getShipMethodListID())
                    {
                        // xxx Do nothing, already in QuickBooks
                        // Add it again anyway, just in case!
                        $ShipMethod = $Integrator->getShipMethod($ShipMethodID);
                        QuickBooks_Callbacks_Integrator_Callbacks::_integrateShipMethod($ShipMethod, $ShipMethodID);
                    }
                    else if ($this->_integrator_config['lookup_shipmethods'] and $ShipMethodID)
                    {
                        // Try to fetch the shipping method by name
                        $API->getShipMethodByName($Integrator->getShipMethodNameForQuery($ShipMethodID), 'QuickBooks_Callbacks_Integrator_Callbacks::getShipMethodByName', $ShipMethodID);
                    }
                    else if ($ShipMethodID)
                    {
                        $ShipMethod = $Integrator->getShipMethod($ShipMethodID);
                        QuickBooks_Callbacks_Integrator_Callbacks::_integrateShipMethod($ShipMethod, $ShipMethodID);
                    }
                }
                */

                $list = $Integrator->getOrderItems($OrderID);
                foreach ($list as $OrderItem) {
                    //
                    $ProductID = $OrderItem->getItemApplicationID();

                    if (!$ProductID) {
                        continue;
                    }

                    //
                    if ($ListID = $OrderItem->getItemListID()) {
                        // Add it again anyway, just in case
                        $Product = $Integrator->getProduct($ProductID, __FILE__, __LINE__);
                        QuickBooks_Callbacks_Integrator_Callbacks::_integrateProduct($Product, $ProductID);
                    } else {
                        // Queue a request *for each type* of item

                        // Try to fetch the product from QuickBooks
                        $API->getItemByName($Integrator->getProductNameForQuery($ProductID), 'QuickBooks_Callbacks_Integrator_Callbacks::getProductByName', $ProductID);
                    }
                }

                QuickBooks_Callbacks_Integrator_Callbacks::_integrateOrder($Order, $OrderID);

                //
                $Payment = $Integrator->getPayment($OrderID);
                if ($Payment and $Payment->getTotalAmount() > 0) {
                    QuickBooks_Callbacks_Integrator_Callbacks::_integratePayment($Payment, $OrderID);
                }
            }
        }
    }

    /**
     * @deprecated
     */
    protected static function _integrateHandling($Handling)
    {
        $API = QuickBooks_API_Singleton::getInstance();

        $API->getItemByName($Handling->getFullName(), 'QuickBooks_Callbacks_Integrator_Callbacks::getProductByName', QUICKBOOKS_INTEGRATOR_HANDLING_ID);

        return QuickBooks_Callbacks_Integrator::_integrateProduct($Handling, QUICKBOOKS_INTEGRATOR_HANDLING_ID);
    }

    protected static function _integrateShipping($Shipping)
    {
        $API = QuickBooks_API_Singleton::getInstance();

        $API->getItemByName($Shipping->getFullName(), 'QuickBooks_Callbacks_Integrator_Callbacks::getProductByName', QUICKBOOKS_INTEGRATOR_SHIPPING_ID);

        return QuickBooks_Callbacks_Integrator_Callbacks::_integrateProduct($Shipping, QUICKBOOKS_INTEGRATOR_SHIPPING_ID);
    }

    protected static function _integrateCoupons($Coupon)
    {
        $API = QuickBooks_API_Singleton::getInstance();

        $API->getItemByName($Coupon->getFullName(), 'QuickBooks_Callbacks_Integrator_Callbacks::getProductByName', QUICKBOOKS_INTEGRATOR_COUPON_ID);

        return QuickBooks_Callbacks_Integrator_Callbacks::_integrateProduct($Coupon, QUICKBOOKS_INTEGRATOR_COUPON_ID);
    }

    protected static function _integrateDiscounts($Discount)
    {
        $API = QuickBooks_API_Singleton::getInstance();

        $API->getItemByName($Discount->getFullName(), 'QuickBooks_Callbacks_Integrator_Callbacks::getProductByName', QUICKBOOKS_INTEGRATOR_DISCOUNT_ID);

        return QuickBooks_Callbacks_Integrator_Callbacks::_integrateProduct($Discount, QUICKBOOKS_INTEGRATOR_DISCOUNT_ID);
    }

    protected static function _integrateOrder($Order, $OrderID)
    {
        $API = QuickBooks_API_Singleton::getInstance();

        $API->log('Integrating order #' . $OrderID . ' as a ' . $Order->object(), QUICKBOOKS_LOG_DEVELOP);
        $user = $API->user();

        // Call a hook to indicate the order is being pushed to QuickBooks
        $hook_data = array(
            'OrderID' => $OrderID,
            'Order'   => $Order,
        );

        // Send the object to QuickBooks
        switch ($Order->object()) {
            case QUICKBOOKS_OBJECT_SALESRECEIPT:

                return $API->addSalesReceipt($Order, 'QuickBooks_Callbacks_Integrator_Callbacks::addSalesReceipt', $OrderID);
            case QUICKBOOKS_OBJECT_SALESORDER:

                return $API->addSalesOrder($Order, 'QuickBooks_Callbacks_Integrator_Callbacks::addSalesOrder', $OrderID);
            case QUICKBOOKS_OBJECT_INVOICE:

                return $API->addInvoice($Order, 'QuickBooks_Callbacks_Integrator_Callbacks::addInvoice', $OrderID);
            default:
                return false;
        }
    }

    protected static function _integrateEstimate($Estimate, $EstimateID)
    {
        $API = QuickBooks_API_Singleton::getInstance();

        $API->log('Integrating estimate #' . $EstimateID . ' as a ' . $Estimate->object(), QUICKBOOKS_LOG_DEVELOP);

        return $API->addEstimate($Estimate, 'QuickBooks_Callbacks_Integrator_Callbacks::addEstimate', $EstimateID);
    }

    /**
     * @param QuickBooks_Object_ReceivePayment $Payment
     * @param mixed                            $OrderID
     *
     * @return boolean
     */
    protected static function _integratePayment($Payment, $OrderID)
    {
        $API = QuickBooks_API_Singleton::getInstance();

        $API->log('Integrating payment for order #' . $OrderID, QUICKBOOKS_LOG_DEVELOP);

        return $API->addReceivePayment($Payment, 'QuickBooks_Callbacks_Integrator_Callbacks::addReceivePayment', $OrderID);
    }

    /**
     * @param QuickBooks_Object $Product
     * @param mixed             $ProductID
     *
     * @return boolean
     */
    protected static function _integrateProduct($Product, $ProductID)
    {
        $API = QuickBooks_API_Singleton::getInstance();

        switch ($Product->object()) {
            case QUICKBOOKS_OBJECT_INVENTORYITEM:

                return $API->addInventoryItem($Product, 'QuickBooks_Callbacks_Integrator_Callbacks::addInventoryItem', $ProductID);
            case QUICKBOOKS_OBJECT_NONINVENTORYITEM:

                return $API->addNonInventoryItem($Product, 'QuickBooks_Callbacks_Integrator_Callbacks::addNonInventoryItem', $ProductID);
            case QUICKBOOKS_OBJECT_SERVICEITEM:

                return $API->addServiceItem($Product, 'QuickBooks_Callbacks_Integrator_Callbacks::addServiceItem', $ProductID);
            case QUICKBOOKS_OBJECT_DISCOUNTITEM:

                return $API->addDiscountItem($Product, 'QuickBooks_Callbacks_Integrator_Callbacks::addDiscountItem', $ProductID);
            case QUICKBOOKS_OBJECT_OTHERCHARGEITEM:

                return $API->addOtherChargeItem($Product, 'QuickBooks_Callbacks_Integrator_Callbacks::addOtherChargeItem', $ProductID);
            default:
                return false;
        }
    }

    protected static function _integrateSalesReceipt()
    {
    }

    protected static function _integrateSalesOrder()
    {
    }

    protected static function _integrateInventoryItem()
    {
    }

    protected static function _integrateNonInventoryItem()
    {
    }

    protected static function _integrateServiceItem()
    {
    }

    protected static function _integrateDiscountItem()
    {
    }

    protected static function _integrateSalesTaxItem()
    {
    }

    public static function integrateQueryCustomer($CustomerID)
    {
        return QuickBooks_Callbacks_Integrator_Callbacks::integrateCustomer($CustomerID, false, true);
    }

    public static function integrateAddCustomer($CustomerID)
    {
        return QuickBooks_Callbacks_Integrator_Callbacks::integrateCustomer($CustomerID, false);
    }

    public static function integrateModCustomer($CustomerID)
    {
        return QuickBooks_Callbacks_Integrator_Callbacks::integrateCustomer($CustomerID, true);
    }

    public static function integrateCustomer($CustomerID, $modify = false, $query = false)
    {
        $API        = QuickBooks_API_Singleton::getInstance();
        $Integrator = QuickBooks_Integrator_Singleton::getInstance();

        $API->log('Analyzing customer #' . $CustomerID, QUICKBOOKS_LOG_DEVELOP);

        $extras = $Integrator->getCustomerExtras($CustomerID, __FILE__, __LINE__);

        if ($Customer = $Integrator->getCustomer($CustomerID, __FILE__, __LINE__)) {
            $action = 'add';
            if ($modify) {
                $action = 'mod';
            } else {
                if ($query) {
                    $action = 'query';
                }
            }

            $API->log('Integrating customer #' . $CustomerID . ' (' . $action . ')', QUICKBOOKS_LOG_DEVELOP);

            $continue = false;

            if ($query and
                $API->getCustomerByName($Integrator->getCustomerNameForQuery($CustomerID), 'QuickBooks_Callbacks_Integrator_Callbacks::getCustomerByName', $CustomerID)
            ) {
                return true;
            } else {
                if (!$modify and !$query and
                                 $API->addCustomer($Customer, 'QuickBooks_Callbacks_Integrator_Callbacks::addCustomer', $CustomerID)
                ) {
                    $continue = true;
                }
            }

            if ($continue) {
                if (is_array($extras)) {
                    foreach ($extras as $key => $Extra) {
                        $API->addDataExt($Extra, 'QuickBooks_Callbacks_Integrator_Callbacks::addExtra', $CustomerID . '-' . $key, null, QUICKBOOKS_ADD_CUSTOMER);

                        if ($modify) {
                            $API->modifyDataExt($Extra, 'QuickBooks_Callbacks_Integrator_Callbacks::modExtra', $CustomerID . '-' . $key, null, QUICKBOOKS_MOD_CUSTOMER);
                        }
                    }
                }

                return true;
            }
        }

        return false;
    }

    /**
     *
     *
     * @param string              $method
     * @param string              $action
     * @param mixed               $ID
     * @param string              $err
     * @param string              $qbxml
     * @param QuickBooks_Iterator $Iterator
     * @param resource            $qbres
     *
     * @return boolean
     */
    public static function getCustomerByName($method, $action, $ID, $err, $qbxml, $Iterator, $qbres)
    {
        $API = QuickBooks_API_Singleton::getInstance();

        if ($Iterator->count() == 1) {
            // If we found the customer in QuickBooks, create a mapping with the ListID value
            $Customer = $Iterator->next();
            if ($API->createMapping(QUICKBOOKS_OBJECT_CUSTOMER, $ID, $Customer->getListID(), $Customer->getEditSequence())) {
                // Let's make sure that this customer is up-to-date
                return QuickBooks_Callbacks_Integrator_Callbacks::integrateModCustomer($ID);
            }
        } else {
            if ($Iterator->count() == 0) {
                // Otherwise, we need to queue up an add request to add this cart item to QuickBooks
                return QuickBooks_Callbacks_Integrator_Callbacks::integrateAddCustomer($ID);
            }
        }

        return false;
    }

    public static function getInvoiceByRefNumber($method, $action, $ID, $err, $qbxml, $Iterator, $qbres)
    {
    }

    public static function getProductByName($method, $action, $ID, $err, $qbxml, $Iterator, $qbres)
    {
        $API        = QuickBooks_API_Singleton::getInstance();
        $Integrator = QuickBooks_Integrator_Singleton::getInstance();

        if ($Iterator->count() == 1) {
            // If we found the item in QuickBooks, create a mapping with the ListID value
            $Item = $Iterator->next();

            // Save the item in our list of integrator items
            $Integrator->saveItem($Item);

            // Create an object mapping
            return $API->createMapping($Item->object(), $ID, $Item->getListID(), $Item->getEditSequence());
        } else {
            if ($Iterator->count() == 0) {
                // Otherwise, we need to queue up an add request to add this cart item to QuickBooks
                if ($ID == QUICKBOOKS_INTEGRATOR_SHIPPING_ID) {
                    return true;
                } else {
                    if ($ID == QUICKBOOKS_INTEGRATOR_DISCOUNT_ID) {
                        return true;
                    }
                }

                $Product = $Integrator->getProduct($ID, __FILE__, __LINE__);

                switch ($Product->object()) {
                    case QUICKBOOKS_OBJECT_SERVICEITEM:
                        return $API->addServiceItem($Product, 'QuickBooks_Callbacks_Integrator_Callbacks::addServiceItem', $ID);
                        break;
                    case QUICKBOOKS_OBJECT_INVENTORYITEM:
                        return $API->addInventoryItem($Product, 'QuickBooks_Callbacks_Integrator_Callbacks::addInventoryItem', $ID);
                        break;
                    case QUICKBOOKS_OBJECT_NONINVENTORYITEM:
                        return $API->addNonInventoryItem($Product, 'QuickBooks_Callbacks_Integrator_Callbacks::addNonInventoryItem', $ID);
                        break;
                }
            }
        }

        return false;
    }

    public static function getServiceItemByName($method, $action, $ID, $err, $qbxml, $Iterator, $qbres)
    {
    }

    public static function getInventoryItemByName($method, $action, $ID, $err, $qbxml, $Iterator, $qbres)
    {
    }

    public static function getNonInventoryItemByName($method, $action, $ID, $err, $qbxml, $Iterator, $qbres)
    {
    }

    public static function getDiscountItemByName($method, $action, $ID, $err, $qbxml, $Iterator, $qbres)
    {
    }

    public static function getClassByName($method, $action, $ID, $err, $qbxml, $Iterator, $qbres)
    {
    }

    public static function getAccountByName($method, $action, $ID, $err, $qbxml, $Iterator, $qbres)
    {
    }

    public static function getShipMethodByName($method, $action, $ID, $err, $qbxml, $Iterator, $qbres)
    {
        $API        = QuickBooks_API_Singleton::getInstance();
        $Integrator = QuickBooks_Integrator_Singleton::getInstance();

        if ($Iterator->count() == 1) {
            // If we found the object in QuickBooks, create a mapping with the ListID value
            $ShipMethod = $Iterator->next();

            return $API->createMapping(QUICKBOOKS_OBJECT_SHIPMETHOD, $ID, $ShipMethod->getListID(), $ShipMethod->getEditSequence());
        } else {
            if ($Iterator->count() == 0) {
                // Otherwise, we need to queue up an add request to add this cart item to QuickBooks
                $ShipMethod = $Integrator->getShipMethod($ID);

                return $API->addShipMethod($ShipMethod, 'QuickBooks_Callbacks_Integrator_Callbacks::addShipMethod', $ID);
            }
        }

        return false;
    }

    /**
     * @param string              $method
     * @param string              $action
     * @param mixed               $ID
     * @param string              $err
     * @param string              $qbxml
     * @param QuickBooks_Iterator $Iterator
     * @param resource            $qbres
     *
     * @return boolean
     */
    public static function getPaymentMethodByName($method, $action, $ID, $err, $qbxml, $Iterator, $qbres)
    {
        $API        = QuickBooks_API_Singleton::getInstance();
        $Integrator = QuickBooks_Integrator_Singleton::getInstance();

        if ($Iterator->count() == 1) {
            // If we found the object in QuickBooks, create a mapping with the ListID value
            $PaymentMethod = $Iterator->next();

            return $API->createMapping(QUICKBOOKS_OBJECT_PAYMENTMETHOD, $ID, $PaymentMethod->getListID(), $PaymentMethod->getEditSequence());
        } else {
            if ($Iterator->count() == 0) {
                // Otherwise, we need to queue up an add request to add this cart item to QuickBooks
                $PaymentMethod = $Integrator->getPaymentMethod($ID);

                return $API->addPaymentMethod($PaymentMethod, 'QuickBooks_Callbacks_Integrator_Callbacks::addPaymentMethod', $ID);
            }
        }

        return false;
    }

    public static function listInvoicesModifiedAfter($method, $action, $ID, $err, $qbxml, $Iterator, $qbres)
    {
        while ($Invoice = $Iterator->next()) {
            return false;
        }

        return true;
    }

    public static function listSalesTaxItemsModifiedAfter($method, $action, $ID, $err, $qbxml, $Iterator, $qbres)
    {
        $Integrator = QuickBooks_Integrator_Singleton::getInstance();

        while ($SalesTaxItem = $Iterator->next()) {
            // Store this in the database
            $Integrator->saveSalesTaxItem($SalesTaxItem);
        }

        return true;
    }

    public static function listSalesTaxGroupItemsModifiedAfter($method, $action, $ID, $err, $qbxml, $Iterator, $qbres)
    {
        $Integrator = QuickBooks_Integrator_Singleton::getInstance();

        while ($SalesTaxGroupItem = $Iterator->next()) {
            // Store this in the database
            $Integrator->saveSalesTaxGroupItem($SalesTaxGroupItem);
        }

        return true;
    }

    public static function listAccountsModifiedAfter($method, $action, $ID, $err, $qbxml, $Iterator, $qbres)
    {
        $Integrator = QuickBooks_Integrator_Singleton::getInstance();

        while ($Account = $Iterator->next()) {
            // Store this in the database
            $Integrator->saveAccount($Account);
        }

        return true;
    }

    public static function listClassesModifiedAfter($method, $action, $ID, $err, $qbxml, $Iterator, $qbres)
    {
        $Integrator = QuickBooks_Integrator_Singleton::getInstance();

        while ($Class = $Iterator->next()) {
            // Store this in the database
            $Integrator->saveClass($Class);
        }

        return true;
    }

    public static function listPaymentMethodsModifiedAfter($method, $action, $ID, $err, $qbxml, $Iterator, $qbres)
    {
        $Integrator = QuickBooks_Integrator_Singleton::getInstance();

        while ($PaymentMethod = $Iterator->next()) {
            // Store this in the database
            $Integrator->savePaymentMethod($PaymentMethod);
        }

        return true;
    }

    public static function listCustomerTypesModifiedAfter($method, $action, $ID, $err, $qbxml, $Iterator, $qbres)
    {
        $Integrator = QuickBooks_Integrator_Singleton::getInstance();

        while ($CustomerType = $Iterator->next()) {
            // Store this in the database
            $Integrator->saveCustomerType($CustomerType);
        }

        return true;
    }

    public static function listShipMethodsModifiedAfter($method, $action, $ID, $err, $qbxml, $Iterator, $qbres)
    {
        $Integrator = QuickBooks_Integrator_Singleton::getInstance();

        while ($ShipMethod = $Iterator->next()) {
            // Store this in the database
            $Integrator->saveShipMethod($ShipMethod);
        }

        return true;
    }

    public static function listSalesTaxCodesModifiedAfter($method, $action, $ID, $err, $qbxml, $Iterator, $qbres)
    {
        $Integrator = QuickBooks_Integrator_Singleton::getInstance();

        while ($SalesTaxCode = $Iterator->next()) {
            // Store this in the database
            $Integrator->saveSalesTaxCode($SalesTaxCode);
        }

        return true;
    }

    public static function listUnitOfMeasureSetsModifiedAfter($method, $action, $ID, $err, $qbxml, $Iterator, $qbres)
    {
        $Integrator = QuickBooks_Integrator_Singleton::getInstance();

        while ($UnitOfMeasureSet = $Iterator->next()) {
            // Store this in the database
            $Integrator->saveUnitOfMeasureSet($UnitOfMeasureSet);
        }

        return true;
    }

    public static function listEstimatesModifiedAfter($method, $action, $ID, $err, $qbxml, $Iterator, $qbres)
    {
        $API        = QuickBooks_API_Singleton::getInstance();
        $Integrator = QuickBooks_Integrator_Singleton::getInstance();

        while ($Estimate = $Iterator->next()) {
            // Let's check if this estimate already exists in the system
            $EstimateID = null;
            if ($API->hasApplicationID(QUICKBOOKS_OBJECT_ESTIMATE, $Estimate->getTxnID())) {
                $EstimateID = $API->fetchApplicationID(QUICKBOOKS_OBJECT_ESTIMATE, $Estimate->getTxnID());
            }

            // Now, there's a customer assigned to this estimate, let's make sure the customer exists
            if (!$API->hasApplicationID(QUICKBOOKS_OBJECT_CUSTOMER, $Estimate->getCustomerListID())) {
                $Customer = new QuickBooks_Object_Customer();
                $Customer->setListID($Estimate->getCustomerListID());
                $Customer->setName($Estimate->getCustomerName());

                $Integrator->setCustomer(null, $Customer);
            }

            $Integrator->setEstimate($EstimateID, $Estimate);
        }

        return true;
    }

    public static function addPaymentMethod()
    {
    }

    public static function addShipMethod()
    {
    }

    public static function addServiceItem($method, $action, $ID, &$err, $qbxml, $ServiceItem, $qbres)
    {
        $API = QuickBooks_API_Singleton::getInstance();

        if (is_object($ServiceItem)) {
            // If we found the customer in QuickBooks, create a mapping with the ListID value
            $API->createMapping(
                QUICKBOOKS_OBJECT_SERVICEITEM,
                $ID,
                $ServiceItem->getListID(),
                $ServiceItem->getEditSequence());
        }
    }

    public static function addInventoryItem($method, $action, $ID, &$err, $qbxml, $InventoryItem, $qbres)
    {
        $API = QuickBooks_API_Singleton::getInstance();

        if (is_object($InventoryItem)) {
            // If we found the customer in QuickBooks, create a mapping with the ListID value
            $API->createMapping(
                QUICKBOOKS_OBJECT_INVENTORYITEM,
                $ID,
                $InventoryItem->getListID(),
                $InventoryItem->getEditSequence());
        }
    }

    public static function addNonInventoryItem($method, $action, $ID, &$err, $qbxml, $NonInventoryItem, $qbres)
    {
        $API = QuickBooks_API_Singleton::getInstance();

        if (is_object($NonInventoryItem)) {
            // If we found the customer in QuickBooks, create a mapping with the ListID value
            $API->createMapping(
                QUICKBOOKS_OBJECT_NONINVENTORYITEM,
                $ID,
                $NonInventoryItem->getListID(),
                $NonInventoryItem->getEditSequence());
        }
    }

    public static function addInvoice($method, $action, $ID, &$err, $qbxml, $Invoice, $qbres)
    {
        $API = QuickBooks_API_Singleton::getInstance();

        if (is_object($Invoice)) {
            // If we found the customer in QuickBooks, create a mapping with the ListID value
            $API->createMapping(
                QUICKBOOKS_OBJECT_INVOICE,
                $ID,
                $Invoice->getTxnID(),
                $Invoice->getEditSequence());
        }
    }

    public static function addEstimate()
    {
    }

    public static function addSalesReceipt()
    {
    }

    public static function addAccount()
    {
    }

    public static function addClass()
    {
    }

    public static function addExtra()
    {
    }

    public static function modExtra()
    {
    }

    public static function addCustomer($method, $action, $ID, &$err, $qbxml, $Customer, $qbres)
    {
        $API = QuickBooks_API_Singleton::getInstance();

        if (is_object($Customer)) {
            // If we found the customer in QuickBooks, create a mapping with the ListID value
            $API->createMapping(
                QUICKBOOKS_OBJECT_CUSTOMER,
                $ID,
                $Customer->getListID(),
                $Customer->getEditSequence());
        }
    }

    public static function modCustomer()
    {
    }

    public static function addReceivePayment($method, $action, $ID, &$err, $qbxml, $ReceivePayment, $qbres)
    {
        $API = QuickBooks_API_Singleton::getInstance();

        if (is_object($ReceivePayment)) {
            // If we found the customer in QuickBooks, create a mapping with the ListID value
            $API->createMapping(
                QUICKBOOKS_OBJECT_RECEIVEPAYMENT,
                $ID,
                $ReceivePayment->getTxnID(),
                $ReceivePayment->getEditSequence());
        }
    }
}
