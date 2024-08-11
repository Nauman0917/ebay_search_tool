<?php
// set_time_limit(0);
// error_reporting(0);
// ini_set("memory_limit", "-1");
// include ('includes/functions.php');
// include ('includes/ebayKeys.php');
// include ('includes/ebayApi.class.php');

// $action = trim($_POST['action']);

// if ($action == 'get_ebay_details') {

//     $output_file_name = 'output_string_temp.html';
//     if (file_exists($output_file_name)) {
//         unlink($output_file_name);
//     }

//     $call_refresh_token = get_application_token($application_id, $certificate_id);
//     $xml_auth = json_decode($call_refresh_token);
//     $access_token = '';

//     if (isset($xml_auth->error)) {

//         $error_auth = $xml_auth->error_description;

//         echo '<i> <strong style="color:red;">' . $error_auth . '</strong> </i>';
//         exit();
//     } else {
//         $access_token = trim((string) $xml_auth->access_token);
//     }

//     $cat_id = trim($_POST['cat_id']);
//     $cat_name = trim($_POST['categoryName']);

//     $excludedSellers = trim($_POST['excludedSellers']);
//     $excludedSellers = rtrim($excludedSellers, ",");
//     $excludedSellers_save = file_put_contents('exclude_sellers.txt', $excludedSellers);
//     $excludedSellers = str_replace(array(",", " "), array("|", ""), $excludedSellers);

//     $spec_seller = trim($_POST['spec_seller']);
//     $spec_seller = rtrim($spec_seller, ",");
//     $spec_seller = str_replace(array(",", " "), array("|", ""), $spec_seller);

//     $condition = trim($_POST['condition']);
//     $condition_save = file_put_contents('condition_tag.txt', $condition);

//     $condition_name = '';
//     if ($condition == 1000) {
//         $condition_name = 'NEW';
//     } elseif ($condition == 3000) {
//         $condition_name = 'USED';
//     }

//     $country = trim($_POST['country']);
//     $start_price = trim($_POST['start_price']);
//     $end_price = trim($_POST['end_price']);
//     $exclude_words = trim($_POST['exclude_words']);
//     $exclude_words_save = file_put_contents('exclude_words.txt', $exclude_words);
//     $exclude_words = explode(",", $exclude_words);

//     $filter = "";

//     $filter_freeship = "filter=";
//     $filter .= $filter_freeship;

//     $filter_buying_opt = ",buyingOptions:{FIXED_PRICE}";
//     $filter .= $filter_buying_opt;

//     $filter_condition = ',conditionIds:{' . $condition . '}';
//     $filter .= $filter_condition;

//     $filter_country = ",itemLocationCountry:$country";
//     $filter .= $filter_country;

//     $filter_price = ",price:[$start_price..$end_price],priceCurrency:USD";
//     $filter .= $filter_price;

//     if ($excludedSellers != '') {
//         $filter_exclude_seller = ',excludeSellers:{' . $excludedSellers . '}';
//         $filter .= $filter_exclude_seller;
//     }

//     if ($spec_seller != '') {
//         $filter_specs_seller = ',sellers:{' . $spec_seller . '}';
//         $filter .= $filter_specs_seller;
//     }

//     //Broswe API call to get the total number of listings found...
//     $find_item_less = browseApi_searchItmByCatId($cat_id, 1, 0, $filter, $access_token);
//     $xml_finditem_less = json_decode($find_item_less);

//     $total_listings = $xml_finditem_less->total;
//     $total_offsets = $total_listings / 200;
//     $total_offsets = floor($total_offsets);

//     $results_json = fetchEbayData($cat_id, 200, $filter, $access_token);
//     $results = json_decode($results_json, true);

//     // Check if the decoding was successful
//     if (json_last_error() !== JSON_ERROR_NONE) {
//         echo '<i><strong style="color:red;">Failed to decode eBay data: ' . json_last_error_msg() . '</strong></i>';
//         exit();
//     }

//     $filename = $cat_id . '.csv';
//     if (file_exists($filename)) {
//         unlink($filename);
//     }

//     $fp = fopen($filename, 'w');
//     fputcsv($fp, array("Item Type", "Product Name", "Product Type", "Product Code/SKU", "Bin Picking Number", "Brand Name", "Option Set", "Option Set Align", "Product Description", "Price", "Cost Price", "Retail Price", "Sale Price", "Fixed Shipping Cost", "Free Shipping", "Product Warranty", "Product Weight", "Product Width", "Product Height", "Product Depth", "Allow Purchases?", "Product Visible?", "Product Availability", "Track Inventory", "Current Stock Level", "Low Stock Level", "Category", "Product Image File - 1", "Product Image Is Thumbnail - 1", "Product Image File - 2", "Product Image Is Thumbnail - 2", "Product Image File - 3", "Product Image Is Thumbnail - 3", "Search Keywords", "Page Title", "META Keywords", "META Description", "Product Condition", "Show Product Condition?", "Sort Order", "Product Tax Class", "Product UPC/EAN", "Stop Processing Rules", "Product URL", "Redirect Old URL?", "GPS Global Trade Item Number", "GPS Manufacturer Part Number", "GPS Gender", "GPS Age Group", "GPS Color", "GPS Size", "GPS Material", "GPS Pattern", "GPS Item Group ID", "GPS Category", "GPS Enabled", "Tax Provider Tax Code", "Product Custom Fields"));

//     $output_string_header = '<table>
//                 <tr>
//                     <td colspan="5"> Total Listings : ' . $total_listings . '  </td>
//                 </tr>
//                 <tr>
//                     <td>Count</td>
//                     <td>Title</td>
//                     <td>Price</td>
//                     <td>Seller</td>
//                     <td>Image</td>
//                 </tr>';
//     file_put_contents($output_file_name, $output_string_header);

//     $output_fp = fopen($output_file_name, 'a');
//     $output_string = '';
//     $output_array = [];

//     $count = 0;
//     foreach ($results as $result) {
//         $count++;

//         $itemId = $result['legacyItemId'];
//         $title = $result['title'];

//         $output_array = [...$output_array, $title];
//         $thumb_image_1 = $result['image']['imageUrl'];

//         $large_image = '';
//         if (isset($result['thumbnailImages'][0]['imageUrl'])) {
//             $large_image = $result['thumbnailImages'][0]['imageUrl'];
//         }

//         $price = $result['price']['value'];
//         $condition = $result['condition'];
//         $shipping_cost = 0;
//         if (isset($result['shippingOptions'][0]['shippingCost']['value'])) {
//             $shipping_cost = $result['shippingOptions'][0]['shippingCost']['value'];
//         }

//         $itemWebUrl = $result['itemWebUrl'];
//         $itemWebUrl = explode("&", $itemWebUrl);
//         $itemWebUrl = $itemWebUrl[0];

