<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace PunchoutCatalog\Zed\PunchoutCatalog\Business\Mapping\Xml;

use DOMText;
use Exception;
use SimpleXMLElement;

/**
 * @see: it uses dom_import_simplexml() and DOMText to add xml node text to existing node (with attributes)
 */
class XmlUtil
{
    /**
     * @var array
     */
    protected $cache = [];

    /**
     * @param $source
     * @param $path
     *
     * @return array|bool
     */
    public function get($source, $path)
    {
        if (!$this->validate($source)) {
            return false;
        }
        try {
            $result = $source->xpath($path);
        } catch (Exception $e) {
            return false;
        }

        if (empty($result)) {
            return false;
        }

        return $result;
    }

    /**
     * @param \SimpleXMLElement $source
     * @param string$path|string
     * @param $value
     *
     * @return \SimpleXMLElement
     */
    public function set(SimpleXMLElement $source, string $path, $value): SimpleXMLElement
    {
        if (!$this->validate($source)) {
            return false;
        }

        //@todo: hotfix
        if (strpos($path, '/cXML') === 0) {
            $path = substr($path, 5);
        } elseif (strpos($path, 'cXML') === 0) {
            $path = substr($path, 4);
        }

        return $this->_set($source, $path, $value);
    }

    /**
     * @param $source
     *
     * @return bool
     */
    protected function validate($source): bool
    {
        return ($source instanceof SimpleXMLElement);
    }

    /**
     * @param \SimpleXMLElement $source
     * @param string $path
     * @param string|mixed $value
     *
     * @return \SimpleXMLElement
     */
    protected function _set(SimpleXMLElement $source, string $path, $value): SimpleXMLElement
    {
        $path = $this->fixPath($path);

        if (is_array($value)) {
            if ($this->isIndexable($path)) {
                foreach ($value as $_value) {
                    $_path = $this->register($path);
                    $this->_set($source, $_path, $_value);
                }
            } else {
                $this->_set($source, $path, $this->wrapArrayValue($value));
            }
        } else {
            $path = trim($path, '/');
            $path = explode('/', $path);
            $currentPath = array_shift($path);

            /** @var \SimpleXMLElement $currentSource */
            //#1. Is attribute
            if (strpos($currentPath, '@') === 0) {
                $attributeValue = empty($path) ? $value : '';
                $this->setAttribute($source, $currentPath, $attributeValue);
                $currentSource = $source;
            } else {
                //#2. Is simple element
                $attrData = [];
                if (strpos($currentPath, '[@') !== false) {
                    $attrData = $this->_getTagAttrs($currentPath);
                }

                try {
                    $lookup = $source->xpath($currentPath);
                } catch (Exception $e) {
                    $lookup = [];
                }

                $nodeValue = empty($path) ? $value : null;

                $currentPath = $this->_replaceTagAttr($currentPath);
                $currentPath = $this->_replaceTagNum($currentPath);

                /** @var \SimpleXMLElement $currentSource */
                if (!count($lookup)) {
                    $currentSource = $source->addChild($currentPath);
                } else {
                    $currentSource = $lookup[0];
                }

                if ($nodeValue !== null) {
                    /** @var \DOMElement $subNode */
                    $subNode = @dom_import_simplexml($currentSource[0]);
                    if ($subNode) {
                        $nodeValue = new DOMText($this->prepareDomValue($nodeValue));
                        //$nodeValue = new \DOMText($nodeValue);
                        $subNode->appendChild($nodeValue);
                    }
                }

                if ($attrData) {
                    $this->setAttribute($currentSource, $attrData['name'], $attrData['value']);
                }
            }

            if (!empty($path)) {
                $path = implode('/', $path);
                $this->_set($currentSource, $path, $value);
            }
        }

        return $source;
    }

    /**
     * @todo: review it and test
     *
     * @param string $value
     *
     * @return string
     */
    protected function prepareDomValue(string $value): string
    {
        return $value;
        //return htmlentities(trim((string)$value));
    }

    /**
     * Replace [1],[2] with '' if it string ends with [x]
     *
     * @param string $value
     *
     * @return mixed
     */
    protected function _replaceTagNum(string $value)
    {
        $value = rtrim($value, '/');
        $value = trim($value);

        return preg_replace('/\[[0-9]+\]$/', '', $value);
    }

