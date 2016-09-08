<?php
/**
 * @author      MagePsycho <info@magepsycho.com>
 * @website     http://www.magepsycho.com
 * @category    Export / Import
 */
$mageFilename = 'app/Mage.php';
require_once $mageFilename;
Mage::setIsDeveloperMode(true);
ini_set('display_errors', 1);
umask(0);
Mage::app('admin');
Mage::register('isSecureArea', 1);
Mage::app()->setCurrentStore(Mage_Core_Model_App::ADMIN_STORE_ID);
 
set_time_limit(0);
ini_set('memory_limit','1024M');
 
/***************** UTILITY FUNCTIONS ********************/
function _getConnection($type = 'core_read'){
    return Mage::getSingleton('core/resource')->getConnection($type);
}
 
function _getTableName($tableName){
    return Mage::getSingleton('core/resource')->getTableName($tableName);
}
 
function _getAttributeId($attribute_code = 'price'){
    $connection = _getConnection('core_read');
    $sql = "SELECT attribute_id
                FROM " . _getTableName('eav_attribute') . "
            WHERE
                entity_type_id = ?
                AND attribute_code = ?";
    $entity_type_id = _getEntityTypeId();
    return $connection->fetchOne($sql, array($entity_type_id, $attribute_code));
}
 
function _getEntityTypeId($entity_type_code = 'catalog_product'){
    $connection = _getConnection('core_read');
    $sql        = "SELECT entity_type_id FROM " . _getTableName('eav_entity_type') . " WHERE entity_type_code = ?";
    return $connection->fetchOne($sql, array($entity_type_code));
}
 
function _getIdFromSku($sku){
    $connection = _getConnection('core_read');
    $sql        = "SELECT entity_id FROM " . _getTableName('catalog_product_entity') . " WHERE sku = ?";
    return $connection->fetchOne($sql, array($sku));
 
}
 
function _checkIfSkuExists($sku){
    $connection = _getConnection('core_read');
    $sql        = "SELECT COUNT(*) AS count_no FROM " . _getTableName('catalog_product_entity') . " WHERE sku = ?";
    $count      = $connection->fetchOne($sql, array($sku));
    if($count > 0){
        return true;
    }else{
        return false;
    }
}
 
function _updatePrices($data){
    $connection     = _getConnection('core_write');
    $sku            = $data[0];
    $newPrice       = $data[1];
    $productId      = _getIdFromSku($sku);
    $attributeId    = _getAttributeId();
 
    $sql = "UPDATE " . _getTableName('catalog_product_entity_decimal') . " cped
                SET  cped.value = ?
            WHERE  cped.attribute_id = ?
            AND cped.entity_id = ?";
    $connection->query($sql, array($newPrice, $attributeId, $productId));
}
/***************** UTILITY FUNCTIONS ********************/
 
$csv                = new Varien_File_Csv();
$data               = $csv->getData('prices.csv'); //path to csv
array_shift($data);
 
$message = '';
$count   = 1;
foreach($data as $_data){
    if(_checkIfSkuExists($_data[0])){
        try{
            _updatePrices($_data);
            $message .= $count . '> Success:: While Updating Price (' . $_data[1] . ') of Sku (' . $_data[0] . '). <br />';
 
        }catch(Exception $e){
            $message .=  $count .'> Error:: While Upating  Price (' . $_data[1] . ') of Sku (' . $_data[0] . ') => '.$e->getMessage().'<br />';
        }
    }else{
        $message .=  $count .'> Error:: Product with Sku (' . $_data[0] . ') does\'t exist.<br />';
    }
    $count++;
}
echo $message;