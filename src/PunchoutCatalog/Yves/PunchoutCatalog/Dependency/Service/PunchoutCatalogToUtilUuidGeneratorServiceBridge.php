<?php

/**
 * Copyright © 2018-present PunchOut Catalogs LLC. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace PunchoutCatalog\Yves\PunchoutCatalog\Dependency\Service;

class PunchoutCatalogToUtilUuidGeneratorServiceBridge implements PunchoutCatalogToUtilUuidGeneratorServiceInterface
{
    /**
     * @var \Spryker\Service\UtilUuidGenerator\UtilUuidGeneratorServiceInterface
     */
    protected $utilUuidGeneratorService;

    /**
     * @param \Spryker\Service\UtilUuidGenerator\UtilUuidGeneratorServiceInterface $utilUuidGeneratorService
     */
    public function __construct($utilUuidGeneratorService)
    {
        $this->utilUuidGeneratorService = $utilUuidGeneratorService;
    }

    /**
     * @param string $name
     *
     * @return string
     */
    public function generateUuid5FromObjectId(string $name): string
    {
        return $this->utilUuidGeneratorService->generateUuid5FromObjectId($name);
    }
}
