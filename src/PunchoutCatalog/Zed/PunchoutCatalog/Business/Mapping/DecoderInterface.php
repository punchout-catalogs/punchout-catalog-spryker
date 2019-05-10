<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace PunchoutCatalog\Zed\PunchoutCatalog\Business\Mapping;

use Generated\Shared\Transfer\PunchoutCatalogMappingTransfer;

/**
 * Interface DecoderInterface
 *
 * @package PunchoutCatalog\Zed\PunchoutCatalog\Business\Mapping
 */
interface DecoderInterface
{
    /**
     * @param \Generated\Shared\Transfer\PunchoutCatalogMappingTransfer $mapping
     * @param \SimpleXMLElement|array $source
     *
     * @return array
     */
    public function execute(PunchoutCatalogMappingTransfer $mapping, $source);
}