//         $img_2 = '';
//         if (isset($result['additionalImages'][0]['imageUrl'])) {
//             $img_2 = $result['additionalImages'][0]['imageUrl'];
//         }
//         $img_3 = '';
//         if (isset($result['additionalImages'][1]['imageUrl'])) {
//             $img_3 = $result['additionalImages'][1]['imageUrl'];
//         }
//         $img_4 = '';
//         if (isset($result['additionalImages'][2]['imageUrl'])) {
//             $img_4 = $result['additionalImages'][2]['imageUrl'];
//         }
//         $img_5 = '';
//         if (isset($result['additionalImages'][3]['imageUrl'])) {
//             $img_5 = $result['additionalImages'][3]['imageUrl'];
//         }
//         $img_6 = '';
//         if (isset($result['additionalImages'][4]['imageUrl'])) {
//             $img_6 = $result['additionalImages'][4]['imageUrl'];
//         }

//         $seller = $result['seller']['username'];

//         foreach ($exclude_words as $c) {
//             if (strpos($title, $c) !== FALSE) {
//                 continue;  // Skip that item and move to next item            
//             }
//         }

//         $output_string .= '<tr>
//                 <td>' . $count . '</td>
//                 <td>' . $title . '</td>
//                 <td>' . $price . '</td>
//                 <td>' . $seller . '</td>
//                 <td>';
//         $output_string .= '<img src = ' . $large_image . ' width=60 />&nbsp;';
//         $output_string .= '</td>
//              </tr>' . "\n";

//         fputcsv($fp, array("Product", $title, "P", " ", " ", " ", " ", "Right", $itemWebUrl, "0", $price, "0", "0", "0", "Y", "6 Month Replacement Warranty", "7", "0", "0", "0", "Y", "Y", "Usually Ships in 1-2 Business Days", "none", "0", "0", $cat_name, $large_image, " ", $img_2, "Y", $img_3, " ", " ", " ", " ", " ", $condition_name, "Y", "0", "Default Tax Class", " ", "N", " ", " ", " ", " ", " ", " ", " ", " ", " ", " ", "N", " ", " "));
//     }

//     echo $output_string;
//     fwrite($output_fp, $output_string);
//     fclose($output_fp);

//     fclose($fp);
// }


set_time_limit(0);
error_reporting(0);
ini_set("memory_limit", "-1");
//include('includes/config.php');
include ('includes/functions.php');
include ('includes/ebayKeys.php');
include ('includes/ebayApi.class.php');

$action = trim($_POST['action']);
if ($action == 'get_ebay_details') {
    $output_file_name = 'output_string_temp.html';
    if (file_exists($output_file_name)) {
        unlink($output_file_name);
    }
    //Get the Access Token
    $call_refresh_token = get_application_token($application_id, $certificate_id);
    $xml_auth = json_decode($call_refresh_token);
    $access_token = '';
    if (isset($xml_auth->error)) {

        $error_auth = $xml_auth->error_description;

        echo '<i> <strong style="color:red;">' . $error_auth . '</strong> </i>';
        exit();
    } else {
        $access_token = trim((string) $xml_auth->access_token);
    }

    $cat_id = trim($_POST['cat_id']);
    $cat_name = trim($_POST['categoryName']);

    $excludedSellers = trim($_POST['excludedSellers']);
    $excludedSellers = rtrim($excludedSellers, ",");
    $excludedSellers_save = file_put_contents('exclude_sellers.txt', $excludedSellers);
    $excludedSellers = str_replace(array(",", " "), array("|", ""), $excludedSellers);

    $spec_seller = trim($_POST['spec_seller']);
    $spec_seller = rtrim($spec_seller, ",");
    $spec_seller = str_replace(array(",", " "), array("|", ""), $spec_seller);

    $condition = trim($_POST['condition']);
    $condition_save = file_put_contents('condition_tag.txt', $condition);

    $condition_name = '';
    if ($condition == 1000) {
        $condition_name = 'NEW';
    } elseif ($condition == 3000) {
        $condition_name = 'USED';
    }

    $country = trim($_POST['country']);
    $start_price = trim($_POST['start_price']);
    $end_price = trim($_POST['end_price']);
    //$wordFilter = trim($_POST['wordFilter']);
    $exclude_words = trim($_POST['exclude_words']);
    $exclude_words_save = file_put_contents('exclude_words.txt', $exclude_words);
    $exclude_words = explode(",", $exclude_words);


    //SET ALL THE FILTERS HERE....
    $filter = "";
    $filter_freeship = "filter=";
    $filter .= $filter_freeship;

    $filter_buying_opt = ",buyingOptions:{FIXED_PRICE}";
    $filter .= $filter_buying_opt;

    $filter_condition = ',conditionIds:{' . $condition . '}';
    $filter .= $filter_condition;

    $filter_country = ",itemLocationCountry:$country";
    $filter .= $filter_country;

    $filter_price = ",price:[$start_price..$end_price],priceCurrency:USD";
    $filter .= $filter_price;

    if ($excludedSellers != '') {
        $filter_exclude_seller = ',excludeSellers:{' . $excludedSellers . '}';
        $filter .= $filter_exclude_seller;
    }

    if ($spec_seller != '') {
        $filter_specs_seller = ',sellers:{' . $spec_seller . '}';
        $filter .= $filter_specs_seller;
    }

    //Broswe API call to get the total number of listings found...
    $find_item_less = browseApi_searchItmByCatId($cat_id, 200, 0, $filter, $access_token);
    $xml_finditem_less = json_decode($find_item_less);

    $total_listings = $xml_finditem_less->total;
    $total_offsets = $total_listings / 200;
    $total_offsets = floor($total_offsets);

    $filename = $cat_id . '.csv';
    if (file_exists($filename)) {
        unlink($filename);
    }

    $fp = fopen($filename, 'w');
    fputcsv($fp, array("Item Type", "Product Name", "Product Type", "Product Code/SKU", "Bin Picking Number", "Brand Name", "Option Set", "Option Set Align", "Product Description", "Price", "Cost Price", "Retail Price", "Sale Price", "Fixed Shipping Cost", "Free Shipping", "Product Warranty", "Product Weight", "Product Width", "Product Height", "Product Depth", "Allow Purchases?", "Product Visible?", "Product Availability", "Track Inventory", "Current Stock Level", "Low Stock Level", "Category", "Product Image File - 1", "Product Image Is Thumbnail - 1", "Product Image File - 2", "Product Image Is Thumbnail - 2", "Product Image File - 3", "Product Image Is Thumbnail - 3", "Search Keywords", "Page Title", "META Keywords", "META Description", "Product Condition", "Show Product Condition?", "Sort Order", "Product Tax Class", "Product UPC/EAN", "Stop Processing Rules", "Product URL", "Redirect Old URL?", "GPS Global Trade Item Number", "GPS Manufacturer Part Number", "GPS Gender", "GPS Age Group", "GPS Color", "GPS Size", "GPS Material", "GPS Pattern", "GPS Item Group ID", "GPS Category", "GPS Enabled", "Tax Provider Tax Code", "Product Custom Fields"));

    $output_string_header = '<table>
                <tr>
                    <td colspan="5"> Total Listings : ' . $total_listings . '  </td>
                </tr>
                <tr>
                    <td>Count</td>
                    <td>Title</td>
                    <td>Price</td>
                    <td>Seller</td>
                    <td>Image</td>
                </tr>';
    file_put_contents($output_file_name, $output_string_header);

    $limit = 200;
    $offset = 0;
    for ($offset_loop = 0; $offset_loop <= $total_offsets; $offset_loop = $offset_loop + 1) {
        //Browse API call
        $find_item = browseApi_searchItmByCatId($cat_id, $limit, $offset, $filter, $access_token);
        $xml_finditem = json_decode($find_item);

        $results = $xml_finditem_less->itemSummaries;
        $output_fp = fopen($output_file_name, 'a');

        $output_string = '';
        $output_array = [];

        foreach ($results as $result) {

            $count++;

            $itemId = $result->legacyItemId;
            $title = $result->title;

            $output_array = [...$output_array, $title];
            $thumb_image_1 = $result->image->imageUrl;

            $large_image = '';
            if (isset($result->thumbnailImages[0]->imageUrl)) {
                $large_image = $result->thumbnailImages[0]->imageUrl;
            }

            $price = $result->price->value;
            $condition = $result->condition;
            $shipping_cost = 0;
            if (isset($result->shippingOptions[0]->shippingCost->value)) {
                $shippingCost = $result->shippingOptions[0]->shippingCost->value;
            }

            $itemWebUrl = $result->itemWebUrl;
            $itemWebUrl = explode("&", $itemWebUrl);
            $itemWebUrl = $itemWebUrl[0];

            $img_2 = '';
            if (isset($result->additionalImages[0]->imageUrl)) {
                $img_2 = $result->additionalImages[0]->imageUrl;
            }
            $img_3 = '';
            if (isset($result->additionalImages[1]->imageUrl)) {
                $img_3 = $result->additionalImages[1]->imageUrl;
            }
            $img_4 = '';
            if (isset($result->additionalImages[2]->imageUrl)) {
                $img_4 = $result->additionalImages[2]->imageUrl;
            }
            $img_5 = '';
            if (isset($result->additionalImages[3]->imageUrl)) {
                $img_5 = $result->additionalImages[3]->imageUrl;
            }
            $img_6 = '';
            if (isset($result->additionalImages[4]->imageUrl)) {
                $img_6 = $result->additionalImages[4]->imageUrl;
            }

            $seller = $result->seller->username;

            //filter the Exclude words and if anything found in the title, skip that.
            foreach ($exclude_words as $c) {
                if (strpos($title, $c) !== FALSE) {
                    continue;  //Skip that item and move to next item            
                }
            }

            $output_string .= '<tr>
                    <td>' . $count . '</td>
                    <td>' . $title . '</td>
                    <td>' . $price . '</td>
                    <td>' . $seller . '</td>
                    <td>';
            $output_string .= '<img src = ' . $large_image . ' width=60 />&nbsp;';
            $output_string .= '</td>
                 </tr>' . "\n";


            fputcsv($fp, array("Product", $title, "P", " ", " ", " ", " ", "Right", $itemWebUrl, "0", $price, "0", "0", "0", "Y", "6 Month Replacement Warranty", "7", "0", "0", "0", "Y", "Y", "Usually Ships in 1-2 Business Days", "none", "0", "0", $cat_name, $large_image, " ", $img_2, "Y", $img_3, " ", " ", " ", " ", " ", $condition_name, "Y", "0", "Default Tax Class", " ", "N", " ", " ", " ", " ", " ", " ", " ", " ", " ", " ", " ", " ", "N", " ", " "));
        }

        fwrite($output_fp, $output_string);
        fclose($output_fp);
        //sleep(1);     

        $offset = $offset + $limit;
    }

    fclose($fp);
}













