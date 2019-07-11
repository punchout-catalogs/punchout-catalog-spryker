<?php

/**
 * Copyright © 2018-present PunchOut Catalogs LLC. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace PunchoutCatalog\Zed\PunchoutCatalog\Business\Mapping\Coder\Transform;

use Generated\Shared\Transfer\PunchoutCatalogMappingTransformTransfer;
use PunchoutCatalog\Zed\PunchoutCatalog\Business\Mapping\Coder\ITransform;

/**
 * Class AmountCommand
 */
class AmountCommand extends AbstractCommand implements ITransform
{
    /**
     * @var string
     */
    protected $_thousandsSeparator = ',';

    /**
     * @param \Generated\Shared\Transfer\PunchoutCatalogMappingTransformTransfer $transform
     * @param $value
     *
     * @return mixed
     */
    protected function _execute(PunchoutCatalogMappingTransformTransfer $transform, $value)
    {
        if ($transform->getParams() !== null && $transform->getParams()->getThousandsSep() !== null) {
            $this->_thousandsSeparator = $transform->getParams()->getThousandsSep();
        }
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
        $value = str_replace($this->_thousandsSeparator, '', (string)$value);//fix numbers like 21,444.19
        return (float)$value;
    }
}
