<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace PunchoutCatalog\Zed\PunchoutCatalog\Business\Mapping;

use Generated\Shared\Transfer\PunchoutCatalogMappingObjectFieldTransfer;
use Generated\Shared\Transfer\PunchoutCatalogMappingObjectTransfer;
use Generated\Shared\Transfer\PunchoutCatalogMappingTransfer;
use PunchoutCatalog\Zed\PunchoutCatalog\Business\Mapping\Coder\ITransform;
use PunchoutCatalog\Zed\PunchoutCatalog\Business\Mapping\Coder\Transform\AmountCommand;
use PunchoutCatalog\Zed\PunchoutCatalog\Business\Mapping\Coder\Transform\AmountFormattedCommand;
use PunchoutCatalog\Zed\PunchoutCatalog\Business\Mapping\Coder\Transform\AppendCommand;
use PunchoutCatalog\Zed\PunchoutCatalog\Business\Mapping\Coder\Transform\CutCommand;
use PunchoutCatalog\Zed\PunchoutCatalog\Business\Mapping\Coder\Transform\DateCommand;
use PunchoutCatalog\Zed\PunchoutCatalog\Business\Mapping\Coder\Transform\DefaultCommand;
use PunchoutCatalog\Zed\PunchoutCatalog\Business\Mapping\Coder\Transform\HtmlspecialCommand;
use PunchoutCatalog\Zed\PunchoutCatalog\Business\Mapping\Coder\Transform\JoinCommand;
use PunchoutCatalog\Zed\PunchoutCatalog\Business\Mapping\Coder\Transform\LowercaseCommand;
use PunchoutCatalog\Zed\PunchoutCatalog\Business\Mapping\Coder\Transform\MapCommand;
use PunchoutCatalog\Zed\PunchoutCatalog\Business\Mapping\Coder\Transform\NotCommand;
use PunchoutCatalog\Zed\PunchoutCatalog\Business\Mapping\Coder\Transform\PrependCommand;
use PunchoutCatalog\Zed\PunchoutCatalog\Business\Mapping\Coder\Transform\RoundCommand;
use PunchoutCatalog\Zed\PunchoutCatalog\Business\Mapping\Coder\Transform\SplitCommand;
use PunchoutCatalog\Zed\PunchoutCatalog\Business\Mapping\Coder\Transform\StripCommand;
use PunchoutCatalog\Zed\PunchoutCatalog\Business\Mapping\Coder\Transform\UppercaseCommand;
use Spryker\Shared\Kernel\Transfer\TransferInterface;
use \InvalidArgumentException;

abstract class AbstractCoder
{
    /**
     * @var array|\Generated\Shared\Transfer\PunchoutCatalogMappingObjectTransfer[]
     */
    protected $snippets = [];

    /**
     * @param \Spryker\Shared\Kernel\Transfer\TransferInterface $transfer
     *
     * @return array
     */
    public function toAssociativeArray(TransferInterface $transfer): array
    {
        return $this->_toAssociativeArray($transfer->toArray());
    }

    /**
     * @param array $transferData
     *
     * @return array
     */
    protected function _toAssociativeArray(array $transferData): array
    {
        foreach ($transferData as $key => &$val) {
            if (($key === 'custom_attributes' || $key === 'options')) {
                $val = $this->_fixCustomAttributes($val);
            } elseif (is_array($val)) {
                $val = $this->_toAssociativeArray($val);
            } elseif (is_object($val)) {
                $val = [];//hot fix for empty ArrayObject in transfer objects
            }
        }
        return $transferData;
    }

