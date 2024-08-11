<?php
set_time_limit(0);
include('includes/ebayKeys.php');
include('includes/ebayApi.class.php');

$item_id = 184402530609;


$get_item = get_item($item_id);
$xml_getItem = new SimpleXMLElement($get_item);

print_r($xml_getItem);



?>

