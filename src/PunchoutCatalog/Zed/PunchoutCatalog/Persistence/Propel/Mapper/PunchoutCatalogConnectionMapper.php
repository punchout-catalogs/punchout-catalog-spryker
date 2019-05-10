<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace PunchoutCatalog\Zed\PunchoutCatalog\Persistence\Propel\Mapper;

use Generated\Shared\Transfer\PunchoutCatalogConnectionCartTransfer;
use Generated\Shared\Transfer\PunchoutCatalogConnectionSetupTransfer;
use Generated\Shared\Transfer\PunchoutCatalogConnectionTransfer;
use Orm\Zed\PunchoutCatalog\Persistence\PgwPunchoutCatalogConnectionCart;
use Orm\Zed\PunchoutCatalog\Persistence\PgwPunchoutCatalogConnection;
use Orm\Zed\PunchoutCatalog\Persistence\PgwPunchoutCatalogConnectionSetup;

class PunchoutCatalogConnectionMapper implements PunchoutCatalogConnectionMapperInterface
{
    /**
     * @param \Generated\Shared\Transfer\PunchoutCatalogConnectionTransfer $punchoutCatalogConnectionTransfer
     * @param \Orm\Zed\PunchoutCatalog\Persistence\PgwPunchoutCatalogConnection $pgwPunchoutCatalogConnection
     *
     * @return \Orm\Zed\PunchoutCatalog\Persistence\PgwPunchoutCatalogConnection
     */
    public function mapConnectionTransferToEntity(
        PunchoutCatalogConnectionTransfer $punchoutCatalogConnectionTransfer,
        PgwPunchoutCatalogConnection $pgwPunchoutCatalogConnection
    ): PgwPunchoutCatalogConnection {
        
        $pgwPunchoutCatalogConnection->fromArray(
            $punchoutCatalogConnectionTransfer->modifiedToArray(false)
        );
        
        return $pgwPunchoutCatalogConnection;
    }

    /**
     * @param \Orm\Zed\PunchoutCatalog\Persistence\PgwPunchoutCatalogConnection $pgwPunchoutCatalogConnection
     * @param \Generated\Shared\Transfer\PunchoutCatalogConnectionTransfer $punchoutCatalogConnectionTransfer
     *
     * @return \Generated\Shared\Transfer\PunchoutCatalogConnectionTransfer
     */
    public function mapEntityToConnectionTransfer(
        PgwPunchoutCatalogConnection $pgwPunchoutCatalogConnection,
        PunchoutCatalogConnectionTransfer $punchoutCatalogConnectionTransfer
    ): PunchoutCatalogConnectionTransfer {
        $punchoutCatalogConnectionTransfer = $punchoutCatalogConnectionTransfer->fromArray(
            $pgwPunchoutCatalogConnection->toArray(),
            true
        );

        $punchoutCatalogConnectionTransfer->setCart($this
            ->mapConnectionCartEntityToConnectionCartTransfer(
                $pgwPunchoutCatalogConnection->getPgwPunchoutCatalogConnectionCart(),
                new PunchoutCatalogConnectionCartTransfer()
            ));

        $punchoutCatalogConnectionTransfer->setSetup($this
            ->mapConnectionSetupEntityToConnectionSetupTransfer(
                $pgwPunchoutCatalogConnection->getPgwPunchoutCatalogConnectionSetup(),
                new PunchoutCatalogConnectionSetupTransfer()
            ));

        return $punchoutCatalogConnectionTransfer;
    }

    /**
     * @param \Orm\Zed\PunchoutCatalog\Persistence\PgwPunchoutCatalogConnectionCart $pgwPunchoutCatalogCart
     * @param \Generated\Shared\Transfer\PunchoutCatalogConnectionCartTransfer $punchoutCatalogConnectionCartTransfer
     *
     * @return \Generated\Shared\Transfer\PunchoutCatalogConnectionCartTransfer
     */
    public function mapConnectionCartEntityToConnectionCartTransfer(
        PgwPunchoutCatalogConnectionCart $pgwPunchoutCatalogCart,
        PunchoutCatalogConnectionCartTransfer $punchoutCatalogConnectionCartTransfer
    ): PunchoutCatalogConnectionCartTransfer {
        return $punchoutCatalogConnectionCartTransfer->fromArray(
            $pgwPunchoutCatalogCart->toArray(),
            true
        );
    }

    /**
     * @param \Orm\Zed\PunchoutCatalog\Persistence\PgwPunchoutCatalogConnectionSetup $pgwPunchoutCatalogSetup
     * @param \Generated\Shared\Transfer\PunchoutCatalogConnectionSetupTransfer $punchoutCatalogConnectionSetupTransfer
     *
     * @return \Generated\Shared\Transfer\PunchoutCatalogConnectionSetupTransfer
     */
    public function mapConnectionSetupEntityToConnectionSetupTransfer(
        PgwPunchoutCatalogConnectionSetup $pgwPunchoutCatalogSetup,
        PunchoutCatalogConnectionSetupTransfer $punchoutCatalogConnectionSetupTransfer
    ): PunchoutCatalogConnectionSetupTransfer {
        return $punchoutCatalogConnectionSetupTransfer->fromArray(
            $pgwPunchoutCatalogSetup->toArray(),
            true
        );
    }

    /**
     * @param \Generated\Shared\Transfer\PunchoutCatalogConnectionCartTransfer $punchoutCatalogConnectionCartTransfer
     * @param \Orm\Zed\PunchoutCatalog\Persistence\PgwPunchoutCatalogConnectionCart $pgwPunchoutCatalogCart
     *
     * @return \Orm\Zed\PunchoutCatalog\Persistence\PgwPunchoutCatalogConnectionCart
     */
    public function mapConnectionCartTransferToCartEntity(
        PunchoutCatalogConnectionCartTransfer $punchoutCatalogConnectionCartTransfer,
        PgwPunchoutCatalogConnectionCart $pgwPunchoutCatalogCart
    ): PgwPunchoutCatalogConnectionCart {
        $pgwPunchoutCatalogCart->fromArray(
            $punchoutCatalogConnectionCartTransfer->modifiedToArray(false)
        );
        return $pgwPunchoutCatalogCart;
    }

    /**
     * @param \Generated\Shared\Transfer\PunchoutCatalogConnectionSetupTransfer $punchoutCatalogConnectionSetupTransfer
     * @param \Orm\Zed\PunchoutCatalog\Persistence\PgwPunchoutCatalogConnectionSetup $pgwPunchoutCatalogSetup
     *
     * @return \Orm\Zed\PunchoutCatalog\Persistence\PgwPunchoutCatalogConnectionSetup
     */
    public function mapConnectionSetupTransferToSetupEntity(
        PunchoutCatalogConnectionSetupTransfer $punchoutCatalogConnectionSetupTransfer,
        PgwPunchoutCatalogConnectionSetup $pgwPunchoutCatalogSetup
    ): PgwPunchoutCatalogConnectionSetup {
        $pgwPunchoutCatalogSetup->fromArray(
            $punchoutCatalogConnectionSetupTransfer->modifiedToArray(false)
        );
        return $pgwPunchoutCatalogSetup;
    }
}
