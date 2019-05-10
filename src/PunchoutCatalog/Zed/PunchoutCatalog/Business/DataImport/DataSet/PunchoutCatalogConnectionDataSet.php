<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace PunchoutCatalog\Zed\PunchoutCatalog\Business\DataImport\DataSet;

interface PunchoutCatalogConnectionDataSet
{
    public const COMPANY_BUSINESS_UNIT_KEY = 'business_unit_key';
    public const CONNECTION_USERNAME = 'username';
    public const CONNECTION_PASSWORD = 'password';
    public const CONNECTION_CREDENTIALS = 'credentials';
    public const CONNECTION_IS_ACTIVE = 'is_active';
    public const CONNECTION_TYPE = 'type';
    public const CONNECTION_FORMAT = 'format';
    public const CONNECTION_MAPPING = 'mapping';
    public const CONNECTION_NAME = 'name';
}
