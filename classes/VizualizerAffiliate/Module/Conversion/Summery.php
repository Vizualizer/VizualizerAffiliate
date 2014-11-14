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
 * 成果を取得する。
 *
 * @package VizualizerAffiliate
 * @author Naohisa Minagawa <info@vizualizer.jp>
 */
class VizualizerAffiliate_Module_Conversion_Summery extends Vizualizer_Plugin_Module
{

    function execute($params)
    {
        $attr = Vizualizer::attr();
        $post = Vizualizer::request();
        $loader = new Vizualizer_Plugin("affiliate");

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
            }
        }

        $conversions = $loader->loadTable("Conversions");
        // 集計用のクエリを発行
        $select = new Vizualizer_Query_Select($conversions);
        $select->addColumn("SUBSTRING(".$conversions->conversion_time.", 1, 7) AS month");
        $select->addColumn("COUNT(" . $conversions->conversion_id . ") AS count");
        $select->addColumn("SUM(" . $conversions->total . ") AS total");
        $select->addColumn("SUM(" . $conversions->reward . ") AS reward");
        $select->addColumn("SUM(" . $conversions->charge . ") AS charge");
        $select->addColumn("SUM(" . $conversions->reward . " + " . $conversions->charge . ") AS bill");
        $select->where($conversions->conversion_status." = '2'")->group("SUBSTRING(".$conversions->conversion_time.", 1, 7)");
        if(!empty($siteIds)){
            $select->where($conversions->site_id." IN (".implode(", ", $siteIds).")");
        }
        $result = $select->execute();

        $summery = array();
        for($time = strtotime("-6 month", strtotime(date("Y-m-01 00:00:00"))); $time < time(); $time = strtotime("+1 month", $time)){
            $summery[date("Y-m", $time)] = array("month" => date("Y-m", $time), "count" => 0, "total" => 0, "reward" => 0, "charge" => 0, "bill" => 0);
        }
        foreach($result as $data){
            if(isset($summery[$data["month"]])){
                $summery[$data["month"]] = $data;
            }
        }

        $attr = Vizualizer::attr();
        $attr[$params->get("result", "conversionSummery")] = $summery;
    }
}
