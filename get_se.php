<?php
    $url = 'http://mikesbikes.com/admin/';
    $fields = 'items/ItemDownload.cfm?requesttimeout=999&Fields=Items.MyItem&Fields=Items.ID&Fields=vpath&Fields=Items.BrandName&Fields=Items.ItemName&Fields=Items.Itemid&Fields=Items.ItemYear&Fields=Items.Price&Fields=Items.SalePrice&Fields=Items.ShipCharge&Fields=Items.Closeout&Fields=Items.onspecial&Fields=Items.NonStocked&Fields=Items.Cart&Fields=Items.iPickup&Fields=Items.iShipGround&Fields=Items.iShipAir&Fields=Items.iORMD&Fields=Items.active&Fields=Items.Weight&Fields=Length%2C+Width%2C+Height&Description=&category=&brand=&itemYear=&active=1&cart=&ipst=&igst=&tax=&nonStocked=&Gender=&onSale=&onSpecial=&closeout=&natItem=&imapped=0&iposexempt=&possync=&shelfStart=&Uncategorized=&ZoomImage=&created=&sort=vpath%2CBrandName%2CItemName&asdsc=0&Froogle=Download+Google+Base+Products+File';
    $path = 'download/googlebase_products_curl.txt';
    $login = 'kdowney:superg77!';
    $fp = fopen($path, 'w');
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_USERPWD, $login);
    curl_setopt($ch, CURLOPT_FILE, $fp);
    curl_setopt($ch, CURLOPT_POST_FIELDS, $fields);
    
    $data = curl_exec($ch);
    curl_close($ch);
    fclose($fp);
    echo "Data $data";
?>