$limit = 200;
$offset = 0;
$next_url = null;
$all_item_summaries = [];

// First API call to get the total number of listings found
$response = browseApi_searchItmByCatId($cat_id, $limit, $offset, $filter, $access_token);
$data = json_decode($response);

if (isset($data->itemSummaries)) {
    $all_item_summaries = array_merge($all_item_summaries, $data->itemSummaries);
}

$next_url = $data->next ?? null;

$output_file_name = 'output.html';
$exclude_words_from_title = ['some', 'excluded', 'words']; // Example words to be excluded
$exclude_words = ['another', 'set', 'of', 'words']; // Example words to exclude titles

// Open file for writing
$output_fp = fopen($output_file_name, 'w');

// Write the initial HTML structure
$html_content = '<html>
<head>
    <title>Item Summaries</title>
    <style>
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid black; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
    </style>
</head>
<body>
    <table>
        <tr>
            <th>Count</th>
            <th>Title</th>
            <th>Price</th>
            <th>Seller</th>
            <th>Image</th>
        </tr>';
fwrite($output_fp, $html_content);

$count = 0;

while ($next_url) {
    $next_response = browseApi_searchItmByCatId_next($next_url, $access_token);
    $next_data = json_decode($next_response);

    if (isset($next_data->itemSummaries)) {
        $all_item_summaries = array_merge($all_item_summaries, $next_data->itemSummaries);
    }

    foreach ($next_data->itemSummaries as $result) {
        $count++;

        $title = $result->title;
        foreach ($exclude_words_from_title as $word) {
            $title = trim(str_ireplace(trim($word), '', $title));
        }

        foreach ($exclude_words as $c) {
            if (strpos($title, $c) !== FALSE) {
                continue 2;
            }
        }

        $large_image = $result->image->imageUrl ?? '';

        $price = $result->price->value ?? 'N/A';
        $seller = $result->seller->username ?? 'N/A';

        $output_string = '<tr>
                <td>' . $count . '</td>
                <td>' . htmlspecialchars($title) . '</td>
                <td>' . htmlspecialchars($price) . '</td>
                <td>' . htmlspecialchars($seller) . '</td>
                <td><img src="' . htmlspecialchars($large_image) . '" width="60" /></td>
             </tr>' . "\n";

        fwrite($output_fp, $output_string);
    }

    $next_url = $next_data->next ?? null;
    $offset += $limit;
}

// Write the closing HTML tags
$html_content = '</table>
</body>
</html>';
fwrite($output_fp, $html_content);

// Close the file
fclose($output_fp);

echo '<pre>';
print_r(json_encode($all_item_summaries));
echo '</pre>';
return;






















set_time_limit(0);
error_reporting(0);
ini_set("memory_limit", "-1");
include('includes/functions.php');
include('includes/ebayKeys.php');
include('includes/ebayApi.class.php');

$action = trim($_POST['action']);

