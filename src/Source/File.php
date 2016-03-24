<?php
/**
 * PHP version 5.3+
 * 
 * File source for test project
 *
 * @category Class
 * @package  Test
 * @author   Sergii Chernenko <panua01@gmail.com>
 * @license  http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link     http://nowere.com/
 */

namespace Che\Source;

/**
 * Class implements work with file source
 *
 * @category Class
 * @package  Test
 * @author   Sergii Chernenko <panua01@gmail.com>
 * @license  http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link     http://nowere.com/
 */
class File extends SourceAbstract
{

    protected $file;

    /**
     * Get data from source 
     * 
     * @param mixed $options some options
     * 
     * @return text some data from file
     */
    public function getData($options = null)
    {
        if ($this->isAvailable()) {
            $this->logger->addDebug(
                'Read file: '.ROOT_PATH.$this->conf->file, 
                array('name'=>$this->srcname)
            );
            return file_get_contents(ROOT_PATH.$this->conf->file);
        }

        $msg = 'No such file: '.ROOT_PATH.$this->conf->file;
        $this->logger->addError($msg);
        throw new SourceException($msg);
    }

    /**
     * Check if file exists
     * 
     * @return boolean true if exists, false if don't
     */
    public function isAvailable()
    {
        $file = ROOT_PATH.$this->conf->file;
        $res = file_exists($file);
        $msg = $res?'exists':'not exists';
        $this->logger->addDebug('File "'.$file.'" '.$msg, [get_class($this)]);
        return $res;
    }   
}