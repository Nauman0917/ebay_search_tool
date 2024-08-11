<?php
set_time_limit(0);
error_reporting(0);

include('includes/ebayKeys.php');
include('includes/ebayApi.class.php');

$filename = $_FILES['file']['name'];

if(is_uploaded_file($_FILES['file']['tmp_name'])) {
				
	$fp = fopen($_FILES['file']['name'], 'w');

	fputcsv($fp, array("Item Type","Product Name","Product Type","Product Code/SKU","Bin Picking Number","Brand Name","Option Set","Option Set Align","Product Description","Price","Cost Price","Retail Price","Sale Price","Fixed Shipping Cost","Free Shipping","Product Warranty","Product Weight","Product Width","Product Height","Product Depth","Allow Purchases?","Product Visible?","Product Availability","Track Inventory","Current Stock Level","Low Stock Level","Category","Product Image File - 1","Product Image Is Thumbnail - 1","Product Image File - 2","Product Image Is Thumbnail - 2","Product Image File - 3","Product Image Is Thumbnail - 3","Search Keywords","Page Title","META Keywords","META Description","Product Condition","Show Product Condition?","Sort Order","Product Tax Class","Product UPC/EAN","Stop Processing Rules","Product URL","Redirect Old URL?","GPS Global Trade Item Number","GPS Manufacturer Part Number","GPS Gender","GPS Age Group","GPS Color","GPS Size","GPS Material","GPS Pattern","GPS Item Group ID","GPS Category","GPS Enabled","Tax Provider Tax Code","Product Custom Fields") );

	$x = 0;
	if (($handle = fopen($_FILES['file']['tmp_name'], "r")) !== FALSE) {
		
		$count_insert = 0;
		while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {					
			
			//print_r($data);
			
			if($x == 0) {
				$x++;
				continue;	
			}

			$title = $data[1];
			$sku = $data[3];
			$brand_name = $data[5];
			$cost_price = $data[9];
			$price = $data[10];
			$retail_price = $data[11];
			$sale_price = $data[12];
			$product_warranty = $data[15];
			$product_weight = $data[16];
			$product_width = $data[17];
			$product_height = $data[18];
			$product_deep = $data[19];
			$product_availability = $data[22];
			$current_stock = $data[24];
			$low_stock = $data[25];
			$cat_name = $data[26];
			$large_image = $data[27];
			$img_2 = $data[29];
			$img_3 = $data[31];
			$condition_name = $data[37];
			

			//Extract the Product URL and get the eBay ID
			$ebay_id_url = $data[8];
			$ebay_id = explode("?",$ebay_id_url);
			$ebay_id = explode("/",$ebay_id[0]);
			$ebay_id = end($ebay_id);

			

			$get_item = get_item($ebay_id);
			$xml_getItem = new SimpleXMLElement($get_item);
			// print_r($get_item);
			// return;

			$item_specifics = $xml_getItem->Item->ItemSpecifics->NameValueList;

			$items_specs_write = '';
			$items_specs_write .= '<ul>';

				$item_specs_condition = '';
				if($condition_name == 'NEW' ) {
					
					$item_specs_condition = '<li> Condition : New  </li>';

				}
				elseif($condition_name == 'USED' ) {

					$item_specs_condition = '<li> Condition : Used  </li>';
					
				}

			if(count($item_specifics) > 0 ) {

				$items_specs_write .= $item_specs_condition;  //Write the condition details in <li>

				foreach($item_specifics as $item_specs) {

					$specs_name = $item_specs->Name;
					$specs_value  = $item_specs->Value;

					$items_specs_write .= '<li>'.$specs_name .' : '.$specs_value.'</li>';


				}
				
			}

			$items_specs_write .= '</ul>';
			
			// echo $items_specs_write;
			// echo '<hr>';

			//Write the Item Specs into the CSV by replace the 8th column value.
			fputcsv($fp, array("Product",$title,"P",$sku," ",$brand_name," ","Right",$items_specs_write,$cost_price,$price,$retail_price,$sale_price,"0","Y",$product_warranty,$product_weight,$product_width,$product_height,$product_deep,"Y","Y",$product_availability,"none",$current_stock,$low_stock,$cat_name,$large_image," ",$img_2,"Y",$img_3," "," "," "," "," ",$condition_name,"Y","0","Default Tax Class"," ","N"," "," "," "," "," "," "," "," "," "," "," "," ","N"," "," ") );

		
		}
		
	}

	if(file_exists($filename)) {					
					
		echo '<a href="download_csv.php?fname='.$filename.'"> <h4 align="center" style="background-color:#6C9;"> Click to Download the Final CSV </h4> </a>'; 
		
	}

	//echo $filename;

}


	

    


?>