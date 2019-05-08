<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace PunchoutCatalogs\Zed\PunchoutCatalogDataImport\Business\DataSet;

interface PunchoutCatalogCartDataSet
{
    public const CONNECTION_NAME = 'connection_name';

    public const CART_MAPPING_CART = 'mapping_cart';
    public const CART_DEFAULT_SUPPLIER_ID = 'default_supplier_id';
    public const CART_MAX_DESCRIPTION_LENGTH = 'max_description_length';
    public const CART_CART_ENCODING = 'cart_encoding';
}