if ($action == 'get_ebay_details') {
    $output_file_name = 'output_string_temp.html';
    if (file_exists($output_file_name)) {
        unlink($output_file_name);
    }

    $call_refresh_token = get_application_token($application_id, $certificate_id);
    $xml_auth = json_decode($call_refresh_token);

    $access_token = '';
    if (isset($xml_auth->error)) {
        $error_auth = $xml_auth->error_description;
        echo '<i> <strong style="color:red;">' . $error_auth . '</strong> </i>';
        exit();
    } else {
        $access_token = trim((string)$xml_auth->access_token);
    }

    $cat_id = trim($_POST['cat_id']);
    $cat_name = trim($_POST['categoryName']);

    $excludedSellers = trim($_POST['excludedSellers']);
    $excludedSellers = rtrim($excludedSellers, ",");
    $excludedSellers_save = file_put_contents('exclude_sellers.txt', $excludedSellers);
    $excludedSellers = str_replace(array(",", " "), array("|", ""), $excludedSellers);

    $exclude_words_from_title = trim($_POST['excludeWordsFromTitle']);
    file_put_contents('exclude_words_from_title.txt', $exclude_words_from_title);

    if (!empty($exclude_words_from_title)) {
        $exclude_words_from_title = strpos($exclude_words_from_title, ',') !== false
            ? explode(",", $exclude_words_from_title)
            : [$exclude_words_from_title];
    } else {
        $exclude_words_from_title = [];
    }

    $spec_seller = trim($_POST['spec_seller']);
    $spec_seller = rtrim($spec_seller, ",");
    $spec_seller = str_replace(array(",", " "), array("|", ""), $spec_seller);

    $condition = trim($_POST['condition']);
    $condition_save = file_put_contents('condition_tag.txt', $condition);

    $condition_name = '';
    if ($condition == 1000) {
        $condition_name = 'NEW';
    } elseif ($condition == 3000) {
        $condition_name = 'USED';
    }

    $country = trim($_POST['country']);
    $start_price = trim($_POST['start_price']);
    $end_price = trim($_POST['end_price']);
    $exclude_words = trim($_POST['exclude_words']);
    $exclude_words_save = file_put_contents('exclude_words.txt', $exclude_words);
    $exclude_words = explode(",", $exclude_words);

    $filter = "";
    $filter_freeship = "filter=";
    $filter .= $filter_freeship;
    $filter_buying_opt = ",buyingOptions:{FIXED_PRICE}";
    $filter .= $filter_buying_opt;
    $filter_condition = ',conditionIds:{' . $condition . '}';
    $filter .= $filter_condition;
    $filter_country = ",itemLocationCountry:$country";
    $filter .= $filter_country;
    $filter_price = ",price:[$start_price..$end_price],priceCurrency:USD";
    $filter .= $filter_price;

    if ($excludedSellers != '') {
        $filter_exclude_seller = ',excludeSellers:{' . $excludedSellers . '}';
        $filter .= $filter_exclude_seller;
    }

    if ($spec_seller != '') {
        $filter_specs_seller = ',sellers:{' . $spec_seller . '}';
        $filter .= $filter_specs_seller;
    }

    // Initialize output string for HTML
    $output_string_header = '<table>
                <tr>
                    <td colspan="5"> Total Listings : ' . $total_listings . '  </td>
                </tr>
                <tr>
                    <td>Count</td>
                    <td>Title</td>
                    <td>Price</td>
                    <td>Seller</td>
                    <td>Image</td>
                </tr>';
    file_put_contents($output_file_name, $output_string_header);

    $limit = 200;
    $offset = 0;
    $count = 0;

    // First API call to get the total number of listings found
    $response = browseApi_searchItmByCatId($cat_id, $limit, $offset, $filter, $access_token);
    $data = json_decode($response);

    if (isset($data->itemSummaries)) {
        $all_item_summaries = $data->itemSummaries;
    }

    $next_url = $data->next ?? null;

    $filename = $cat_id . '.csv';
    if (file_exists($filename)) {
        unlink($filename);
    }

    $product_description = file_get_contents("product_description.txt");

    $fp = fopen($filename, 'w');
    fputcsv($fp, array("Item Type", "Product Name", "Product Type", "Product Code/SKU", "Bin Picking Number", "Brand Name", "Option Set", "Option Set Align", "Product Description", "Price", "Cost Price", "Retail Price", "Sale Price", "Fixed Shipping Cost", "Free Shipping", "Product Warranty", "Product Weight", "Product Width", "Product Height", "Product Depth", "Allow Purchases?", "Product Visible?", "Product Availability", "Track Inventory", "Current Stock Level", "Low Stock Level", "Category", "Product Image File - 1", "Product Image Is Thumbnail - 1", "Product Image File - 2", "Product Image Is Thumbnail - 2", "Product Image File - 3", "Product Image Is Thumbnail - 3", "Search Keywords", "Page Title", "META Keywords", "META Description", "Product Condition", "Show Product Condition?", "Sort Order", "Product Tax Class", "Product UPC/EAN", "Stop Processing Rules", "Product URL", "Redirect Old URL?", "GPS Global Trade Item Number", "GPS Manufacturer Part Number", "GPS Gender", "GPS Age Group", "GPS Color", "GPS Size", "GPS Material", "GPS Pattern", "GPS Item Group ID", "GPS Category", "GPS Enabled", "Tax Provider Tax Code", "Product Custom Fields"));

    while ($next_url) {
        $next_response = browseApi_searchItmByCatId_next($next_url, $access_token);
        $next_data = json_decode($next_response);

        if (isset($next_data->itemSummaries)) {
            $all_item_summaries = array_merge($all_item_summaries, $next_data->itemSummaries);
        }

        $output_fp = fopen($output_file_name, 'a');

        $output_string = '';
        foreach ($next_data->itemSummaries as $result) {
            $count++;

            $title = $result->title;

            // Remove the excluded words from the title
            foreach ($exclude_words_from_title as $word) {
                $title = trim(str_ireplace(trim($word), '', $title));
            }

            $category_name = $cat_name;
            if ($result->categories) {
                foreach ($result->categories as $category) {
                    if ($category->categoryId == $cat_id) {
                        $category_name = $category->categoryName;
                    }
                }
            }

            $thumb_image_1 = $result->image->imageUrl;

            $large_image = '';
            if (isset($result->thumbnailImages[0]->imageUrl)) {
                $large_image = $result->thumbnailImages[0]->imageUrl;
            }

            $price = $result->price->value;
            $condition = $result->condition;
            $shipping_cost = 0;
            if (isset($result->shippingOptions[0]->shippingCost->value)) {
                $shippingCost = $result->shippingOptions[0]->shippingCost->value;
            }

            $itemWebUrl = $result->itemWebUrl;
            $itemWebUrl = explode("&", $itemWebUrl);
            $itemWebUrl = $itemWebUrl[0];

            $img_2 = '';
            if (isset($result->additionalImages[0]->imageUrl)) {
                $img_2 = $result->additionalImages[0]->imageUrl;
            }
            $img_3 = '';
            if (isset($result->additionalImages[1]->imageUrl)) {
                $img_3 = $result->additionalImages[1]->imageUrl;
            }
            $img_4 = '';
            if (isset($result->additionalImages[2]->imageUrl)) {
                $img_4 = $result->additionalImages[2]->imageUrl;
            }
            $img_5 = '';
            if (isset($result->additionalImages[3]->imageUrl)) {
                $img_5 = $result->additionalImages[3]->imageUrl;
            }
            $img_6 = '';
            if (isset($result->additionalImages[4]->imageUrl)) {
                $img_6 = $result->additionalImages[4]->imageUrl;
            }

            $seller = $result->seller->username;

            // Filter the Exclude words and if anything found in the title, skip that.
            $skip = false;
            foreach ($exclude_words as $c) {
                if (strpos($title, $c) !== FALSE) {
                    $skip = true;
                    break;
                }
            }
            if ($skip) continue;

            $output_string .= '<tr>
                    <td>' . $count . '</td>
                    <td>' . $title . '</td>
                    <td>' . $price . '</td>
                    <td>' . $seller . '</td>
                    <td><img src="' . $large_image . '" width="60" /></td>
                 </tr>';

            fputcsv($fp, array("Product", $title, "P", " ", " ", " ", " ", "Right", $product_description, "0", $price, "0", "0", "0", "Y", "6 Month Replacement Warranty", "7", "0", "0", "0", "Y", "Y", "Usually Ships in 1-2 Business Days", "none", "0", "0", $category_name, $large_image, " ", $img_2, "Y", $img_3, " ", " ", " ", " ", " ", $condition_name, "Y", "0", "Default Tax Class", " ", "N", " ", " ", " ", " ", " ", " ", " ", " ", " ", " ", " ", "N", " ", " "));
        }

        fwrite($output_fp, $output_string);
        fclose($output_fp);

        $next_url = $next_data->next ?? null;
    }

    fclose($fp);

    $output_string_footer = '</table>';
    file_put_contents($output_file_name, $output_string_footer, FILE_APPEND);

    echo '<pre>';
    print_r(json_encode($all_item_summaries));
    echo '</pre>';
}


















