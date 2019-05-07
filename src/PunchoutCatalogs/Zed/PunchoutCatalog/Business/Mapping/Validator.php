<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace PunchoutCatalogs\Zed\PunchoutCatalog\Business\Mapping;

use Spryker\Zed\ProductStorage\Exception\InvalidArgumentException;

class Validator
{
    /**
     * @param null $mapping
     *
     * @throws \Spryker\Zed\ProductStorage\Exception\InvalidArgumentException
     *
     * @return bool
     */
    public function validate($mapping = null): bool
    {
        if (!is_array($mapping)) {
            throw new InvalidArgumentException('Invalid mapping source');
        }

        if (!$this->validateMapping($mapping)) {
            throw new InvalidArgumentException('Invalid mapping format');
        }

        return true;
    }

    /**
     * @param array $mapping
     *
     * @return bool
     */
    protected function validateMapping(array $mapping): bool
    {
        foreach ($mapping as $objectName => $objectMapping) {
            if (!$this->validateObjectMapping($objectMapping)) {
                return false;
            }
        }
        return true;
    }

    /**
     * @param $objectMapping
     *
     * @return bool
     */
    public function validateObjectMapping($objectMapping): bool
    {
        if (!is_array($objectMapping)) {
            return false;
        }

        if (array_diff(array_keys($objectMapping), ['fields', 'multi_lines'])) {
            return false;
        }

        if (!isset($objectMapping['fields']) || !is_array($objectMapping['fields'])) {
            return false;
        }

        if (isset($objectMapping['multi_lines']) && !$this->validateMultiLineMapping($objectMapping)) {
            return false;
        }

        foreach ($objectMapping['fields'] as $fieldName => $field) {
            if (!$this->validateObjectFieldMapping($field)) {
                return false;
            }
        }

        return true;
    }

    /**
     * @param $field
     *
     * @return bool
     */
    public function validateObjectFieldMapping($field): bool
    {
        if (!is_array($field)) {
            return false;
        }

        if (array_diff(array_keys($field), ['path', 'append', 'transform', 'multiple', 'required'])) {
            return false;
        }

        if (empty($field['path']) || !is_string($field['path'])) {
            return false;
        }

        if (!empty($field['append']) && !is_bool($field['append'])) {
            return false;
        }

        if (!empty($field['transform']) && !is_array($field['transform'])) {
            return false;
        } elseif (!empty($field['transform'])) {
            foreach ($field['transform'] as $transform) {
                if (!$this->validateTransformMapping($transform)) {
                    return false;
                }
            }
        }

        return true;
    }

    /**
     * @param $transform
     *
     * @return bool
     */
    public function validateTransformMapping($transform): bool
    {
        if (!is_string($transform) && !is_array($transform)) {
            return false;
        } elseif (is_array($transform)) {
            $transformName = key($transform);
            $transformParams = current($transform);

            if (!is_string($transformName) || !is_array($transformParams)) {
                return false;
            }
        }

        return true;
    }

    /**
     * @param array $obj
     *
     * @return bool
     */
    protected function validateMultiLineMapping(array $obj): bool
    {
        return (
            (!empty($obj['multi_lines']['path']) && is_string($obj['multi_lines']['path']))
            || is_bool($obj['multi_lines'])
        );
    }
}
