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

/**
 * Abstract class for parser
 * contains common methods like App reader
 *
 * @category Class
 * @package  Test
 * @author   Sergii Chernenko <panua01@gmail.com>
 * @license  http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link     http://nowere.com/
 */
abstract class ParserAbstract
{
    /**
     * Link to Config object
     * 
     * @var Config
     */
    protected $conf;

    /**
     * Link to SourceManager object
     * 
     * @var SourceManager
     */
    protected $srcname;

    /**
     * Constructor
     * 
     * @param string $srcname name
     * @param Config $conf    Config
     * @param Logger $logger  Logger
     */
    function __construct($srcname, $conf, $logger) 
    {
        $this->srcname = $srcname;
        $this->logger = $logger;
        $a = $conf->getConf();

        // get child class name to obtain default configuration
        $name = get_class($this);
        if (isset($a->parsers->$name)) {
            $this->conf = $a->parsers->$name;
        }
    }

    /**
     * Set additional Configuration
     * 
     * @param stdClass $conf App object
     *
     * @return void
     */
    public function setApp($conf) 
    {
        $this->conf = (object) array_merge((array) $this->conf, (array) $conf);
    }

    /**
     * Parse text and returns array of VacancyExt
     * 
     * @param string $txt unparced data
     * 
     * @return array Array of VacancyExt
     */
    abstract function parse(&$txt);
}
