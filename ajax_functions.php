<?php
set_time_limit(0);
error_reporting(E_ALL);
ini_set("memory_limit", "-1");
//include('includes/config.php');
include('includes/functions.php');
include('includes/ebayKeys.php');
include('includes/ebayApi.class.php');

$action = trim($_POST['action']);

if ($action == 'get_ebay_details') {
    try {
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
            $access_token = trim((string) $xml_auth->access_token);
        }

        $cat_id = trim($_POST['cat_id']);
        $cat_name = trim($_POST['categoryName']);

        $current_offset = intval(trim($_POST['currentOffset']));

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

        //SET ALL THE FILTERS HERE....
        $filter = "";

        // $filter_freeship = "filter=maxDeliveryCost:0";
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

        $limit = 200;
        $offset = 0;
        $offset_limit = 1000;
        $count = 0;

        if ($current_offset !== 0) {
            $offset = $current_offset;

            if ($offset % $limit !== 0) {
                $offset = floor($offset / $limit) * $limit;
            }
            $offset_limit = $offset + 1000;
        }

        $next_url = null;
        $all_item_summaries = [];

        $response = browseApi_searchItmByCatId($cat_id, $limit, $offset, $filter, $access_token);
        $data = json_decode($response);
        $total_listings = $data->total;
        $offset += $limit;

        $total_listings_remaining = intval($total_listings) - $offset;

        $filename = $cat_id . '.csv';
        if (file_exists($filename)) {
            unlink($filename);
        }

        // Read the product description from the file
        $product_description = file_get_contents("product_description.txt");

        $fp = fopen($filename, 'w');
        fputcsv($fp, array("Item Type", "Product Name", "Product Type", "Product Code/SKU", "Bin Picking Number", "Brand Name", "Option Set", "Option Set Align", "Product Description", "Price", "Cost Price", "Retail Price", "Sale Price", "Fixed Shipping Cost", "Free Shipping", "Product Warranty", "Product Weight", "Product Width", "Product Height", "Product Depth", "Allow Purchases?", "Product Visible?", "Product Availability", "Track Inventory", "Current Stock Level", "Low Stock Level", "Category", "Product Image File - 1", "Product Image Is Thumbnail - 1", "Product Image File - 2", "Product Image Is Thumbnail - 2", "Product Image File - 3", "Product Image Is Thumbnail - 3", "Search Keywords", "Page Title", "META Keywords", "META Description", "Product Condition", "Show Product Condition?", "Sort Order", "Product Tax Class", "Product UPC/EAN", "Stop Processing Rules", "Product URL", "Redirect Old URL?", "GPS Global Trade Item Number", "GPS Manufacturer Part Number", "GPS Gender", "GPS Age Group", "GPS Color", "GPS Size", "GPS Material", "GPS Pattern", "GPS Item Group ID", "GPS Category", "GPS Enabled", "Tax Provider Tax Code", "Product Custom Fields", "Product Id on Ebay"));

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

        if (isset($data->itemSummaries)) {
            $all_item_summaries = array_merge($all_item_summaries, $data->itemSummaries);
        }

        $next_url = $data->next ?? null;

        do {
            if ($next_url) {
                $next_response = browseApi_searchItmByCatId_next($next_url, $access_token);
                $next_data = json_decode($next_response);

                if (isset($next_data->itemSummaries)) {
                    $all_item_summaries = array_merge($all_item_summaries, $next_data->itemSummaries);
                }
                $next_url = $next_data->next ?? null;
            } else {
                if (count($all_item_summaries) >= $limit) {
                    $next_response = browseApi_searchItmByCatId($cat_id, $limit, $offset, $filter, $access_token);

                    if (json_last_error() !== JSON_ERROR_NONE || !isset($data->itemSummaries)) {
                        echo json_encode(["success" => false, "error" => "Error fetching data."]);
                        break;
                    }

                    $next_data = json_decode($next_response);

                    if (isset($next_data->itemSummaries)) {
                        $all_item_summaries = array_merge($all_item_summaries, $next_data->itemSummaries);
                    }

                    $next_url = $next_data->next ?? null;
                }
            }

            $offset = $offset + $limit;

            $output_string = '';

            if (isset($all_item_summaries) && is_array($all_item_summaries)) {
                $output_fp = fopen($output_file_name, 'a');

                foreach ($all_item_summaries as $result) {
                    $count++;

                    $itemId = $result?->legacyItemId;
                    $itemId_onEbay = $result?->itemId ?? "";
                    $title = $result?->title;

                    // // Remove the excluded words from the title
                    foreach ($exclude_words_from_title as $word) {
                        $title = trim(str_ireplace(trim($word), '', $title));
                    }

                    $category_name = $cat_name;
                    if ($result?->categories) {
                        foreach ($result?->categories as $category) {
                            if ($category?->categoryId == $cat_id) {
                                $category_name = $category?->categoryName;
                            }
                        }
                    }

                    $large_image = '';
                    if (isset($result?->thumbnailImages[0]?->imageUrl)) {
                        $large_image = $result?->thumbnailImages[0]?->imageUrl;
                    }

                    $price = $result?->price?->value ?? "";
                    $condition = $result?->condition;
                    $shipping_cost = 0;
                    if (isset($result?->shippingOptions[0]?->shippingCost?->value)) {
                        $shippingCost = $result?->shippingOptions[0]?->shippingCost?->value;
                    }

                    $itemWebUrl = $result?->itemWebUrl;
                    $itemWebUrl = explode("&", $itemWebUrl);
                    $itemWebUrl = $itemWebUrl[0] ?? "";

                    $img_2 = '';
                    if (isset($result?->additionalImages[0]?->imageUrl)) {
                        $img_2 = $result?->additionalImages[0]?->imageUrl;
                    }
                    $img_3 = '';
                    if (isset($result?->additionalImages[1]?->imageUrl)) {
                        $img_3 = $result?->additionalImages[1]?->imageUrl;
                    }
                    $img_4 = '';
                    if (isset($result?->additionalImages[2]?->imageUrl)) {
                        $img_4 = $result?->additionalImages[2]?->imageUrl;
                    }
                    $img_5 = '';
                    if (isset($result?->additionalImages[3]?->imageUrl)) {
                        $img_5 = $result?->additionalImages[3]?->imageUrl;
                    }
                    $img_6 = '';
                    if (isset($result?->additionalImages[4]?->imageUrl)) {
                        $img_6 = $result?->additionalImages[4]?->imageUrl;
                    }

                    $seller = $result?->seller?->username;

                    foreach ($exclude_words as $c) {
                        if (strpos($title, $c) !== FALSE) {
                            continue;
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

                    fputcsv($fp, array("Product", $title, "P", " ", " ", " ", " ", "Right", $product_description, "0", $price, "0", "0", "0", "Y", "6 Month Replacement Warranty", "7", "0", "0", "0", "Y", "Y", "Usually Ships in 1-2 Business Days", "none", "0", "0", $category_name, $large_image, " ", $img_2, "Y", $img_3, " ", " ", " ", "unique_string", $itemId_onEbay, $condition_name, "Y", "0", "Default Tax Class", " ", "N", " ", " ", " ", " ", " ", " ", " ", " ", " ", " ", " ", " ", "N", " ", " ", $itemId_onEbay));
                }
            }

            fwrite($output_fp, $output_string);
            fclose($output_fp);
            
            if ($offset >= $offset_limit) {
                break;
            }

            $next_url = $next_data->next ?? null;
        } while (count($all_item_summaries) < $total_listings_remaining);

        fclose($fp);

        $output_string_footer = '</table>';
        file_put_contents($output_file_name, $output_string_footer, FILE_APPEND);

        if (count($all_item_summaries) < $total_listings) {
            echo json_encode(["success" => true, "offset" => $offset]);
        } else {
            echo json_encode(["success" => true]);
        }
        return;
    } catch (Exception $error) {
        echo json_encode(["success" => false, "error" => $error->getMessage()]);
        return;
    }
}
