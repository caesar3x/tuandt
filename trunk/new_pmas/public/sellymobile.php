<?php
/**
 * Created by Nguyen Tien Dat.
 * Email : datnguyen.cntt@gmail.com
 * Date: 11/22/13
 */
$opts = array(
    'http'=>array(
        'method'=>"GET",
        'header'=>"Accept-language: en\r\n" .
            "Cookie: foo=bar\r\n".
            "User-Agent: Mozilla/5.0 (iPad; U; CPU OS 3_2 like Mac OS X; en-us) AppleWebKit/531.21.10 (KHTML, like Gecko) Version/4.0.4 Mobile/7B334b Safari/531.21.102011-10-16 20:23:10\r\n" // i.e. An iPad
    )
);

$http_header = stream_context_create($opts);
$host  = 'tdmdev.cmljsugageo1.ap-southeast-1.rds.amazonaws.com';
$user = 'pmasdev';
$password = 'furuFe8a';
$dbname = 'pmas_dev';
/*$host  = 'localhost';
$user = 'root';
$password = '';
$dbname = 'pmas';*/
execute();
index_data();
/*echo get_brand_id_by_name('Philipssss');*/
function execute(){
    global $host,$user,$password,$dbname;
    $con = new mysqli($host,$user ,$password ,$dbname);
    // Check connection
    if (mysqli_connect_errno($con)){
        echo "Failed to connect to MySQL: " . mysqli_connect_error();
    }
    /**
     * start processing
     */
    $brands = getAllBrands();
    foreach ($brands as $brand_name => $brand_url) {
        $phones = getAllPhonesFromBrand($brand_url);
        foreach ($phones as $phone_url => $phone_name) {
            $rec = getAllRecyclers($phone_url);
            if(isset($rec['Top Dollar Mobile'])){
                unset($rec['Top Dollar Mobile']);
            }
            if(!empty($rec)){
                foreach($rec as $rec_name=>$rec_price){
                    $row = array();
                    $rec_price = str_replace('£', '', $rec_price);
                    $row['price'] = $rec_price;
                    $row['currency'] = 'GBP';
                    $row['name'] = $phone_name;
                    $row['model'] = $phone_name;
                    $row['recycler_id'] = get_recycler_id_by_name($rec_name);
                    $row['condition_id'] = 5;
                    $row['lastest'] = 1;
                    $row['type_id'] = 3;
                    $row['date'] = time();
                    $row['brand_id'] = get_brand_id_by_name($brand_name);
                    save_recycler_product($row);
                }
            }
        }
    }
    $con->close();
}

/**
 * Save data to db
 * @param $data
 */
function save_recycler_product($data){
    global $host,$user,$password,$dbname;
    $con = new mysqli($host,$user ,$password ,$dbname);
    // Check connection
    if (mysqli_connect_errno($con)){
        echo "Failed to connect to MySQL: " . mysqli_connect_error();
    }
    if(!empty($data)){
        /**
         * Reset lastest
         */
        $model = $data['model'];
        $condition = $data['condition_id'];
        $update = "UPDATE recycler_product SET lastest = 0 WHERE model = '$model' AND condition_id = $condition AND recycler_id = '11' AND lastest = 1";
        $con->query($update);
        $fields = array_keys($data);
        $values = array_values($data);
        $fields_to_string = implode(',',$fields);
        $values_to_string = "'" . implode("','", $values) . "'";
        $insert = "INSERT INTO recycler_product ($fields_to_string) VALUES ($values_to_string)";
        $con->query($insert);
    }
    $con->close();
}
/**
 * Get recycler id by name
 * @param $name
 * @return mixed
 */
function get_recycler_id_by_name($name){
    global $host,$user,$password,$dbname;
    $con = new mysqli($host,$user ,$password ,$dbname);
    // Check connection
    if (mysqli_connect_errno($con)){
        echo "Failed to connect to MySQL: " . mysqli_connect_error();
    }
    $sql = "SELECT recycler_id FROM recycler WHERE name = '$name'";
    $result = $con->query($sql);
    $row = $result->fetch_row();
    $result->free();
    mysqli_close($con);
    if(!empty($row)){
        return current($row);
    }
}

/**
 * Get brand id by name
 * If brand does not existed, insert new
 * @param $name
 * @return mixed
 */
