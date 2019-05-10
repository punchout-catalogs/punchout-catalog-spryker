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

interface PunchoutCatalogConnectionMapperInterface
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
    ): PgwPunchoutCatalogConnection;

    /**
     * @param \Orm\Zed\PunchoutCatalog\Persistence\PgwPunchoutCatalogConnection $spyPunchoutCatalogConnection
     * @param \Generated\Shared\Transfer\PunchoutCatalogConnectionTransfer $punchoutCatalogConnectionTransfer
     *
     * @return \Generated\Shared\Transfer\PunchoutCatalogConnectionTransfer
     */
    public function mapEntityToConnectionTransfer(
        PgwPunchoutCatalogConnection $spyPunchoutCatalogConnection,
        PunchoutCatalogConnectionTransfer $punchoutCatalogConnectionTransfer
    ): PunchoutCatalogConnectionTransfer;

    /**
     * @param \Generated\Shared\Transfer\PunchoutCatalogConnectionCartTransfer $punchoutCatalogConnectionCartTransfer
     * @param \Orm\Zed\PunchoutCatalog\Persistence\PgwPunchoutCatalogConnectionCart $spyPunchoutCatalogCart
     *
     * @return \Orm\Zed\PunchoutCatalog\Persistence\PgwPunchoutCatalogConnectionCart
     */
    public function mapConnectionCartTransferToCartEntity(
        PunchoutCatalogConnectionCartTransfer $punchoutCatalogConnectionCartTransfer,
        PgwPunchoutCatalogConnectionCart $spyPunchoutCatalogCart
    ): PgwPunchoutCatalogConnectionCart;

    /**
     * @param \Orm\Zed\PunchoutCatalog\Persistence\PgwPunchoutCatalogConnectionCart $spyPunchoutCatalogCart
     * @param \Generated\Shared\Transfer\PunchoutCatalogConnectionCartTransfer $punchoutCatalogConnectionCartTransfer
     *
     * @return \Generated\Shared\Transfer\PunchoutCatalogConnectionCartTransfer
     */
    public function mapConnectionCartEntityToConnectionCartTransfer(
        PgwPunchoutCatalogConnectionCart $spyPunchoutCatalogCart,
        PunchoutCatalogConnectionCartTransfer $punchoutCatalogConnectionCartTransfer
    ): PunchoutCatalogConnectionCartTransfer;

    /**
     * @param \Generated\Shared\Transfer\PunchoutCatalogConnectionSetupTransfer $punchoutCatalogConnectionSetupTransfer
     * @param \Orm\Zed\PunchoutCatalog\Persistence\PgwPunchoutCatalogConnectionSetup $spyPunchoutCatalogSetup
     *
     * @return \Orm\Zed\PunchoutCatalog\Persistence\PgwPunchoutCatalogConnectionSetup
     */
    public function mapConnectionSetupTransferToSetupEntity(
        PunchoutCatalogConnectionSetupTransfer $punchoutCatalogConnectionSetupTransfer,
        PgwPunchoutCatalogConnectionSetup $spyPunchoutCatalogSetup
    ): PgwPunchoutCatalogConnectionSetup;

    /**
     * @param \Orm\Zed\PunchoutCatalog\Persistence\PgwPunchoutCatalogConnectionSetup $spyPunchoutCatalogSetup
     * @param \Generated\Shared\Transfer\PunchoutCatalogConnectionSetupTransfer $punchoutCatalogConnectionSetupTransfer
     *
     * @return \Generated\Shared\Transfer\PunchoutCatalogConnectionSetupTransfer
     */
    public function mapConnectionSetupEntityToConnectionSetupTransfer(
        PgwPunchoutCatalogConnectionSetup $spyPunchoutCatalogSetup,
        PunchoutCatalogConnectionSetupTransfer $punchoutCatalogConnectionSetupTransfer
    ): PunchoutCatalogConnectionSetupTransfer;
}