    /**
     * @param $transferData
     *
     * @return array
     */
    protected function _fixCustomAttributes($transferData): array
    {
        if (!is_array($transferData)) {
            return [];
        }

        $newTransferData = [];
        foreach ($transferData as $key => $val) {
            $idx = $val['code'] ?? '';
            $newTransferData[$idx] = $val;
        }

        return $newTransferData;
    }

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
    )
    {
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
            $result = !empty($result) ? current($result) : null;
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
     * @param string $path
     *
     * @return \Generated\Shared\Transfer\PunchoutCatalogMappingObjectTransfer|null
     */
    protected function getSnippetFromPath(string $path): ?PunchoutCatalogMappingObjectTransfer
    {
        $snippetName = $this->toSnippet($path);
        $snippet = $snippetName ? $this->getSnippet($snippetName) : null;
        $snippetRelPath = $snippet ? $this->toSnippetPath($path) : null;

        $_snippet = null;
        if ($snippetRelPath && is_string($snippetRelPath)) {
            $_snippet = clone $snippet;
            $_snippet->setPath([$snippetRelPath]);
        }

        return $_snippet;
    }

    /**
     * @param $xpath
     *
     * @return bool|string
     */
    protected function toSnippet($xpath)
    {
        $xpath = explode('/', $xpath);
        $snippet = end($xpath);

        if (!$snippet || strlen($snippet) < 2 || (strpos($snippet, '()') === false)) {
            return false;
        }
        $snippet = str_replace('()', '', $snippet);
        return trim($snippet);
    }

    /**
     * @param string $name
     *
     * @return \Generated\Shared\Transfer\PunchoutCatalogMappingObjectTransfer|null
     */
    protected function getSnippet(string $name): ?PunchoutCatalogMappingObjectTransfer
    {
        return isset($this->snippets[$name]) ? $this->snippets[$name] : null;
    }

    /**
     * @param $xpath
     *
     * @return string
     */
    protected function toSnippetPath($xpath): string
    {
        $xpath = explode('/', $xpath);
        array_pop($xpath);//remove last element
        return implode('/', $xpath);
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
     * @param \SimpleXMLElement|string $val
     *
     * @return string
     */
    public function cast($val): string
    {
        return (string)$val;
    }

    /**
     * @param \Generated\Shared\Transfer\PunchoutCatalogMappingObjectFieldTransfer $field
     * @param $value |null
     *
     * @return mixed
     */
    public function mapTransformations(PunchoutCatalogMappingObjectFieldTransfer $field, $value = null)
    {
        foreach ($field->getTransformations() as $transformation) {
            $value = $this->getTransformCommand($transformation->getName())->execute($transformation, $value);
        }
        return $value;
    }

    /**
     * @param string $name
     *
     * @throws \InvalidArgumentException
     *
     * @return \PunchoutCatalog\Zed\PunchoutCatalog\Business\Mapping\Coder\ITransform
     */
    protected function getTransformCommand(string $name): ITransform
    {
        $transformations = $this->getTransformations();
        if (!isset($transformations[$name])) {
            throw new InvalidArgumentException('Could not handle transform: ' . $name);
        }

        if (!($transformations[$name] instanceof ITransform)) {
            throw new InvalidArgumentException('Undefined transform command: ' . $name);
        }

        return $transformations[$name];
    }

    /**
     * @return \PunchoutCatalog\Zed\PunchoutCatalog\Business\Mapping\Coder\ITransform[]
     */
    protected function getTransformations(): array
    {
        return [
            'default' => new DefaultCommand(),
            'join' => new JoinCommand(),
            'split' => new SplitCommand(),
            'cut' => new CutCommand(),
            'uppercase' => new UppercaseCommand(),
            'lowercase' => new LowercaseCommand(),
            'not' => new NotCommand(),
            'date' => new DateCommand(),
            'append' => new AppendCommand(),
            'prepend' => new PrependCommand(),
            'map' => new MapCommand(),
            'amount' => new AmountCommand(),
            'famount' => new AmountFormattedCommand(),
            'round' => new RoundCommand(),
            'strip' => new StripCommand(),
            'htmlspecial' => new HtmlspecialCommand(),
        ];
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

        if ($val === null) {
            return [];
        }

        if (!is_array($val)) {
            return [$val];
        }

        return $val;
    }

    /**
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

    /**
     * @param \Generated\Shared\Transfer\PunchoutCatalogMappingTransfer $mapping
     *
     * @return $this
     */
    protected function registerSnippets(PunchoutCatalogMappingTransfer $mapping)
    {
        /** @var \Generated\Shared\Transfer\PunchoutCatalogMappingObjectTransfer $object */
        foreach ($mapping->getObjects() as $object) {
            if ($object->getIsCustom()) {
                $this->snippets[$object->getName()] = $object;
            }
        }
        return $this;
    }
}
