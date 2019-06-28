<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace PunchoutCatalog\Zed\PunchoutCatalog\Business\DataImport\DataSet;

interface PunchoutCatalogConnectionCartDataSet
{
    public const NAME = 'connection_name';
    public const DEFAULT_SUPPLIER_ID = 'default_supplier_id';
    public const MAX_DESCRIPTION_LENGTH = 'max_description_length';
    public const BUNDLE_MODE = 'bundle_mode';
    public const TOTALS_MODE = 'totals_mode';
    public const ENCODING = 'encoding';
    public const MAPPING = 'mapping';
}
