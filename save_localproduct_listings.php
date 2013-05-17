<?php

/*
//////////////////////////////
//////////////////////////////
// save_localproduct_listings.php

// this script saves the data 
// in LOCAL_PRODUCT_LISTINGS to a remote text file
// then uploads it to Google
//////////////////////////////
//////////////////////////////
*/


//increased error reporting
error_reporting(E_ALL | E_STRICT);
ini_set('display_errors', 'On');

//include our functions file
require_once('config.php');
require_once('mb_feeds_functions.php');

//name of the output file
$local_outfile = 'upload/google_local_products.txt';
$remote_outfile = $feedSettings[2]['upload_path'];



////////////////////////////
//MAIN

//string which we'll concat all the data on to
//we'll initialize it as the column titles
$data_string = 'itemid' . "\t" . 
		'title' . "\t" .
		'webitemid' . "\t" .
		'gtin' . "\t" . 
		'mpn' . "\t" . 
		'brand' . "\t" . 
		'price' . "\t" . 
		'condition' . "\t" . 
		'link' . "\t" . 
		'image_link' . "\t" . 
		'color' . "\t" . 
		'size' . "\t" .
		'description' . "\t" .
		'product_type' . "\t" .
		'google product category' . "\n";

/////////////////////
//here's the query
$query = "select * from LOCAL_PRODUCT_LISTINGS where 1";
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
	$product_id = stripslashes($row['ProductID']);
	$title = stripslashes($row['Title']);
	$webitemid = stripcslashes($row['webitemid']);
	$gtin = stripslashes($row['GTIN']);
	$mpn = stripslashes($row['MPN']);
	$brand = stripslashes($row['Brand']);
	$price = stripslashes($row['Price']);
	$condition = stripslashes($row['ProductCondition']);
	$url = stripslashes($row['URL']);
	$image_url = stripslashes($row['ImageURL']);
	$color = stripslashes($row['Color']);
	$size = stripslashes($row['Size']);
	
	// get fields from SE file
	$sql = "select Description,ProductType,GoogleProductCategory from ONLINE_LISTINGS where OldID = '$webitemid' limit 1";
	$ext_results = mysql_query($sql);
	$ext_row = mysql_fetch_assoc($ext_results);
	$description = $ext_row['Description'];
	$product_type = $ext_row['ProductType'];
	$googleCategory = $ext_row['GoogleProductCategory'];
	
	//concat the data onto our string
	//using tabs as delimiters
	$data_string .= $product_id . "\t" . 
			$title . "\t" .
			$webitemid . "\t" .
			$gtin . "\t" . 
			$mpn . "\t" . 
			$brand . "\t" . 
			"USD $price" . "\t" . 
			$condition . "\t" . 
			$url . "\t" . 
			$image_url . "\t" . 
			$color . "\t" . 
			$size . "\t" .
			$description . "\t" .
			$product_type . "\t" .
			$googleCategory . "\n";
}

//echo "<br><br>data_string is $data_string<br>";

//////////////////////////////////////
//////////////////////////////////////

//save the data to a local text file
echo "Creating Google Local Products file<br />";
saveLocalTextFile($data_string, $local_outfile);

////////////////////////////
// Now upload file via FTP
echo "Uploading Google Local Products file...<br />";
uploadFile($remote_outfile,$local_outfile,'Google Local Products');
?>