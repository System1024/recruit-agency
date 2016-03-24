<?php
/**
 * PHP version 5.3+
 * 
 * File Doc Comment
 *
 * @category Class
 * @package  Utils\ArrayUtils
 * @author   Sergii Chernenko <panua01@gmail.com>
 * @license  http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link     http://nowere.com/
 */

namespace Che\Utils;

/**
 * Array utils class. 
 *
 * @category Class
 * @package  Utils\ArrayUtils
 * @author   Sergii Chernenko <panua01@gmail.com>
 * @license  http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link     http://nowere.com/
 */
class ArrayUtils
{
    /**
     * Remove doubles from array
     * 
     * @param array $ar     array with doubles
     * @param array $fields unique fields
     * 
     * @return void
     */
    public static function removeDoubles(array &$ar,array $fields)
    {
        $res = self::sortArray($ar, $fields);
        if ($res === false) {
            return false;
        }
        $res = self::removeDoublesFromSorted($ar, $fields);
        if ($res === false) {
            return false;
        }
        return true;
    }

    /**
     * Sort arry by multiple field
     * 
     * @param array $ar     array to sort
     * @param array $fields unique fields
     * 
     * @return void 
     **/
    public static function sortArray(array &$ar,array $fields) 
    {
        $count = count($fields);
        if ($count < 1) {
            throw new ArrayUtilsException(
                'Count of fields must be >1 now '.$count, 
                1
            );
        }

        $res = usort(
            $ar, function ($a, $b) use ($fields) {
                foreach ($fields as $v) {
                    if (!isset($a->$v)) {
                        throw new ArrayUtilsException(
                            'Field $a->'.$v.' not exists', 
                            2
                        ); 
                    }
                    if (!isset($b->$v)) {
                        throw new ArrayUtilsException(
                            'Field $b->'.$v.' not exists',
                            2
                        ); 
                    }

                    $strc = strcmp($a->$v, $b->$v);
                    if (0 === $strc) {
                        continue;
                    } else {
                        return $strc;
                    }
                }
                return 0;
            }
        );

        if ($res === false) {
            throw new ArrayUtilsException(
                'Error while sorting arrays', 
                1
            );
        }

    }

    /**
     * Remove doubles from array.
     * Array must be sorted
     * 
     * @param array $ar     array with doubles
     * @param array $fields unique fields
     * 
     * @return void
     */    
    public static function removeDoublesFromSorted(array &$ar,array $fields) 
    {
        $count = count($fields);
        if ($count < 1) {
            throw new ArrayUtilsException(
                'Count of fields must be >1 now '.$count,
                1
            );
        }
        $lastval = array();

        $out = array();

        foreach ($ar as $key => $item) {
            $curvals = array();
            foreach ($fields as $fname) {
                $curvals[] = $item->$fname;
            }

            $t = $curvals == $lastval;
            if (!$t) {
                $out[] = $item;
            }
            $lastval = $curvals;

        }
        $ar = $out;
    }    

}