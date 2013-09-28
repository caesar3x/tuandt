<?php
/**
 * Created by Nguyen Tien Dat.
 * Date: 9/28/13
 */
namespace Core\Model;

class SlugFile
{
    /**
     * @param $string
     * @return string
     */
    public static function parseFilename($string)
    {
        if(null === $string){
            return;
        }
        $string = utf8_decode($string);
        $string = html_entity_decode($string);

        $a = 'ÀÁÂÃÄÅẢẠĂẮẴẶẲẰẤẦẪẨẬàáạâãäåảăắằẳẵặấầẫẩậÒÓÔÕỎÖØỌỐỒỘỖỔƠỚỜỠỢỞòóôỏõöøọốồỗộổơớờợỡởÈÉÊẺËẾỀỆỄỂèéêẻëếềễệểÇçÌỈÍÎÏỈĨỊìíîïĩỉịÙÚÛÜŨỤỦƯÙỨỮỰỬùúûüủũụưừứữựửÿỳýỹỵỷÑñĐđ·/_,:;%';
        $b = 'AAAAAAAAAAAAAAAAAAAaaaaaaaaaaaaaaaaaaaOOOOOOOOOOOOOOOOOOOoooooooooooooooooooEEEEEEEEEEeeeeeeeeeeCcIIIIIIIIiiiiiiiUUUUUUUUUUUUUuuuuuuuuuuuuuyyyyyyNnDd-------';
        $string = strtr($string, utf8_decode($a), $b);

        $ponctu = array("?", ".", "!", ",");
        $string = str_replace($ponctu, "", $string);

        $string = trim($string);
        $string = preg_replace('/([^a-z0-9]+)/i', '_', $string);
        $string = strtolower($string);

        if (empty($string)) return 'n-a';

        return utf8_encode($string);
    }
}