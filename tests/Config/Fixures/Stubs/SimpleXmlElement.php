<?php

/**
 * This File is part of the vendor\symphony\tests\Config\Fixures\Stubs package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Thapp\Tests\XmlConf\Fixures\Stubs;

use Thapp\XmlConf\SimpleXmlConfigInterface;
use \SimpleXmlElement as SimpleXml;

/**
 * Class: SimpleXmlElement
 *
 * @implements SimpleXmlConfig
 *
 * @package
 * @version
 * @author Thomas Appel <mail@thomas-appel.com>
 * @license MIT
 */
class SimpleXmlElement extends SimpleXml implements SimpleXmlConfigInterface
{
    public function parse()
    {
        return array(
            'foo' => 'bar'
        );
    }
}
