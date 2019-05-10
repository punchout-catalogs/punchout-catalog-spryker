<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace PunchoutCatalog\Zed\PunchoutCatalog\Business\Mapping\Coder\Transform;

use Generated\Shared\Transfer\PunchoutCatalogMappingTransformTransfer;
use PunchoutCatalog\Zed\PunchoutCatalog\Business\Mapping\Coder\ITransform;

/**
 * Class DateCommand
 */
class DateCommand extends AbstractCommand implements ITransform
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
        } else {
            return $this->toDate((string)$value);
        }
    }

    /**
     * @param $dateString
     *
     * @return string|null
     */
    protected function toDate($dateString): ?string
    {
        if (empty($dateString)) {
            return null;
        }

        $info = date_parse($dateString);
        if (empty($info['year'])) {
            return null;
        }

        return sprintf(
            '%d-%02d-%02d %02d:%02d:%02d',
            $info['year'],
            $info['month'],
            $info['day'],
            $info['hour'],
            $info['minute'],
            $info['second']
        );
    }
}
