<?php

/**
 * Copyright © 2018-present PunchOut Catalogs LLC. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace PunchoutCatalog\Zed\PunchoutCatalog\Business\DataImport\DataSet;

interface PunchoutCatalogConnectionSetupDataSet
{
    public const CONNECTION_NAME = 'connection_name';

    public const COMPANY_BUSINESS_UNIT_KEY = 'business_unit_key';
    public const COMPANY_USER_KEY = 'company_user_key';
    public const SETUP_LOGIN_MODE = 'login_mode';
}
