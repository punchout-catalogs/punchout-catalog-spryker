<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace PunchoutCatalogs\Zed\PunchoutCatalog\Dependency\Facade;

use PunchoutCatalogs\Shared\PunchoutCatalog\PunchoutCatalogConfig;

class PunchoutCatalogToVaultFacadeBridge implements PunchoutCatalogToVaultFacadeInterface
{
    // TODO: hack to simulate Vault behavior. Key equals to idPunchoutCatalogConnection.
    protected $secrets = [
        PunchoutCatalogConfig::VAULT_PASSWORD_DATA_TYPE => [
            1 => 'user_1_pass',
            2 => 'user_1_pass',
            3 => 'user_2_pass',
            4 => 'user_1_pass',
            5 => 'user_3_pass',
    
            6 => 'user_1_pass',
            7 => 'user_1_pass',
            8 => 'user_2_pass',
            9 => 'user_1_pass',
            10 => 'user_3_pass',
    
            11 => 'user_1_pass',
            12 => 'user_1_pass',
            13 => 'user_2_pass',
            14 => 'user_1_pass',
            15 => 'user_3_pass',
        ],
    ];

    public function __construct()
    {
    }

    /**
     * @param string $dataType
     * @param string $dataIndex
     *
     * @return string|null
     */
    public function retrieve(string $dataType, string $dataIndex): ?string
    {
        return $this->secrets[$dataType][$dataIndex] ?? null;
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
        // Will be implemented so it saves the provided data for later retrieval.
    }
}
