<?php

/**
 * Copyright (C) 2012 Vizualizer All Rights Reserved.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 *
 * @author    Naohisa Minagawa <info@vizualizer.jp>
 * @copyright Copyright (c) 2010, Vizualizer
 * @license http://www.apache.org/licenses/LICENSE-2.0.html Apache License, Version 2.0
 * @since PHP 5.3
 * @version   1.0.0
 */

/**
 * 広告のリストを取得する。
 *
 * @package VizualizerAffiliate
 * @author Naohisa Minagawa <info@vizualizer.jp>
 */
class VizualizerAffiliate_Module_Advertise_List extends Vizualizer_Plugin_Module_List
{

    function execute($params)
    {
        if (class_exists("VizualizerAdmin")) {
            $attr = Vizualizer::attr();
            $operator = $attr[VizualizerAdmin::KEY];
            if(!empty($operator) && $operator->operator_id > 0){
                // オペレータとしてログインしているときは、出力する広告を制限する。
                $post = Vizualizer::request();
                $search = $post["search"];
                $search["company_id"] = $operator->company_id;
                $post->set("search", $search);
            }
        }
        $this->executeImpl($params, "Affiliate", "Advertise", $params->get("result", "advertises"));
    }
}