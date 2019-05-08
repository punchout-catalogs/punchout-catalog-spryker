<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace PunchoutCatalogs\Zed\PunchoutCatalog\Persistence\Propel\Mapper;

use Generated\Shared\Transfer\PunchoutCatalogConnectionCartTransfer;
use Generated\Shared\Transfer\PunchoutCatalogConnectionSetupTransfer;
use Generated\Shared\Transfer\PunchoutCatalogConnectionTransfer;
use Orm\Zed\PunchoutCatalog\Persistence\PgwPunchoutCatalogCart;
use Orm\Zed\PunchoutCatalog\Persistence\PgwPunchoutCatalogConnection;
use Orm\Zed\PunchoutCatalog\Persistence\PgwPunchoutCatalogSetup;

class PunchoutCatalogConnectionMapper implements PunchoutCatalogConnectionMapperInterface
{
    /**
     * @param \Generated\Shared\Transfer\PunchoutCatalogConnectionTransfer $punchoutCatalogConnectionTransfer
     * @param \Orm\Zed\PunchoutCatalog\Persistence\PgwPunchoutCatalogConnection $spyPunchoutCatalogConnection
     *
     * @return \Orm\Zed\PunchoutCatalog\Persistence\PgwPunchoutCatalogConnection
     */
    public function mapConnectionTransferToEntity(
        PunchoutCatalogConnectionTransfer $punchoutCatalogConnectionTransfer,
        PgwPunchoutCatalogConnection $spyPunchoutCatalogConnection
    ): PgwPunchoutCatalogConnection {
        $spyPunchoutCatalogConnection->fromArray(
            $punchoutCatalogConnectionTransfer->modifiedToArray(false)
        );
        return $spyPunchoutCatalogConnection;
    }

    /**
     * @param \Orm\Zed\PunchoutCatalog\Persistence\PgwPunchoutCatalogConnection $spyPunchoutCatalogConnection
     * @param \Generated\Shared\Transfer\PunchoutCatalogConnectionTransfer $punchoutCatalogConnectionTransfer
     *
     * @return \Generated\Shared\Transfer\PunchoutCatalogConnectionTransfer
     */
    public function mapEntityToConnectionTransfer(
        PgwPunchoutCatalogConnection $spyPunchoutCatalogConnection,
        PunchoutCatalogConnectionTransfer $punchoutCatalogConnectionTransfer
    ): PunchoutCatalogConnectionTransfer {
        $punchoutCatalogConnectionTransfer = $punchoutCatalogConnectionTransfer->fromArray(
            $spyPunchoutCatalogConnection->toArray(),
            true
        );

        $punchoutCatalogConnectionTransfer->setCart($this
            ->mapCartEntityToConnectionCartTransfer(
                $spyPunchoutCatalogConnection->getPgwPunchoutCatalogCart(),
                new PunchoutCatalogConnectionCartTransfer()
            ));

        $punchoutCatalogConnectionTransfer->setSetup($this
            ->mapSetupEntityToConnectionSetupTransfer(
                $spyPunchoutCatalogConnection->getPgwPunchoutCatalogSetup(),
                new PunchoutCatalogConnectionSetupTransfer()
            ));

        return $punchoutCatalogConnectionTransfer;
    }

    /**
     * @param \Orm\Zed\PunchoutCatalog\Persistence\PgwPunchoutCatalogCart $spyPunchoutCatalogCart
     * @param \Generated\Shared\Transfer\PunchoutCatalogConnectionCartTransfer $punchoutCatalogConnectionCartTransfer
     *
     * @return \Generated\Shared\Transfer\PunchoutCatalogConnectionCartTransfer
     */
    public function mapCartEntityToConnectionCartTransfer(
        PgwPunchoutCatalogCart $spyPunchoutCatalogCart,
        PunchoutCatalogConnectionCartTransfer $punchoutCatalogConnectionCartTransfer
    ): PunchoutCatalogConnectionCartTransfer {
        return $punchoutCatalogConnectionCartTransfer->fromArray(
            $spyPunchoutCatalogCart->toArray(),
            true
        );
    }

    /**
     * @param \Orm\Zed\PunchoutCatalog\Persistence\PgwPunchoutCatalogSetup $spyPunchoutCatalogSetup
     * @param \Generated\Shared\Transfer\PunchoutCatalogConnectionSetupTransfer $punchoutCatalogConnectionSetupTransfer
     *
     * @return \Generated\Shared\Transfer\PunchoutCatalogConnectionSetupTransfer
     */
    public function mapSetupEntityToConnectionSetupTransfer(
        PgwPunchoutCatalogSetup $spyPunchoutCatalogSetup,
        PunchoutCatalogConnectionSetupTransfer $punchoutCatalogConnectionSetupTransfer
    ): PunchoutCatalogConnectionSetupTransfer {
        return $punchoutCatalogConnectionSetupTransfer->fromArray(
            $spyPunchoutCatalogSetup->toArray(),
            true
        );
    }

    /**
     * @param \Generated\Shared\Transfer\PunchoutCatalogConnectionCartTransfer $punchoutCatalogConnectionCartTransfer
     * @param \Orm\Zed\PunchoutCatalog\Persistence\PgwPunchoutCatalogCart $spyPunchoutCatalogCart
     *
     * @return \Orm\Zed\PunchoutCatalog\Persistence\PgwPunchoutCatalogCart
     */
    public function mapConnectionCartTransferToCartEntity(
        PunchoutCatalogConnectionCartTransfer $punchoutCatalogConnectionCartTransfer,
        PgwPunchoutCatalogCart $spyPunchoutCatalogCart
    ): PgwPunchoutCatalogCart {
        $spyPunchoutCatalogCart->fromArray(
            $punchoutCatalogConnectionCartTransfer->modifiedToArray(false)
        );
        return $spyPunchoutCatalogCart;
    }

    /**
     * @param \Generated\Shared\Transfer\PunchoutCatalogConnectionSetupTransfer $punchoutCatalogConnectionSetupTransfer
     * @param \Orm\Zed\PunchoutCatalog\Persistence\PgwPunchoutCatalogSetup $spyPunchoutCatalogSetup
     *
     * @return \Orm\Zed\PunchoutCatalog\Persistence\PgwPunchoutCatalogSetup
     */
    public function mapConnectionSetupTransferToSetupEntity(
        PunchoutCatalogConnectionSetupTransfer $punchoutCatalogConnectionSetupTransfer,
        PgwPunchoutCatalogSetup $spyPunchoutCatalogSetup
    ): PgwPunchoutCatalogSetup {
        $spyPunchoutCatalogSetup->fromArray(
            $punchoutCatalogConnectionSetupTransfer->modifiedToArray(false)
        );
        return $spyPunchoutCatalogSetup;
    }
}
