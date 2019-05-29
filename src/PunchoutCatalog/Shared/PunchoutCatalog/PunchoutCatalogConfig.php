<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace PunchoutCatalog\Shared\PunchoutCatalog;

use Generated\Shared\Transfer\ChartLayoutTransfer;
use Spryker\Shared\Kernel\AbstractSharedConfig;

class PunchoutCatalogConfig extends AbstractSharedConfig
{
    public const VAULT_TYPE_PUNCHOUT_CATALOG_CONNECTION_PASSWORD = 'punchout-catalog-connection-password';

    protected const CHART_TYPE_BAR = 'bar';
    protected const CHART_TYPE_PIE = 'pie';
    protected const CHART_TYPE_LINE = 'scatter';

    /**
     * @deprecated Will be removed without replacement.
     *
     * @return string[]
     */
    public function getChartTypes(): array
    {
        return [
            static::CHART_TYPE_BAR,
            static::CHART_TYPE_PIE,
            static::CHART_TYPE_LINE,
        ];
    }

    /**
     * @deprecated Will be removed without replacement.
     *
     * @return string
     */
    public function getDefaultChartType(): string
    {
        return static::CHART_TYPE_BAR;
    }

    /**
     * @deprecated Will be removed without replacement.
     *
     * @return \Generated\Shared\Transfer\ChartLayoutTransfer
     */
    public function getDefaultChartLayout(): ChartLayoutTransfer
    {
        return new ChartLayoutTransfer();
    }
}