function get_brand_id_by_name($name){
    global $host,$user,$password,$dbname;
    $con = new mysqli($host,$user ,$password ,$dbname);
    // Check connection
    if (mysqli_connect_errno($con)){
        echo "Failed to connect to MySQL: " . mysqli_connect_error();
    }
    $sql = "SELECT brand_id FROM brand WHERE name = '$name'";
    $result = $con->query($sql);
    $row = $result->fetch_row();
    if(!empty($row)){
        $result->free();
        $con->close();
        return current($row);
    }else{
        $insert = "INSERT INTO brand (name,deleted) VALUES ('$name','3')";
        $con->query($insert);
        $con->close();
        return get_brand_id_by_name($name);
    }
}
function getAllRecyclers($phone_url) { //returns dictionary [recycler_name => price] sorted by price for specific phone
    global $http_header;
    $page_source = file_get_contents($phone_url, false, $http_header);
    $chunks = preg_split('/Complete List/', $page_source);
    preg_match_all('/<a class="trigger-comp merchant small.*?m2:\'(?P<name>.*?)\'.*?<td class="price" width="50%">(?P<price>.*?)<\/td>/s', $chunks[1], $rec_temp);
    //var_dump($rec_temp['name']);
    //var_dump($rec_temp['price']);
    $rec = array();
    for ($i = 0; $i < count($rec_temp['name']); $i += 1) {
        $rec[$rec_temp['name'][$i]] = $rec_temp['price'][$i];
    }
    natsort($rec);
    $rec = array_reverse($rec);
    //var_dump($rec);
    return $rec;
}
function getAllPhonesFromBrand($brand_url) { //returns dictionary [phone_url => phone_name]
    global $http_header;
    $page_source = file_get_contents($brand_url, false, $http_header);
    $chunks = preg_split('/<h1 class="clear">Search All/', $page_source);
    //var_dump($chunks);
    preg_match_all('/<li class="loading.*?<a href="(?P<url>.*?)" title="Compare prices for the (?P<name>.*?)"/s', $chunks[1], $phones_temp);
    $phones = array();
    for ($i = 0;  $i < count($phones_temp['url']); $i += 1) {
        $phones[trim($phones_temp['url'][$i])] = trim($phones_temp['name'][$i]);
    }
    //var_dump($phones);
    return $phones;
}
function getAllBrands() { //returns dictionary [brand_name => url]
    global $http_header;
    $page_source = file_get_contents('http://www.sellmymobile.com/search/', false, $http_header);

    preg_match_all('/<li class="loading.*?<a href="(?P<url>.*?)" title="View all (?P<name>.*?) handsets">/s', $page_source, $brands_temp);
    $brands = array();
    for ($i = 0;  $i < count($brands_temp['name']); $i += 1) {
        $brands[trim($brands_temp['name'][$i])] = trim($brands_temp['url'][$i]);
    }
    //var_dump($brands);
    return $brands;
}
/**
 * Reindex data
 */
