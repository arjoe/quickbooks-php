<?php

QuickBooks_Loader::load('/QuickBooks/IPP/Object.php');

class QuickBooks_IPP_Object_PrimaryEmailAddr extends QuickBooks_IPP_Object
{
    /**
     * Returns a user readable string interpretation of this instance.
     * @return string
     */
    public function __toString() {
        return $this->getAddress();
    }
}
