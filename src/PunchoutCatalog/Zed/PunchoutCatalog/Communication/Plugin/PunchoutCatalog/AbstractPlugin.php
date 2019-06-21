<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace PunchoutCatalog\Zed\PunchoutCatalog\Communication\Plugin\PunchoutCatalog;

use Generated\Shared\Transfer\PunchoutCatalogMappingTransfer;
use Spryker\Zed\Kernel\Communication\AbstractPlugin as CoreAbstractPlugin;

/**
 * @todo The methods in this class are not open for extension + they are fake abstract methods (facade)
 *
 * @method \PunchoutCatalog\Zed\PunchoutCatalog\Business\PunchoutCatalogFacade getFacade()
 * @method \PunchoutCatalog\Zed\PunchoutCatalog\Communication\PunchoutCatalogCommunicationFactory getFactory()
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
        return ($this->getFactory()->createMappingConverter())->convert(
            $this->convertToArray($mapping)
        );
    }
    
    /**
     * @param string $mapping
     *
     * @return array|null
     */
    protected function convertToArray(string $mapping): ?array
    {
        return json_decode(trim($mapping), true);
    }
    
    /**
     * @todo This is method does not belong to the abstract class (+SRP violation)
     *
     * @api
     *
     * @param string $storeName
     *
     * @return string
     */
    public function getYvesPayloadId(string $storeName): string
    {
        return $this->getPayloadId($this->getYvesHostname($storeName));
    }

    /**
     * @api
     *
     * @return string
     */
    public function getZedPayloadId(): string
    {
        return $this->getPayloadId($this->getZedHostname());
    }

    /**
     * @param string $hostName
     *
     * @return string
     */
    protected function getPayloadId(string $hostName): string
    {
        $dti = $this->getTimestamp();

        $randomNumber = rand(1, 999999999);
        $payloadId = $dti . '.' . $randomNumber . '@' . $hostName;

        return $payloadId;
    }

    /**
     * @todo This is method does not belong to the abstract class (+SRP violation)
     * @todo has a public class that is not part of the plugin interfaces
     *
     * @api
     *
     * @return string
     */
    public function getTimestamp(): string
    {
        return date('Y-m-d\TH:i:sP');
    }
    
    /**
     * @param string $storeName
     *
     * @throws \PunchoutCatalog\Zed\PunchoutCatalog\Exception\MissingYvesUrlConfigurationException
     *
     * @return string
     */
    protected function getYvesHostname(string $storeName): string
    {
        $yvesUrl = $this->getConfig()->getBaseUrlYves($storeName);

        return parse_url($yvesUrl)['host'];
    }

    /**
     * @throws \PunchoutCatalog\Zed\PunchoutCatalog\Exception\MissingYvesUrlConfigurationException
     *
     * @return string
     */
    protected function getZedHostname(): string
    {
        $zedUrl = $this->getConfig()->getBaseUrlZed();

        return parse_url($zedUrl)['host'];
    }
}
