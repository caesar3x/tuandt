<?php
require_once 'inc/config.php';
require_once 'inc/functions.php';
require_once 'inc/Nav.class.php';
require_once 'inc/Sync.class.php';
ini_set('display_errors', '1');
?>
<html>
	<body>
	<?php		
		$nav = new Nav();
		$nav->init();
		$db_stoepje = new Db_stoepje();
		date_default_timezone_set('EST');
		$navProductSkuList = $nav->getProductSkuList();
		if($navProductSkuList){
			$sync = new Sync();
			$sync->init();
			$categoryList = $sync->getCategoryList();
			$categoryArray = array();
			if($categoryList){
				foreach($categoryList->children as $category){
					$categoryArray[] = array(
												'id' 	=> $category->category_id,
												'name'	=> $category->name
						);
				}
			}
			foreach ($navProductSkuList as $navSkuProduct){
				$navProduct = $nav->getProduct($navSkuProduct['Id']);
				$lastModified = strtotime($navProduct['Last DateTime Modified']);
				if(time() - $lastModified > $db_stoepje->productCronjobTime){
					continue;
				}
				try {
					$product = $sync->getProduct($navProduct['Id']);
					//print_r($product);
					//die();
					$priceTierList = $nav->getProductTierPriceList($navProduct['Id']);
					if(round($navProduct['tierprice_data']) > 0){
						$price = round($navProduct['netosalesprice']/round($navProduct['tierprice_data']), 2);
					}else{
						$price = 0;
					}
					$priceTier = array();
					if($priceTierList){
						foreach($priceTierList as $tier){
							$priceTier[] = array(
								'customer_group_id' => 0,
								'website'	=> 0,
								'qty'	=> round($tier['tierprice_data']),
								'price'	=> round($tier['netosalesprice'], 2)
							);
						}
					}
					if(!count($priceTier) > 0){
						//$priceTier = array($priceTier);
					}
					$categoryId = 0;
					foreach($categoryArray as $category){
						if($category['name'] == $navProduct['label']){
							$categoryId = $category['id'];
							break; 
						}
					}
					$type = 'simple';
					$sku = $navProduct['Id'];
					$productData = array(
						'categories' => array($categoryId),
					    //'websites' => array(1),
					    'name' => utf8_encode(addslashes($navProduct['itemname'])),
					    'description' => utf8_encode(addslashes($navProduct['Description'])),
					    'short_description' => utf8_encode(addslashes($navProduct['Description'])),
					    //'weight' => '10',
					    'status' => '1',
					    //'url_key' => 'product-url-key',
					    //'url_path' => 'product-url-path',
					    'visibility' => '4',
					    'price' => $price,
						'special_from_date' => $navProduct['Special Price from'],
						'special_to_date'	=> $navProduct['Special Price to'],
						'tier_price' => $priceTier,
					    'tax_class_id' => 1,
					    'website_ids' => array(1),
					    'meta_title' => utf8_encode(addslashes($navProduct['Description'])),
					    'meta_keyword' => utf8_encode(addslashes($navProduct['Description'])),
					    'meta_description' => utf8_encode(addslashes($navProduct['Description'])),
						'stock_data' => array(
							'qty'	=> $navProduct['productquality'],
							'is_in_stock' => 1,
							'max_sale_qty' => 10000,
							'use_config_manage_stock' => 1,
							'use_config_min_qty'	=> 1,
							'use_config_min_sale_qty' => 1,
							'use_config_max_sale_qty'	=> 1
						)
					);
					$productId = $sync->updateProduct($product->product_id, $productData);
					try{
						$mediaList = $sync->getMediaList($product->product_id);
						$currentMedia = null;
						if($mediaList){
							foreach ($mediaList as $media){
								$currentMedia = $media;
								break;
							}
						}
						if($currentMedia){
							$imageName = str_replace("\\\\Mfgportal01", "http://applicatie.stoepje.biz", $navProduct['picture']);
							$imageName = str_replace('\\', '/', $imageName);
							//echo $imageName."<br/>";
							$ch = curl_init ($imageName);
							curl_setopt($ch, CURLOPT_HEADER, 0);
							curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
							curl_setopt($ch, CURLOPT_BINARYTRANSFER,1);
							$content=curl_exec($ch);
							if($content === false){
							}else{
								$content = base64_encode($content);
								$imageData = array(
									'file' => array(
										'content' => $content,
										'mime' => 'image/jpeg'
										//'name'	=> $imageName
									),
									'label' => $navProduct['Description'],
									'position'	=> '1',
									'types' => array('thumbnail','small_image', 'image'),
									'exclude' => '0',
									'remove' => '0'
								);
								$sync->updateImage($product->product_id, $currentMedia->file, $imageData);
							}
						}else{
							$imageName = str_replace("\\\\Mfgportal01", "http://applicatie.stoepje.biz", $navProduct['picture']);
							//$content =  file_get_contents($imageName, false);
							//$imageName = 'http://applicatie.stoepje.biz/img/Ambachtsbrood/witrond.gif';
							$imageName = str_replace('\\', '/', $imageName);
							//echo $imageName."<br/>";
							$ch = curl_init ($imageName);
							curl_setopt($ch, CURLOPT_HEADER, 0);
							curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
							curl_setopt($ch, CURLOPT_BINARYTRANSFER,1);
							$content=curl_exec($ch);
							if($content === false){
							}else{
								$content = base64_encode($content);
								$imageData = array(
									'file' => array(
										'content' => $content,
										'mime' => 'image/jpeg'
										//'name'	=> $imageName
									),
									'label' => $navProduct['Description'],
									'position'	=> '1',
									'types' => array('thumbnail','small_image', 'image'),
									'exclude' => '0',
									'remove' => '0'
								);
								$sync->createImage($product->product_id, $imageData);
							}
						}
					} catch (Exception $e) {
						//print_r($e);
					}
					echo "Updated product has sku: ". $navProduct['Id']."<br/>";
				} catch (Exception $e) {
					$priceTierList = $nav->getProductTierPriceList($navProduct['Id']);
					if(round($navProduct['tierprice_data']) > 0){
						$price = round($navProduct['netosalesprice']/round($navProduct['tierprice_data']), 2);
					}else{
						$price = 0;
					}
					$priceTier = array();
					if($priceTierList){
						foreach($priceTierList as $tier){
							$priceTier[] = array(
								'customer_group_id' => 0,
								'website'	=> 0,
								'qty'	=> round($tier['tierprice_data']),
								'price'	=> round($tier['netosalesprice'], 2)
							);
						}
					}
					if(!count($priceTier) > 0){
						//$priceTier = array($priceTier);
					}
					
					$categoryId = 0;
					foreach($categoryArray as $category){
						if($category['name'] == $navProduct['label']){
							$categoryId = $category['id'];
							break; 
						}
					}
					
					$type = 'simple';
					$attributeSets = $sync->getAttributeSetList();
					$attributeSet = 9;
					$sku = $navProduct['Id'];
					$productData = array(
						'categories' => array($categoryId),
					    //'websites' => array(1),
					    'name' => utf8_encode(addslashes($navProduct['itemname'])),
					    'description' => utf8_encode(addslashes($navProduct['Description'])),
					    'short_description' => utf8_encode(addslashes($navProduct['Description'])),
					    //'weight' => '10',
					    'status' => '1',
					    //'url_key' => 'product-url-key',
					    //'url_path' => 'product-url-path',
					    'visibility' => '4',
					    'price' => $price,
						'special_price' => $price,
						'special_from_date' => $navProduct['Special Price from'],
						'special_to_date'	=> $navProduct['Special Price to'],
						'tier_price' => $priceTier,
					    'tax_class_id' => 1,
					    'website_ids' => array(1),
					    'meta_title' => utf8_encode(addslashes($navProduct['Description'])),
					    'meta_keyword' => utf8_encode(addslashes($navProduct['Description'])),
					    'meta_description' => utf8_encode(addslashes($navProduct['Description'])),
						'stock_data' => array(
							'qty'	=> $navProduct['productquality'],
							'is_in_stock' => 1,
							'max_sale_qty' => 10000,
							'use_config_manage_stock' => 1,
							'use_config_min_qty'	=> 1,
							'use_config_min_sale_qty' => 1,
							'use_config_max_sale_qty'	=> 1
						)
					);
					
					$storeView = 0;
					$productId = $sync->addProduct($type, $attributeSet, $sku, $productData, $storeView);
					
					$imageName = str_replace("\\\\Mfgportal01", "http://applicatie.stoepje.biz", $navProduct['picture']);
					$imageName = str_replace('\\', '/', $imageName);
					//echo $imageName."<br/>";
					$ch = curl_init ($imageName);
					curl_setopt($ch, CURLOPT_HEADER, 0);
					curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
					curl_setopt($ch, CURLOPT_BINARYTRANSFER,1);
					$content=curl_exec($ch);
					if($content === false){
					}else{
						$content = base64_encode($content);						
						$imageData = array(
							'file' => array(
								'content' => $content,
								'mime' => 'image/jpeg'
								//'name'	=> $imageName
							),
							'label' => $navProduct['Description'],
							'position'	=> '1',
							'types' => array('thumbnail','small_image', 'image'),
							'exclude' => '0',
							'remove' => '0'
						);
						$sync->createImage($productId, $imageData);
					}
					echo "Added product has sku: ". $navProduct['Id']."<br/>";
				}
			}
			
			$currentProductList = $sync->listAllProduct();
			$currentProductSkuList = array();
			if($currentProductList){
				foreach($currentProductList as $currentProduct){
					$currentProductSkuList[] = array(
								'sku' => $currentProduct->sku,
								'id'  => $currentProduct->product_id
					);
				}
			}
			
			//print_r($navProductSkuList);
			//echo "<br/>";
			//print_r($currentProductSkuList);
			
			foreach ($currentProductSkuList as $currentProductSku){
				$isEixted = false;
				foreach($navProductSkuList as $skuNav){
					if($currentProductSku['sku'] == $skuNav['Id']){
						$isEixted = true;
						$productId = $currentProductSku['id'];
						break;
					}
				}
				
				if(!$isEixted){
					$sync->deleteProduct($currentProductSku['id']);
					echo "Deleted product has sku: ".$currentProductSku['sku']."<br/>";
				}
			}
			
		}
		$nav->close();
	?>
	</body>
</html>