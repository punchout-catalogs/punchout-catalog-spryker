<?php

/**
 * Copyright © 2018-present PunchOut Catalogs LLC. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace PunchoutCatalog\Zed\PunchoutCatalog\Business\Mapping\Coder\Transform;

use Generated\Shared\Transfer\PunchoutCatalogMappingTransformTransfer;
use PunchoutCatalog\Zed\PunchoutCatalog\Business\Mapping\Coder\ITransform;

/**
 * Class JoinCommand
 */
class JoinCommand extends AbstractCommand implements ITransform
{
    /**
     * @param \Generated\Shared\Transfer\PunchoutCatalogMappingTransformTransfer $transform
     * @param $value
     *
     * @return array|string|null
     */
    protected function _execute(PunchoutCatalogMappingTransformTransfer $transform, $value)
    {
        if (!is_array($value)) {
            return $value;
        }

        //fix some phantoms and don't cause PHP warnings
        foreach ($value as &$_val) {
            if (is_array($_val)) {
                $_val = $this->_execute($transform, $_val);
            }
        }

        $value = $this->_filter($value);

        $sep = $this->_getDelimiter($transform);
        $sep = ($sep === false) ? ', ' : $sep;

        return implode($sep, $value);
    }

    /**
     * @param $value
     *
     * @return bool
     */
    protected function _canProcessOriginalValue($value): bool
    {
        if (is_array($value)) {
            reset($value);
            $value = !empty($value) ? current($value) : $value;
            return !is_array($value);
        }
        return true;
    }
}
