<?php
/**
 * PHP version 5.3+
 * 
 * Http source for test project
 *
 * @category Class
 * @package  Test
 * @author   Sergii Chernenko <panua01@gmail.com>
 * @license  http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link     http://nowere.com/
 */

namespace Che\Source;

/**
 * Http resource
 *
 * @category Class
 * @package  Test
 * @author   Sergii Chernenko <panua01@gmail.com>
 * @license  http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link     http://nowere.com/
 */
class Http extends SourceAbstract
{

    protected $curl = false;
    protected $isavailable = true;

    /**
     * Some initialize stuff
     * 
     * @return curl curl object
     */
    public function init()
    {
        return $this->curl ?: $this->curl = curl_init();
    }

    /**
     * Get data from source
     * 
     * @param mixed $options some options for source
     * 
     * @return string some data, whitch web server returns
     */
    public function getData($options = null)
    {
        $out = false;

        if ($this->isAvailable()) {
            if ($curl = $this->init()) {

                $this->logger->addDebug('GET '.$this->conf->url);
                
                //Settings of curl
                curl_setopt($curl, CURLOPT_URL, $this->conf->url);
                curl_setopt($curl, CURLOPT_PORT, $this->conf->port);
                curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
                
                // use POST method
                curl_setopt($curl, CURLOPT_POST, true);
                
                // send JSON data
                curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($options));
                curl_setopt(
                    $curl,
                    CURLOPT_HTTPHEADER, 
                    array('Content-Type: application/json')
                );
                $out = curl_exec($curl);

                
                if (curl_errno($curl)) {
                    $this->isavailable = false;
                    $this->logger->addError('Http error: '.curl_error($curl));
                    throw new SourceException(
                        'Server error '.curl_error($curl), 
                        2
                    );   
                } 

                $httpcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
                if ($httpcode >= 300) {
                    $this->isavailable = false;
                    $this->logger->addError('Http finished with code: '.$httpcode);
                    throw new SourceException('Server error '.$httpcode, 2);   
                }
                
                $this->logger->addDebug(
                    'Http finished with code: '.$httpcode
                );                

            } else {
                $this->isavailable = false;
                $this->logger->addError('Curl initialize error');
                throw new SourceException('Can\'t initialize CURL', 1);
            }
                    
        }
        return $out;
    }

    /**
     * Check if resource available
     * 
     * @return boolean [description]
     */
    public function isAvailable()
    {
        return $this->isavailable;
    }   
}