<?php
set_time_limit(0);
error_reporting(0);
ini_set("memory_limit", "-1");
include('includes/functions.php');
include('includes/ebayKeys.php');
include('includes/ebayApi.class.php');

$action = trim($_POST['action']);

if ($action == 'get_ebay_details') {
    $output_file_name = 'output_string_temp.html';
    if (file_exists($output_file_name)) {
        unlink($output_file_name);
    }

    $call_refresh_token = get_application_token($application_id, $certificate_id);
    $xml_auth = json_decode($call_refresh_token);

    $access_token = '';
    if (isset($xml_auth->error)) {
        $error_auth = $xml_auth->error_description;
        echo '<i> <strong style="color:red;">' . $error_auth . '</strong> </i>';
        exit();
    } else {
        $access_token = trim((string)$xml_auth->access_token);
    }

    $cat_id = trim($_POST['cat_id']);
    $cat_name = trim($_POST['categoryName']);

    $excludedSellers = trim($_POST['excludedSellers']);
    $excludedSellers = rtrim($excludedSellers, ",");
    $excludedSellers_save = file_put_contents('exclude_sellers.txt', $excludedSellers);
    $excludedSellers = str_replace(array(",", " "), array("|", ""), $excludedSellers);

    $exclude_words_from_title = trim($_POST['excludeWordsFromTitle']);
    file_put_contents('exclude_words_from_title.txt', $exclude_words_from_title);

    if (!empty($exclude_words_from_title)) {
        $exclude_words_from_title = strpos($exclude_words_from_title, ',') !== false
            ? explode(",", $exclude_words_from_title)
            : [$exclude_words_from_title];
    } else {
        $exclude_words_from_title = [];
    }

    $spec_seller = trim($_POST['spec_seller']);
    $spec_seller = rtrim($spec_seller, ",");
    $spec_seller = str_replace(array(",", " "), array("|", ""), $spec_seller);

    $condition = trim($_POST['condition']);
    $condition_save = file_put_contents('condition_tag.txt', $condition);

    $condition_name = '';
    if ($condition == 1000) {
        $condition_name = 'NEW';
    } elseif ($condition == 3000) {
        $condition_name = 'USED';
    }

    $country = trim($_POST['country']);
    $start_price = trim($_POST['start_price']);
    $end_price = trim($_POST['end_price']);
    $exclude_words = trim($_POST['exclude_words']);
    $exclude_words_save = file_put_contents('exclude_words.txt', $exclude_words);
    $exclude_words = explode(",", $exclude_words);

    $filter = "";
    $filter_freeship = "filter=";
    $filter .= $filter_freeship;
    $filter_buying_opt = ",buyingOptions:{FIXED_PRICE}";
    $filter .= $filter_buying_opt;
    $filter_condition = ',conditionIds:{' . $condition . '}';
    $filter .= $filter_condition;
    $filter_country = ",itemLocationCountry:$country";
    $filter .= $filter_country;
    $filter_price = ",price:[$start_price..$end_price],priceCurrency:USD";
    $filter .= $filter_price;

    if ($excludedSellers != '') {
        $filter_exclude_seller = ',excludeSellers:{' . $excludedSellers . '}';
        $filter .= $filter_exclude_seller;
    }

    if ($spec_seller != '') {
        $filter_specs_seller = ',sellers:{' . $spec_seller . '}';
        $filter .= $filter_specs_seller;
    }

    // Initialize output string for HTML
    $output_string_header = '<html><head><link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.css">
    <script type="text/javascript" charset="utf8" src="https://code.jquery.com/jquery-3.5.1.js"></script>
    <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.js"></script>
    </head><body>
    <table id="ebayTable" class="display">
                <thead>
                <tr>
                    <th>Count</th>
                    <th>Title</th>
                    <th>Price</th>
                    <th>Seller</th>
                    <th>Image</th>
                </tr>
                </thead>
                <tbody>';
    file_put_contents($output_file_name, $output_string_header);

    $limit = 200;
    $offset = 0;
    $count = 0;

    // First API call to get the total number of listings found
    $response = browseApi_searchItmByCatId($cat_id, $limit, $offset, $filter, $access_token);
    $data = json_decode($response);

    if (isset($data->itemSummaries)) {
        $all_item_summaries = $data->itemSummaries;
    }

    $next_url = $data->next ?? null;

    $filename = $cat_id . '.csv';
    if (file_exists($filename)) {
        unlink($filename);
    }

    $product_description = file_get_contents("product_description.txt");

    $fp = fopen($filename, 'w');
    fputcsv($fp, array("Item Type", "Product Name", "Product Type", "Product Code/SKU", "Bin Picking Number", "Brand Name", "Option Set", "Option Set Align", "Product Description", "Price", "Cost Price", "Retail Price", "Sale Price", "Fixed Shipping Cost", "Free Shipping", "Product Warranty", "Product Weight", "Product Width", "Product Height", "Product Depth", "Allow Purchases?", "Product Visible?", "Product Availability", "Track Inventory", "Current Stock Level", "Low Stock Level", "Category", "Product Image File - 1", "Product Image Is Thumbnail - 1", "Product Image File - 2", "Product Image Is Thumbnail - 2", "Product Image File - 3", "Product Image Is Thumbnail - 3", "Search Keywords", "Page Title", "META Keywords", "META Description", "Product Condition", "Show Product Condition?", "Sort Order", "Product Tax Class", "Product UPC/EAN", "Stop Processing Rules", "Product URL", "Redirect Old URL?", "GPS Global Trade Item Number", "GPS Manufacturer Part Number", "GPS Gender", "GPS Age Group", "GPS Color", "GPS Size", "GPS Material", "GPS Pattern", "GPS Item Group ID", "GPS Category", "GPS Enabled", "Tax Provider Tax Code", "Product Custom Fields"));

    while ($next_url) {
        $next_response = browseApi_searchItmByCatId_next($next_url, $access_token);
        $next_data = json_decode($next_response);

        if (isset($next_data->itemSummaries)) {
            $all_item_summaries = array_merge($all_item_summaries, $next_data->itemSummaries);
        }

        $output_fp = fopen($output_file_name, 'a');

        $output_string = '';
        foreach ($next_data->itemSummaries as $result) {
            $count++;

            $title = $result->title;

            // Remove the excluded words from the title
            foreach ($exclude_words_from_title as $word) {
                $title = trim(str_ireplace(trim($word), '', $title));
            }

            $category_name = $cat_name;
            if ($result->categories) {
                foreach ($result->categories as $category) {
                    if ($category->categoryId == $cat_id) {
                        $category_name = $category->categoryName;
                    }
                }
            }

            $thumb_image_1 = $result->image->imageUrl;

            $large_image = '';
            if (isset($result->thumbnailImages[0]->imageUrl)) {
                $large_image = $result->thumbnailImages[0]->imageUrl;
            }

            $price = $result->price->value;
            $condition = $result->condition;
            $shipping_cost = 0;
            if (isset($result->shippingOptions[0]->shippingCost->value)) {
                $shippingCost = $result->shippingOptions[0]->shippingCost->value;
            }

            $itemWebUrl = $result->itemWebUrl;
            $itemWebUrl = explode("&", $itemWebUrl);
            $itemWebUrl = $itemWebUrl[0];

            $img_2 = '';
            if (isset($result->additionalImages[0]->imageUrl)) {
                $img_2 = $result->additionalImages[0]->imageUrl;
            }
            $img_3 = '';
            if (isset($result->additionalImages[1]->imageUrl)) {
                $img_3 = $result->additionalImages[1]->imageUrl;
            }
            $img_4 = '';
            if (isset($result->additionalImages[2]->imageUrl)) {
                $img_4 = $result->additionalImages[2]->imageUrl;
            }
            $img_5 = '';
            if (isset($result->additionalImages[3]->imageUrl)) {
                $img_5 = $result->additionalImages[3]->imageUrl;
            }
            $img_6 = '';
            if (isset($result->additionalImages[4]->imageUrl)) {
                $img_6 = $result->additionalImages[4]->imageUrl;
            }

            $seller = $result->seller->username;

            $skip = false;
            foreach ($exclude_words as $c) {
                if (strpos($title, $c) !== FALSE) {
                    $skip = true;
                    break;
                }
            }
            if ($skip) continue;

            $output_string .= '<tr>
                    <td>' . $count . '</td>
                    <td>' . $title . '</td>
                    <td>' . $price . '</td>
                    <td>' . $seller . '</td>
                    <td><img src="' . $large_image . '" width="60" /></td>
                 </tr>';

            fputcsv($fp, array("Product", $title, "P", " ", " ", " ", " ", "Right", $product_description, "0", $price, "0", "0", "0", "Y", "6 Month Replacement Warranty", "7", "0", "0", "0", "Y", "Y", "Usually Ships in 1-2 Business Days", "none", "0", "0", $category_name, $large_image, " ", $img_2, "Y", $img_3, " ", " ", " ", " ", " ", $condition_name, "Y", "0", "Default Tax Class", " ", "N", " ", " ", " ", " ", " ", " ", " ", " ", " ", " ", "N", " ", " "));

        }

        fwrite($output_fp, $output_string);
        fclose($output_fp);

        $next_url = $next_data->next ?? null;
    }

    fclose($fp);

    $output_string_footer = '</tbody></table>
    <script>
    $(document).ready(function() {
        $("#ebayTable").DataTable({
            "pageLength": 50
        });
    });
    </script>
    </body></html>';
    file_put_contents($output_file_name, $output_string_footer, FILE_APPEND);

    echo '<pre>';
    print_r(json_encode($all_item_summaries));
    echo '</pre>';
}
?>

















