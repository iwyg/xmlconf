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

/**
 * Class: Section
 *
 *
 * @package
 * @version
 * @author Thomas Appel <mail@thomas-appel.com>
 * @license MIT
 */
class Section
{
    /**
     * fields
     *
     * @var array
     */
    protected $fields;

    /**
     * attributes
     *
     * @var array
     */
    protected $attributes = array();

    /**
     * setFields
     *
     * @param array $fields
     * @access public
     * @return void
     */
    public function setFields(array $fields)
    {
        $this->fields = $fields;
    }

    /**
     * addField
     *
     * @param Field $field
     * @access public
     * @return void
     */
    public function addField(Field $field)
    {
        $this->fields[] = $field;
    }

    /**
     * __set
     *
     * @param mixed $attribute
     * @param mixed $value
     * @access public
     * @return void
     */
    public function __set($attribute, $value)
    {
        return $this->attributes[$attribute] = $value;
    }

    /**
     * __get
     *
     * @param mixed $attribute
     * @access public
     * @return mixed
     */
    public function __get($attribute)
    {
        if (array_key_exists($attribute, $this->attributes)) {
            return $this->attributes[$attribute];
        }
    }
}
