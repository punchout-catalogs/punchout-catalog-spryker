<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace PunchoutCatalog\Zed\PunchoutCatalog;

use Generated\Shared\Transfer\DataImporterConfigurationTransfer;
use PunchoutCatalog\Zed\PunchoutCatalog\Communication\Controller\RequestController;
use Spryker\Zed\DataImport\DataImportConfig;
use Spryker\Shared\Application\ApplicationConstants;

use PunchoutCatalog\Zed\PunchoutCatalog\Exception\MissingYvesUrlConfigurationException;
use PunchoutCatalog\Zed\PunchoutCatalog\Exception\MissingZedUrlConfigurationException;

class PunchoutCatalogConfig extends DataImportConfig
{
    public const IMPORT_TYPE_PUNCHOUT_CATALOG_CONNECTION = 'punchout-catalog-connection';
    public const IMPORT_TYPE_PUNCHOUT_CATALOG_SETUP = 'punchout-catalog-connection-setup';
    public const IMPORT_TYPE_PUNCHOUT_CATALOG_CART = 'punchout-catalog-connection-cart';

    /**
     * @uses RequestController::indexAction()
     */
    protected const PUNCHOUT_REQUEST_URL = '/punchout-catalog/request';
    protected const PUNCHOUT_DEFAULT_LOCALE = 'en_US';
    protected const PUNCHOUT_DEFAULT_STORE_LOCALE = 'de';
    
    /**
     * @return \Generated\Shared\Transfer\DataImporterConfigurationTransfer
     */
    public function getPunchoutCatalogConnectionDataImporterConfiguration(): DataImporterConfigurationTransfer
    {
        return $this->buildImporterConfiguration(
            implode(DIRECTORY_SEPARATOR, [$this->getModuleDataImportDirectory(), 'punchout_catalog_connection.csv']),
            static::IMPORT_TYPE_PUNCHOUT_CATALOG_CONNECTION
        );
    }

    /**
     * @return \Generated\Shared\Transfer\DataImporterConfigurationTransfer
     */
    public function getPunchoutCatalogSetupDataImporterConfiguration(): DataImporterConfigurationTransfer
    {
        return $this->buildImporterConfiguration(
            implode(DIRECTORY_SEPARATOR, [$this->getModuleDataImportDirectory(), 'punchout_catalog_connection_setup.csv']),
            static::IMPORT_TYPE_PUNCHOUT_CATALOG_SETUP
        );
    }

    /**
     * @return \Generated\Shared\Transfer\DataImporterConfigurationTransfer
     */
    public function getPunchoutCatalogCartDataImporterConfiguration(): DataImporterConfigurationTransfer
    {
        return $this->buildImporterConfiguration(
            implode(DIRECTORY_SEPARATOR, [$this->getModuleDataImportDirectory(), 'punchout_catalog_connection_cart.csv']),
            static::IMPORT_TYPE_PUNCHOUT_CATALOG_CART
        );
    }

    /**
     * @return string
     */
    protected function getModuleDataImportDirectory(): string
    {
        return implode(DIRECTORY_SEPARATOR, [
                $this->getModuleRoot(),
                'data',
                'import',
            ]) . DIRECTORY_SEPARATOR;
    }

    /**
     * @return string
     */
    protected function getModuleRoot(): string
    {
        $moduleRoot = realpath(
            __DIR__
            . DIRECTORY_SEPARATOR . '..'
            . DIRECTORY_SEPARATOR . '..'
            . DIRECTORY_SEPARATOR . '..'
            . DIRECTORY_SEPARATOR . '..'
        );

        return $moduleRoot . DIRECTORY_SEPARATOR;
    }
    
    /**
     * @return string
     */
    public function getDefaultLocale(): string
    {
        return static::PUNCHOUT_DEFAULT_LOCALE;
    }
    
    /**
     * @return string
     */
    public function getDefaultStoreLocale(): string
    {
        return static::PUNCHOUT_DEFAULT_STORE_LOCALE;
    }
    
    /**
     * @return string
     *
     * @throws \PunchoutCatalog\Zed\PunchoutCatalog\Exception\MissingZedUrlConfigurationException
     */
    public function getZedPunchoutUrl(): string
    {
        return $this->getBaseUrlZed() . static::PUNCHOUT_REQUEST_URL;
    }
    
    /**
     * @param string $storeName
     *
     * @return string
     */
    public function getBaseUrlYvesByStore(string $storeName): string
    {
        return $this->getBaseUrlYves()[$storeName];
    }

    /**
     * @throws \PunchoutCatalog\Zed\PunchoutCatalog\Exception\MissingYvesUrlConfigurationException
     *
     * @return string[]
     */
    protected function getBaseUrlYves(): array
    {
        throw new MissingYvesUrlConfigurationException(
            'Missing configuration! You need to configure Yves URL ' .
            'in your own PunchoutCatalogConfig::getBaseUrlYves() ' .
            'to be able to generate login URL with access token for remote systems.'
        );
    }

    /**
     * @return string
     *
     * @throws \PunchoutCatalog\Zed\PunchoutCatalog\Exception\MissingZedUrlConfigurationException
     */
    public function getBaseUrlZed(): string
    {
        if ($this->getConfig()->hasKey(ApplicationConstants::BASE_URL_ZED)) {
            return $this->getConfig()->get(ApplicationConstants::BASE_URL_ZED);
        }
        
        throw new MissingZedUrlConfigurationException(
            'Missing configuration! You need to configure Zed URL ' .
            'in your own PunchoutCatalogConfig::getBaseUrlZed() ' .
            'to be able to generate PunchOut URL for remote systems.'
        );
    }
}
