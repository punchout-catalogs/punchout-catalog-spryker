<?php

/**
 * Copyright © 2018-present PunchOut Catalogs LLC. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace PunchoutCatalog\Zed\PunchoutCatalog\Persistence\Propel\Mapper;

use Generated\Shared\Transfer\PunchoutCatalogConnectionCartTransfer;
use Generated\Shared\Transfer\PunchoutCatalogConnectionSetupTransfer;
use Generated\Shared\Transfer\PunchoutCatalogConnectionTransfer;
use Orm\Zed\PunchoutCatalog\Persistence\PgwPunchoutCatalogConnection;
use Orm\Zed\PunchoutCatalog\Persistence\PgwPunchoutCatalogConnectionCart;
use Orm\Zed\PunchoutCatalog\Persistence\PgwPunchoutCatalogConnectionSetup;

interface PunchoutCatalogConnectionMapperInterface
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
    ): PgwPunchoutCatalogConnection;

    /**
     * @param \Orm\Zed\PunchoutCatalog\Persistence\PgwPunchoutCatalogConnection $pgwPunchoutCatalogConnection
     * @param \Generated\Shared\Transfer\PunchoutCatalogConnectionTransfer $punchoutCatalogConnectionTransfer
     *
     * @return \Generated\Shared\Transfer\PunchoutCatalogConnectionTransfer
     */
    public function mapEntityToConnectionTransfer(
        PgwPunchoutCatalogConnection $pgwPunchoutCatalogConnection,
        PunchoutCatalogConnectionTransfer $punchoutCatalogConnectionTransfer
    ): PunchoutCatalogConnectionTransfer;

    /**
     * @param \Generated\Shared\Transfer\PunchoutCatalogConnectionCartTransfer $punchoutCatalogConnectionCartTransfer
     * @param \Orm\Zed\PunchoutCatalog\Persistence\PgwPunchoutCatalogConnectionCart $pgwPunchoutCatalogCart
     *
     * @return \Orm\Zed\PunchoutCatalog\Persistence\PgwPunchoutCatalogConnectionCart
     */
    public function mapConnectionCartTransferToCartEntity(
        PunchoutCatalogConnectionCartTransfer $punchoutCatalogConnectionCartTransfer,
        PgwPunchoutCatalogConnectionCart $pgwPunchoutCatalogCart
    ): PgwPunchoutCatalogConnectionCart;

    /**
     * @param \Orm\Zed\PunchoutCatalog\Persistence\PgwPunchoutCatalogConnectionCart $pgwPunchoutCatalogCart
     * @param \Generated\Shared\Transfer\PunchoutCatalogConnectionCartTransfer $punchoutCatalogConnectionCartTransfer
     *
     * @return \Generated\Shared\Transfer\PunchoutCatalogConnectionCartTransfer
     */
    public function mapConnectionCartEntityToConnectionCartTransfer(
        PgwPunchoutCatalogConnectionCart $pgwPunchoutCatalogCart,
        PunchoutCatalogConnectionCartTransfer $punchoutCatalogConnectionCartTransfer
    ): PunchoutCatalogConnectionCartTransfer;

    /**
     * @param \Generated\Shared\Transfer\PunchoutCatalogConnectionSetupTransfer $punchoutCatalogConnectionSetupTransfer
     * @param \Orm\Zed\PunchoutCatalog\Persistence\PgwPunchoutCatalogConnectionSetup $pgwPunchoutCatalogSetup
     *
     * @return \Orm\Zed\PunchoutCatalog\Persistence\PgwPunchoutCatalogConnectionSetup
     */
    public function mapConnectionSetupTransferToSetupEntity(
        PunchoutCatalogConnectionSetupTransfer $punchoutCatalogConnectionSetupTransfer,
        PgwPunchoutCatalogConnectionSetup $pgwPunchoutCatalogSetup
    ): PgwPunchoutCatalogConnectionSetup;

    /**
     * @param \Orm\Zed\PunchoutCatalog\Persistence\PgwPunchoutCatalogConnectionSetup $pgwPunchoutCatalogSetup
     * @param \Generated\Shared\Transfer\PunchoutCatalogConnectionSetupTransfer $punchoutCatalogConnectionSetupTransfer
     *
     * @return \Generated\Shared\Transfer\PunchoutCatalogConnectionSetupTransfer
     */
    public function mapConnectionSetupEntityToConnectionSetupTransfer(
        PgwPunchoutCatalogConnectionSetup $pgwPunchoutCatalogSetup,
        PunchoutCatalogConnectionSetupTransfer $punchoutCatalogConnectionSetupTransfer
    ): PunchoutCatalogConnectionSetupTransfer;
}
