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
        $update = "UPDATE recycler_product SET lastest = 0 WHERE model = '$model' AND condition_id = $condition AND lastest = 1";
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