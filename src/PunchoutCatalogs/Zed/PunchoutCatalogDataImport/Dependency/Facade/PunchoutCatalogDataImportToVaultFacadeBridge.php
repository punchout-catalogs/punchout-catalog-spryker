<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace PunchoutCatalogs\Zed\PunchoutCatalogDataImport\Dependency\Facade;

class PunchoutCatalogDataImportToVaultFacadeBridge implements PunchoutCatalogDataImportToVaultFacadeInterface
{
    /**
     * @param string $dataType
     * @param string $dataIndex
     *
     * @return string|null
     */
    public function retrieve(string $dataType, string $dataIndex): ?string
    {
        // @TODO implement it
        return null;
    }

    /**
     * @param string $dataType
     * @param string $dataIndex
     * @param string $data
     *
     * @return void
     */
    public function store(string $dataType, string $dataIndex, string $data): void
    {
        // @TODO implement it
    }
}
