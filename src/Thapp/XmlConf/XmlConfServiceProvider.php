<?php

/**
 * This File is part of the vendor\thapp\xmlconf\src\Thapp\XmlConf package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Thapp\XmlConf;

use Illuminate\Support\ServiceProvider;

/**
 * @class XmlConfServiceProvider
 */

class XmlConfServiceProvider extends ServiceProvider
{
    /**
     * register
     *
     * @access public
     * @return void
     */
    public function register()
    {
        $me = $this;

        $this->package('thapp/xmlconf');

        $base = $this->app['config']->get('xmlconf::basedir');

        foreach ($this->app['config']->get('xmlconf::reader', array()) as $reader => $namespace) {

            $this->app['xmlconf.' . $reader] = $this->app->share(function ($app) use ($me, $base, $reader, $namespace)
            {
                $class     = $this->getReaderClass($reader, $namespace);
                $cache     = new Cache\Cache($app['cache']->driver(), $reader);
                $xmlreader = new $class($cache, $me->getConfPath($reader));

                $xmlreader->setSimpleXmlClass($me->getSimpleXmlClass($reader, $namespace));
                $xmlreader->setSchema($this->getSchemaPath($reader, $base));

                return $xmlreader;
            });
        }
    }

    /**
     * getConfPath
     *
     * @param string $reader
     * @access public
     * @return string
     */
    public function getConfPath($reader)
    {
        return realpath(sprintf("%s/storage/%s/config.xml", app_path(), $reader));
    }

    /**
     * getSimpleXmlClass
     *
     * @param string $reader
     * @access public
     * @return string
     */
    public function getSimpleXmlClass($reader, $namespace)
    {
        return $namespace . '\\' . ucfirst($reader) . 'SimpleXml';
    }

    /**
     * getSchamePath
     *
     * @param string $reader
     * @access public
     * @return string
     */
    public function getSchemaPath($reader, $base)
    {
        return sprintf('%s/%s/%s/Schema/%s.xsd', dirname(app_path()), $base, ucfirst($reader), $reader);
    }

    /**
     * getReaderClass
     *
     * @param string $reader
     * @param string $namespace
     * @access public
     * @return string
     */
    public function getReaderClass($reader, $namespace)
    {
        return sprintf('%s\%sConfigReader', $namespace, ucfirst($reader));
    }
}
