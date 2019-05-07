<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace PunchoutCatalogs\Zed\PunchoutCatalog\Business\Mapping\Coder;

use Generated\Shared\Transfer\PunchoutCatalogMappingTransformTransfer;

/**
 * Interface ITransform
 * @package PunchoutCatalogs\Zed\PunchoutCatalog\Business\Mapping\Coder
 */
interface ITransform
{
    /**
     * @param \Generated\Shared\Transfer\PunchoutCatalogMappingTransformTransfer $transform
     * @param $value
     *
     * @return mixed
     */
    public function execute(PunchoutCatalogMappingTransformTransfer $transform, $value);
}
