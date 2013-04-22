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


/**
 * Class: SimpleXmlConfig
 *
 * @package Thapp\XmlConf
 * @version
 * @author Thomas Appel <mail@thomas-appel.com>
 * @license MIT
 */
interface SimpleXmlConfigInterface
{
    /**
     * parse should return the
     * xml configuration as an array
     *
     * @access public
     * @return array
     */
    public function parse();
}
