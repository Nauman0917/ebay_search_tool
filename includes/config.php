<?php
$db_host = "localhost";
$db_name = "nhsweblk_common_db";
$db_username = "nhsweblk_common";
$db_pass = "WeCallYou@99Names";

//Localhost
// $db_host = "localhost";
// $db_name = "ebay";
// $db_username = "root";
// $db_pass = "";

$dbc=($GLOBALS["___mysqli_ston"] = mysqli_connect("$db_host", "$db_username", "$db_pass")) or die(mysqli_error($GLOBALS["___mysqli_ston"]));
mysqli_select_db($GLOBALS["___mysqli_ston"], $db_name) or die("no database by that name");


mysqli_query($GLOBALS["___mysqli_ston"], "CREATE TABLE IF NOT EXISTS `products` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `keyword` varchar(255) NOT NULL,
  `ebay_id` varchar(20) NOT NULL,
  `title` varchar(2000) NOT NULL,
  `cat_id` int(11) NOT NULL,
  `cat_name` varchar(500) NOT NULL,
  `ebay_price` decimal(10,2) NOT NULL,
  `qty` int(11) NOT NULL,
  `pic_url` varchar(2000) NOT NULL,
  `itm_url` varchar(1000) NOT NULL,
  `description` TEXT NOT NULL,
  `condition_tag` varchar(20) NOT NULL,
  `sku` varchar(50) NOT NULL,
  `shipping_cost` decimal(10,2) NOT NULL,
  `last_update` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `created_date` datetime NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `ebay_id` (`ebay_id`)
) ");

mysqli_query($GLOBALS["___mysqli_ston"], "CREATE TABLE IF NOT EXISTS `ebay_keys` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `app_id` varchar(255) NOT NULL,
  `dev_id` varchar(255) NOT NULL,
  `cert_id` varchar(255) NOT NULL,
  `is_exceeded` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `app_id` (`app_id`)
) ");



?>