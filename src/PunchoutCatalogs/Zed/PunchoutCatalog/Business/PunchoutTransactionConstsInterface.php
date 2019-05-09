<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace PunchoutCatalogs\Zed\PunchoutCatalog\Business;

interface PunchoutTransactionConstsInterface
{
    public const TRANSACTION_TYPE_SETUP_REQUEST  = 'setup_request';
    public const TRANSACTION_TYPE_SETUP_RESPONSE = 'setup_response';
    public const TRANSACTION_TYPE_TRANSFER_TO_REQUISITION = 'transfer_to_requisition';

    public const STATUS_SUCCESS = 'success';
    public const STATUS_FAILURE = 'failure';
}
