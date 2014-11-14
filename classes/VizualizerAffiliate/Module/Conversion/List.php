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
 * 成果のリストを取得する。
 *
 * @package VizualizerAffiliate
 * @author Naohisa Minagawa <info@vizualizer.jp>
 */
class VizualizerAffiliate_Module_Conversion_List extends Vizualizer_Plugin_Module_List
{

    function execute($params)
    {
        $attr = Vizualizer::attr();
        $post = Vizualizer::request();
        $search = $post["search"];
        if (class_exists("VizualizerAdmin")) {
            $operator = $attr[VizualizerAdmin::KEY];
            if (!empty($operator) && $operator->operator_id > 0) {
                $loader = new Vizualizer_Plugin("affiliate");
                $advertise = $loader->loadModel("Advertise");
                $advertises = $advertise->findAllByCompanyId($operator->company_id);
                $advertiseIds = array("0");
                foreach ($advertises as $advertise) {
                    $advertiseIds[] = $advertise->advertise_id;
                }
                $search["in:advertise_id"] = $advertiseIds;
            }
        }
        if (class_exists("VizualizerMember")) {
            $customer = $attr[VizualizerMember::KEY];
            if (!empty($customer) && $customer->customer_id > 0) {
                // カスタマーとしてログインしているときは、出力する広告を制限する。
                $loader = new Vizualizer_Plugin("affiliate");
                $site = $loader->loadModel("Site");
                $sites = $site->findAllBy(array("customer_id" => $customer->customer_id));
                $siteIds = array("0");
                foreach ($sites as $site) {
                    $siteIds[] = $site->site_id;
                }
                $post = Vizualizer::request();
                $search = $post["search"];
                $search["in:site_id"] = $siteIds;
                $post->set("search", $search);
            }
        }
        $search["in:conversion_status"] = array("1", "2", "3");
        if (isset($post["site_id"])) {
            $search["site_id"] = $post["site_id"];
        } else {
            unset($search["site_id"]);
        }
        $post->set("search", $search);
        $this->executeImpl($params, "Affiliate", "Conversion", $params->get("result", "conversions"));
    }
}