<?php
set_time_limit(0);
error_reporting(0);
ini_set("memory_limit", "-1");
include('includes/functions.php');
include('includes/ebayKeys.php');
include('includes/ebayApi.class.php');

$action = trim($_POST['action']);

if ($action == 'get_ebay_details') {
    $output_file_name = 'output_string_temp.html';
    if (file_exists($output_file_name)) {
        unlink($output_file_name);
    }

    $call_refresh_token = get_application_token($application_id, $certificate_id);
    $xml_auth = json_decode($call_refresh_token);

    $access_token = '';
    if (isset($xml_auth->error)) {
        $error_auth = $xml_auth->error_description;
        echo '<i> <strong style="color:red;">' . $error_auth . '</strong> </i>';
        exit();
    } else {
        $access_token = trim((string)$xml_auth->access_token);
    }

    $cat_id = trim($_POST['cat_id']);
    $cat_name = trim($_POST['categoryName']);

    $excludedSellers = trim($_POST['excludedSellers']);
    $excludedSellers = rtrim($excludedSellers, ",");
    $excludedSellers_save = file_put_contents('exclude_sellers.txt', $excludedSellers);
    $excludedSellers = str_replace(array(",", " "), array("|", ""), $excludedSellers);

    $exclude_words_from_title = trim($_POST['excludeWordsFromTitle']);
    file_put_contents('exclude_words_from_title.txt', $exclude_words_from_title);

    if (!empty($exclude_words_from_title)) {
        $exclude_words_from_title = strpos($exclude_words_from_title, ',') !== false
            ? explode(",", $exclude_words_from_title)
            : [$exclude_words_from_title];
    } else {
        $exclude_words_from_title = [];
    }

    $spec_seller = trim($_POST['spec_seller']);
    $spec_seller = rtrim($spec_seller, ",");
    $spec_seller = str_replace(array(",", " "), array("|", ""), $spec_seller);

    $condition = trim($_POST['condition']);
    $condition_save = file_put_contents('condition_tag.txt', $condition);

    $condition_name = '';
    if ($condition == 1000) {
        $condition_name = 'NEW';
    } elseif ($condition == 3000) {
        $condition_name = 'USED';
    }

    $country = trim($_POST['country']);
    $start_price = trim($_POST['start_price']);
    $end_price = trim($_POST['end_price']);
    $exclude_words = trim($_POST['exclude_words']);
    $exclude_words_save = file_put_contents('exclude_words.txt', $exclude_words);
    $exclude_words = explode(",", $exclude_words);

    $filter = "";
    $filter_freeship = "filter=";
    $filter .= $filter_freeship;
    $filter_buying_opt = ",buyingOptions:{FIXED_PRICE}";
    $filter .= $filter_buying_opt;
    $filter_condition = ',conditionIds:{' . $condition . '}';
    $filter .= $filter_condition;
    $filter_country = ",itemLocationCountry:$country";
    $filter .= $filter_country;
    $filter_price = ",price:[$start_price..$end_price],priceCurrency:USD";
    $filter .= $filter_price;

    if ($excludedSellers != '') {
        $filter_exclude_seller = ',excludeSellers:{' . $excludedSellers . '}';
        $filter .= $filter_exclude_seller;
    }

    if ($spec_seller != '') {
        $filter_specs_seller = ',sellers:{' . $spec_seller . '}';
        $filter .= $filter_specs_seller;
    }

    // Initialize output string for HTML
    $output_string_header = '<html><head><link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.css">
    <script type="text/javascript" charset="utf8" src="https://code.jquery.com/jquery-3.5.1.js"></script>
    <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.js"></script>
    </head><body>
    <table id="ebayTable" class="display">
                <thead>
                <tr>
                    <th>Count</th>
                    <th>Title</th>
                    <th>Price</th>
                    <th>Seller</th>
                    <th>Image</th>
                </tr>
                </thead>
                <tbody>';
    file_put_contents($output_file_name, $output_string_header);

    $limit = 200;
    $offset = 0;
    $count = 0;

    // First API call to get the total number of listings found
    $response = browseApi_searchItmByCatId($cat_id, $limit, $offset, $filter, $access_token);
    $data = json_decode($response);

    $total_listings = $data->total;

    if (isset($data->itemSummaries)) {
        $all_item_summaries = $data->itemSummaries;
    }

    $next_url = $data->next ?? null;

    $filename = $cat_id . '.csv';
    if (file_exists($filename)) {
        unlink($filename);
    }

    $product_description = file_get_contents("product_description.txt");

    $fp = fopen($filename, 'w');
    fputcsv($fp, array("Item Type", "Product Name", "Product Type", "Product Code/SKU", "Bin Picking Number", "Brand Name", "Option Set", "Option Set Align", "Product Description", "Price", "Cost Price", "Retail Price", "Sale Price", "Fixed Shipping Cost", "Free Shipping", "Product Warranty", "Product Weight", "Product Width", "Product Height", "Product Depth", "Allow Purchases?", "Product Visible?", "Product Availability", "Track Inventory", "Current Stock Level", "Low Stock Level", "Category", "Product Image File - 1", "Product Image Is Thumbnail - 1", "Product Image File - 2", "Product Image Is Thumbnail - 2", "Product Image File - 3", "Product Image Is Thumbnail - 3", "Search Keywords", "Page Title", "META Keywords", "META Description", "Product Condition", "Show Product Condition?", "Sort Order", "Product Tax Class", "Product UPC/EAN", "Stop Processing Rules", "Product URL", "Redirect Old URL?", "GPS Global Trade Item Number", "GPS Manufacturer Part Number", "GPS Gender", "GPS Age Group", "GPS Color", "GPS Size", "GPS Material", "GPS Pattern", "GPS Item Group ID", "GPS Category", "GPS Enabled", "Tax Provider Tax Code", "Product Custom Fields"));

    do {
        foreach ($all_item_summaries as $result) {
            $count++;

            $title = $result->title;

            // Remove the excluded words from the title
            foreach ($exclude_words_from_title as $word) {
                $title = trim(str_ireplace(trim($word), '', $title));
            }

            $category_name = $cat_name;
            if ($result->categories) {
                foreach ($result->categories as $category) {
                    if ($category->categoryId == $cat_id) {
                        $category_name = $category->categoryName;
                    }
                }
            }

            $thumb_image_1 = $result->image->imageUrl;

            $large_image = '';
            if (isset($result->thumbnailImages[0]->imageUrl)) {
                $large_image = $result->thumbnailImages[0]->imageUrl;
            }

            $price = $result->price->value;
            $condition = $result->condition;
            $shipping_cost = 0;
            if (isset($result->shippingOptions[0]->shippingCost->value)) {
                $shippingCost = $result->shippingOptions[0]->shippingCost->value;
            }

            $itemWebUrl = $result->itemWebUrl;
            $itemWebUrl = explode("&", $itemWebUrl);
            $itemWebUrl = $itemWebUrl[0];

            $img_2 = '';
            if (isset($result->additionalImages[0]->imageUrl)) {
                $img_2 = $result->additionalImages[0]->imageUrl;
            }
            $img_3 = '';
            if (isset($result->additionalImages[1]->imageUrl)) {
                $img_3 = $result->additionalImages[1]->imageUrl;
            }
            $img_4 = '';
            if (isset($result->additionalImages[2]->imageUrl)) {
                $img_4 = $result->additionalImages[2]->imageUrl;
            }
            $img_5 = '';
            if (isset($result->additionalImages[3]->imageUrl)) {
                $img_5 = $result->additionalImages[3]->imageUrl;
            }
            $img_6 = '';
            if (isset($result->additionalImages[4]->imageUrl)) {
                $img_6 = $result->additionalImages[4]->imageUrl;
            }

            $seller = $result->seller->username;

            // Filter the Exclude words and if anything found in the title, skip that.
            $skip = false;
            foreach ($exclude_words as $c) {
                if (strpos($title, $c) !== FALSE) {
                    $skip = true;
                    break;
                }
            }
            if ($skip) continue;

            $output_string .= '<tr>
                    <td>' . $count . '</td>
                    <td>' . $title . '</td>
                    <td>' . $price . '</td>
                    <td>' . $seller . '</td>
                    <td><img src="' . $large_image . '" width="60" /></td>
                 </tr>';

            fputcsv($fp, array("Product", $title, "P", " ", " ", " ", " ", "Right", $product_description, "0", $price, "0", "0", "0", "Y", "6 Month Replacement Warranty", "7", "0", "0", "0", "Y", "Y", "Usually Ships in 1-2 Business Days", "none", "0", "0", $category_name, $large_image, " ", $img_2, "Y", $img_3, " ", " ", " ", " ", " ", $condition_name, "Y", "0", "Default Tax Class", " ", "N", " ", " ", " ", " ", " ", " ", " ", " ", " ", " ", "N", " ", " "));
        }

        $offset += $limit;
        $next_response = browseApi_searchItmByCatId($cat_id, $limit, $offset, $filter, $access_token);
        $next_data = json_decode($next_response);
        $all_item_summaries = $next_data->itemSummaries ?? [];
        $next_url = $next_data->next ?? null;
    } while ($next_url && $offset < $total_listings);

    fclose($fp);

    $output_string_footer = '</tbody></table>
    <script>
    $(document).ready(function() {
        $("#ebayTable").DataTable({
            "pageLength": 50
        });
    });
    </script>
    </body></html>';
    file_put_contents($output_file_name, $output_string_footer, FILE_APPEND);

    echo '<pre>';
    print_r(json_encode($all_item_summaries));
    echo '</pre>';
}
?>


































