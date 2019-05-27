<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace PunchoutCatalog\Zed\PunchoutCatalog\Business\Mapping\Coder\Transform;

use Generated\Shared\Transfer\PunchoutCatalogMappingTransformTransfer;
use PunchoutCatalog\Zed\PunchoutCatalog\Business\Mapping\Coder\ITransform;

/**
 * Class SplitCommand
 */
class SplitCommand extends AbstractCommand implements ITransform
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

        if (is_array($value)) {
            //fix some phantoms and don't cause PHP warnings
            foreach ($value as &$_val) {
                $_val = $this->_execute($transform, $_val);
            }
            return $value;
        }

        $sep = $this->_getDelimiter($transform);
        if ($sep === false || $sep === '') {
            return $value;
        }
    
        $idx = (string)$transform->getParams()->getIndex();

        $exploded = explode($sep, $value);
        $value = $this->_filter($exploded);

        if ($idx == 'all') {
            return $value;
        } elseif ($idx == 'last') {
            $value = trim((string)end($value));
        } else {
            $idx = (int)$idx;
            $idx = $idx ? $idx - 1 : 0;//1 - get 0, 2 - get 1
            $value = isset($value[$idx]) ? trim($value[$idx]) : null;
        }
        return $value;
    }
}
