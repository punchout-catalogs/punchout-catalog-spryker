<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace PunchoutCatalogs\Zed\PunchoutCatalog\Business;

interface PunchoutTransactionConstsInterface
{
    public const TRANSACTION_TYPE_SETUP_REQUEST = 'SETUP_REQUEST';
    public const TRANSACTION_TYPE_SETUP_RESPONSE = 'SETUP_RESPONSE';
    public const TRANSACTION_TYPE_ORDER_REQUEST  = 'ORDER_REQUEST';
    public const TRANSACTION_TYPE_ORDER_RESPONSE  = 'ORDER_RESPONSE';
    public const TRANSACTION_TYPE_TRANSFER_TO_REQUISITION  = 'TRANSFER_TO_REQUISITION';

    public const STATUS_SUCCESS = 'SUCCESS';
    public const STATUS_FAILURE = 'FAILURE';
}
