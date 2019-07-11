<?php

/**
 * Copyright © 2018-present PunchOut Catalogs LLC. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace PunchoutCatalog\Zed\PunchoutCatalog\Business\Mapping\Coder\Transform;

use PunchoutCatalog\Zed\PunchoutCatalog\Business\Mapping\Coder\ITransform;

/**
 * Class AmountFormattedCommand
 */
class AmountFormattedCommand extends AmountCommand implements ITransform
{
    /**
     * @param $value
     *
     * @return string|null
     */
    protected function toAmount($value): ?string
    {
        $value = parent::toAmount($value);
        $value = round($value, 2);//Need to fix // $65.00 (incorrect behavior for PHP_ROUND_HALF_UP)
        return money_format('%.2n', $value); // $65.01 (correct)
    }
}
