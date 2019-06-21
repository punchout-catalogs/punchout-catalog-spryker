<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace PunchoutCatalog\Zed\PunchoutCatalog\Business\EntryPoint;

use Generated\Shared\Transfer\PunchoutCatalogEntryPointFilterTransfer;
use Generated\Shared\Transfer\PunchoutCatalogEntryPointTransfer;
use Generated\Shared\Transfer\StoreTransfer;
use PunchoutCatalog\Zed\PunchoutCatalog\Communication\Controller\RequestController;
use PunchoutCatalog\Zed\PunchoutCatalog\Dependency\Facade\PunchoutCatalogToStoreFacadeInterface;
use PunchoutCatalog\Zed\PunchoutCatalog\PunchoutCatalogConfig;

class RequestEntryPointReader implements RequestEntryPointReaderInterface
{
    /**
     * @var PunchoutCatalogConfig
     */
    protected $punchoutCatalogConfig;

    /**
     * @var PunchoutCatalogToStoreFacadeInterface
     */
    protected $storeFacade;

    /**
     * @param PunchoutCatalogConfig $punchoutCatalogConfig
     * @param PunchoutCatalogToStoreFacadeInterface $storeFacade
     */
    public function __construct(PunchoutCatalogConfig $punchoutCatalogConfig, PunchoutCatalogToStoreFacadeInterface $storeFacade)
    {
        $this->punchoutCatalogConfig = $punchoutCatalogConfig;
        $this->storeFacade = $storeFacade;
    }

    /**
     * @param PunchoutCatalogEntryPointFilterTransfer $entryPointFilter
     *
     * @return PunchoutCatalogEntryPointTransfer[]
     */
    public function getRequestEntryPointsByBusinessUnit(PunchoutCatalogEntryPointFilterTransfer $entryPointFilter): array
    {
        $entryPointFilter->requireIdCompanyBusinessUnit();

        $stores = $this->storeFacade->getAllStores();

        $entryPoints = [];
        foreach($stores as $store) {
            $entryPoints[] = (new PunchoutCatalogEntryPointTransfer())
                ->setIdCompanyBusinessUnit($entryPointFilter->getIdCompanyBusinessUnit())
                ->setStore($store)
                ->setUrl(
                    $this->createEntryPointUrl($store, $entryPointFilter->getIdCompanyBusinessUnit())
                );
        }

        return $entryPoints;
    }

    /**
     * @param StoreTransfer $store
     * @param int $idCompanyBusinessUnit
     *
     * @return string
     */
    protected function createEntryPointUrl(StoreTransfer $store, int $idCompanyBusinessUnit): string
    {
        $zedPunchoutUrl = $this->punchoutCatalogConfig->getZedPunchoutUrl();

        $params = [
            RequestController::PARAM_BUSINESS_UNIT => $idCompanyBusinessUnit,
            RequestController::PARAM_STORE => $store->getName(),
        ];

        return sprintf('%s?%s', $zedPunchoutUrl, http_build_query($params));
    }
}