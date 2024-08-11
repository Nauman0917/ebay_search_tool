<?php
set_time_limit(0);
include('includes/functions.php');
include('includes/ebayKeys.php');
include('includes/ebayApi.class.php');

//Get the Access Token
$call_refresh_token = get_application_token($application_id,$certificate_id);
$xml_op = json_decode($call_refresh_token);		 
// print_r($xml_op);
// return;

$access_token = trim((string)$xml_op->access_token);


$cat_id = 26232;
$country = 'US';
$condition = 1000;
$start_price = 200;
$end_price = 20000;

$excludedSellers = 'autopartshome2022,starlinkone,onevee_direct';
$excludedSellers = rtrim($excludedSellers,",");
$excludedSellers = str_replace(array(","," "),array("|",""),$excludedSellers);

$spec_seller = trim($_POST['spec_seller']);
$spec_seller = rtrim($spec_seller,",");
$spec_seller = str_replace(array(","," "),array("|",""),$spec_seller);

$filter = "";

$filter_freeship = "filter=maxDeliveryCost:0";
$filter .= $filter_freeship;

$filter_buying_opt = ",buyingOptions:{FIXED_PRICE|BEST_OFFER}";
$filter .= $filter_buying_opt;

$filter_condition = ',conditionIds:{'.$condition.'}';
$filter .= $filter_condition;

$filter_country = ",itemLocationCountry:$country";
$filter .= $filter_country;

$filter_price = ",price:[$start_price..$end_price],priceCurrency:USD";
$filter .= $filter_price;

if($excludedSellers != '') {
    $filter_exclude_seller = ',excludeSellers:{'.$excludedSellers.'}';
    $filter .= $filter_exclude_seller;
}

if($spec_seller != '') {
    $filter_specs_seller = ',sellers:{'.$spec_seller.'}';
    $filter .= $filter_specs_seller;
}



    $limit = 200;
    $offset = 0;

    $find_item = browseApi_searchItmByCatId($cat_id,$limit,$offset,$filter,$access_token);
    $xml_finditem = json_decode($find_item);
    echo '<pre>';
    print_r($xml_finditem);
    echo '</pre>';
    return;