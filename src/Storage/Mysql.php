<?php
/**
 * PHP version 5.3+
 * 
 * Class of Mysql
 *
 * @category Class
 * @package  Test
 * @author   Sergii Chernenko <panua01@gmail.com>
 * @license  http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link     http://nowere.com/
 */
namespace Che\Storage;

use Che\Model\VacancyExt;

/**
 * Class for Mysql storage
 *
 * @category Class
 * @package  Storage\StorageAbstract
 * @author   Sergii Chernenko <panua01@gmail.com>
 * @license  http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link     http://nowere.com/
 */
class Mysql extends StorageAbstract
{

    /**
     * Some initialization
     *
     * @param string $name name in config
     * 
     * @return void
     */
    function init($name)
    {

        $params = $this->conf->getConf();
        if ($params) {
            if (isset($params->db)) {
                $params = $params->db;
                if (isset($params->$name)) {
                    $params = $params->$name;
                } else {
                    $msg = 'You must define params for "'.
                        $name.'" in DB section in config';
                    $this->logger->addError($msg);
                    throw new MysqlException($msg, 1);                    
                }

            } else {
                $msg = 'You must define DB section in config';
                $this->logger->addError($msg);
                throw new MysqlException($msg, 1);
            }
        }

        $this->db = new \Mysqli(
            $params->host, 
            $params->login, 
            $params->password, 
            $params->dbname
        );
        if ($this->db->connect_errno) {
            $msg = $this->db->connect_error;
            $this->logger->addError($msg);
            throw new MysqlException($msg, 1);
            
        }
        return true;
    }

    /**
     * Get data from database by Id field
     * 
     * @param mixed $data id
     * 
     * @return array Data
     */
    function getDataById($data) 
    {
        $sql = 'select extid as id, title, content, 
            description, source from vacancy where `id` ';
        if (is_array($data)) {
            $sql .= ' in ('.implode(', ', $data).')';
        } else {
            $sql .= ' = '.$data;
        }

        $res = $this->db->query($sql);
        if (!$res) {
            throw new MysqlException("Error while getDataById", 1);
        }

        $out = array();
        while ($obj = $res->fetch_object()) {
            $n = new VacancyExt;
            $n->id = $obj->id; 
            $n->title = $obj->title;
            $n->content = $obj->content;
            $n->description = $obj->description; 
            $n->source = $obj->source; 
            $out[] = $n;
        } 
        $res->close();

        return $out;



    }


    /**
     * Get data from database
     * 
     * @param mixed $options some options
     * 
     * @return array array of data
     */
    function getData($options) 
    {
    }
    /**
     * Remove data from database
     * 
     * @param mixed $id id of records in database
     * 
     * @return void
     */
    function deleteData($id) 
    {
        $sql = 'delete from vacancy where id = "'.
            $this->db->real_escape_string($id).'"';
        if (!$this->db->query($sql)) {
            return false;
        }
        $this->dispatch('ondelete');
        return $this->db->affected_rows;        
    }

    /**
     * Add data to database
     * 
     * @param mixed   $data              array or one instance of Vacancy object
     * @param boolean $onduplicateupdate if true data 
     * in database will be updated if we have some there already
     *
     * @return void
     */
    function addData($data, $onduplicateupdate = true) 
    {

        try {
            $this->db->query('START TRANSACTION');

            $sql = 'insert into vacancy (`extid`, `source`
                    ,`title`, `content`, `description`) values ';
            if (is_array($data)) {
                $tmparr = array();
                foreach ($data as $v) {
                    $tmparr[] = '("'.$this->db->real_escape_string($v->id).'",
                        "'.$this->db->real_escape_string($v->source).'",
                        "'.$this->db->real_escape_string($v->title).'",
                        "'.$this->db->real_escape_string($v->content).'"
                        ,"'.$this->db->real_escape_string($v->description).'")';
                }
                $sql .= implode(', ', $tmparr);
            } else {
                $sql .= '("'.$this->db->real_escape_string($data->id).'",
                        "'.$this->db->real_escape_string($data->source).'",
                        "'.$this->db->real_escape_string($data->title).'",
                        "'.$this->db->real_escape_string($data->content).'"
                        ,"'.$this->db->real_escape_string($data->description).'")';

            }
            if ($onduplicateupdate) {
                $sql .= ' ON DUPLICATE KEY UPDATE `title`=VALUES(`title`)
, `content` = VALUES(`content`), `description` = VALUES(`description`)';
            }

            if (!$this->db->query($sql)) {
                throw new MysqlException($this->db->error, 1);
            }
            // if (!$this->db->errno) {
            //            throw new \Exception($this->db->error, 1);
            // }

            $this->db->query('COMMIT');

            return true;
        } catch (MysqlException $e) {
            $this->db->query('ROLLBACK');
            throw new $e;
            
        }
        return false;
    }
}