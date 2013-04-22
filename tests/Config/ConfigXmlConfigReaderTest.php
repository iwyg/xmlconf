<?php

/**
 * This File is part of the vendor\symphony\tests\Config package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Thapp\Tests\XmlConf;

use Mockery as m;
use Thapp\XmlConf\Cache\Cache;
use Illuminate\Cache\StoreInterface;
use Thapp\Tests\XmlConf\Fixures\Stubs\XmlConfigReaderStub as XmlConfigReader;

/**
 * Class: ConfigXmlReaderTest
 *
 * @uses PHPUnit_Framework_TestCase
 *
 * @package
 * @version
 * @author Thomas Appel <mail@thomas-appel.com>
 * @license MIT
 */
class ConfigXmlConfigReaderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * close mockery
     */
    protected function tearDown()
    {
        m::close();
    }

    /**
     * @test
     */
    public function testLoadConfigShouldSucceed()
    {
        $reader = $this->getReader($this->makeCacheMock(function (&$cache = null) {
            return true;
        }), $this->getValidXml());

        $this->assertEquals(array('foo' => 'bar'), $reader->load());
    }

    /**
     * @test
     * @expectedException Thapp\XmlConf\Exception\InvalidConfigurationSchema
     */
    public function testSchemaValidateShouldFail()
    {
        $reader = $this->getReader($this->makeCacheMock(function (&$cache = null) {
            return true;
        }), $this->getInvalidXml());

        $reader->load();
    }

    /**
     * @test
     */
    public function testReaderShouldReadFromCache()
    {
        $reader = $this->getReader($this->makeCacheMock(function (&$cache = null) {
            return false;
        }), $this->getValidXml());

        $this->assertEquals(array('cached' => true), $reader->load());
    }

    /**
     * @test
     */
    public function testLoadDefault()
    {
        $reader = $this->getReader($this->makeCacheMock(function (&$cache = null) {
            //$cache->shouldReceive('get')->andReturn(array('cached' => true));
            return true;
        }), 'file.xml');

        $this->assertEquals(array('foo' => 'bar'), $reader->load(array('foo' => 'bar')));
    }

    /**
     * makeCacheMock
     *
     * @param Closure $construct
     * @access protected
     * @return mixed
     */
    protected function makeCacheMock(\Closure $returnResults)
    {
        $cache = m::mock('Thapp\XmlConf\Cache\Cache');
        $cache->shouldReceive('isNew')->andReturn($returnResults($cache));
        $cache->shouldReceive('write');
        $cache->shouldReceive('get')->andReturn(array('cached' => true));
        return $cache;
    }

    /**
     * getReader
     *
     * @param mixed Symphony\Config\Cache\Cache
     * @access protected
     * @return XmlConfigReader
     */
    protected function getReader($cache, $file)
    {
        $reader = new XmlConfigReader($cache, $file);
        $reader->setSimpleXmlClass('Thapp\Tests\XmlConf\Fixures\Stubs\SimpleXmlElement');
        $reader->setSchema($this->getSchema());
        return $reader;
    }

    /**
     * getValidXml
     *
     * @access protected
     * @return mixed
     */
    protected function getValidXml()
    {
        return dirname(__FILE__) . '/Fixures/Xml/valid.xml';
    }

    /**
     * getInvalidXml
     *
     * @access protected
     * @return string
     */
    protected function getInvalidXml()
    {
        return dirname(__FILE__) . '/Fixures/Xml/invalid.xml';
    }

    /**
     * getSchema
     *
     * @access protected
     * @return string
     */
    protected function getSchema()
    {
        return dirname(__FILE__) . '/Fixures/Schema/schema.xsd';
    }

}
