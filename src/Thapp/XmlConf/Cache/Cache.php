<?php

/**
 * This File is part of the Thapp\XmlConf package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Thapp\XmlConf\Cache;
use Illuminate\Cache\Repository as CacheRepository;

/**
 * Class: Cache
 *
 * @package Thapp\XmlConf
 * @version
 * @author Thomas Appel <mail@thomas-appel.com>
 * @license MIT
 */
class Cache implements ConfigurationCacheInterface
{
    /**
     * the cache
     *
     * @var Illuminate\Cache\StoreInterface
     */
    protected $storage;

    /**
     * cache identifyer key
     *
     * @var string
     */
    protected $storagekey;

    /**
     * __construct
     *
     * @param Storage $storage cache instance
     * @param string $config filepath of the configuration file
     * @access public
     */
    public function __construct(CacheRepository $storage, $key = null)
    {
        $this->storage    = $storage;
        $this->storagekey = $key;
    }

    /**
     * {@inheritDoc}
     */
    public function setStorageKey($key)
    {
        $this->storagekey =  $key;
    }

    /**
     * {@inheritDoc}
     */
    public function isNew($file)
    {
        list($filemtime, $lastmodified) = $this->getCacheModificationDate($file);
        return $filemtime > $lastmodified;
    }

    /**
     * {@inheritDoc}
     */
    public function get()
    {
        return $this->storage->get($this->getStorageKey());
    }

    /**
     * {@inheritDoc}
     */
    public function write($data)
    {
        $this->storage->forget($this->getStorageKey() . '.lasmodified');
        $this->storage->forget($this->getStorageKey());

        $this->storage->forever($this->getStorageKey() . '.lasmodified', time());
        $this->storage->forever($this->getStorageKey(), $data);
    }

    /**
     * Returns the modification date of a given file and the the
     * date of the last cache write.
     *
     * @access protected
     * @return array
     */
    protected function getCacheModificationDate($file)
    {
        $storageKey   = $this->getStorageKey() . '.lasmodified';
        $filemtime    = filemtime($file);
        $lastmodified = $this->storage->has($storageKey) ? $this->storage->get($storageKey) : $filemtime - 1;

        return array($filemtime, $lastmodified);
    }

    /**
     * Get the storage key identifyer
     *
     * @access protected
     * @return string
     */
    protected function getStorageKey()
    {
        return $this->storagekey;
    }
}
