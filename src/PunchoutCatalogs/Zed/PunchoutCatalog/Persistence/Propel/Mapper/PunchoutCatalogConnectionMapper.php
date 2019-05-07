<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace PunchoutCatalogs\Zed\PunchoutCatalog\Persistence\Propel\Mapper;

use Generated\Shared\Transfer\PunchoutCatalogConnectionCartTransfer;
use Generated\Shared\Transfer\PunchoutCatalogConnectionSetupTransfer;
use Generated\Shared\Transfer\PunchoutCatalogConnectionTransfer;
use Orm\Zed\PunchoutCatalog\Persistence\EcoPunchoutCatalogCart;
use Orm\Zed\PunchoutCatalog\Persistence\EcoPunchoutCatalogConnection;
use Orm\Zed\PunchoutCatalog\Persistence\EcoPunchoutCatalogSetup;

class PunchoutCatalogConnectionMapper implements PunchoutCatalogConnectionMapperInterface
{
    /**
     * @param \Generated\Shared\Transfer\PunchoutCatalogConnectionTransfer $punchoutCatalogConnectionTransfer
     * @param \Orm\Zed\PunchoutCatalog\Persistence\EcoPunchoutCatalogConnection $spyPunchoutCatalogConnection
     *
     * @return \Orm\Zed\PunchoutCatalog\Persistence\EcoPunchoutCatalogConnection
     */
    public function mapConnectionTransferToEntity(
        PunchoutCatalogConnectionTransfer $punchoutCatalogConnectionTransfer,
        EcoPunchoutCatalogConnection $spyPunchoutCatalogConnection
    ): EcoPunchoutCatalogConnection {
        $spyPunchoutCatalogConnection->fromArray(
            $punchoutCatalogConnectionTransfer->modifiedToArray(false)
        );
        return $spyPunchoutCatalogConnection;
    }

    /**
     * @param \Orm\Zed\PunchoutCatalog\Persistence\EcoPunchoutCatalogConnection $spyPunchoutCatalogConnection
     * @param \Generated\Shared\Transfer\PunchoutCatalogConnectionTransfer $punchoutCatalogConnectionTransfer
     *
     * @return \Generated\Shared\Transfer\PunchoutCatalogConnectionTransfer
     */
    public function mapEntityToConnectionTransfer(
        EcoPunchoutCatalogConnection $spyPunchoutCatalogConnection,
        PunchoutCatalogConnectionTransfer $punchoutCatalogConnectionTransfer
    ): PunchoutCatalogConnectionTransfer {
        $punchoutCatalogConnectionTransfer = $punchoutCatalogConnectionTransfer->fromArray(
            $spyPunchoutCatalogConnection->toArray(),
            true
        );

        $punchoutCatalogConnectionTransfer->setCart($this
            ->mapCartEntityToConnectionCartTransfer(
                $spyPunchoutCatalogConnection->getEcoPunchoutCatalogCart(),
                new PunchoutCatalogConnectionCartTransfer()
            ));

        $punchoutCatalogConnectionTransfer->setSetup($this
            ->mapSetupEntityToConnectionSetupTransfer(
                $spyPunchoutCatalogConnection->getEcoPunchoutCatalogSetup(),
                new PunchoutCatalogConnectionSetupTransfer()
            ));

        return $punchoutCatalogConnectionTransfer;
    }

    /**
     * @param \Orm\Zed\PunchoutCatalog\Persistence\EcoPunchoutCatalogCart $spyPunchoutCatalogCart
     * @param \Generated\Shared\Transfer\PunchoutCatalogConnectionCartTransfer $punchoutCatalogConnectionCartTransfer
     *
     * @return \Generated\Shared\Transfer\PunchoutCatalogConnectionCartTransfer
     */
    public function mapCartEntityToConnectionCartTransfer(
        EcoPunchoutCatalogCart $spyPunchoutCatalogCart,
        PunchoutCatalogConnectionCartTransfer $punchoutCatalogConnectionCartTransfer
    ): PunchoutCatalogConnectionCartTransfer {
        return $punchoutCatalogConnectionCartTransfer->fromArray(
            $spyPunchoutCatalogCart->toArray(),
            true
        );
    }

    /**
     * @param \Orm\Zed\PunchoutCatalog\Persistence\EcoPunchoutCatalogSetup $spyPunchoutCatalogSetup
     * @param \Generated\Shared\Transfer\PunchoutCatalogConnectionSetupTransfer $punchoutCatalogConnectionSetupTransfer
     *
     * @return \Generated\Shared\Transfer\PunchoutCatalogConnectionSetupTransfer
     */
    public function mapSetupEntityToConnectionSetupTransfer(
        EcoPunchoutCatalogSetup $spyPunchoutCatalogSetup,
        PunchoutCatalogConnectionSetupTransfer $punchoutCatalogConnectionSetupTransfer
    ): PunchoutCatalogConnectionSetupTransfer {
        return $punchoutCatalogConnectionSetupTransfer->fromArray(
            $spyPunchoutCatalogSetup->toArray(),
            true
        );
    }

    /**
     * @param \Generated\Shared\Transfer\PunchoutCatalogConnectionCartTransfer $punchoutCatalogConnectionCartTransfer
     * @param \Orm\Zed\PunchoutCatalog\Persistence\EcoPunchoutCatalogCart $spyPunchoutCatalogCart
     *
     * @return \Orm\Zed\PunchoutCatalog\Persistence\EcoPunchoutCatalogCart
     */
    public function mapConnectionCartTransferToCartEntity(
        PunchoutCatalogConnectionCartTransfer $punchoutCatalogConnectionCartTransfer,
        EcoPunchoutCatalogCart $spyPunchoutCatalogCart
    ): EcoPunchoutCatalogCart {
        $spyPunchoutCatalogCart->fromArray(
            $punchoutCatalogConnectionCartTransfer->modifiedToArray(false)
        );
        return $spyPunchoutCatalogCart;
    }

    /**
     * @param \Generated\Shared\Transfer\PunchoutCatalogConnectionSetupTransfer $punchoutCatalogConnectionSetupTransfer
     * @param \Orm\Zed\PunchoutCatalog\Persistence\EcoPunchoutCatalogSetup $spyPunchoutCatalogSetup
     *
     * @return \Orm\Zed\PunchoutCatalog\Persistence\EcoPunchoutCatalogSetup
     */
    public function mapConnectionSetupTransferToSetupEntity(
        PunchoutCatalogConnectionSetupTransfer $punchoutCatalogConnectionSetupTransfer,
        EcoPunchoutCatalogSetup $spyPunchoutCatalogSetup
    ): EcoPunchoutCatalogSetup {
        $spyPunchoutCatalogSetup->fromArray(
            $punchoutCatalogConnectionSetupTransfer->modifiedToArray(false)
        );
        return $spyPunchoutCatalogSetup;
    }
}
