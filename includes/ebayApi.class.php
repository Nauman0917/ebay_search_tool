<?php

function find_item_by_category($callname, $application_id, $cat_id, $country, $start_price, $end_price, $excludedSellers)
{

	$call_url = 'https://svcs.ebay.com/services/search/FindingService/v1?';

	$headers = array(
		"X-EBAY-SOA-OPERATION-NAME: $callname",
		"X-EBAY-SOA-SERVICE-VERSION: 1.13.0",
		"X-EBAY-SOA-SECURITY-APPNAME: $application_id",
		"X-EBAY-SOA-GLOBAL-ID: $country"
	);

	print_r($headers);

	$request_xml = '<?xml version="1.0" encoding="UTF-8"?>
						<findItemsByCategoryRequest xmlns="http://www.ebay.com/marketplace/search/v1/services">
							<categoryId>' . $cat_id . '</categoryId>
							<itemFilter>
								<name>HideDuplicateItems</name>
								<value>true</value>
							</itemFilter>
							<itemFilter>
								<name>ListingType</name>
								<value>FixedPrice</value>
							</itemFilter>
							<itemFilter>
								<name>Condition</name>
								<value>New</value>
							</itemFilter>
							<itemFilter>
								<name>MinPrice</name>
								<paramName>Currency</paramName>
    							<paramValue>USD</paramValue>
								<value>' . $start_price . '</value>
							</itemFilter>
							<itemFilter>
								<name>MaxPrice</name>
								<paramName>Currency</paramName>
    							<paramValue>USD</paramValue>
								<value>' . $end_price . '</value>
							</itemFilter>';
	$count_ex_seller = 0;
	if ($excludedSellers[0] != '') {
		$count_ex_seller = count($excludedSellers);
	}
	if ($count_ex_seller > 0) {
		$request_xml .= '<itemFilter>
								<name>ExcludeSeller</name>';
		foreach ($excludedSellers as $excludedSeller) {
			$request_xml .= '<value>' . $excludedSeller . '</value>';
		}
		$request_xml .= '</itemFilter>';
	}
	$request_xml .= '<outputSelector>SellerInfo</outputSelector>
							<outputSelector>PictureURLSuperSize</outputSelector>
							<outputSelector>StoreInfo</outputSelector>
							<outputSelector>GalleryInfo</outputSelector>
							<sortOrder>BestMatch</sortOrder>
							<paginationInput>
								<entriesPerPage>10</entriesPerPage>
							</paginationInput>
						</findItemsByCategoryRequest>';

	echo $request_xml;

	$curl = curl_init();
	curl_setopt($curl, CURLOPT_URL, $call_url);


	curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
	curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
	curl_setopt($curl, CURLOPT_HEADER, 0);
	curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
	curl_setopt($curl, CURLOPT_POST, 1);
	curl_setopt($curl, CURLOPT_POSTFIELDS, $request_xml);
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
	$contant = curl_exec($curl);

	curl_close($curl);

	//echo $contant;

	return $contant;
}

function browseApi_searchItmByCatId($cat_id, $limit, $offset, $filter, $access_token)
{
	$headers = array(
		"Authorization: Bearer $access_token",
		"Content-Type:application/json"
	);

	$call_url = "https://api.ebay.com/buy/browse/v1/item_summary/search?category_ids=$cat_id&limit=$limit&offset=$offset&fieldgroups=MATCHING_ITEMS,ASPECT_REFINEMENTS&$filter";

	//echo "CALLL URL ---> $call_url <br>";

	$curl = curl_init();
	curl_setopt($curl, CURLOPT_URL, $call_url);


	curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
	curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
	curl_setopt($curl, CURLOPT_HEADER, 0);
	curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
	//curl_setopt($curl, CURLOPT_POST, 1);
	//curl_setopt($curl, CURLOPT_POSTFIELDS, $request_xml);
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
	$contant = curl_exec($curl);

	curl_close($curl);

	return $contant;
}

