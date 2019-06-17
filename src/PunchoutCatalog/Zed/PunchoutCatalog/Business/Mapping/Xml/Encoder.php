<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace PunchoutCatalog\Zed\PunchoutCatalog\Business\Mapping\Xml;

use Generated\Shared\Transfer\PunchoutCatalogMappingObjectTransfer;
use Generated\Shared\Transfer\PunchoutCatalogMappingTransfer;
use SimpleXMLElement;
use Spryker\Shared\Kernel\Transfer\TransferInterface;
use InvalidArgumentException;
use PunchoutCatalog\Zed\PunchoutCatalog\Business\Mapping\AbstractCoder;
use PunchoutCatalog\Zed\PunchoutCatalog\Business\Mapping\Coder\SnippetTrait;
use PunchoutCatalog\Zed\PunchoutCatalog\Business\Mapping\Coder\TransferDataTrait;
use PunchoutCatalog\Zed\PunchoutCatalog\Business\Mapping\Coder\TransformationTrait;
use PunchoutCatalog\Zed\PunchoutCatalog\Business\Mapping\EncoderInterface;

class Encoder extends AbstractCoder implements EncoderInterface
{
    use TransferDataTrait;
    use SnippetTrait;
    use TransformationTrait;

    /**
     * @var array
     */
    protected $appendOnly = [];

    /**
     * @var \SimpleXMLElement|null
     */
    protected $document = null;

    /**
     * @var \PunchoutCatalog\Zed\PunchoutCatalog\Business\Mapping\Xml\XmlUtil
     */
    protected $xmlUtil;

    public function execute(PunchoutCatalogMappingTransfer $mapping, TransferInterface $transferData, ?SimpleXMLElement $document = null)
    {
        if (($document !== null) && !($document instanceof SimpleXMLElement)) {
            throw new InvalidArgumentException('punchout-catalog.error.invalid.document.data');
        }

        $transferData = $this->toAssociativeArray($transferData);

        $this->xmlUtil = new XmlUtil();
        $this->appendOnly = [];

        if ($document === null) {
            $this->document = new SimpleXMLElement('<?xml version="1.0" encoding="UTF-8"?><cXML />');
        } else {
            $this->document = $document;
        }

        // @todo The snippet trait defines a state property although it is not necessary. The prepared map could be directly injected into the "toFlat" method.
        $this->snippets = [];
        $this->registerSnippets($mapping);

        $data = [];

        /** @var \Generated\Shared\Transfer\PunchoutCatalogMappingObjectTransfer $object */
        foreach ($mapping->getObjects() as $object) {
            if ($object->getIsCustom()) {
                continue;
            }

            $_data = $this->toFlat($object, $transferData[$object->getName()] ?? []);
            $data = array_merge($data, $_data);
        }

        $this->setData($data);

        return $this->document;
    }

    /**
     * @param \Generated\Shared\Transfer\PunchoutCatalogMappingObjectTransfer $object
     * @param array $data
     * @param string|null $parentPath
     *
     * @return array
     */
    protected function toFlat(PunchoutCatalogMappingObjectTransfer $object, array $data, ?string $parentPath = null): array
    {
        $result = [];
        if ($object->getIsMultiple()) {
            $_object = clone $object;
            $_object->setIsMultiple(false);

            foreach ($data as $_data) {
                foreach ($object->getPath() as $mlPath) {
                    $_mlPath = $this->register($mlPath);

                    $_result = $this->toFlat($_object, $_data, $_mlPath);
                    $result = array_merge($result, $_result);
                }
            }
        } else {
            /** @var \Generated\Shared\Transfer\PunchoutCatalogMappingObjectFieldTransfer $field */
            foreach ($object->getFields() as $field) {
                foreach ($field->getPath() as $_path) {
                    $_path = $this->processPath($_path, $data);

                    $snippet = $this->getSnippetFromPath($_path);
                    $val = $this->fetchDataValueByField($field, $data, ($snippet === null));

                    if ($snippet) {
                        foreach ($snippet->getPath() as $snippetPath) {
                            foreach ($val as $_val) {
                                $registeredPath = $this->register($snippetPath, $parentPath);
                                $_result = $this->toFlat($snippet, $_val, $registeredPath);
                                $result = array_merge($result, $_result);
                            }
                        }
                    } else {
                        foreach ($val as $_val) {
                            $registeredPath = $this->register($_path, $parentPath);
                            $result[$registeredPath] = $_val;

                            if ($field->getIsAppend()) {
                                $this->appendOnly[$registeredPath] = $field->getIsAppend();
                            }
                        }
                    }
                }
            }
        }

        return $result;
    }

    /**
     * @param array $data
     *
     * @return $this
     */
    protected function setData(array $data)
    {
        foreach ($data as $_path => $value) {
            if ($value === null) {
                continue;
            }

            $_path = $_path[0] !== '/' ? '/' . $_path : $_path;

            //Skip setting elements if parent does not exists
            if (!empty($this->appendOnly[$_path])) {
                $parentPath = $this->getParentPath($_path);
                if (!$parentPath || !$this->get($this->document, $parentPath)) {
                    continue;
                }
            }

            //Create Tree and Set values
            $this->set($this->document, $_path, $value);
        }

        return $this;
    }

    /**
     * @param string $path
     *
     * @return string|null
     */
    public function getParentPath(string $path): ?string
    {
        $lastPart = strrchr($path, "/");
        if ($lastPart !== false) {
            return substr($path, 0, strlen($path) - strlen($lastPart));
        }
        return null;
    }

    /**
     * @param $source
     * @param $path
     *
     * @return array|bool
     */
    public function get($source, $path)
    {
        return $this->xmlUtil->get($source, $path);
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
        return $this->xmlUtil->set($source, $path, $value);
    }

    /**
     * @param string $path
     * @param string|null $parentPath
     *
     * @return string
     */
    public function register(string $path, ?string $parentPath = null): string
    {
        return $this->xmlUtil->register($path, $parentPath);
    }
}
