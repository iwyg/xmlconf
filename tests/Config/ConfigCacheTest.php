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
use org\bovigo\vfs\vfsStream;
use Illuminate\Cache\StoreInterface;
use Thapp\Tests\XmlConf\Fixures\Stubs\ConfigCacheStub as Cache;

/**
 * Class: ConfigCacheTest
 *
 * @uses \PHPUnit_Framework_TestCase
 *
 * @package Symphony
 * @version
 * @author Thomas Appel <mail@thomas-appel.com>
 * @license MIT
 */
class ConfigCacheTest extends \PHPUnit_Framework_TestCase
{
    /**
     * configPath
     *
     * @var string
     */
    protected $configPath;

    /**
     * root
     *
     * @var mixed
     */
    protected $root;

    /**
     * setUp
     */
    protected function setUp()
    {
        $this->root = vfsStream::setup('config');
        $this->configPath = vfsStream::url('config');
    }

    /**
     * tearDown
     */
    protected function tearDown()
    {
        m::close();
    }

    /**
     * @test
     */
    public function testCacheIsNewShouldReturnTrue()
    {
        $file = $this->getConfigFile();

        $store = $this->getStoreMock();
        $store->shouldReceive('has')->andReturn(false);
        $cache = $this->getCache($store, $file);
        $this->assertTrue($cache->isNew($file));
    }

    /**
     * @test
     */
    public function testCacheIsNewShouldReturnFalse()
    {

        $file = $this->getConfigFile();

        $store = $this->getStoreMock();

        $store->shouldReceive('get')->with('xml_confing.lasmodified')->andReturn(filemtime($file));
        $store->shouldReceive('has')->andReturn(true);

        $cache = $this->getCache($store, $file);

        $this->assertFalse($cache->isNew($file));
    }

    /**
     * @test
     */
    public function testCacheIsNewShouldReturnFalseAfterWrite()
    {
        $file = $this->getConfigFile();

        $store = $this->getStoreMock();

        $filemtime = filemtime($file);

        $time = function () {
            static $time;

            if (!is_null($time)) {
                return $time;
            }

            $time = time();
            return $time;
        };

        $store->shouldReceive('forget');
        $store->shouldReceive('forget');
        $store->shouldReceive('forever')->with('xml_confing.lasmodified', $time());
        $store->shouldReceive('forever')->with('xml_confing', array('foo' => 'bar'));
        $store->shouldReceive('get')->with('xml_confing.lasmodified')->andReturn($time());
        $store->shouldReceive('has')->andReturn(true);

        $cache = $this->getCache($store, $file);
        $cache->write(array('foo' => 'bar'));

        $this->assertFalse($cache->isNew($file));
    }

    /**
     * @test
     */
    public function testCacheIsNewShouldReturnTrueAfterModified()
    {
        $file = $this->getConfigFile();
        $store = $this->getStoreMock();
        $filemtime = filemtime($file);

        $store->shouldReceive('has')->with('xml_confing.lasmodified')->andReturn(true);
        $store->shouldReceive('get')->with('xml_confing.lasmodified')->andReturn($filemtime);

        $cache = $this->getCache($store, $file);

        $fileObj = $this->root->getChild('config/config.xml');
        $fileObj->lastModified(time());

        // simmulate a file modification:
        $file = $this->getConfigFile();

        $this->root->getChild('config/config.xml')->lastModified(time() + 1);
        $this->assertTrue($cache->isNew($file));
    }


    /**
     * get the test object
     *
     * @param Illuminate\Cache\StoreInterfacemixed $store
     * @param string $file
     * @access protected
     * @return Thapp\Tests\XmlConf\Fixures\Stubs\ConfigCacheStub
     */
    protected function getCache($store, $file = null)
    {
        if (is_null($file)) {
            $file = $this->getConfigFile();
        }

        $cache = new Cache($store, $file);
        $cache->setStorageKey('xml_confing');
        return $cache;
    }

    /**
     * create the test mock for storeinterface
     *
     * @access protected
     * @return Illuminate\Cache\StoreInterface
     */
    protected function getStoreMock()
    {
        $mock = m::mock('Illuminate\Cache\Repository');
        return $mock;
    }

    /**
     * create the testfile and set filemtime
     *
     * @param integer $time filemtime
     * @access protected
     * @return string
     */
    protected function getConfigFile($time = null)
    {
        $time = !is_null($time) ? $time : time();
        $file = $this->configPath . '/config.xml';

        if (file_exists($file)) {
            unlink ($file);
        }
        touch($file, 0777);
        $this->root->getChild(basename($this->configPath) . '/config.xml')->lastModified($time);
        return $file;
    }
}
