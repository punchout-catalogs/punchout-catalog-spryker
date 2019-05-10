<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace PunchoutCatalog\Zed\PunchoutCatalog\Business\Mapping;

use Generated\Shared\Transfer\PunchoutCatalogMappingTransfer;
use Spryker\Shared\Kernel\Transfer\TransferInterface;

/**
 * Interface EncoderInterface
 *
 * @package PunchoutCatalog\Zed\PunchoutCatalog\Business\Mapping
 */
interface EncoderInterface
{
    /**
     * @param \Generated\Shared\Transfer\PunchoutCatalogMappingTransfer $mapping
     * @param \Spryker\Shared\Kernel\Transfer\TransferInterface $source
     *
     * @return mixed
     */
    public function execute(PunchoutCatalogMappingTransfer $mapping, TransferInterface $source);
}
