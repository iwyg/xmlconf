<?php

/**
 * This File is part of the Thapp\XmlConf\Console package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Thapp\XmlConf\Console;

use Illuminate\Console\Command;
use Illuminate\Container\Container;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

/**
 * Class: XmlConfWarmupCommand
 *
 * @uses Command
 *
 * @package
 * @version
 * @author Thomas Appel <mail@thomas-appel.com>
 * @license MIT
 */
class XmlConfWarmupCommand extends Command
{
    /**
     * config
     *
     * @var array
     */
    protected $config;

    /**
     * container
     *
     * @var Illuminate\Container\Container
     */
    protected $container;

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'xmlconf:warmup';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Warmup configuration cache.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(Container $container, $conf)
    {
        $this->config    = $conf;
        $this->container = $container;

        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function fire()
    {
        foreach ($this->config as $reader => $namesapce) {

            try {

                $reader = $this->container['xmlconf.' . $reader];
                $reader->load();

            } catch (\Exception $e) {

                $this->error($e->getMessage());

            }
        }

        $this->info('cache warmup ran successfully');
    }
}
