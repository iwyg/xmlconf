<?php

/**
 * This File is part of the Thapp\XmlConf\Examples\Sections package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Thapp\XmlConf\Examples\Sections;

use Thapp\XmlConf\ConfigReaderInterface;

/**
 * Class: Repository
 *
 *
 * @package
 * @version
 * @author Thomas Appel <mail@thomas-appel.com>
 * @license MIT
 */
class Repository
{

    /**
     * reader
     *
     * @var mixed
     */
    protected $reader;

    /**
     * sections
     *
     * @var mixed
     */
    protected $sections = array();


    /**
     * __construct
     *
     * @access public
     * @return mixed
     */
    public function __construct(ConfigReaderInterface $reader)
    {
        $this->reader = $reader;
        $this->load();
    }

    /**
     * get
     *
     * @param mixed $param
     * @access public
     * @return mixed
     */
    public function get($id = null)
    {
        if (!is_null($id)) {
            if (isset($this->sections[$id])) {
                return $this->sections[$id];
            }
            return;

        }
        return $this->sections;
    }

    /**
     * load
     *
     * @access protected
     * @return void
     */
    protected function load()
    {
        $this->sections = $this->reader->load(array());
    }
}
