<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace PunchoutCatalogs\Service\UtilCxml;

interface UtilCxmlServiceInterface
{
    /**
     * Specification:
     * -
     *
     * @api
     *
     * @param string $content
     *
     * @return array
     */
    public function fetchHeaderAsArray(string $content): array;

    /**
     * Specification:
     * -
     *
     * @api
     *
     * @param string $content
     *
     * @return string|null
     */
    public function getOperation(string $content): string;

    /**
     * Specification:
     * -
     *
     * @api
     *
     * @param string $content
     *
     * @return bool
     */
    public function isCXml(string $content): bool;
}
