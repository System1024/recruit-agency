<?php
/**
 * PHP version 5.3+
 * 
 * File Doc Comment
 *
 * @category Class
 * @package  Test
 * @author   Sergii Chernenko <panua01@gmail.com>
 * @license  http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link     http://nowere.com/
 */
namespace Che\Storage;

use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\EventDispatcher\Event;

/**
 * Abstract class for storage
 *
 * @category Class
 * @package  Storage\StorageAbstract
 * @author   Sergii Chernenko <panua01@gmail.com>
 * @license  http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link     http://nowere.com/
 */
abstract class StorageAbstract extends EventDispatcher
{
    
    protected $db;
    protected $conf;
    protected $logger;

    /**
     * Some initialization
     *
     * @param Config $conf   Link to config object
     * @param Logger $logger Link to logger object
     */
    function __construct($conf, $logger)
    {
        $this->conf = $conf;
        $this->logger = $logger;
    }

    /**
     * Some init stuff
     * 
     * @param string $name path in config
     * 
     * @return void
     */
    abstract function init($name);

    /**
     * Get data from database
     * 
     * @param mixed $options some query options
     * 
     * @return array array of VacancyExt
     */
    abstract function getData($options);

    /**
     * Remove data from database
     * 
     * @param mixed $id id of records in database
     * 
     * @return void
     */
    abstract function deleteData($id);

    /**
     * Add data to database
     * 
     * @param mixed   $data              array or one instance of Vacancy object
     * @param boolean $onduplicateupdate if true data 
     * in database will be updated if we have some there already
     *
     * @return void
     */
    abstract function addData($data, $onduplicateupdate = true);
}