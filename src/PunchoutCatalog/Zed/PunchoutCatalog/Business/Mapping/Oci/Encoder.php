<?php

/**
 * Copyright © 2018-present PunchOut Catalogs LLC. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace PunchoutCatalog\Zed\PunchoutCatalog\Business\Mapping\Oci;

use Generated\Shared\Transfer\PunchoutCatalogMappingObjectTransfer;
use Generated\Shared\Transfer\PunchoutCatalogMappingTransfer;
use Spryker\Shared\Kernel\Transfer\TransferInterface;
use InvalidArgumentException;
use PunchoutCatalog\Zed\PunchoutCatalog\Business\Mapping\AbstractCoder;
use PunchoutCatalog\Zed\PunchoutCatalog\Business\Mapping\Coder\TransferDataTrait;
use PunchoutCatalog\Zed\PunchoutCatalog\Business\Mapping\EncoderInterface;

class Encoder extends AbstractCoder implements EncoderInterface
{
    /**
     * @var array
     */
    protected $document = [];

    public function execute(PunchoutCatalogMappingTransfer $mapping, TransferInterface $transferData, ?array $document = null)
    {
        if ($document !== null && !is_array($document)) {
            throw new InvalidArgumentException('punchout-catalog.error.invalid.document.data');
        }

        $transferData = $this->toAssociativeArray($transferData);

        $this->document = $document ? $document : [];

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
     *
     * @return array
     */
    protected function toFlat(PunchoutCatalogMappingObjectTransfer $object, array $data): array
    {
        $result = [];

        if ($object->getIsMultiple()) {
            $_object = clone $object;
            $_object->setIsMultiple(false);

            foreach ($data as $_data) {
                $_result = $this->toFlat($_object, $_data);
                $result = array_merge($result, $_result);
            }
        } else {
            /** @var \Generated\Shared\Transfer\PunchoutCatalogMappingObjectFieldTransfer $field */
            foreach ($object->getFields() as $field) {
                foreach ($field->getPath() as $path) {
                    $path = $this->processPath($path, $data);
                    $result[$path] = $this->fetchDataValueByField($field, $data);
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

            $value = is_array($value) ? $this->wrapArrayValue($value) : $value;
            $this->document = array_merge($this->document, ["{$_path}" => $value]);
        }

        return $this;
    }
}
