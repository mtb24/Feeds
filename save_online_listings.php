<?php

/*
//////////////////////////////
//////////////////////////////
//this is save_online_listings.php

//this script saves the data 
//in ONLINE_LISTINGS to a remote text file
//////////////////////////////
//////////////////////////////
*/


//increased error reporting
error_reporting(E_ALL | E_STRICT);
ini_set('display_errors', 'On');

//include our functions file
require_once('config.php');
require_once('functions.php');


//name of the output file
$local_outfile = 'upload/googlebase_products.txt';
$remote_outfile = $feedSettings[4]['upload_path'];


////////////////////////////
//MAIN

//string which we'll concat all the data on to
//we'll initialize it as the column titles
$data_string = 	'link' . "\t" . 
		'condition' . "\t" . 
		'brand' . "\t" . 
		'title' . "\t" . 
		'description' . "\t" .
		'image link' . "\t" . 
		'product type' . "\t" . 
		'google product category' . "\t" . 
		'price' . "\t" . 
		'availability' . "\t" . 
		'expiration date' . "\t" .
		'id' . "\t" .
		'mpn' . "\t" . 
		'gtin' . "\t" . 
		'color' . "\t" . 
		'size' . "\t" . 
		'shipping_weight' . "\t" .
		'shipping' . "\t" .
		'adwords_labels' . "\n"; 


/////////////////////
//here's the query
$query = "select * from ONLINE_LISTINGS where 1";
$results = mysql_query($query);
//check for general error
if(!$results){
	$error_message = "Im sorry, there was a database select error";
	errorHandler($error_message);
}
/////////////////////



//////////////////////////////////////
//////////////////////////////////////
//loop through the results and concat all the
//pertinent data on to the string
while( $row = mysql_fetch_assoc($results) ){

	//get the values we need
	$link = stripslashes($row['Link']);
	$condition = stripslashes($row['ItemCondition']);
	$brand = stripslashes($row['Brand']);
	$title = stripslashes($row['Title']);
	$description = stripslashes($row['Description']);
	$image_link = stripslashes($row['ImageLink']);
	$product_type = stripslashes($row['ProductType']);
	$google_product_category = stripslashes($row['GoogleProductCategory']);
	$price = stripslashes($row['Price']);
	$availability = stripslashes($row['Availability']);
	$expiration_date = stripslashes($row['ExpirationDate']);
	$item_id = stripslashes($row['id']);
	$mpn = stripslashes($row['MPN']);
	$gtin = stripslashes($row['GTIN']);
	$color = stripslashes($row['Color']);
	$size = stripslashes($row['Size']);
	$shipping_weight = stripslashes($row['ShippingWeight']);
	$shipping = stripcslashes($row['shipping']);
	$adwords_labels = stripslashes($row['MPN']);

	//concat the google affiliate param
	//the macro is defined at the top of the functions file
	$link .= GOOGLE_AFFILIATE_ID;

	//concat the data onto our string
	//using tabs as delimiters
	$data_string .= $link . "\t" . 
			$condition . "\t" . 
			$brand . "\t" . 
			$brand." ".$title . "\t" . 
			$description . "\t" . 
			$image_link . "\t" . 
			$product_type . "\t" . 
			$google_product_category . "\t" . 
			"USD $price" . "\t" . 
			$availability . "\t" . 
			"". "\t" . 
			$item_id . "\t" . 
			$mpn . "\t" . 
			$gtin . "\t" . 
			$color . "\t" . 
			$size . "\t" . 
			$shipping_weight . " lb" . "\t" .
			$shipping . "\t" .
			$adwords_labels . "\n"; 
}

//////////////////////////////////////
//////////////////////////////////////

//save the data to a local text file
echo "Creating GoogleBase Products file<br>";
saveLocalTextFile($data_string, $local_outfile);

////////////////////////////
// Now upload file via FTP
echo "Uploading GoogleBase Products file...<br />";
uploadFile($remote_outfile,$local_outfile,'GoogleBase Products');
?>