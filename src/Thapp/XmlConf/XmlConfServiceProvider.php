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

use Illuminate\Support\ServiceProvider;

/**
 * Class: XmlConfServiceProvider
 *
 * @uses ServiceProvider
 * @package Thapp\XmlConf
 * @version
 * @author Thomas Appel <mail@thomas-appel.com>
 * @license MIT
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

        // register package
        $this->package('thapp/xmlconf');

        $this->regsiterCommands();

        $this->regsiterConfigDrivers();
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
        return realpath(sprintf("%s/%s/config.xml", storage_path(), $reader));
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
        return sprintf('%s/%s/%s/Schema/%s.xsd', dirname(app_path()), $base, ucfirst(camel_case($reader)), $reader);
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
        return sprintf('%s\%sConfigReader', $namespace, ucfirst(camel_case($reader)));
    }

    /**
     * checkBaseDir
     *
     * @param string $reader
     * @param array $base
     * @throws \RuntimeException
     * @access private
     * @return void
     */
    private function checkBaseDir($reader, array $base)
    {
        if (!isset($base[$reader]) || !is_dir(dirname(app_path()) . '/' . $base[$reader])) {
            throw new \RuntimeException('Either a basedir is not set or basedir is not a directory');
        }
    }

    /**
     * regsiterCommands
     *
     * @access protected
     * @return void
     */
    protected function regsiterCommands()
    {

        $this->app['command.xmlconf.warmup'] = $this->app->share(

            function ($app)
            {
                return new Console\XmlConfWarmupCommand($app, $app['config']->get('xmlconf::namespaces'));
            }

        );

        $this->commands(
            'command.xmlconf.warmup'
        );
    }

    /**
     * regsiterConfigDrivers
     *
     * @access protected
     * @return void
     */
    protected function regsiterConfigDrivers()
    {
        // register xml config dirvers
        $me          = $this;
        $base        = $this->app['config']->get('xmlconf::basedir', array());
        $cacheDriver = $this->app['config']->get('cache.driver', 'file');

        foreach ($this->app['config']->get('xmlconf::namespaces', array()) as $reader => $namespace) {

            $this->checkBaseDir($reader, $base);

            $this->app['xmlconf.' . $reader] = $this->app->share(function ($app) use ($me, $base, $reader, $namespace, $cacheDriver)
            {
                $class     = $me->getReaderClass($reader, $namespace);
                $cache     = new Cache\Cache($app['cache']->driver($cacheDriver), $reader);
                $xmlreader = new $class(

                    $cache,
                    $me->getSimpleXmlClass($reader, $namespace),
                    $me->getConfPath($reader),
                    $me->getSchemaPath($reader, $base[$reader])

                );

                return $xmlreader;
            });
        }
    }
}