function index_data(){
    global $host,$user,$password,$dbname;
    $con = new mysqli($host,$user ,$password ,$dbname);
    if (mysqli_connect_errno($con)){
        echo "Failed to connect to MySQL: " . mysqli_connect_error();
    }
    $time = time();
    $query = "CREATE TABLE IF NOT EXISTS `tdm_product_index` (
                  `product_id` bigint(20) NOT NULL,
                  `country_id` int(11) DEFAULT NULL,
                  `country_name` varchar(255) DEFAULT NULL,
                  `name` varchar(255) DEFAULT NULL,
                  `model` varchar(255) DEFAULT NULL,
                  `type_id` int(11) DEFAULT NULL,
                  `type_name` varchar(255) DEFAULT NULL,
                  `brand_id` int(11) DEFAULT NULL,
                  `brand_name` varchar(255) DEFAULT NULL,
                  `condition_id` int(11) DEFAULT NULL,
                  `condition_name` varchar(255) DEFAULT NULL,
                  `popular` tinyint(4) DEFAULT '0',
                  `ssa_price` decimal(12,4) DEFAULT NULL,
                  `ssa_currency` varchar(10) DEFAULT NULL,
                  PRIMARY KEY (`product_id`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
                CREATE TABLE IF NOT EXISTS `tdm_product_match` (
                  `id` bigint(20) NOT NULL AUTO_INCREMENT,
                  `product_id` bigint(20) DEFAULT NULL,
                  `recycler_id` int(11) DEFAULT NULL,
                  `recycler_name` varchar(255) DEFAULT NULL,
                  `price` decimal(12,6) DEFAULT NULL,
                  `price_in_hkd` decimal(12,6) DEFAULT NULL,
                  `percentage` decimal(4,2) DEFAULT NULL,
                  `recycler_country_id` int(11) DEFAULT NULL,
                  `recycler_country_name` varchar(255) DEFAULT NULL,
                  `date` int(11) DEFAULT NULL,
                  PRIMARY KEY (`id`)
                ) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;
                TRUNCATE TABLE `tdm_product_index`; TRUNCATE TABLE `tdm_product_match`;";
    $con->query($query);
    $query2 = "SELECT `m`.*, `b`.`name` AS `brand_name`, `c`.`name` AS `country_name`, `t`.`name` AS `type_name`, `cd`.`name` AS `condition_name` FROM `tdm_product` AS `m` INNER JOIN `brand` AS `b` ON `m`.`brand_id` = `b`.`brand_id` INNER JOIN `country` AS `c` ON `m`.`country_id` = `c`.`country_id` INNER JOIN `product_type` AS `t` ON `m`.`type_id` = `t`.`type_id` INNER JOIN `tdm_product_condition` AS `cd` ON `m`.`condition_id` = `cd`.`condition_id`";
    $result2 = $con->query($query2);
    $products = $result2->fetch_all(MYSQLI_ASSOC);
    if(!empty($products)){
        foreach($products as $product){
            $model = $product['model'];
            $condition = $product['condition_id'];
            $query3 = "SELECT `recycler_product`.`currency` AS `currency`, `recycler_product`.`price` AS `price` FROM `recycler_product` WHERE `condition_id` = '$condition' AND `model` = '$model' AND `recycler_id` = '1'";
            $result = $con->query($query3);
            $row = $result->fetch_array(MYSQLI_ASSOC);
            if(!empty($row)){
                /**
                 * Get current exchange rate
                 */
                $currency = $row['currency'];
                $exchange_query = "SELECT `m`.`exchange_rate` AS `exchange_rate` FROM `exchange` AS `m` WHERE m.currency = '$currency' AND m.time <= $time ORDER BY `m`.`time` DESC LIMIT 1";
                $exchange_query_result = $con->query($exchange_query);
                $exchange_row = $exchange_query_result->fetch_array(MYSQLI_ASSOC);
                $price = (float) $row['price'];
                if(!empty($exchange_row)){
                    $exchange = (float) $exchange_row['exchange_rate'];
                }else{
                    $exchange = 1;
                }
                $ssa = $price/$exchange;
            }else{
                $currency = null;
                $ssa = null;
            }
            $product['ssa_price'] = $ssa;
            $product['ssa_currency'] = $currency;
            $fields_string = implode(',',array_keys($product));
            $values_string = "'".implode("','",array_values($product))."'";
            $insert_sql = "INSERT INTO `tdm_product_index` ($fields_string) VALUES ($values_string)";
            $con->query($insert_sql);
            /*$recycler_products_query = "SELECT * FROM (SELECT `m`.* FROM `recycler_product` AS `m` WHERE `m`.`condition_id` = '$condition' AND `m`.`model` = '$model' AND `m`.`lastest` = '1' AND `m`.`recycler_id` > '1' ORDER BY `m`.`product_id` DESC) AS tmp_table GROUP BY recycler_id";*/
            $recycler_products_query = "SELECT * FROM (SELECT `m`.*, `r`.`name` AS `recycler_name`, `r`.`country_id` AS `recycler_country_id`, `c`.`name` AS `country_name` FROM `recycler_product` AS `m` INNER JOIN `recycler` AS `r` ON `m`.`recycler_id` = `r`.`recycler_id` INNER JOIN `country` AS `c` ON `r`.`country_id` = `c`.`country_id` WHERE `m`.`condition_id` = '$condition' AND `m`.`model` = '$model' AND `m`.`lastest` = '1' AND `m`.`recycler_id` > '1' ORDER BY `m`.`product_id` DESC) AS tmp_table GROUP BY recycler_id";
            $recycler_products_result = $con->query($recycler_products_query);
            $recycler_products = $recycler_products_result->fetch_all(MYSQLI_ASSOC);
            if(!empty($recycler_products)){
                /*print_r("<pre>");var_dump($recycler_products);
                echo '-------------';*/
                foreach($recycler_products as $rp){
                    $r_currency = $rp['currency'];
                    $query5 = "SELECT `m`.`exchange_rate` AS `exchange_rate` FROM `exchange` AS `m` WHERE m.currency = '$r_currency' AND m.time <= $time ORDER BY `m`.`time` DESC LIMIT 1";
                    $result5 = $con->query($query5);
                    $r_exchange_row = $result5->fetch_array(MYSQLI_ASSOC);
                    if(!empty($r_exchange_row)){
                        $r_exchange = (float) $r_exchange_row['exchange_rate'];
                    }else{
                        $r_exchange = 1;
                    }
                    $priceExchange = ((float) $rp['price']) / $r_exchange;
                    if(!empty($ssa)){
                        $sub = $priceExchange-$ssa;
                        $percentage = $sub/$ssa;
                    }else{
                        $percentage = null;
                    }
                    $row_data = array(
                        'recycler_product_id' => $rp['product_id'],
                        'product_id' => $product['product_id'],
                        'recycler_id' => $rp['recycler_id'],
                        'recycler_name' => $rp['recycler_name'],
                        'price' => $rp['price'],
                        'price_in_hkd' => $priceExchange,
                        'percentage' => $percentage*100,
                        'recycler_country_id' => $rp['recycler_country_id'],
                        'recycler_country_name' => $rp['country_name'],
                        'date' => $rp['date']
                    );
                    $fields_string2 = implode(',',array_keys($row_data));
                    $values_string2 = "'".implode("','",array_values($row_data))."'";
                    $insert_sql2 = "INSERT INTO `tdm_product_match` ($fields_string2) VALUES ($values_string2)";
                    $con->query($insert_sql2);
                }
            }
            /*print_r("<pre>");var_dump($fields_string);
            print_r("<pre>");var_dump($values_string);*/
            /*print_r("<pre>");var_dump($product);*/
        }
    }
    $result->free();
    $con->close();
    return true;
    /*$query = "INSERT INTO `tdm_product_match` (`product_id`, `recycler_id`, `recycler_name`, `price_in_hkd`, `percentage`) VALUES ('8910', '26', 'Mobile Cash Mate', '37.037037037037', NULL)";*/
}