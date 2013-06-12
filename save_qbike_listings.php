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
$local_outfile = 'upload/mbqbike.csv';
$outfile = $feedSettings[6]['upload_path'];


////////////////////////////
//MAIN

//string which we'll concat all the data on to
//we'll initialize it as the column titles
$data_string =  'title' . "," . 
		'price' . "," . 
		'link' . "," . 
		'image link' . "," . 
		'product type' . "\n"; 


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
	$title = stripslashes($row['Title']);
	$price = stripslashes($row['Price']);
	$link = stripslashes($row['Link']);
	$image_link = stripslashes($row['ImageLink']);
	$product_type = stripslashes($row['ProductType']);

	//concat the google affiliate param
	//the macro is defined at the top of the functions file
	$link .= QBIKE_AFFILIATE_ID;

	//concat the data onto our string
	//using tabs as delimiters
	$data_string .= $title . "," .
	                $price . "," .
			$link . "," . 
			$image_link . "," . 
			$product_type . "\n"; 
}

//echo "<br><br>data_string is $data_string<br>";

//////////////////////////////////////

//save the data to a local text file
saveLocalTextFile($data_string, $local_outfile);
echo "Created Qbike file ---><a href=\"http://www.qbike.com/cgi-bin/mikesbikes-data.cgi\" target=\"_new\">Upload here</a><br />";
////////////////////////////
// POST file to qBike
/*    echo "Uploading qBike file...<br />";
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, "$feedSettings[6]['upload_path']");
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_VERBOSE, 0);
    curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/4.0 (compatible;)");
    curl_setopt($ch, CURLOPT_POST, true);
    $post = array(
        "pswd"=>"bridge2",
	"upload_file"=>"$data_string",
    );
    curl_setopt($ch, CURLOPT_POSTFIELDS, $post); 
    $response = curl_exec($ch);
    if( $response ) {
	    echo '<span style="color:green;">Upload Successful for Qbike file</span><br />';
    } else { 
	    echo '<span style="color:red;">Upload Failed for Qbike file!</span><br />';
    }
*/
?>