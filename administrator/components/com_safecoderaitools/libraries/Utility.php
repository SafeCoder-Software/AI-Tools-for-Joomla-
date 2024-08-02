<?php

/**
 * @package     SafeCoder AI Tools
 * @subpackage  com_safecoderaitools
 * 
 * @version     1.0.0
 * 
 * @author      Miron Savan <hello@safecoder.com>
 * @link        https://www.safecoder.com/aitools
 * @copyright   Copyright (C) 2012 SafeCoder Software SRL (RO30786660)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html> or later; see LICENSE.txt
 */

namespace SafeCoderSoftwareAITools\Libraries;

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\Database\DatabaseInterface;

class Utility
{

    /**
     * Check if we have a valid positive integer
     *
     * @param [type] $value
     * @return boolean
     */
    public static function isValidPositiveNumber($value)
    {
        try {

            if (!is_string($value) && !is_int($value)) {
                return false;
            }

            $number = filter_var($value, FILTER_VALIDATE_INT);

            if ($number === false || $number <= 0) {
                return false;
            }

            return true;
        } catch (\Throwable $th) {
            return false;
        }
    }

    /**
     * Check if allowed request type
     *
     * @param [type] $value
     * @return boolean
     */
    public static function isAllowedType($value)
    {

        try {

            $AllowedTypes = array('article', 'category', 'tag');
            if (!\in_array($value, $AllowedTypes)) {
                return true;
            }

            return false;
        } catch (\Throwable $th) {
            return false;
        }
    }

    /**
     * Cleans string / leave new lines
     *
     * @param [type] $value
     * @return string
     */
    public static function cleanString($value)
    {
        try {

            if (!is_string($value) && !is_int($value)) {
                return '';
            }

            $value = trim(strip_tags($value));

            $value = preg_replace('/[^\p{L}\p{N}\p{P}\p{Z}\n]+/u', '', $value);

            $value = preg_replace('/^\n+|\n+$/', '', $value);

            return (string) $value;
        } catch (\Throwable $th) {
            return $value;
        }
    }


    /**
     * @template T
     * @param array $array
     * @param T $object
     * @return T|bool
     */
    public static function ArrayValuesToObject($array, $object)
    {

        try {

            if (!\is_object($object)) {
                return false;
            }

            if (!\is_array($array) || count($array) < 1) {
                return $object;
            }

            foreach ($array as $key => $value) {
                if (isset($object->$key)) {
                    $object->$key = $value;
                }
            }

            if (method_exists($object, 'ProcessValues')) {
                $object->ProcessValues();
            }

            return $object;
        } catch (\Throwable $th) {
            return $object;
        }
    }

    /**
     * check if empty or non array, or if property exists
     *
     * @param array $array - the array
     * @param [type] $propertyKey - array key name
     * @return boolean
     */
    public static function IsValidNonEmptyArray($array = array(), $propertyKey = null)
    {
        try {

            if (!\is_array($array)) {
                return false;
            }

            if (\count($array) < 1) {
                return false;
            }

            if ($propertyKey !== null) {
                if (!array_key_exists($propertyKey, $array)) {
                    return false;
                }
            }

            return true;
        } catch (\Throwable $th) {
            return false;
        }
    }

    /**
     * Get name from userid
     *
     * @param integer $userID
     * @return string
     */
    public static function getNameFromUserID($userID = 0)
    {

        try {

            /** @var \Joomla\Database\DatabaseInterface $db */
            $db = Factory::getContainer()->get(DatabaseInterface::class);

            $query = $db->getQuery(true);
            $query->select($db->quoteName('name'));
            $query->from($db->quoteName('#__users'));
            $query->where($db->quoteName('id') . ' = ' . $db->quote($userID));
            $db->setQuery($query);
            return $db->loadResult();
        } catch (\Throwable $th) {
            return '';
        }
    }

    /**
     * Check if given string is json
     *
     * @param [type] $string
     * @return boolean
     */
    public static function isJson($string)
    {
        try {
            json_decode($string);
            return json_last_error() === JSON_ERROR_NONE;
        } catch (\Throwable $th) {
            return false;
        }
    }

