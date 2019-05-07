<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace PunchoutCatalogs\Zed\PunchoutCatalog\Business\Mapping\Coder\Transform;

use Generated\Shared\Transfer\PunchoutCatalogMappingTransformTransfer;
use PunchoutCatalogs\Zed\PunchoutCatalog\Business\Mapping\Coder\ITransform;

/**
 * Class MapCommand
 */
class MapCommand extends AbstractCommand implements ITransform
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
        }

        $value = $this->_fixValue($value);

        $_value = $this->_getValue($transform);
        $_result = $this->_getResult($transform);

        return ($value == $_value) ? $_result : $value;
    }

    /**
     * @param \Generated\Shared\Transfer\PunchoutCatalogMappingTransformTransfer $transform
     *
     * @return mixed|string
     */
    protected function _getValue(PunchoutCatalogMappingTransformTransfer $transform)
    {
        $params = $transform->getParams();
        $value = (!empty($params['value']) && is_string($params['value'])) ? $params['value'] : false;
        return $this->_fixValue($value);
    }

    /**
     * @param \Generated\Shared\Transfer\PunchoutCatalogMappingTransformTransfer $transform
     *
     * @return mixed|string
     */
    protected function _getResult(PunchoutCatalogMappingTransformTransfer $transform)
    {
        $params = $transform->getParams();
        $value = (!empty($params['result']) && is_string($params['result'])) ? $params['result'] : false;
        return $this->_fixValue($value);
    }
}
