<?php
/**
 * Created by PhpStorm.
 * User: Chamal
 * Date: 2/25/16
 * Time: 7:04 PM
 */

if(isset($_GET['file']) && !empty($_GET['file'])){

    $file = fopen('uploads/'.$_GET['file'], 'r');
    $gender = array('Men', 'Women', 'Children');
    $colours = array('Natural White', 'Sage', 'Blue', 'Dapple Grey', 'Peach');
    $sizes = array('XS', 'S', 'M', 'L', 'XL', 'XXL', 'XXXL', '3-4', '5-6', '7-8', '9-10', '11-12');

    $simples = array('sku::store_view_code::attribute_set_code::product_type::categories::product_websites::name::description::short_description::weight::product_online::tax_class_name::visibility::price::special_price::special_price_from_date::special_price_to_date::url_key::meta_title::meta_keywords::meta_description::base_image::base_image_label::small_image::small_image_label::thumbnail_image::thumbnail_image_label::swatch_image::swatch_image_label::created_at::updated_at::new_from_date::new_to_date::display_product_options_in::map_price::msrp_price::map_enabled::gift_message_available::custom_design::custom_design_from::custom_design_to::custom_layout_update::page_layout::product_options_container::msrp_display_actual_price_type::country_of_manufacture::additional_attributes::qty::out_of_stock_qty::use_config_min_qty::is_qty_decimal::allow_backorders::use_config_backorders::min_cart_qty::use_config_min_sale_qty::max_cart_qty::use_config_max_sale_qty::is_in_stock::notify_on_stock_below::use_config_notify_stock_qty::manage_stock::use_config_manage_stock::use_config_qty_increments::qty_increments::use_config_enable_qty_inc::enable_qty_increments::is_decimal_divided::website_id::related_skus::crosssell_skus::upsell_skus::additional_images::additional_image_labels::hide_from_product_page::bundle_price_type::bundle_sku_type::bundle_price_view::bundle_weight_type::bundle_values::configurable_variations::configurable_variation_labels::associated_skus');

    $configurable_variations = array();
    $weight = '1';
    $price = '19';

    $file_name = '';

    while (($product = fgetcsv($file)) !== FALSE) {

        $file_name = $product[0];
        $sku = $product[0];
        $name = trim($product[6]);
        $category = $product[4];
        $rest = str_replace('"',"'",$product[7]);
        $description = trim(strip_tags($rest,'<p><br>'));
        $online = $product[10];


        foreach ($gender as $gen) {
            foreach ($colours as $colour) {
                foreach ($sizes as $size) {
                    if ($gen === 'Children' && ($colour === 'Natural White' || $colour === 'Dapple Grey')) { // children
                        if ($size === '3-4' || $size === '5-6' || $size === '7-8' || $size === '9-10' || $size === '11-12') {

                            $simple_sku = $sku . '-' . $gen . '-' . $colour . '-' . $size;
                            $additional_attributes = 'admire_color=' . $colour . ',admire_gender=' . $gen . ',admire_size=' . $size;
                            $image = '/shirts/' . str_replace(' ', '-', trim(strtolower($colour))) . '.png';

                            $additional_attributes_artwork = 'admire_color=' . $colour . ',admire_gender=' . $gen . ',admire_size=' . $size . ',admire_sticker=/artworks/' . $product[0] . '.png';

                            $configurable_variations[] = 'sku=' . $simple_sku . ',' . $additional_attributes;

                            $simples[] = $simple_sku . '::'
                                . '::'
                                . 'Default::'
                                . 'simple::'
                                . $category . '::'
                                . 'base::'
                                . $simple_sku . '::' // name
                                . $description . '::'
                                . '::'
                                . $weight . '::'
                                . $online . '::'
                                . 'Taxable Goods::'
                                . 'Not Visible Individually::'
                                . $price . '::'
                                . '::'
                                . '::'
                                . '::'
                                . str_replace(' ', '-', strtolower($simple_sku)) . '::' // url key
                                . $name . '::'
                                . $name . '::'
                                . '::'
                                . $image . '::'
                                . '::'
                                . $image . '::'
                                . '::'
                                . $image . '::'
                                . '::'
                                . $image . '::'
                                . '::'
                                . '::'
                                . '::'
                                . '::'
                                . '::'
                                . 'Block after Info Column::'
                                . '::'
                                . '::'
                                . '::'
                                . '::'
                                . '::'
                                . '::'
                                . '::'
                                . '::'
                                . '::'
                                . '::'
                                . '::'
                                . '::'
                                . trim($additional_attributes_artwork) . '::' // additional attributes
                                . '::' // qty
                                . '0::'
                                . '1::'
                                . '0::'
                                . '0::'
                                . '1::'
                                . '1::'
                                . '1::'
                                . '0::'
                                . '1::'
                                . '1::'
                                . '::'
                                . '1::'
                                . '0::'
                                . '0::'
                                . '1::'
                                . '1::'
                                . '0::'
                                . '0::'
                                . '0::'
                                . '1::'
                                . '::'
                                . '::'
                                . '::'
                                . $image . '::'
                                . '::'
                                . '::'
                                . '::'
                                . '::'
                                . '::'
                                . '::'
                                . '::'
                                . '::'
                                . '::'
                                . '::';
                        }
                    }
                    if ($gen == 'Men') { // Men
                        if ($size === 'S' || $size === 'M' || $size === 'L' || $size === 'XL' || $size === 'XXL' || $size === 'XXXL') {
                            $simple_sku = $sku . '-' . $gen . '-' . $colour . '-' . $size;
                            $additional_attributes = 'admire_color=' . $colour . ',admire_gender=' . $gen . ',admire_size=' . $size;
                            $image = '/shirts/' . str_replace(' ', '-', trim(strtolower($colour))) . '.png';

                            $additional_attributes_artwork = 'admire_color=' . $colour . ',admire_gender=' . $gen . ',admire_size=' . $size . ',admire_sticker=/artworks/' . $product[0] . '.png';


                            $configurable_variations[] = 'sku=' . $simple_sku . ',' . $additional_attributes;

                            $simples[] = $simple_sku . '::'
                                . '::'
                                . 'Default::'
                                . 'simple::'
                                . $category . '::'
                                . 'base::'
                                . $simple_sku . '::'
                                . $description . '::'
                                . '::'
                                . $weight . '::'
                                . $online . '::'
                                . 'Taxable Goods::'
                                . 'Not Visible Individually::'
                                . $price . '::'
                                . '::'
                                . '::'
                                . '::'
                                . str_replace(' ', '-', strtolower($simple_sku)) . '::' // url key
                                . $name . '::'
                                . $name . '::'
                                . '::'
                                . $image . '::'
                                . '::'
                                . $image . '::'
                                . '::'
                                . $image . '::'
                                . '::'
                                . $image . '::'
                                . '::'
                                . '::'
                                . '::'
                                . '::'
                                . '::'
                                . 'Block after Info Column::'
                                . '::'
                                . '::'
                                . '::'
                                . '::'
                                . '::'
                                . '::'
                                . '::'
                                . '::'
                                . '::'
                                . '::'
                                . '::'
                                . '::'
                                . trim($additional_attributes_artwork) . '::'
                                . '::' // qty
                                . '0::'
                                . '1::'
                                . '0::'
                                . '0::'
                                . '1::'
                                . '1::'
                                . '1::'
                                . '0::'
                                . '1::'
                                . '1::'
                                . '::'
                                . '1::'
                                . '0::'
                                . '0::'
                                . '1::'
                                . '1::'
                                . '0::'
                                . '0::'
                                . '0::'
                                . '1::'
                                . '::'
                                . '::'
                                . '::'
                                . $image . '::'
                                . '::'
                                . '::'
                                . '::'
                                . '::'
                                . '::'
                                . '::'
                                . '::'
                                . '::'
                                . '::'
                                . '::';
                        }
                    }
                    if ($gen == 'Women') { // Women
                        if ($size === 'XS' || $size === 'S' || $size === 'M' || $size === 'L' || $size === 'XL' || $size === 'XXL') {
                            $simple_sku = $sku . '-' . $gen . '-' . $colour . '-' . $size;
                            $additional_attributes = 'admire_color=' . $colour . ',admire_gender=' . $gen . ',admire_size=' . $size;
                            $image = '/shirts/ladies-' . str_replace(' ', '-', trim(strtolower($colour))) . '.png';

                            $additional_attributes_artwork = 'admire_color=' . $colour . ',admire_gender=' . $gen . ',admire_size=' . $size . ',admire_sticker=/artworks/' . $product[0] . '.png';

                            $configurable_variations[] = 'sku=' . $simple_sku . ',' . $additional_attributes;

                            $simples[] = $simple_sku . '::'
                                . '::'
                                . 'Default::'
                                . 'simple::'
                                . $category . '::'
                                . 'base::'
                                . $simple_sku . '::'
                                . $description . '::'
                                . '::'
                                . $weight . '::'
                                . $online . '::'
                                . 'Taxable Goods::'
                                . 'Not Visible Individually::'
                                . $price . '::'
                                . '::'
                                . '::'
                                . '::'
                                . str_replace(' ', '-', strtolower($simple_sku)) . '::' // url key
                                . $name . '::'
                                . $name . '::'
                                . '::'
                                . $image . '::'
                                . '::'
                                . $image . '::'
                                . '::'
                                . $image . '::'
                                . '::'
                                . $image . '::'
                                . '::'
                                . '::'
                                . '::'
                                . '::'
                                . '::'
                                . 'Block after Info Column::'
                                . '::'
                                . '::'
                                . '::'
                                . '::'
                                . '::'
                                . '::'
                                . '::'
                                . '::'
                                . '::'
                                . '::'
                                . '::'
                                . '::'
                                . trim($additional_attributes_artwork) . '::'
                                . '::' // qty
                                . '0::'
                                . '1::'
                                . '0::'
                                . '0::'
                                . '1::'
                                . '1::'
                                . '1::'
                                . '0::'
                                . '1::'
                                . '1::'
                                . '::'
                                . '1::'
                                . '0::'
                                . '0::'
                                . '1::'
                                . '1::'
                                . '0::'
                                . '0::'
                                . '0::'
                                . '1::'
                                . '::'
                                . '::'
                                . '::'
                                . $image . '::'
                                . '::'
                                . '::'
                                . '::'
                                . '::'
                                . '::'
                                . '::'
                                . '::'
                                . '::'
                                . '::'
                                . '::';
                        }
                    }
                }
            }
        }
        $configprod = '';
        for ($i = 0; $i < 20; $i++) {
            if($i==7){
                $rest_config = str_replace('"',"\"",$product[$i]);
                $configprod .= strip_tags($rest_config,'<p><br>') . '::';
            }else{
                $configprod .= $product[$i] . '::';
            }
        }

        $config_var = implode("|", $configurable_variations);

        $simples[] = $configprod
            . '::'
            . '/shirts/natural-white.png::'
            . '::'
            . '/shirts/natural-white.png::'
            . '::'
            . '/shirts/natural-white.png::'
            . '::'
            . '/shirts/natural-white.png::'
            . '::'
            . '::'
            . '::'
            . '::'
            . '::'
            . 'Block after Info Column::'
            . '::'
            . '::'
            . '::'
            . '::'
            . '::'
            . '::'
            . '::'
            . '::'
            . '::'
            . '::'
            . '::'
            . '::'
            . 'admire_sticker=/artworks/' . $product[0] . '.png::'
            . '0::'
            . '0::'
            . '1::'
            . '0::'
            . '0::'
            . '1::'
            . '1::'
            . '1::'
            . '0::'
            . '1::'
            . '1::'
            . '::'
            . '1::'
            . '1::'
            . '1::'
            . '1::'
            . '1::'
            . '0::'
            . '0::'
            . '0::'
            . '1::'
            . '::'
            . '::'
            . '::'
            . '/shirts/natural-white.png,/artworks/' . $product[0] . '.png::'
            . ',::'
            . '/artworks/' . $product[0] . '.png::'
            . '::'
            . '::'
            . '::'
            . '::'
            . '::'
            . $config_var . '::'
            . 'admire_gender=Gender,admire_color=Color,admire_size=Size::'
            . '::';


    }

    fclose($file);

//$fp = fopen('config-simple/'.$file_name.'-xx.csv', 'w');

// output headers so that the file is downloaded rather than displayed
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename='.$file_name.'.csv');

// create a file pointer connected to the output stream
    $fp = fopen('php://output', 'w');

    foreach ($simples as $fields) {
        fputcsv($fp, explode('::', $fields));
    }
//mb_convert_encoding($fp, 'UTF-16LE', 'UTF-8');
    fclose($fp);


    header('location:index.php');
}else{

    header('location:index.php');
}