$limit = 200; // Number of records per API call
$batch_limit = 10000; // Maximum number of records to fetch per "next" URL
$offset = 0;
$all_item_summaries = [];

do {
    $response = browseApi_searchItmByCatId($cat_id, $limit, $offset, $filter, $access_token);
    $data = json_decode($response);
    $total_listings = $data->total;

    if (isset($data->itemSummaries)) {
        $all_item_summaries = array_merge($all_item_summaries, $data->itemSummaries);
    }

    // Write the data to CSV and HTML after every batch of 200 records
    if (!empty($all_item_summaries)) {
        $output_fp = fopen($output_file_name, 'a');
        $output_string = '';

        foreach ($all_item_summaries as $result) {
            $count++;

            $itemId = $result->legacyItemId;
            $itemId_onEbay = $result->itemId ?? "";
            $title = $result->title;

            foreach ($exclude_words_from_title as $word) {
                $title = trim(str_ireplace(trim($word), '', $title));
            }

            $category_name = $cat_name;
            if ($result->categories) {
                foreach ($result->categories as $category) {
                    if ($category->categoryId == $cat_id) {
                        $category_name = $category->categoryName;
                    }
                }
            }

            $thumb_image_1 = $result->image->imageUrl;
            $large_image = $result->thumbnailImages[0]->imageUrl ?? '';

            $price = $result->price->value;
            $condition = $result->condition;
            $shippingCost = $result->shippingOptions[0]->shippingCost->value ?? 0;
            $itemWebUrl = explode("&", $result->itemWebUrl)[0];

            $img_2 = $result->additionalImages[0]->imageUrl ?? '';
            $img_3 = $result->additionalImages[1]->imageUrl ?? '';
            $img_4 = $result->additionalImages[2]->imageUrl ?? '';
            $img_5 = $result->additionalImages[3]->imageUrl ?? '';
            $img_6 = $result->additionalImages[4]->imageUrl ?? '';

            $seller = $result->seller->username;

            foreach ($exclude_words as $c) {
                if (strpos($title, $c) !== FALSE) {
                    continue 2; // Skip this item if it contains any excluded words
                }
            }

            $output_string .= '<tr>
                <td>' . $count . '</td>
                <td>' . $title . '</td>
                <td>' . $price . '</td>
                <td>' . $seller . '</td>
                <td><img src="' . $large_image . '" width="60" /></td>
            </tr>' . "\n";

            fputcsv($fp, array("Product", $title, "P", " ", " ", " ", " ", "Right", $product_description, "0", $price, "0", "0", "0", "Y", "6 Month Replacement Warranty", "7", "0", "0", "0", "Y", "Y", "Usually Ships in 1-2 Business Days", "none", "0", "0", $category_name, $large_image, " ", $img_2, "Y", $img_3, " ", " ", " ", "unique_string", $itemId_onEbay, $condition_name, "Y", "0", "Default Tax Class", " ", "N", " ", " ", " ", " ", " ", " ", " ", " ", " ", " ", "N", " ", " ", $itemId_onEbay));
        }

        fwrite($output_fp, $output_string);
        fclose($output_fp);
    }

    $offset += $limit;

    // If the current batch has reached the 10,000 records limit, reset and start a new batch
    if ($offset % $batch_limit === 0) {
        $offset = 0; // Reset offset for the new batch
        $current_offset += $batch_limit; // Increase the current offset to fetch the next 10,000 records
    }

    // Prepare for the next API call
    $next_url = $data->next ?? null;

    // If there's no next URL or we've fetched all items, break out of the loop
    if (!$next_url || $offset > $total_listings) {
        break;
    }

} while ($offset <= $total_listings);

