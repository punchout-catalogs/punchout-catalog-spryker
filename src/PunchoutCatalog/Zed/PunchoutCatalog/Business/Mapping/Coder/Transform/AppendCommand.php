<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace PunchoutCatalog\Zed\PunchoutCatalog\Business\Mapping\Coder\Transform;

use Generated\Shared\Transfer\PunchoutCatalogMappingTransformTransfer;
use PunchoutCatalog\Zed\PunchoutCatalog\Business\Mapping\Coder\ITransform;

/**
 * Class AppendCommand
 */
class AppendCommand extends AbstractCommand implements ITransform
{
    /**
     * @param \Generated\Shared\Transfer\PunchoutCatalogMappingTransformTransfer $transform
     * @param $value
     *
     * @return array|string|null
     */
    protected function _execute(PunchoutCatalogMappingTransformTransfer $transform, $value)
    {
        if (null === $transform->getParams()) {
            return $value;
        }
        
        //fix some phantoms and don't cause PHP warnings
        if (is_array($value)) {
            foreach ($value as &$_val) {
                $_val = $this->_execute($transform, $_val);
            }
            return $value;
        } elseif (!is_string($value) && !is_int($value) && !is_float($value)) {
            return $value;
        } elseif (null === $transform->getParams()->getValue()) {
            return $value;
        }
        
        return ((string)$value) . ((string)$transform->getParams()->getValue());
    }
}
