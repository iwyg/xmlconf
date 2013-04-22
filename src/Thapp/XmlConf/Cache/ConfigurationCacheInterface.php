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

/**
 * Interface: ConfigurationCacheInterface
 *
 *
 * @package Thapp\XmlConf
 * @version
 * @author Thomas Appel <mail@thomas-appel.com>
 * @license MIT
 */
interface ConfigurationCacheInterface
{
    /**
     * determine if the configuration file was modified
     * since the last cacheing
     *
     * @access public
     * @return boolean
     */
    public function isNew($file);

    /**
     * get contents from cache
     *
     * @access public
     * @return mixed
     */
    public function get();

    /**
     * write contents to cache
     *
     * @param mixed $data
     * @access public
     * @return void
     */
    public function write($data);

    /**
     * set the cache identifyer key
     *
     * @param mixed $key
     * @access public
     * @return mixed
     */
    public function setStorageKey($key);

}

