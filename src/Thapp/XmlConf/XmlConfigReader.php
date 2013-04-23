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
     * @param ConfigCache $cache
     * @param string $simpleXmlClass
     * @param string $xml
     * @param string $xsd
     * @access public
     * @final
     * @return mixed
     */
    final public function __construct(ConfigCache $cache, $simpleXmlClass, $xml, $xsd)
    {
        $this->cache    = $cache;
        $this->xmlfile  = $xml;

        $this->setSchema($xsd);
        $this->setSimpleXmlClass($simpleXmlClass);
    }


    /**
     * validateSchema
     *
     * @param mixed $schema
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
     * load
     *
     * @param mixed $default
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
     * parse
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
     * getSimpleXmlObject
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
     * setSimpleXmlClass
     *
     * @param mixed $simplexmlclass
     * @access public
     * @throws Thapp
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
     * setSchema
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
