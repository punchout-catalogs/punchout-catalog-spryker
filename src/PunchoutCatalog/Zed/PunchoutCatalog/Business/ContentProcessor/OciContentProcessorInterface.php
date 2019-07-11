<?php

/**
 * Copyright © 2018-present PunchOut Catalogs LLC. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace PunchoutCatalog\Zed\PunchoutCatalog\Business\ContentProcessor;

use Generated\Shared\Transfer\PunchoutCatalogProtocolDataTransfer;

interface OciContentProcessorInterface
{
    /**
     * @param array $content
     *
     * @return PunchoutCatalogProtocolDataTransfer
     */
    public function fetchHeader(array $content): PunchoutCatalogProtocolDataTransfer;

    /**
     * @param array $content
     *
     * @return string
     */
    public function fetchOperation(array $content): string;

    /**
     * @param array $content
     *
     * @return bool
     */
    public  function isOciContent(array $content): bool;
}
