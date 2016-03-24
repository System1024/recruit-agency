<?php
/**
 * PHP version 5.3+
 * 
 * File Doc Comment
 *
 * @category Class
 * @package  Source\ASource
 * @author   Sergii Chernenko <panua01@gmail.com>
 * @license  http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link     http://nowere.com/
 */

namespace Che\Source;

use Symfony\Component\EventDispatcher\Event;

/**
 * Abstract class for source
 *
 * @category Class
 * @package  Source\ASource
 * @author   Sergii Chernenko <panua01@gmail.com>
 * @license  http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link     http://nowere.com/
 */
abstract class SourceAbstract
{
    
    protected $conf;
    protected $srcname;
    protected $logger;

    /**
     * Constructor
     * 
     * @param string $srcname name
     * @param Config $conf    Config
     * @param Logger $logger  Logger
     */
    function __construct($srcname, $conf, $logger)
    {
        if (!$logger) {
            throw new \Exception('Logger not defined', 1);
        }
        $this->logger = $logger;
        if (!$conf) {
            throw new \Exception('Config not defined', 1);
        }

        $this->srcname = $srcname;
        $a = $conf->getConf();
        $name = get_class($this);
        if (isset($a->parsers->$name)) {
            $this->conf = $a->parsers->$name;
        }
    }

    /**
     * On delete evevt handler
     * 
     * @param Event $event Event
     * 
     * @return void
     */
    public function onDeleteData(Event $event)
    {
        $this->logger->addDebug("Delete data from $this->srcname");
        // do something
    }

    /**
     * On update evevt handler
     * 
     * @param Event $event Event
     * 
     * @return void
     */    
    public function onUpdateData(Event $event)
    {
        $this->logger->addDebug("Update data in $this->srcname");
        // do something
    }

    /**
     * Set additional config
     * 
     * @param stdClass $conf Config object
     *
     * @return void
     */    
    public function setApp($conf)
    {
        $this->conf = (object) array_merge((array) $this->conf, (array) $conf);
    }

    /**
     * Get data from source
     * 
     * @param mixed $options some options for API
     * 
     * @return string some data
     */
    abstract function getData($options = null);

    /**
     * Check if source is available
     * 
     * @return boolean availability
     */
    abstract function isAvailable();
}