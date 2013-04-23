<?php

/**
 * This File is part of the Thapp\XmlConf package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Thapp\XmlConf;

use Thapp\XmlConf\Cache\Cache as ConfigCache;
use Thapp\XmlConf\Exception\InvalidConfigurationSchema;

/**
 * Class: XmlConfigReader
 *
 * @package Thapp\XmlConf
 * @version
 * @author Thomas Appel <mail@thomas-appel.com>
 * @license MIT
 */
abstract class XmlConfigReader implements ConfigReaderInterface
{
    /**
     * cache
     *
     * @var XmlConfigCache
     */
    protected $cache;

    /**
     * schema
     *
     * @var string
     */
    protected $schema;

    /**
     * xmlfile
     *
     * @var mixed
     */
    protected $xmlfile;

    /**
     * simplexmlclass
     *
     * @var string
     */
    protected $simplexmlclass;

    /**
     * __construct
     *
     * @param ConfigCache $cache      Instance of `Thapp\XmlConf\Cache\Cache`
     * @param string $simpleXmlClass  fully qualified class name
     * @param string $xml             path to the xml config file
     * @param string $xsd             path to the xsd schema file
     * @access public
     * @final
     */
    final public function __construct(ConfigCache $cache, $simpleXmlClass, $xml, $xsd)
    {
        $this->cache    = $cache;
        $this->xmlfile  = $xml;

        $this->setSchema($xsd);
        $this->setSimpleXmlClass($simpleXmlClass);
    }


    /**
     * validate the xsd schema
     *
     * @param string $schema path to the xsd schema file
     * @access protected
     * @throws Thapp\XmlConf\Exception\InvalidConfigurationSchema
     * @return boolean
     */
    protected function validateSchema(\DOMDocument $dom)
    {
        try {
            $valid = $dom->schemaValidate($this->schema);
            return $valid;
        } catch (\Exception $e) {}

        throw new InvalidConfigurationSchema('Schema is not valid');
    }

    /**
     * load the xml configuration.
     *
     * Checks if the file was altered since it was cached.
     * Returns the parsed or the cached result.
     *
     * @param mixed $default the default value to return if the given xml file
     * does not exist yet.
     * @access public
     * @return mixed
     */
    public function load($default = null)
    {
        if (file_exists($this->xmlfile)) {

            if (!$this->cache->isNew($this->xmlfile)) {
                return $this->cache->get();
            }

            $data = $this->parse();
            $this->cache->write($data);

            return $data;
        }

        return $default;
    }

    /**
     * calls the `parse` method on the SimpleXmlElement
     *
     * @access protected
     * @return mixed|array
     */
    protected function parse()
    {
        $xml = $this->getSimpleXmlObject();
        return $xml->parse();
    }

    /**
     * get the `SimpleXmlObject`
     *
     * First creates a new `DOMDocument` and loads the xml config.
     * If the xsd schema is valid, the dom is imported
     * into a `SimpleXmlElement`.
     *
     * @access protected
     * @return \SimpleXmlElement
     */
    protected function getSimpleXmlObject()
    {
        $dom = new \DOMDocument;
        $dom->load($this->xmlfile);

        if ($this->validateSchema($dom)) {
            return simplexml_import_dom($dom, $this->simplexmlclass);
        }
    }

    /**
     * set the SimpleXml class
     *
     * @param string $simplexmlclass fully qualified class name
     * @access public
     * @throws \InvalidArgumentException
     * @return void
     */
    public function setSimpleXmlClass($simplexmlclass)
    {
        $interfaces = class_implements($simplexmlclass);
        $interface  = sprintf('%s\%s', __NAMESPACE__, 'SimpleXmlConfigInterface');

        if (!in_array($interface, $interfaces)) {
            throw new \InvalidArgumentException(sprintf('SimpleXml class must implement %s', $interface));
        }
        $this->simplexmlclass = $simplexmlclass;
    }

    /**
     * set the xsd schema
     *
     * @param mixed $xsd
     * @access public
     * @return void
     */
    public function setSchema($xsd)
    {
        $this->schema = $xsd;
    }
}
