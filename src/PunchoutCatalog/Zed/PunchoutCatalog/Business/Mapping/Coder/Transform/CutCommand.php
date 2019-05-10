<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
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
        $params = $transform->getParams();

        if (is_array($value)) {
            //fix some phantoms and don't cause PHP warnings
            foreach ($value as &$_val) {
                $_val = $this->_execute($transform, $_val);
            }
            return $value;
        } elseif (!is_string($value) && !is_int($value) && !is_float($value)) {
            return $value;
        }

        $len = !empty($params['len']) ? (int)$params['len'] : 0;
        $len = $len ? $len : null;

        $start = !empty($params['start']) ? (int)$params['start'] : 0;

        if (abs($len) < 1 && abs($start) < 1) {
            return $value;
        } elseif ($start < 0) {
            return substr((string)$value, $start);
        } else {
            return substr((string)$value, $start, $len);
        }
    }
}
