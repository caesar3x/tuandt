<?php
/**
 * Created by Nguyen Tien Dat.
 * Date: 8/10/13
 */
$img_url = 'http://images.soslaundry.us/bg_wrap.png';
$b64_url = 'php://filter/read=convert.base64-encode/resource='.$img_url;
$b64_img = file_get_contents($b64_url);

// Echo out a sample image
echo '<img src="data:image/png;base64,'.$b64_img.'">';