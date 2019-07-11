<?php

/**
 * Copyright © 2018-present PunchOut Catalogs LLC. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace PunchoutCatalog\Zed\PunchoutCatalog\Business\Mapping\Coder;

use Generated\Shared\Transfer\PunchoutCatalogMappingTransformTransfer;

/**
 * Interface ITransform
 * @package PunchoutCatalog\Zed\PunchoutCatalog\Business\Mapping\Coder
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
