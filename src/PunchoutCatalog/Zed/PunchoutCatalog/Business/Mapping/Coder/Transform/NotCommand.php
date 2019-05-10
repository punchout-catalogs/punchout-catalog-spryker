<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace PunchoutCatalog\Zed\PunchoutCatalog\Business\Mapping\Coder\Transform;

use Generated\Shared\Transfer\PunchoutCatalogMappingTransformTransfer;
use PunchoutCatalog\Zed\PunchoutCatalog\Business\Mapping\Coder\ITransform;

/**
 * Class NotCommand
 */
class NotCommand extends AbstractCommand implements ITransform
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
            /** fix some phantoms and don't cause PHP warnings */
            foreach ($value as &$_val) {
                $_val = $this->_execute($transform, $_val);
            }
            return $value;
        } else {
            return !$this->_bool($value);
        }
    }

    /**
     * @param $boolStr
     *
     * @return bool
     */
    protected function _bool($boolStr): bool
    {
        return ($boolStr === true
            || strtolower((string)$boolStr) === 'true'
            || strtolower((string)$boolStr) === 'yes'
            || $boolStr === '1'
        );
    }
}
