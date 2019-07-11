<?php

/**
 * Copyright © 2018-present PunchOut Catalogs LLC. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace PunchoutCatalog\Zed\PunchoutCatalog\Business\Mapping;

use Generated\Shared\Transfer\PunchoutCatalogMappingObjectFieldTransfer;
use Generated\Shared\Transfer\PunchoutCatalogMappingObjectTransfer;
use Generated\Shared\Transfer\PunchoutCatalogMappingTransfer;
use Generated\Shared\Transfer\PunchoutCatalogMappingTransformTransfer;
use Generated\Shared\Transfer\PunchoutCatalogMappingTransformParamsTransfer;

class Converter
{
    /**
     * @var array
     */
    protected $defaultStandardObjNames = [
        'cart',
        'cart_item',
        'customer',
        'billing',
        'shipping',
    ];

    /**
     * @var array
     */
    protected $standardObjNames = [];

    /**
     * @param array|null $mapping
     * @param array|null $standardObjNames
     *
     * @return \Generated\Shared\Transfer\PunchoutCatalogMappingTransfer
     */
    public function convert(?array $mapping = null, ?array $standardObjNames = null): PunchoutCatalogMappingTransfer
    {
        (new Validator())->validate($mapping);

        $this->standardObjNames = $this->defaultStandardObjNames;
        if ($standardObjNames !== null) {
            $this->standardObjNames = $standardObjNames;
        }

        $mappingTransfer = new PunchoutCatalogMappingTransfer();

        foreach ($mapping as $objectName => $objectMapping) {
            $mappingTransfer->addObject(
                $this->convertToObject($objectName, $objectMapping)
            );
        }

        return $mappingTransfer;
    }

    /**
     * @param string $objectName
     * @param array $objectMapping
     *
     * @return \Generated\Shared\Transfer\PunchoutCatalogMappingObjectTransfer
     */
    protected function convertToObject(string $objectName, array $objectMapping): PunchoutCatalogMappingObjectTransfer
    {
        $objectMappingTransfer = new PunchoutCatalogMappingObjectTransfer();
        $objectMappingTransfer->setName($objectName);
        $objectMappingTransfer->setIsCustom(!in_array($objectName, $this->standardObjNames));

        if (isset($objectMapping['multi_lines']) && $objectMapping['multi_lines'] === true) {
            $objectMappingTransfer->setIsMultiple(true);
        } elseif (isset($objectMapping['multi_lines']['path']) && is_string($objectMapping['multi_lines']['path'])) {
            $objectMappingTransfer->setIsMultiple(true);
            $objectMappingTransfer->setPath($this->toMultiPath($objectMapping['multi_lines']['path']));
        }

        if (!empty($objectMapping['fields']) && is_array($objectMapping['fields'])) {
            foreach ($objectMapping['fields'] as $fieldName => $fieldMapping) {
                $objectMappingTransfer->addField(
                    $this->convertToObjectField($fieldName, $fieldMapping)
                );
            }
        }

        return $objectMappingTransfer;
    }

    /**
     * @param string $fieldName
     * @param array $fieldMapping
     *
     * @return \Generated\Shared\Transfer\PunchoutCatalogMappingObjectFieldTransfer
     */
    protected function convertToObjectField(string $fieldName, array $fieldMapping): PunchoutCatalogMappingObjectFieldTransfer
    {
        $fieldMappingTransfer = new PunchoutCatalogMappingObjectFieldTransfer();
        $fieldMappingTransfer->setName($fieldName);

        if (!empty($fieldMapping['path'])) {
            $fieldMappingTransfer->setPath($this->toMultiPath($fieldMapping['path']));
        }

        $fieldMappingTransfer->setIsRequired(!empty($fieldMapping['required']));
        $fieldMappingTransfer->setIsMultiple(!empty($fieldMapping['multiple']));
        $fieldMappingTransfer->setIsAppend(!empty($fieldMapping['append']));

        if (!empty($fieldMapping['transform']) && is_array($fieldMapping['transform'])) {
            foreach ($fieldMapping['transform'] as $transform) {
                $fieldMappingTransfer->addTransformation(
                    $this->convertToTransform($transform)
                );
            }
        }
        return $fieldMappingTransfer;
    }

    /**
     * @param $transform
     *
     * @return \Generated\Shared\Transfer\PunchoutCatalogMappingTransformTransfer
     */
    protected function convertToTransform($transform): PunchoutCatalogMappingTransformTransfer
    {
        $transformTransfer = new PunchoutCatalogMappingTransformTransfer();

        if (is_string($transform)) {
            $transformTransfer->setName(strtolower($transform));
        } elseif (is_array($transform)) {
            $transformName = key($transform);
            $transformParams = current($transform);

            $transformParamsTransfer = new PunchoutCatalogMappingTransformParamsTransfer();
            $transformParamsTransfer->fromArray($transformParams, true);
            
            $transformTransfer->setName(strtolower($transformName));
            $transformTransfer->setParams($transformParamsTransfer);
        }

        return $transformTransfer;
    }

    /**
     * @param string $path
     *
     * @return array
     */
    protected function toMultiPath(string $path): array
    {
        return array_filter(array_map('trim', explode(',', $path)));
    }
}
