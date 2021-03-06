<?php

/**
 * Copyright © 2018-present PunchOut Catalogs LLC. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace PunchoutCatalog\Zed\PunchoutCatalog\Business\Mapping\Coder\Transform;

use Generated\Shared\Transfer\PunchoutCatalogMappingTransformTransfer;
use PunchoutCatalog\Zed\PunchoutCatalog\Business\Mapping\Coder\ITransform;

/**
 * Class DefaultCommand
 */
class DefaultCommand extends AbstractCommand implements ITransform
{
    /**
     * @param \Generated\Shared\Transfer\PunchoutCatalogMappingTransformTransfer $transform
     * @param $value
     *
     * @return array|string|null
     */
    protected function _execute(PunchoutCatalogMappingTransformTransfer $transform, $value)
    {
        if (null === $transform->getParams() || null === $transform->getParams()->getValue()) {
            return $value;
        }
        
        if (is_array($value)) {
            //fix some phantoms and don't cause PHP warnings
            foreach ($value as &$_val) {
                $_val = $this->_execute($transform, $_val);
            }
            return $value;
        } else {
            return $this->toDefaultValue($transform, (string)$value);
        }
    }

    /**
     * @param \Generated\Shared\Transfer\PunchoutCatalogMappingTransformTransfer $transform
     * @param $value|null
     *
     * @return string|null
     */
    protected function toDefaultValue(PunchoutCatalogMappingTransformTransfer $transform, $value = null): ?string
    {
        $default = false;
        if ($value === '' || $value === false) {
            $default = $this->_fixValue($transform->getParams()->getValue());
        }
        return ($default !== false) ? $default : $value;
    }
}
