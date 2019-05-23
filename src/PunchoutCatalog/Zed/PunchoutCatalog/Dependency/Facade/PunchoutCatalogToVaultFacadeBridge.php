<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace PunchoutCatalog\Zed\PunchoutCatalog\Dependency\Facade;

use Spryker\Zed\Vault\Business\VaultFacadeInterface;

class PunchoutCatalogToVaultFacadeBridge implements PunchoutCatalogToVaultFacadeInterface
{
    /**
     * @var VaultFacadeInterface
     */
    protected $vaultFacade;

    /**
     * @param VaultFacadeInterface $vaultFacade
     */
    public function __construct($vaultFacade)
    {
        $this->vaultFacade = $vaultFacade;
    }

    /**
     * @param string $dataType
     * @param string $dataKey
     *
     * @return string|null
     */
    public function retrieve(string $dataType, string $dataKey): ?string
    {
        return $this->vaultFacade->retrieve($dataType, $dataKey);
    }

    /**
     * @param string $dataType
     * @param string $dataKey
     * @param string $data
     *
     * @return bool
     */
    public function store(string $dataType, string $dataKey, string $data): bool
    {
        return $this->vaultFacade->store($dataType, $dataKey, $data);
    }
}
