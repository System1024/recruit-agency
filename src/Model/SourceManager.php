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

namespace Che\Model;

use Netwerwen\Test\SourceException;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\EventDispatcher\Event;

/**
 * Source manager class
 *
 * @category Class
 * @package  Test
 * @author   Sergii Chernenko <panua01@gmail.com>
 * @license  http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link     http://nowere.com/
 */
class SourceManager
{

    protected $resources = [];
    protected $conf;
    protected $logger;
    protected $dispatcher;

    /**
     * Some initializing stuff
     *
     * @param Config $conf   Config
     * @param Logger $logger Logger
     */
    function __construct($conf, $logger)
    {
        $this->conf = $conf;
        $this->logger = $logger;
    }

    /**
     * Set event dispatcher interface
     * 
     * @param EventDispatcher $dispatcher event dispatcher
     *
     * @return void
     */
    public function setDispatcher(EventDispatcher $dispatcher)
    {
        $this->dispatcher = $dispatcher;
    }

    /**
     * Delete resource function
     * 
     * @param string $name name of resource
     * 
     * @return bool true if success, false on fail
     */
    public function deleteResource($name)
    {
        if (isset($this->resources[$name])) {
            unset($this->resources[$name]);
            return true;
        }
        return false;
    }
    
    /**
     * Create source/parcer pair
     * 
     * @param string $name name of resource in config
     * 
     * @return callable function on success or false on fail
     */
    public function createResource($name)
    {
        $params = $this->conf->getSource($name);

        $sourceClassName = $params->source->class;
        $sourceParams = $params->source->params;        
        $parserClassName = $params->parser->class;
        $parserParams = $params->parser->params;

        $group = $name;

        // check group name in config
        // name of group can be used for grouping few sources into one 
        // for avoiding dada dublicates, 
        // defining different protocols of one source, etc.
        


        if (isset($params->group)) {
            $group = $params->group;
        }

        // create parser class instance
        $parser = new $parserClassName($group, $this->conf, $this->logger);
        if (isset($parserParams)) {
            $parser->setApp($parserParams);
        }

        // create resource class instance
        $source = new $sourceClassName($group, $this->conf, $this->logger);
        if (isset($sourceParams)) {
            $source->setApp($sourceParams);
        }

        if (isset($this->dispatcher)) {
            // handle event Delete data from database
            $this->dispatcher->addListener(
                'ondelete', 
                function (Event $event) use ($source) {
                    $source->onDeleteData($event);
                }
            );

            // handle event Delete data in database
            $this->dispatcher->addListener(
                'onupdate',
                function (Event $event) use ($source) {
                    $source->onUpdateData($event);
                }
            );                
        }

        // define function for getting parced from resource
        $this->resources[$name] = function ($options=null) use ($source, $parser) {
            $out = false;

            try {
                $data = $source->getData($options);
                if ($data) {
                    $out = $parser->parse($data);                        
                }
                return $out;
            } catch (\SourceManagerException $e) {
                throw $e;
            }
            
        };

        return $this->resources[$name];
        
    }

    /**
     * Get resource function
     * 
     * @param string $name name of resource
     * 
     * @return callable resource function
     */
    public function getResource($name)
    {
        if (isset($this->resources[$name])) {
            return $this->resources[$name];
        }

        return $this->createResource($name);
    }

    /**
     * Load all recources defined in config
     * 
     * @return void
     */
    protected function loadAllResources()
    {
        $confSources = $this->conf->getSources();
        foreach ($confSources as $key => $value) {
            if (!isset($this->resources[$key])) {
                $this->getResource($key);
            }
        }
    }


    /**
     * Get data from resource
     * 
     * @param string/null $name    name of resource. If $name empty 
     * function get data from all available resources
     * @param mixed       $options some options for resource
     * 
     * @return array array of VacancyExt objects
     */
    public function getData($name = null, $options = null)
    {
        if ($name !== null) {
            try {
                $res = $this->getResource($name);
                if ($res) {
                    return $res($options);
                }
            } catch (\Exception $e) {
                // here we can disable the failed source
                // $this->deleteResource($name)
                throw $e;
            }
        } else {
            $this->loadAllResources();
            $out = array();
            foreach ($this->resources as $res) {
                try {
                    $result = $res($options);
                    if ($result !== false) {
                        $out = array_merge($out, $result);
                    }
                } catch (\Exception $e) {
                    // here we can disable the failed source
                    // $this->deleteResource($name)
                    throw $e;
                }
            }
            return $out;
        }
        return false;
    }
}