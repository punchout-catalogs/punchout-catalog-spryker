<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace PunchoutCatalog\Zed\PunchoutCatalog\Business\Mapping\Coder\Transform;

use Generated\Shared\Transfer\PunchoutCatalogMappingTransformTransfer;

/**
 * Class AbstractCommand
 */
abstract class AbstractCommand
{
    /**
     * @param \Generated\Shared\Transfer\PunchoutCatalogMappingTransformTransfer $transform
     * @param $value
     *
     * @return mixed
     */
    public function execute(PunchoutCatalogMappingTransformTransfer $transform, $value)
    {
        if (!$this->_canProcessOriginalValue($value) && !empty($value)) {
            foreach ($value as &$_val) {
                $_val = $this->_execute($transform, $_val);
            }
        } elseif (!$this->_canProcessOriginalValue($value) && empty($value)) {
            $value = $this->_execute($transform, '');
        } else {
            $value = $this->_execute($transform, $value);
        }
        return $value;
    }

    /**
     * @param \Generated\Shared\Transfer\PunchoutCatalogMappingTransformTransfer $transform
     * @param $value
     *
     * @return array|string|null
     */
    abstract protected function _execute(PunchoutCatalogMappingTransformTransfer $transform, $value);

    /**
     * @param \Generated\Shared\Transfer\PunchoutCatalogMappingTransformTransfer $transform
     *
     * @return mixed|string
     */
    protected function _getDelimiter(PunchoutCatalogMappingTransformTransfer $transform)
    {
        $params = $transform->getParams();
        $sep = (!empty($params['sep']) && is_string($params['sep'])) ? $params['sep'] : false;
        return $this->_fixValue($sep);
    }

    /**
     * @param $value
     *
     * @return mixed
     */
    protected function _fixValue($value)
    {
        if ($value !== false && is_string($value)) {
            $value = str_replace('\\\n', "\n", $value);//New Line
            $value = str_replace('\\n', "\n", $value);//New Line

            $value = str_replace('\\\s', "\s", $value);//Blank Space
            $value = str_replace('\\s', "\s", $value);//Blank Space
            $value = str_replace('\s', " ", $value);//Blank Space
        }
        return $value;
    }

    /**
     * @param $value
     *
     * @return mixed
     */
    protected function _filter($value)
    {
        $newValue = [];
        foreach ($value as $k => $v) {
            if ($v !== '' && $v !== null && $v !== false) {
                $newValue[$k] = $v;
            }
        }
        return $newValue;
    }

    /**
     * @param $value
     *
     * @return bool
     */
    protected function _canProcessOriginalValue($value): bool
    {
        return !is_array($value);
    }
}
