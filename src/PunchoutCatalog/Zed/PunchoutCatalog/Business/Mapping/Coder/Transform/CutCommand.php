<?php

/**
 * Copyright © 2018-present PunchOut Catalogs LLC. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace PunchoutCatalog\Zed\PunchoutCatalog\Business\Mapping\Coder\Transform;

use Generated\Shared\Transfer\PunchoutCatalogMappingTransformTransfer;
use PunchoutCatalog\Zed\PunchoutCatalog\Business\Mapping\Coder\ITransform;

/**
 * Class CutCommand
 */
class CutCommand extends AbstractCommand implements ITransform
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
        }
        
        $len = (int)$transform->getParams()->getLen();
        $len = $len ? $len : null;
        
        $start = (int)$transform->getParams()->getStart();
        
        if (abs($len) < 1 && abs($start) < 1) {
            return $value;
        } elseif ($start < 0) {
            return substr((string)$value, $start);
        } else {
            return substr((string)$value, $start, $len);
        }
    }
}
