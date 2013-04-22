<?php

/**
 * This File is part of the app\sections package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Thapp\XmlConf\Examples\Sections;

use Thapp\XmlConf\SimpleXmlConfigInterface;

/**
 * Class: SectionsSimpleXml
 *
 * @implements SimpleXmlConfig
 * @uses \SimpleXMLElement
 *
 * @package
 * @version
 * @author Thomas Appel <mail@thomas-appel.com>
 * @license MIT
 */
class SectionsSimpleXml extends \SimpleXMLElement implements SimpleXmlConfigInterface
{
    /**
     * sectionattributes
     *
     * @var array
     */
    protected static $sectionattributes = array('id', 'name', 'handle', 'navgroup');

    /**
     * fieldattributes
     *
     * @var array
     */
    protected static $fieldattributes   = array('id', 'name', 'handle', 'type');

    /**
     * parse
     *
     * @access public
     * @return mixed
     */
    public function parse()
    {
        return $this->getSections();
    }

    /**
     * getSections
     *
     * @access public
     * @return array
     */
    public function getSections()
    {
        $sections = array();

        foreach ($this->section as $sectionObj) {

            $section = new Section;

            foreach (static::$sectionattributes as $attribute) {
                $section->{$attribute} = $this->getValue((string)$sectionObj->attributes()->{$attribute});
            }

            $fields = $sectionObj->fields;
            $section->setFields($this->getFields($fields));
            $sections[$section->id] = $section;

        }

        return $sections;
    }

    /**
     * getFields
     *
     * @param SectionXML $fields
     * @access protected
     * @return array
     */
    protected function getFields(&$sectionObj)
    {
        $fields = array();

        foreach ($sectionObj->field as $fieldObj) {

            $field = array();

            foreach (static::$fieldattributes as $attribute) {
                $field[$attribute] = $this->getValue((string)$fieldObj->attributes()->{$attribute});
            }

            $fields[] = $field;
        }

        return $fields;
    }

    /**
     * getValue
     *
     * @param mixed $value
     * @access protected
     * @return string|int|double
     */
    protected function getValue($value)
    {
        if (is_numeric($value)) {
            return false !== strpos('.', $value) ? (double)$value : (int)$value;
        }
        return $value;
    }
}
