<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace PunchoutCatalog\Zed\PunchoutCatalog\Communication\Plugin\PunchoutCatalog;

use Generated\Shared\Transfer\PunchoutCatalogMappingTransfer;
use Spryker\Zed\Kernel\Communication\AbstractPlugin as CoreAbstractPlugin;
use PunchoutCatalog\Zed\PunchoutCatalog\Business\Mapping\Converter as MappingConverter;

/**
 * @method \PunchoutCatalog\Zed\PunchoutCatalog\Business\PunchoutCatalogFacade getFacade()
 * @method \PunchoutCatalog\Zed\PunchoutCatalog\PunchoutCatalogConfig getConfig()
 */
abstract class AbstractPlugin extends CoreAbstractPlugin
{
    /**
     * @param string $mapping
     *
     * @return \Generated\Shared\Transfer\PunchoutCatalogMappingTransfer
     */
    protected function convertToMappingTransfer(string $mapping): PunchoutCatalogMappingTransfer
    {
        $mapping = trim($mapping);
        $mapping = json_decode(trim($mapping), true);
        return (new MappingConverter())->convert($mapping);
    }

    /**
     * @todo: fix it
     * @api
     *
     * @return string
     */
    public function getPayloadId(): string
    {
        $dti = $this->getTimestamp();

        $hostname = isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : 'example.com';
        $processId = 1;
        $randomNumber = rand(1, 999999999);
        $payloadId = $dti . '.' . $processId . '.' . $randomNumber . '@' . $hostname;

        return $payloadId;
    }

    /**
     * @todo: fix it
     * @api
     *
     * @return string
     */
    public function getTimestamp(): string
    {
        return date('Y-m-d\TH:i:sP');
    }
}
