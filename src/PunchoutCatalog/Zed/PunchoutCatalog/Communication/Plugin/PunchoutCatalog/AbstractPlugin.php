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
        return (new MappingConverter())->convert(
            $this->convertToArray($mapping)
        );
    }
    
    /**
     * @param string $mapping
     *
     * @return array
     */
    protected function convertToArray(string $mapping): array
    {
        return json_decode(trim($mapping), true);
    }
    
    /**
     * @api
     *
     * @return string
     */
    public function getPayloadId(): string
    {
        $dti = $this->getTimestamp();
        
        $randomNumber = rand(1, 999999999);
        $payloadId = $dti . '.' . $randomNumber . '@' . $this->getHostname();

        return $payloadId;
    }

    /**
     * @return string
     */
    public function getTimestamp(): string
    {
        return date('Y-m-d\TH:i:sP');
    }
    
    /**
     * @return string
     * @throws \PunchoutCatalog\Zed\PunchoutCatalog\Exception\MissingYvesUrlConfigurationException
     */
    protected function getHostname()
    {
        $zedUrl = $this->getConfig()->getBaseUrlYves();
        return parse_url($zedUrl)['host'];
    }
}
