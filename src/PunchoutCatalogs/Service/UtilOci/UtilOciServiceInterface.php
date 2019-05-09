<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace PunchoutCatalogs\Service\UtilOci;

interface UtilOciServiceInterface
{
    /**
     * Specification:
     * -
     *
     * @api
     *
     * @param array $content
     *
     * @return array
     */
    public function fetchHeaderAsArray(array $content): array;

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
    public function getOperation(array $content): string;

    /**
     * Specification:
     * -
     *
     * @api
     *
     * @param array $content
     *
     * @return bool
     */
    public function isOci(array $content): bool;
}
