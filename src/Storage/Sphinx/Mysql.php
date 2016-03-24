<?php
/**
 * PHP version 5.3+
 * 
 * File Doc Comment
 *
 * @category Class
 * @package  Storage\Sphinx\Mysql
 * @author   Sergii Chernenko <panua01@gmail.com>
 * @license  http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link     http://nowere.com/
 */
namespace Che\Storage\Sphinx;

use Sphinx\SphinxClient;
use Che\Storage\Mysql as MysqlBase;

/**
 * Array utils class. 
 *
 * @category Class
 * @package  Storage\Sphinx\Mysql
 * @author   Sergii Chernenko <panua01@gmail.com>
 * @license  http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link     http://nowere.com/
 */
class Mysql extends MysqlBase
{
    protected $sphinx;

    /**
     * Some initialization
     *
     * @param Config $conf   Link to config object
     * @param Logger $logger Link to logger object
     */    
    function __construct($conf, $logger)
    {
        parent::__construct($conf, $logger);
        $this->sphinx = new SphinxClient();
        $this->sphinx->setServer($conf->db->redis->host);
    }

    /**
     * Reindex Spninx index
     * 
     * @return void
     */
    public function reindexData()
    {
        // do something to rebuild index
        // exec('indexer --all --rotate');
    }

    /**
     * Search string in index
     * 
     * @param string $query query
     * 
     * @return array of indexes in DataBase
     */
    public function find($query)
    {
        $res = $this->sphinx->query($query);

        if ($res === false) {
            throw new \Exception($this->sphinx->getLastError(), 1);
        }

        $matches = false;
        if ($res) {
            if ($res['total'] > 0) {
                $matches = array_keys($res['matches']);
            }
        }
        return $matches;
    }

    /**
     * Get data from database
     * 
     * @param mixed $options some options
     * 
     * @return array array of data
     */
    public function getData($options)
    {
        $res = $this->find($options);
        return $this->getDataById($res);
    }

    /**
     * Add data to database
     * 
     * @param array   $data              Some data
     * @param boolean $onduplicateupdate check for dupes
     *
     * @return void
     */
    function addData($data, $onduplicateupdate = true)
    {
        parent::addData($data, $onduplicateupdate);
        // here we can place 
        // $this->reindexData();
    }
}