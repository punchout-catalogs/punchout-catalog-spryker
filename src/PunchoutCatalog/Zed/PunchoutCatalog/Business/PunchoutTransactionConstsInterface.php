<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace PunchoutCatalog\Zed\PunchoutCatalog\Business;

interface PunchoutTransactionConstsInterface
{
    // @todo move these to the related file
    public const TRANSACTION_TYPE_SETUP_REQUEST  = 'setup_request';
    // @todo move these to the related file
    public const TRANSACTION_TYPE_SETUP_RESPONSE = 'setup_response';
    // @todo move these to the related file
    public const TRANSACTION_TYPE_TRANSFER_TO_REQUISITION = 'transfer_to_requisition';
}