function fetchEbayData($cat_id, $limit, $offset, $filter, $access_token)
{
	$headers = array(
		"Authorization: Bearer $access_token",
		"Content-Type: application/json"
	);

	$all_item_summaries = array();

	$call_url = "https://api.ebay.com/buy/browse/v1/item_summary/search?category_ids=$cat_id&limit=$limit&offset=$offset&fieldgroups=MATCHING_ITEMS,ASPECT_REFINEMENTS&filter=$filter";

	do {
		$curl = curl_init();
		curl_setopt($curl, CURLOPT_URL, $call_url);
		curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
		curl_setopt($curl, CURLOPT_HEADER, 0);
		curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);

		$response = curl_exec($curl);

		curl_close($curl);

		$data = json_decode($response, true);

		if (isset($data['itemSummaries'])) {
			$all_item_summaries = array_merge($all_item_summaries, $data['itemSummaries']);
		}

		// Check if 'next' URL is present in the response
		if (isset($data['href']) && isset($data['next'])) {
			$call_url = $data['next'];
		} else {
			$call_url = null;
		}

	} while ($call_url);

	return json_encode($all_item_summaries);
}

function browseApi_searchItmByCatId_next($call_url, $access_token)
{
	$headers = array(
		"Authorization: Bearer $access_token",
		"Content-Type:application/json"
	);


	$curl = curl_init();
	curl_setopt($curl, CURLOPT_URL, $call_url);


	curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
	curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
	curl_setopt($curl, CURLOPT_HEADER, 0);
	curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
	//curl_setopt($curl, CURLOPT_POST, 1);
	//curl_setopt($curl, CURLOPT_POSTFIELDS, $request_xml);
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
	$contant = curl_exec($curl);

	curl_close($curl);

	return $contant;
}

function get_application_token($application_id, $certificate_id)
{

	$headers = array(
		'Content-Type: application/x-www-form-urlencoded',
		'Authorization: ' . sprintf('Basic %s', base64_encode(sprintf('%s:%s', $application_id, $certificate_id)))
	);

	$call_url = 'https://api.ebay.com/identity/v1/oauth2/token';

	$request_xml = "grant_type=client_credentials&scope=https%3A%2F%2Fapi.ebay.com%2Foauth%2Fapi_scope";

	$curl = curl_init();
	$res = curl_setopt($curl, CURLOPT_URL, $call_url);


	curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
	curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
	curl_setopt($curl, CURLOPT_HEADER, 0);
	curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
	curl_setopt($curl, CURLOPT_POST, 1);
	curl_setopt($curl, CURLOPT_POSTFIELDS, $request_xml);
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
	$contant = curl_exec($curl);

	curl_close($curl);

	return $contant;
}

function get_item($item_id)
{

	global $trading_ebay_token, $trading_compatible_level, $trading_dev_id, $trading_app_id, $trading_cert_id, $trading_ebay_call_url, $site_id;
	;

	$request_xml = '<?xml version="1.0" encoding="utf-8"?>
					<GetItemRequest xmlns="urn:ebay:apis:eBLBaseComponents">
						<ItemID>' . $item_id . '</ItemID>
						<RequesterCredentials>
							<eBayAuthToken>' . $trading_ebay_token . '</eBayAuthToken>
						</RequesterCredentials>
						<IncludeItemSpecifics>true</IncludeItemSpecifics>
						<OutputSelector>Item.ItemSpecifics</OutputSelector>
						<WarningLevel>High</WarningLevel>
					</GetItemRequest>​​';

	//echo $request_xml;

	$headers = array(
		"X-EBAY-API-COMPATIBILITY-LEVEL: $trading_compatible_level",
		"X-EBAY-API-DEV-NAME: $trading_dev_id",
		"X-EBAY-API-APP-NAME: $trading_app_id",
		"X-EBAY-API-CERT-NAME: $trading_cert_id",
		"X-EBAY-API-SITEID: $site_id",
		"X-EBAY-API-CALL-NAME: GetItem"
	);

	$curl = curl_init();
	$res = curl_setopt($curl, CURLOPT_URL, $trading_ebay_call_url);


	curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
	curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);

	curl_setopt($curl, CURLOPT_HEADER, 0);
	curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
	curl_setopt($curl, CURLOPT_POST, 1);
	curl_setopt($curl, CURLOPT_POSTFIELDS, $request_xml);
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
	$contant = curl_exec($curl);

	curl_close($curl);

	return $contant;
}
