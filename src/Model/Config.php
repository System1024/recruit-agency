<?php
/**
 * PHP version 5.3+
 * 
 * File doc comment
 *
 * @category Class
 * @package  Model\Config
 * @author   Sergii Chernenko <panua01@gmail.com>
 * @license  http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link     http://nowere.com/
 */

namespace Che\Model;

/**
 * Abstract class for parser
 * contains common methods like App reader
 *
 * @category Class
 * @package  Model\Config
 * @author   Sergii Chernenko <panua01@gmail.com>
 * @license  http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link     http://nowere.com/
 */
class Config
{
    protected $conf;
    protected $file;

    /**
     * Define file of config
     * 
     * @param string $file filename
     *
     * @return void
     */
    public function setConfFile($file) 
    {
        $this->file = $file;
    }

    /**
     * Read config
     * 
     * @param string $file filename
     * 
     * @return void
     */
    public function readConfFile($file) 
    {
        $content = file_get_contents($file);
        if ($content === false) {
            throw new \Exception('Can\'t read conf file '.$file, 1);
            
        }
        $this->conf = json_decode($content);
    }

    /**
     * Config
     * 
     * @return stdClass config 
     */
    public function getConf() 
    {
        if (!$this->conf) {
            $this->readConfFile($this->file); 
        }
        return $this->conf;
    }
    
    /**
     * Get sorce part of config
     * 
     * @param string $name name of source
     * 
     * @return stdClass config
     */
    public function getSource($name) 
    {
        
        if (!$this->conf) {
            $this->readConfFile($this->file); 
        }
        if (!isset($this->conf->sources->$name)) {
            throw new \Exception('No configuration for source '.$name, 2); 
        }
            
        return $this->conf->sources->$name;
    }    

    /**
     * Get sorces part of config
     * 
     * @return stdClass config
     */
    public function getSources() 
    {
        
        if (!$this->conf) {
            $this->readConfFile($this->file); 
        }
        if (!isset($this->conf->sources)) {
            throw new \Exception('No configuration for sources ', 2); 
        }
            
        return $this->conf->sources;
    }

    /**
     * Check if config valid
     * 
     * @return boolean True if valid
     */
    public function checkConfig() 
    {
        // do something to validate config
    }
}