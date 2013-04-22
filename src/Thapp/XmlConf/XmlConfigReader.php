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
 * @package Symphony
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
     * @param XmlConfigCache $cache
     * @access public
     * @final
     * @return mixed
     */
    final public function __construct(ConfigCache $cache, $file)
    {
        $this->cache    = $cache;
        $this->xmlfile  = $file;
    }

    /**
     * setSchema
     *
     * @param mixed $xsd
     * @access public
     * @return mixed
     */
    public function setSchema($xsd)
    {
        $this->schema = $xsd;
    }

    /**
     * validateSchema
     *
     * @param mixed $schema
     * @access public
     * @return boolean
     */
    public function validateSchema(\DOMDocument $dom)
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
     * @param mixed $xml
     * @access public
     * @return array
     */
    public function load()
    {
        if (!$this->cache->isNew($this->xmlfile)) {
            return $this->cache->get();
        }

        $data = $this->parse();
        $this->cache->write($data);

        return $data;
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
     * @throws Symphony\Config\Exception\InvalidConfigurationSchema
     * @return mixed
     */
    protected function getSimpleXmlObject()
    {
        $dom = new \DOMDocument;
        $dom->load($this->xmlfile);

        if (!$this->validateSchema($dom)) {
            throw new InvalidConfigurationSchema('Schema is not valid');
        }

        return simplexml_import_dom($dom, $this->simplexmlclass);
    }

    /**
     * setSimpleXmlClass
     *
     * @param mixed $simplexmlclass
     * @access public
     * @return mixed
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
}
