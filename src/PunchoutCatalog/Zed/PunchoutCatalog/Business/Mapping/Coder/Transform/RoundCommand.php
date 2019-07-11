<?php

/**
 * Copyright © 2018-present PunchOut Catalogs LLC. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace PunchoutCatalog\Zed\PunchoutCatalog\Business\Mapping\Coder\Transform;

use Generated\Shared\Transfer\PunchoutCatalogMappingTransformTransfer;
use PunchoutCatalog\Zed\PunchoutCatalog\Business\Mapping\Coder\ITransform;

/**
 * Class RoundCommand
 */
class RoundCommand extends AmountCommand implements ITransform
{
    /**
     * @param \Generated\Shared\Transfer\PunchoutCatalogMappingTransformTransfer $transform
     * @param $value
     *
     * @return array|string|null
     */
    protected function _execute(PunchoutCatalogMappingTransformTransfer $transform, $value)
    {
        if (is_array($value)) {
            //fix some phantoms and don't cause PHP warnings
            foreach ($value as &$_val) {
                $_val = $this->_execute($transform, $_val);
            }
            return $value;
        } elseif ($value !== null && $value !== '') {
            return $this->toAmount((string)$value, $this->_getPrecision($transform));
        }
        return null;
    }

    /**
     * @param \Generated\Shared\Transfer\PunchoutCatalogMappingTransformTransfer $transform
     *
     * @return mixed|string
     */
    protected function _getPrecision(PunchoutCatalogMappingTransformTransfer $transform)
    {
        if (null !== $transform->getParams() && null !== $transform->getParams()->getPrecision()) {
            return (int)$transform->getParams()->getPrecision();
        }
        return 0;
    }

    /**
     * @param $value
     * @param $precision|int
     *
     * @return string|null
     */
    protected function toAmount($value, $precision = 0): ?string
    {
        $value = parent::toAmount($value);
        $value = (float)$value;
        return round($value, $precision);
    }
}
