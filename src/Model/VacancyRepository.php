<?php
/**
 * PHP version 5
 * 
 * MyClass File Doc Comment
 *
 * @category Class
 * @package  Test
 * @author   Sergii Chernenko <panua01@gmail.com>
 * @license  http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link     http://nowere.com/
 */

namespace Che\Model;

use MongoDB\Driver\Exception\Exception;
use Che\Utils\ArrayUtils;
use Che\Storage\Sphinx\Mysql;


/**
 * Centralized point for vacancies
 *
 * @category Class
 * @package  Test
 * @author   Sergii Chernenko <panua01@gmail.com>
 * @license  http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link     http://nowere.com/
 */
class VacancyRepository
{
    
    protected $srcmgr;
    protected $whdb;
    private $_conf;
    private $_logger;
    
    /**
     * Initialize Sourcemanager, Db, etc.
     * 
     * @param Config $conf   Config
     * @param Logger $logger Logger
     */
    function __construct($conf, $logger)
    {

        $this->_conf = $conf;
        $this->_logger = $logger;

        $this->srcmgr = new SourceManager($conf, $logger);
        $this->whdb = new Mysql($conf, $logger);
        $stat = $this->whdb->init('mysql');
        if ($stat === false) {
            throw new \Exception('Can\'t load MYSQL', 1);
        }

        $this->srcmgr->setDispatcher($this->whdb);
            
    }

    /**
     * Gets data from SourceManager and saves it to warehouse database
     * 
     * @param string $source  name of source
     * @param mixed  $options some options for source
     * 
     * @return array          array of VacancyExt
     */
    public function getData($source = null, $options = null)
    {
        $out = array();

        if (($source == 'local') || ($source == null)) {
          
            // get data from local database
            $out = $this->whdb->getData($options);
        }

        if ($source != 'local') {

            // get data from remote databases
            $res = $this->srcmgr->getData($source, $options);

            // if we've got some data
            if (is_array($res)) {
                if (count($res)>0) {
                    $out = array_merge($out, $res);

                    // If we've got data from different sources check for doubles
                    // before store to database although we have unique
                    // key in Db, or may be not... )
                    if ($source == null) {
                        $this->removeDoubles($res);
                    }

                    // save data to database
                    try {
                        $this->whdb->addData($res); 
                    } catch (\Exception $e) {
                        $this->$_logger->addError($e);
                        throw $e;
                    }
                }
            }
        }

        // If we've got data from different sources check for doubles
        if ($source == null) {
            $this->removeDoubles($out);
        }
        return $out;
    }

    /**
     * Delete some data from database
     * 
     * @param mixed $options some options to delete data
     * 
     * @return void
     */
    public function deleteData($options)
    {
        $this->whdb->deleteData($options);
    }

    /**
     * Update some data from database
     * 
     * @param array $ar some options to update data
     * 
     * @return void
     * @throws Exception Error
     */
    protected function removeDoubles(array &$ar)
    {
        // get names of unique fields from config
        $fields = $this->_conf->getConf()->uniquefields;

        // remove doubles
        $res = ArrayUtils::removeDoubles($ar, $fields);
        if ($res == false) {
            throw new \Exception("Remove doubles error", 1);
        }
    }
}