    /**
     * @param string $value
     *
     * @return int|false
     */
    protected function isIndexed(string $value)
    {
        return preg_match('/\[[0-9]+\]$/', $value);
    }

    /**
     * @param string $string
     *
     * @return mixed
     */
    protected function isAttributed(string $string)
    {
        return (strpos($string, '[@') !== false);
    }

    /**
     * @param string $string
     *
     * @return mixed
     */
    protected function _replaceTagAttr(string $string)
    {
        if (strpos($string, '[@') !== false) {
            $string = substr($string, 0, strpos($string, '[@'));
        }

        return $string;
    }

    /**
     * @param string $string
     *
     * @return array
     */
    protected function _getTagAttrs(string $string): array
    {
        $result = [];
        preg_match('/\[\@([a-zA-Z0-9]+)\=(\"|\')([a-zA-Z0-9_-]+)(\"|\')\]/', $string, $result);
        if (isset($result[1]) && $result[3]) {
            return ['name' => $result[1], 'value' => $result[3]];
        }

        return null;
    }

    /**
     * @param \SimpleXMLElement $node
     * @param string $attributeName
     * @param string|mixed $attributeValue
     *
     * @return mixed
     */
    protected function setAttribute(SimpleXMLElement $node, string $attributeName, $attributeValue)
    {
        if (!$this->validate($node)) {
            return $node;
        }

        if (is_array($attributeValue)) {
            $attributeValue = $this->wrapArrayValue($attributeValue);
        }

        $attributeValue = $this->prepareDomValue($attributeValue);

        $attributeName = trim($attributeName, '@');
        $attributeName = $this->prepareDomValue($attributeName);
        $updateAttributeName = $attributeName;

        $ns = null;
        if (strpos($attributeName, ':') !== false) {
            [$ns, $updateAttributeName] = explode(':', $attributeName);
        }

        $attributes = $node->attributes($ns);
        if ($attributeValue && isset($attributes->{$updateAttributeName})) {
            $attributes->{$updateAttributeName} = $attributeValue;
        } elseif ($attributeValue) {
            $node->addAttribute($attributeName, $attributeValue, $ns);
        }

        return $node;
    }

    /**
     * @param string $path
     * @param string|null $parentPath
     *
     * @return string
     */
    public function register(string $path, ?string $parentPath = null): string
    {
        if ($parentPath !== null) {
            $path = ltrim($parentPath, '/') . '/' . rtrim($path, '/');
        }

        $_path = $this->_replaceTagAttr($path);
        $_path = $this->_replaceTagNum($_path);

        if (!isset($this->cache['registry'][$path])) {
            $this->cache['registry'][$path] = 0;
        }

        if (!isset($this->cache['registry'][$_path])) {
            $this->cache['registry'][$_path] = 0;
        }

        if ($this->isIndexed($path)) {
            $this->cache['registry'][$path] = 1;
        } else {
            if ($this->isIndexable($path)) {
                $this->cache['registry'][$_path]++;

                if ($this->isAttributed($path)) {
                    $this->cache['registry'][$path]++;

                    $idx = $this->cache['registry'][$path];
                    $path = sprintf('%s[%d]', $path, $idx);
                } else {
                    $idx = $this->cache['registry'][$_path];
                    $path = sprintf('%s[%d]', $_path, $idx);
                }
            }
        }

        return $path;
    }

    /**
     * @param string $path
     *
     * @return bool
     */
    protected function isIndexable(string $path): bool
    {
        if ($this->isIndexed($path)) {
            return false;
        }

        $path = ltrim(strrchr($path, "/"), '/');

        return (
            $path
            && ($path != '.')
            && (strpos($path, '@') !== 0)
            && (strpos($path, '()') !== (strlen($path) - 2))
        );
    }

    /**
     * @param string $path
     *
     * @return string
     */
    protected function fixPath(string $path): string
    {
        $path = str_replace('/text()', '', $path);
        $path = str_replace('text()', '.', $path);
        $path = str_replace('/.', '', $path);
        return $path;
    }
}
