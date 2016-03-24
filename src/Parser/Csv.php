<?php
/**
 * PHP version 5.3+
 *
 * File doc comment
 *
 * @category Class
 * @package  Test
 * @author   Sergii Chernenko <panua01@gmail.com>
 * @license  http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link     http://nowere.com/
 */

namespace Che\Parser;

use Che\Model\VacancyExt;

/**
 * CSV parser
 *
 * @category Class
 * @package  Test
 * @author   Sergii Chernenko <panua01@gmail.com>
 * @license  http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link     http://nowere.com/
 */
class Csv extends ParserAbstract
{
    /**
     * This function parse csv text and returns an array of VacancyExt objects
     *
     * @param string $txt csv data
     *
     * @return array of VacancyExt
     */
    function parse(&$txt)
    {
        $this->logger->addDebug('Parce data', [get_class($this), $this->srcname]);
        // separate rows
        $lines = explode("\n", $txt);

        // array for return
        $ar = array();
        foreach ($lines as $value) {

            $tmpar = str_getcsv(
                $value,
                $this->conf->delimiter,
                $this->conf->enclosure,
                $this->conf->escape
            );

            if (count($tmpar) == 4) {
                $tmpobj = new VacancyExt;
                $tmpobj->source = $this->srcname;
                $tmpobj->id = $tmpar[0];
                $tmpobj->title = $tmpar[1];
                $tmpobj->content = $tmpar[2];
                $tmpobj->description = $tmpar[3];
                $ar[] = $tmpobj;
            } else {
                $this->logger->addError(
                    'Error in data', [get_class($this),
                    $this->srcname]
                );
                throw new ParserException('Error in data', 1);
            }

        }
        return $ar;
    }
}