<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace PunchoutCatalog\Zed\PunchoutCatalog\Business\Mapping;

use Generated\Shared\Transfer\PunchoutCatalogMappingObjectFieldTransfer;
use Generated\Shared\Transfer\PunchoutCatalogMappingObjectTransfer;

abstract class AbstractCoder
{
    /**
     * @param \Generated\Shared\Transfer\PunchoutCatalogMappingObjectTransfer $object
     * @param \Generated\Shared\Transfer\PunchoutCatalogMappingObjectFieldTransfer $field
     * @param $source
     *
     * @throws InvalidArgumentException
     *
     * @return array|bool|mixed
     */
    protected function fetchSourceValueByField(
        PunchoutCatalogMappingObjectTransfer $object,
        PunchoutCatalogMappingObjectFieldTransfer $field,
        $source
    ) {
        $result = $this->fetchSourceValueByPath($field->getPath(), $source, true);

        if ($field->getIsRequired() && ($result === null || $result === '')) {
            throw new InvalidArgumentException(
                sprintf('Empty %s required field %s', $object->getName(), $field->getName())
            );
        }

        if ($field->getTransformations()) {
            $result = $this->mapTransformations($field, $result);
        }

        if (!$field->getIsMultiple() && is_array($result)) {
            reset($result);
            $result = current($result);
        }

        return $result;
    }

    /**
     * @param array $path
     * @param $source
     * @param bool $cast
     *
     * @return array|bool
     */
    protected function fetchSourceValueByPath(array $path, $source, bool $cast = false)
    {
        $result = $fetched = [];
        foreach ($path as $_path) {
            $snippet = $this->getSnippetFromPath($_path);
            if ($snippet) {
                $_fetched = [];
                foreach ($snippet->getPath() as $snippetPath) {
                    $_lines = $this->get($source, $snippetPath);
                    if ($_lines && is_array($_lines)) {
                        foreach ($_lines as $_line) {
                            $_val = $this->map($snippet, $_line);
                            if ($_val !== null) {
                                $_fetched[] = ['value' => $_val, 'is_snippet' => true];
                            }
                        }
                    }
                }
            } else {
                $_fetched = $this->get($source, $_path);
            }

            if ($_fetched && is_array($_fetched)) {
                $fetched = array_merge($fetched, $_fetched);
            } elseif ($_fetched) {
                $fetched[] = $_fetched;
            }
        }

        if (is_array($fetched)) {
            foreach ($fetched as $_fetched) {
                if (!empty($_fetched['is_snippet'])) {
                    $val = $_fetched['value'];
                } else {
                    $val = $cast ? $this->cast($_fetched) : $_fetched;
                }

                if ($val !== null && $val !== false) {
                    $result[] = $val;
                }
            }
        }

        return $result ?? null;
    }

    /**
     * @param \SimpleXMLElement|string $val
     *
     * @return string
     */
    public function cast($val): string
    {
        return (string)$val;
    }

    /**
     * @param \Generated\Shared\Transfer\PunchoutCatalogMappingObjectTransfer $object
     * @param array|\SimpleXMLElement $source
     *
     * @return array
     */
    protected function map(PunchoutCatalogMappingObjectTransfer $object, $source): array
    {
        $document = [];

        /** @var \Generated\Shared\Transfer\PunchoutCatalogMappingObjectFieldTransfer $field */
        foreach ($object->getFields() as $field) {
            $result = $this->fetchSourceValueByField($object, $field, $source);
            $this->populateDocument($document, $field->getName(), $result);
        }

        return $document;
    }

    /**
     * @param array $document
     * @param $fieldName
     * @param null $result
     *
     * @return array
     */
    protected function populateDocument(array &$document, $fieldName, $result = null): array
    {
        if (strpos($fieldName, '/') === false) {
            $document[$fieldName] = $result;
        } else {
            $keys = explode('/', $fieldName);
            $keys = array_map('trim', $keys);
            $keys = array_filter($keys);
            $keysCnt = count($keys);

            $i = 1;
            $lastObj = &$document;
            foreach ($keys as $idx => $key) {
                if ($i != $keysCnt && !isset($lastObj[$key])) {
                    $lastObj[$key] = [];
                    $lastObj = &$lastObj[$key];
                } elseif ($i != $keysCnt) {
                    $lastObj = &$lastObj[$key];
                } else {
                    $lastObj[$key] = $result;
                }
                $i++;
            }
        }
        return $document;
    }

    /**
     * @param \Generated\Shared\Transfer\PunchoutCatalogMappingObjectFieldTransfer $field
     * @param $data
     * @param bool $normalize
     *
     * @return mixed
     */
    protected function fetchDataValueByField(PunchoutCatalogMappingObjectFieldTransfer $field, $data, bool $normalize = true)
    {
        //Hanle single field
        if (strpos($field->getName(), ',') === false) {
            $val = $this->fetchDataValueByFieldPath($field->getName(), $data);
        } else {
            //Handle multiple fields entered via comma
            $val = [];
            foreach ($this->getMultipleFields($field->getName()) as $_field) {
                $val[] = $this->fetchDataValueByFieldPath($_field, $data);
            }
        }

        if ($normalize) {
            $val = $this->normalizeValue($val);
        }

        if ($field->getTransformations()) {
            $val = $this->mapTransformations($field, $val);
        }

        if (is_array($val) && !$field->getIsMultiple()) {
            $val = current($val);
            return [$val];
        }

        if (!is_array($val)) {
            return [$val];
        }

        return $val;
    }

    /**
     * @todo: fix custom_attributes and options
     *
     * @param string $field
     * @param $data
     *
     * @return mixed|string|null
     */
    protected function fetchDataValueByFieldPath($key, $data)
    {
        if (strpos($key, '/') !== false) {
            $keys = explode('/', $key);

            foreach ($keys as $key) {
                if (is_array($data) && isset($data[$key])) {
                    $data = $data[$key];
                } elseif (is_array($data)) {
                    $data = null;
                    break;
                }
            }

            return !is_array($data) ? $data : null;
        } elseif (is_array($data) && isset($data[$key])) {
            return $data[$key];
        } else {
            return null;
        }
    }

    /**
     * @param $fieldName
     *
     * @return array
     */
    protected function getMultipleFields($fieldName): array
    {
        return array_filter(array_map('trim', explode(',', (string)$fieldName)));
    }

    /**
     * @param $val
     *
     * @return array|mixed|null
     */
    protected function normalizeValue($val)
    {
        if (is_array($val)) {
            //$val = array_map('trim', $val);
            $val = array_filter($val);
            if (count($val) == 1) {
                $val = current($val);
            }
        }

        return $val !== null ? $val : null;
    }

    /**
     * @param string $path
     * @param array $data
     *
     * @return mixed|string
     */
    protected function processPath(string $path, array $data)
    {
        foreach ($data as $key => $val) {
            $searchKey = "%{$key}%";
            if ((!is_string($val) && !is_int($val) && !is_float($val))
                || strpos($path, $searchKey) === false
            ) {
                continue;
            }

            $path = str_replace($searchKey, $val, $path);
        }
        return $path;
    }

    /**
     * @param array $value
     *
     * @return string
     */
    protected function wrapArrayValue(array $value): string
    {
        $result = [];

        foreach ($value as $key => $_val) {
            if (is_array($_val)) {
                $_val = $this->wrapArrayValue($_val);
            }
            if (!is_int($key)) {
                $_val = $key . ": " . $_val;
            }
            $result[] = $_val;
        }

        return implode("\n", $result);
    }
}
