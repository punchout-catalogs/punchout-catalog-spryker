<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace PunchoutCatalogs\Zed\PunchoutCatalog\Business\Mapping\Coder\Transform;

use Generated\Shared\Transfer\PunchoutCatalogMappingTransformTransfer;
use PunchoutCatalogs\Zed\PunchoutCatalog\Business\Mapping\Coder\ITransform;

/**
 * Class AmountCommand
 */
class AmountCommand extends AbstractCommand implements ITransform
{
    /**
     * @param \Generated\Shared\Transfer\PunchoutCatalogMappingTransformTransfer $transform
     * @param $value
     *
     * @return mixed
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
            return $this->toAmount((string)$value);
        }
        return null;
    }

    /**
     * @param $value
     *
     * @return string|null
     */
    protected function toAmount($value): ?string
    {
        $value = str_replace(",", "", (string)$value);//fix numbers like 21,444.19
        return (float)$value;
    }
}
