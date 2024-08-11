<?php
function update_keypair($arr, $key, $val)
{
   if(empty($arr[$key])) {
	    $arr[$key] = array($val);
   }
   else {
	   $arr[$key][] = $val;
   }

   return $arr;
}


function get_woo_price($ebay_price) {

	$ebay_price = (float)$ebay_price;

	$sql = "SELECT profit_percentage FROM `tbl_nhs_settings` WHERE `settings_id` = 1 ";
	$rs = mysqli_query($GLOBALS["___mysqli_ston"], $sql);
	$row = mysqli_fetch_array($rs);
		
		
	$profit_percentage = trim($row['profit_percentage']);
	
	$woo_price = $ebay_price + ($ebay_price * $profit_percentage/100);

	return $woo_price;

}

function Pages($path,$table,$is_listed,$perpage) {
  			
			
			$sql = "SELECT COUNT(*) AS 'tot' FROM $table WHERE is_listed_to_woo = $is_listed";
			
			
			$row = mysqli_fetch_array(mysqli_query($GLOBALS["___mysqli_ston"], $sql));
			//print_r($row);
			$total_pages = $row['tot'];
						
			
			$adjacents = "2";
			if(isset($_GET['page'])) {
			$page = $_GET['page'];
			//echo $page;
			}
			else
			$page=1;
			
			$noofpages=ceil($total_pages/$perpage);
				//$page = $_GET['page'];
			if($page)
			$start = ($page - 1) * $perpage;
			else
			$start = 0;
		
		   if ($page == 0) $page = 1;
			$prev = $page - 1;
			$next = $page + 1;
			$lastpage = ceil($total_pages/$perpage);
			$lpm1 = $lastpage - 1;
		
			$pagination = "";
		if($lastpage > 1) {   
			$pagination .= "<div class='pagination'>";
		if ($page > 1)
			$pagination.= "<a href='".$path."page=$prev'>&laquo; Previous</a>";
		else
			$pagination.= "<span class='disabled'>&laquo; Previous</span>";   
			
		if ($lastpage < 7 + ($adjacents * 2))
			{   
		  for ($counter = 1; $counter <= $lastpage; $counter++)	{
			if ($counter == $page)
				$pagination.= "<span class='current'>$counter</span>";
			else
				$pagination.= "<a href='".$path."page=$counter'>$counter</a>";                   
		  }
			}
			elseif($lastpage > 5 + ($adjacents * 2))
			{
			if($page < 1 + ($adjacents * 2))       
			{
			for ($counter = 1; $counter < 4 + ($adjacents * 2); $counter++)
			{
			if ($counter == $page)
				$pagination.= "<span class='current'>$counter</span>";
			else
				$pagination.= "<a href='".$path."page=$counter'>$counter</a>";                   
			}
				$pagination.= "...";
				$pagination.= "<a href='".$path."page=$lpm1'>$lpm1</a>";
				$pagination.= "<a href='".$path."page=$lastpage'>$lastpage</a>";       
			}
			elseif($lastpage - ($adjacents * 2) > $page && $page > ($adjacents * 2))
			{
				$pagination.= "<a href='".$path."page=1'>1</a>";
				$pagination.= "<a href='".$path."page=2'>2</a>";
				$pagination.= "...";
			for ($counter = $page - $adjacents; $counter <= $page + $adjacents; $counter++)
			{
			if ($counter == $page)
				$pagination.= "<span class='current'>$counter</span>";
			else
				$pagination.= "<a href='".$path."page=$counter'>$counter</a>";                   
			}
				$pagination.= "..";
				$pagination.= "<a href='".$path."page=$lpm1'>$lpm1</a>";
				$pagination.= "<a href='".$path."page=$lastpage'>$lastpage</a>";       
			}
			else
			{
				$pagination.= "<a href='".$path."page=1'>1</a>";
				$pagination.= "<a href='".$path."page=2'>2</a>";
				$pagination.= "..";
			for ($counter = $lastpage - (2 + ($adjacents * 2)); $counter <= $lastpage; $counter++)
			{
			if ($counter == $page)
				$pagination.= "<span class='current'>$counter</span>";
			else
				$pagination.= "<a href='".$path."page=$counter'>$counter</a>";                   
			}
			}
			}
			
			if ($page < $counter - 1)
				$pagination.= "<a href='".$path."page=$next'>Next &raquo;</a>";
			else
				$pagination.= "<span class='disabled'>Next &raquo;</span>";
				$pagination.= "</div>\n";       
		}


	return $pagination;
}



function download_images($url) {

	$image_name = explode("/",$url);
	$image_name = end($image_name);
	$image_name = str_replace(array("%","&"),"",$image_name);
	
	$img = 'eBayImages/'.$image_name;
	file_put_contents($img, file_get_contents($url));
	
	return $img;	
	
}


function remove_special_chars($string) {
   $string = str_replace(' ', '-', $string);
   
   return preg_replace('/[\/\&£%#\$]/', '', $string); 
}

function getExtension($str) 
{ 
	$i = strrpos($str,".");
	if (!$i) { return ""; } 
	$l = strlen($str) - $i; 
	$ext = substr($str,$i+1,$l); 
	return $ext; 
}

function get_data($url)
{
    
    $ch = curl_init();
    
    
    $header[0] = "Accept: text/xml,application/xml,application/xhtml+xml,";
    $header[0] .= "text/html;q=0.9,text/plain;q=0.8,image/png,*/*;q=0.5";
    $header[] = "Cache-Control: max-age=0";
    $header[] = "Connection: keep-alive";
    $header[] = "Keep-Alive: 300";
    $header[] = "Accept-Charset: ISO-8859-1,utf-8;q=0.7,*;q=0.7";
    $header[] = "Accept-Language: en-us,en;q=0.5";
    $header[] = "Pragma: ";
    
    curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows; U; Windows NT 5.2; en-US; rv:1.8.1.7) Gecko/20070914 Firefox/2.0.0.7');
    curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
    //curl_setopt($ch, CURLOPT_COOKIEJAR, "cookies_asin.txt");
    //curl_setopt($ch, CURLOPT_COOKIEFILE, "cookies_asin.txt");
    curl_setopt($ch, CURLOPT_AUTOREFERER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    
    
    curl_setopt($ch, CURLOPT_URL, $url);
    $data = curl_exec($ch);
    curl_close($ch);
    
    return $data;
}




	
?>