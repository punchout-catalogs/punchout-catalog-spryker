<?php

/**
 * Copyright © 2018-present PunchOut Catalogs LLC. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace PunchoutCatalog\Zed\PunchoutCatalog\Business\ContentProcessor;

interface CxmlContentProcessorInterface
{
    /**
     * @param string $content
     *
     * @return array
     */
    public function fetchHeaderAsArray(string $content): array;

    /**
     * @param string $content
     *
     * @return string|null
     */
    public function fetchOperation(string $content): ?string;

    /**
     * @param string $content
     *
     * @return bool
     */
    public function isCXmlContent(string $content): bool;
}
