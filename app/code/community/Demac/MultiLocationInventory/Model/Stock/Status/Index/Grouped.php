<?php

/**
 * Class Demac_MultiLocationInventory_Model_Stock_Status_Index_Grouped
 */
class Demac_MultiLocationInventory_Model_Stock_Status_Index_Grouped
    implements Demac_MultiLocationInventory_Model_Stock_Status_Index_Interface
{
    /**
     * A select query to retrieve the stock status index data of grouped products.
     *
     * @param bool|array $productIds Product IDs to reindex, if a non-array is provided we reindex all products.
     *
     * @return string
     */
    public function getStockStatusIndexSelectQuery($productIds = false)
    {
        $stockTable                    = Mage::getModel('core/resource')->getTableName('demac_multilocationinventory/stock');
        $storesTable                   = Mage::getModel('core/resource')->getTableName('demac_multilocationinventory/stores');
        $locationsTable                = Mage::getModel('core/resource')->getTableName('demac_multilocationinventory/location');
        $coreCatalogProductEntityTable = Mage::getModel('core/resource')->getTableName('catalog/product');
        $coreCatalogProductlink        = Mage::getModel('core/resource')->getTableName('catalog/product_link');

        $query =
            '    SELECT'
            . '      stores.store_id as store_id,'
            . '      product_entity.entity_id as product_id,'
            . '      IF(SUM(IF(stock.is_in_stock = 1, stock.qty, 0)) AND SUM(stock.is_in_stock) > 0, 1, 0) as qty,'
            . '      IF(SUM(IF(stock.is_in_stock = 1, stock.qty, 0)) AND SUM(stock.is_in_stock) > 0, 1, 0) as is_in_stock,'
            . '      IF(SUM(stock.backorders) > 0, 1, 0) as backorders'
            . '    FROM ' . $coreCatalogProductEntityTable . ' as product_entity'
            . '    JOIN ' . $coreCatalogProductlink . ' as link'
            . '      ON product_entity.entity_id = link.product_id'
            . '    JOIN ' . $stockTable . ' AS stock'
            . '      ON link.linked_product_id = stock.product_id'
            . '    JOIN ' . $storesTable . ' as stores'
            . '      ON stock.location_id = stores.location_id'
            . '    JOIN ' . $locationsTable . ' as location'
            . '      ON stock.location_id = location.id'
            . '    WHERE'
            . '      location.status = 1'
            . '      AND product_entity.type_id = "grouped"';

        if(is_array($productIds)) {
            $query .= '      AND product_entity.entity_id IN (' . implode(',', $productIds) . ')';
        }

        $query .= '    GROUP BY CONCAT(stores.store_id, "_", product_entity.entity_id)';


        return $query;
    }


    /**
     * A select query to retrieve the global stock status index data of grouped products.
     *
     * @param bool|array $productIds Product IDs to reindex, if a non-array is provided we reindex all products.
     *
     * @return string
     */
    public function getGlobalStockStatusIndexSelectQuery($productIds = false)
    {
        $stockTable                    = Mage::getModel('core/resource')->getTableName('demac_multilocationinventory/stock');
        $locationsTable                = Mage::getModel('core/resource')->getTableName('demac_multilocationinventory/location');
        $coreCatalogProductEntityTable = Mage::getModel('core/resource')->getTableName('catalog/product');
        $coreCatalogProductlink        = Mage::getModel('core/resource')->getTableName('catalog/product_link');

        $query =
            '    SELECT'
            . '      0 as store_id,'
            . '      product_entity.entity_id as product_id,'
            . '      IF(SUM(IF(stock.is_in_stock = 1, stock.qty, 0)) AND SUM(stock.is_in_stock) > 0, 1, 0) as qty,'
            . '      IF(SUM(IF(stock.is_in_stock = 1, stock.qty, 0)) AND SUM(stock.is_in_stock) > 0, 1, 0) as is_in_stock,'
            . '      IF(SUM(stock.backorders) > 0, 1, 0) as backorders'
            . '    FROM ' . $coreCatalogProductEntityTable . ' as product_entity'
            . '    JOIN ' . $coreCatalogProductlink . ' as link'
            . '      ON product_entity.entity_id = link.product_id'
            . '    JOIN ' . $stockTable . ' AS stock'
            . '      ON link.linked_product_id = stock.product_id'
            . '    JOIN ' . $locationsTable . ' as location'
            . '      ON stock.location_id = location.id'
            . '    WHERE'
            . '      location.status = 1'
            . '      AND product_entity.type_id = "grouped"';

        if(is_array($productIds)) {
            $query .= '      AND product_entity.entity_id IN (' . implode(',', $productIds) . ')';
        }

        $query .= '    GROUP BY product_entity.entity_id';


        return $query;
    }
}