    /**
     * Return server response message based on code.
     *
     * @param [type] $errorCode
     * @return void
     */
    public static function ReturnResponseMessageBasedOnCode($errorCode)
    {
        try {
            // Define an array of error codes and messages
            $errorMessages = array(
                100 => 'Continue',
                101 => 'Switching Protocols',
                200 => 'OK',
                201 => 'Created',
                202 => 'Accepted',
                203 => 'Non-Authoritative Information',
                204 => 'No Content',
                205 => 'Reset Content',
                206 => 'Partial Content',
                300 => 'Multiple Choices',
                301 => 'Moved Permanently',
                302 => 'Found',
                303 => 'See Other',
                304 => 'Not Modified',
                305 => 'Use Proxy',
                306 => '(Unused)',
                307 => 'Temporary Redirect',
                400 => 'Bad Request',
                401 => 'Unauthorized',
                402 => 'Payment Required',
                403 => 'Forbidden',
                404 => 'Not Found',
                405 => 'Method Not Allowed',
                406 => 'Not Acceptable',
                407 => 'Proxy Authentication Required',
                408 => 'Request Timeout',
                409 => 'Conflict',
                410 => 'Gone',
                411 => 'Length Required',
                412 => 'Precondition Failed',
                413 => 'Request Entity Too Large',
                414 => 'Request-URI Too Long',
                415 => 'Unsupported Media Type',
                416 => 'Requested Range Not Satisfiable',
                417 => 'Expectation Failed',
                500 => 'Internal Server Error',
                501 => 'Not Implemented',
                502 => 'Bad Gateway',
                503 => 'Service Unavailable',
                504 => 'Gateway Timeout',
                505 => 'HTTP Version Not Supported',
                // add more error codes and messages here
            );

            // Check if the error code is valid and return the corresponding error message if it is
            if (array_key_exists($errorCode, $errorMessages)) {
                return $errorMessages[$errorCode];
            } else {
                return Text::_('COM_SAFECODERAITOOLS_SERVER_CODE_NONE');
            }
        } catch (\Throwable $th) {
            return Text::_('COM_SAFECODERAITOOLS_SERVER_CODE_NONE');
        }
    }

    /**
     * Check if valid date format from string
     *
     * @param [type] $value
     * @param string $format
     * @return boolean
     */
    public static function IsValidMySQLDate($value, $format = 'Y-m-d H:i:s.u')
    {
        try {
            
            $value = (string) trim(strip_tags($value));
            $d = \DateTime::createFromFormat($format, $value);

            if ($d === false || $d->format($format) !== $value) {
                return false;
            } else {
                return true;
            }
        } catch (\Throwable $th) {
            return false;
        }
    }



    /**
     * Get time difference between two dates
     *
     * @param [type] $dateupdated
     * @param [type] $date
     * @return string
     */
    public static function getTimeDifference($dateupdated, $date)
    {

        if (!self::IsValidMySQLDate($dateupdated) || !self::IsValidMySQLDate($date)) {
            return '00.00';
        }

        $updated = new \DateTime($dateupdated);
        $original = new \DateTime($date);
        $diff = $updated->diff($original);

        if ($updated < $original) {
            return '00.00';
        } else {
            $seconds = $diff->s + ($diff->f / 1000000);
            return (string) number_format($seconds, 2);
        }
    }

    /**
     * 
     * Turn object into an array and remove the private properties
     *
     * @param [type] $dateupdated
     * @param [type] $date
     * @return string
     * 
     * @since 1.1.0
     * @version 1.0.0
     * 
     */
    public static function ObjectToCleanArray($object) {

        if(!is_object($object)) {
            return array();
        }

        $array = (array) $object;
    
        foreach ($array as $key => $value) {
            if (strpos($key, "\0") !== false) {
                unset($array[$key]);
            }
        }
    
        return $array;
    }
}
