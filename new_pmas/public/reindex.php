<?php
/**
 * Created by Nguyen Tien Dat.
 * Email : datnguyen.cntt@gmail.com
 * Date: 11/28/13
 */
$host  = 'localhost';
$user = 'root';
$password = '';
$dbname = 'pmas';
/**
 * Implement
 */
print_r("<pre>");var_dump(get_tdm_products(10));
/**
 * Create tdm product index table
 */
function create_tdm_product_index_table($do_truncate = true){
    global $host,$user,$password,$dbname;
    $con = new mysqli($host,$user ,$password ,$dbname);
    if (mysqli_connect_errno($con)){
        echo "Failed to connect to MySQL: " . mysqli_connect_error();
    }
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
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8;";
    $con->query($query);
    if($do_truncate !== false){
        $truncate = "TRUNCATE TABLE `tdm_product_index`";
        $con->query($truncate);
    }
    $con->close();
    return true;
}
/**
 * Create tdm product matching table
 */
function create_tdm_product_match($do_truncate = true){
    global $host,$user,$password,$dbname;
    $con = new mysqli($host,$user ,$password ,$dbname);
    if (mysqli_connect_errno($con)){
        echo "Failed to connect to MySQL: " . mysqli_connect_error();
    }
    $query = "CREATE TABLE IF NOT EXISTS `tdm_product_match` (
                  `id` bigint(20) NOT NULL AUTO_INCREMENT,
                  `product_id` bigint(20) DEFAULT NULL,
                  `recycler_id` int(11) DEFAULT NULL,
                  `recycler_name` varchar(255) DEFAULT NULL,
                  `price_in_hkd` decimal(12,4) DEFAULT NULL,
                  `percentage` decimal(2,2) DEFAULT NULL,
                  PRIMARY KEY (`id`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;";
    $con->query($query);
    $con->close();
    return true;
}
/**
 * Get all tdm products
 */
function get_tdm_products($limit = null){
    global $host,$user,$password,$dbname;
    $con = new mysqli($host,$user ,$password ,$dbname);
    if (mysqli_connect_errno($con)){
        echo "Failed to connect to MySQL: " . mysqli_connect_error();
    }
    $query = "SELECT `m`.*, `b`.`name` AS `brand_name`, `c`.`name` AS `country_name`, `t`.`name` AS `type_name`, `cd`.`name` AS `condition_name` FROM `tdm_product` AS `m` INNER JOIN `brand` AS `b` ON `m`.`brand_id` = `b`.`brand_id` INNER JOIN `country` AS `c` ON `m`.`country_id` = `c`.`country_id` INNER JOIN `product_type` AS `t` ON `m`.`type_id` = `t`.`type_id` INNER JOIN `tdm_product_condition` AS `cd` ON `m`.`condition_id` = `cd`.`condition_id`";
    if($limit != null){
        $query .= " LIMIT $limit";
    }
    $result = $con->query($query);
    $rowset = $result->fetch_all(MYSQLI_BOTH);
    $result->free();
    $con->close();
    return $rowset;
}

/**
 * Get recycler product by model and condition
 */
function get_ssa_price_and_currency_by_model_and_condition($model,$condition){
    global $host,$user,$password,$dbname;
    $con = new mysqli($host,$user ,$password ,$dbname);
    if (mysqli_connect_errno($con)){
        echo "Failed to connect to MySQL: " . mysqli_connect_error();
    }
}
function index_data(){
    global $host,$user,$password,$dbname;
    $con = new mysqli($host,$user ,$password ,$dbname);
    if (mysqli_connect_errno($con)){
        echo "Failed to connect to MySQL: " . mysqli_connect_error();
    }
    create_tdm_product_index_table();
    create_tdm_product_match();
    $products = get_tdm_products(10);
    if(!empty($products)){

    }
    /*$query = "INSERT INTO `tdm_product_match` (`product_id`, `recycler_id`, `recycler_name`, `price_in_hkd`, `percentage`) VALUES ('8910', '26', 'Mobile Cash Mate', '37.037037037037', NULL)";*/
}