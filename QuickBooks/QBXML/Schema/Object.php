<?php

/**
 *
 *
 * @author     Keith Palmer <keith@consolibyte.com>
 * @license    LICENSE.txt
 *
 * @package    QuickBooks
 * @subpackage QBXML
 */

define('QUICKBOOKS_QBXML_SCHEMA_TYPE_STRTYPE', 'STRTYPE');
define('QUICKBOOKS_QBXML_SCHEMA_TYPE_IDTYPE', 'IDTYPE');
define('QUICKBOOKS_QBXML_SCHEMA_TYPE_BOOLTYPE', 'BOOLTYPE');
define('QUICKBOOKS_QBXML_SCHEMA_TYPE_AMTTYPE', 'AMTTYPE');

abstract class QuickBooks_QBXML_Schema_Object
{
    abstract protected function &_qbxmlWrapper();

    public function qbxmlWrapper()
    {
        return $this->_qbxmlWrapper();
    }

    abstract protected function &_dataTypePaths();

    /**
     * @param string $match
     *
     * @return array
     */
    public function paths($match = null)
    {
        $paths = $this->_dataTypePaths();

        return array_keys($paths);
    }

    /**
     * @param string  $path
     * @param boolean $case_doesnt_matter
     *
     * @return string
     */
    public function dataType($path, $case_doesnt_matter = true)
    {
        $paths = $this->_dataTypePaths();

        if (isset($paths[$path])) {
            return $paths[$path];
        } else {
            if ($case_doesnt_matter) {
                foreach ($paths as $dtpath => $datatype) {
                    if (strtolower($dtpath) == strtolower($path)) {
                        return $datatype;
                    }
                }
            }
        }

        return null;
    }

    abstract protected function &_maxLengthPaths();

    /**
     * @param string  $path
     * @param boolean $case_doesnt_matter
     * @param string  $locale
     *
     * @return integer
     */
    public function maxLength($path, $case_doesnt_matter = true, $locale = null)
    {
        $paths = $this->_maxLengthPaths();

        if (isset($paths[$path])) {
            return $paths[$path];
        } else {
            if ($case_doesnt_matter) {
                foreach ($paths as $mlpath => $maxlength) {
                    if (strtolower($mlpath) == strtolower($path)) {
                        return $paths[$mlpath];
                    }
                }
            }
        }

        return 0;
    }

    abstract protected function &_isOptionalPaths();

    public function isOptional($path)
    {
        $paths = $this->_isOptionalPaths();

        if (isset($paths[$path])) {
            return $paths[$path];
        }

        return true;
    }

    abstract protected function &_sinceVersionPaths();

    public function sinceVersion($path)
    {
        $paths = $this->_sinceVersionPaths();

        if (isset($paths[$path])) {
            return $paths[$path];
        }

        return '999.99';
    }

    abstract protected function &_isRepeatablePaths();

    /**
     * Tell whether or not a specific element is repeatable
     *
     * @param string $path
     *
     * @return boolean
     */
    public function isRepeatable($path)
    {
        $paths = $this->_isRepeatablePaths();

        if (isset($paths[$path])) {
            return $paths[$path];
        }

        return false;
    }

    /**
     * Tell whether or not an element exists
     *
     * @param string $path
     * @param bool   $case_doesnt_matter
     * @param bool   $is_end_element
     *
     * @return boolean
     */
    public function exists($path, $case_doesnt_matter = true, $is_end_element = false)
    {
        $ordered_paths = $this->_reorderPathsPaths();

        if (in_array($path, $ordered_paths)) {
            return true;
        } else {
            if ($case_doesnt_matter) {
                foreach ($ordered_paths as $ordered_path) {
                    if (strtolower($path) == strtolower($ordered_path)) {
                        return true;
                    }
                }
            }
        }

        return false;
    }

    public function unfold($path)
    {
        static $paths = null;

        if (is_null($paths)) {
            $paths = $this->_reorderPathsPaths();
            $paths = array_change_key_case(array_combine(array_values($paths), array_values($paths)), CASE_LOWER);
        }

        if (isset($paths[strtolower($path)])) {
            return $paths[strtolower($path)];
        }

        return null;
    }

    /**
     * @note WARNING! These are lists of UNSUPPORTED locales, NOT lists of supported ones!
     */
    protected function &_inLocalePaths()
    {
        $arr = array();

        return $arr;
    }

    /**
     * @note WARNING! These are lists of UNSUPPORTED locales, NOT lists of supported ones!
     */
    public function localePaths()
    {
        return $this->_inLocalePaths();
    }

    /**
     * Return a list of paths in a specific schema order
     *
     * @return array
     */
    abstract protected function &_reorderPathsPaths();

    /**
     * Re-order an array to match the schema order
     *
     * @param array $unordered_paths
     * @param bool  $allow_application_id
     *
     * @return array
     */
    public function reorderPaths($unordered_paths, $allow_application_id = true)
    {
        $ordered_paths = $this->_reorderPathsPaths();

        $tmp = array();

        foreach ($ordered_paths as $key => $path) {
            if (in_array($path, $unordered_paths)) {
                $tmp[$key] = $path;
            }
        }

        return array_merge($tmp);
    }
}

