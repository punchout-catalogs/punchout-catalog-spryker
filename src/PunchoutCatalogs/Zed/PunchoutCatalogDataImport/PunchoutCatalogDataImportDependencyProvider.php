<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace PunchoutCatalogs\Zed\PunchoutCatalogDataImport;

use Spryker\Zed\DataImport\DataImportDependencyProvider;
use Spryker\Zed\Kernel\Container;
use PunchoutCatalogs\Zed\PunchoutCatalogDataImport\Dependency\Facade\PunchoutCatalogDataImportToVaultFacadeBridge;

/**
 * @method \PunchoutCatalogs\Zed\PunchoutCatalogDataImport\PunchoutCatalogDataImportConfig getConfig()
 */
class PunchoutCatalogDataImportDependencyProvider extends DataImportDependencyProvider
{
    public const FACADE_VAULT = 'FACADE_VAULT';

    /**
     * @param \Spryker\Zed\Kernel\Container $container
     *
     * @return \Spryker\Zed\Kernel\Container
     */
    public function provideBusinessLayerDependencies(Container $container)
    {
        $container = parent::provideBusinessLayerDependencies($container);
        $container = $this->addVaultFacade($container);

        return $container;
    }

    /**
     * @param \Spryker\Zed\Kernel\Container $container
     *
     * @return \Spryker\Zed\Kernel\Container
     */
    protected function addVaultFacade(Container $container): Container
    {
        $container[static::FACADE_VAULT] = function (Container $container) {
            return new PunchoutCatalogDataImportToVaultFacadeBridge();
        };

        return $container;
    }
}
