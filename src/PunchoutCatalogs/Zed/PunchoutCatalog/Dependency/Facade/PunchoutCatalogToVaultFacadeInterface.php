<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace PunchoutCatalogs\Zed\PunchoutCatalog\Dependency\Facade;

interface PunchoutCatalogToVaultFacadeInterface
{
    /**
     * @param string $dataType
     * @param string $dataIndex
     *
     * @return string|null
     */
    public function retrieve(string $dataType, string $dataIndex): ?string;

    /**
     * @param string $dataType
     * @param string $dataIndex
     * @param string $data
     *
     * @return void
     */
    public function store(string $dataType, string $dataIndex, string $data): void;
}
