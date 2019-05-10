<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace PunchoutCatalog\Zed\PunchoutCatalog\Business\Mapping\Coder\Transform;

use Generated\Shared\Transfer\PunchoutCatalogMappingTransformTransfer;
use PunchoutCatalog\Zed\PunchoutCatalog\Business\Mapping\Coder\ITransform;

/**
 * Class RoundCommand
 */
class RoundCommand extends AbstractCommand implements ITransform
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
        $params = $transform->getParams();
        $value = (!empty($params['precision']) && is_string($params['precision'])) ? $params['precision'] : 4;
        return (int)$value;
    }

    /**
     * @param $value
     * @param $precision|int
     *
     * @return string|null
     */
    protected function toAmount($value, $precision = 0): ?string
    {
        $value = str_replace(",", "", (string)$value);//fix numbers like 21,444.19
        $value = (float)$value;
        return round($value, $precision);
    }
}
