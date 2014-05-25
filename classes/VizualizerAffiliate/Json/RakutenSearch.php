<?php

class VizualizerAffiliate_Json_RakutenSearch
{

    public function execute()
    {
        $post = Vizualizer::request();

        if(array_key_exists("app_id", $post) && !empty($post["app_id"]) && array_key_exists("keyword", $post) && !empty($post["keyword"])){
            $url = VizualizerAffiliate::getRakutenApiBase($post["app_id"]);
            $url .= "&operation=".VizualizerAffiliate::RAKUTEN_SEARCH;
            $url .= "&version=".VizualizerAffiliate::RAKUTEN_VERSION;
            $url .= "&keyword=".urlencode($post["keyword"]);
            $url .= "&sort=".urlencode("-reviewAverage");

            $result = file_get_contents($url);
            print_r($result);
            exit;
        }
    }
}
