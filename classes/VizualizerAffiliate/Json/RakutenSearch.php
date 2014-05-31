<?php

class VizualizerAffiliate_Json_RakutenSearch
{

    public function execute()
    {
        $post = Vizualizer::request();

        $result = array();
        if (!empty($post["app_id"]) && !empty($post["keyword"])) {
            $url = "https://app.rakuten.co.jp/services/api/IchibaItem/Search/20140222?applicationId=".$post["app_id"];
            if (!empty($post["aff_id"])) {
                $url .= "&affiliateId=".$post["aff_id"];
            }
            $url .= "&format=json";
            $url .= "&keyword=".urlencode($post["keyword"]);
            $url .= "&sort=".urlencode("-reviewAverage");
            $list = json_decode(file_get_contents($url));
            foreach ($list->Items as $item) {
                $result[] = $item->Item;
            }
        }

        return $result;
    }
}