fclose($fp);

// Write the footer to the HTML output file
$output_string_footer = '</table>';
file_put_contents($output_file_name, $output_string_footer, FILE_APPEND);

echo json_encode(["success" => true, "offset" => $offset]);
return;


















function write_data_in_files($next_data) {
    $output_file_name = 'output_string_temp.html';
    $filename = 'output.csv';

    // Open HTML file in append mode
    $output_fp_html = fopen($output_file_name, 'a');

    // Open CSV file in append mode
    $fp_csv = fopen($filename, 'a');

    // Initialize HTML string
    $output_string = '';

    foreach ($next_data->itemSummaries as $result) {
        $title = $result->title;
        $price = $result->price->value;
        $seller = $result->seller->username;

        // Handling images
        $large_image = isset($result->thumbnailImages[0]->imageUrl) ? $result->thumbnailImages[0]->imageUrl : '';

        $category_name = 'Default Category';
        if ($result->categories) {
            foreach ($result->categories as $category) {
                $category_name = $category->categoryName;
            }
        }

        // Building the HTML row
        $output_string .= '<tr>
            <td>' . $title . '</td>
            <td>' . $price . '</td>
            <td>' . $seller . '</td>
            <td>';
        $output_string .= '<img src="' . $large_image . '" width="60" />&nbsp;';
        $output_string .= '</td>
        </tr>' . "\n";

        // Writing to CSV
        fputcsv($fp_csv, [
            "Product", $title, "P", " ", " ", " ", " ", "Right", "Product description here", "0", $price, "0", "0", "0", "Y", 
            "6 Month Replacement Warranty", "7", "0", "0", "0", "Y", "Y", "Usually Ships in 1-2 Business Days", "none", "0", "0", 
            $category_name, $large_image, " ", " ", "Y", " ", " ", " ", "unique_string", $result->itemId ?? "", 
            $result->condition ?? "", "Y", "0", "Default Tax Class", " ", "N", " ", " ", " ", " ", " ", " ", " ", " ", 
            " ", " ", " ", " ", " ", "N", " ", " ", $result->itemId ?? ""
        ]);
    }

    // Write the HTML rows to the file
    fwrite($output_fp_html, $output_string);

    // Close the file handles
    fclose($output_fp_html);
    fclose($fp_csv);
}
