<?php

/**
 * Copyright © 2022-present PunchOut Catalogs LLC. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace PunchoutCatalog\Zed\PunchoutCatalog\Business\Mapping\Coder\Transform;

use Generated\Shared\Transfer\PunchoutCatalogMappingTransformTransfer;
use PunchoutCatalog\Zed\PunchoutCatalog\Business\Mapping\Coder\ITransform;

/**
 * Class RoundCommand
 */
class AbsCommand extends AmountCommand implements ITransform
{
    /**
     * @param $value
     *
     * @return string|null
     */
    protected function toAmount($value): ?string
    {
        $value = parent::toAmount($value);
        $value = (float)$value;
        return abs($value);
    }
}
