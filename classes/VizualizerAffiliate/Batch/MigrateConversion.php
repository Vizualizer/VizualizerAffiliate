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
 * コンバージョン情報生成のバッチです。
 *
 * @package VizualizerAffiliate
 * @author Naohisa Minagawa <info@vizualizer.jp>
 */
class VizualizerAffiliate_Batch_MigrateConversion extends Vizualizer_Plugin_Batch
{

    public function getDaemonName()
    {
        return "migrateConversion";
    }

    public function getName()
    {
        return "Migrate Conversion";
    }

    public function getFlows()
    {
        return array("migrateConversion");
    }

    public function getDaemonInterval()
    {
        return 120;
    }

    /**
     * コンバージョンログで移動していないものを移動。
     *
     * @param $params バッチ自体のパラメータ
     * @param $data バッチで引き回すデータ
     * @return バッチで引き回すデータ
     */
    protected function migrateConversion($params, $data)
    {
        $loader = new Vizualizer_Plugin("Affiliate");
        $conversion = $loader->loadModel("Conversion");

        $conversions = $conversion->findAllBy(array("conversion_status" => "0"));
        foreach($conversions as $conversion){
            $entranceLog = $loader->loadModel("EntranceLog");
            $entranceLog->findByTrackingCode($conversion->tracking_code);

            // コンバージョンデータを更新
            $connection = Vizualizer_Database_Factory::begin("affiliate");
            try {
                if($entranceLog->tracking_id > 0){
                    $conversion->tracking_id = $entranceLog->tracking_id;
                    $conversion->site_id = $entranceLog->site_id;
                    $conversion->advertise_id = $entranceLog->advertise_id;
                    $conversion->product_id = $entranceLog->product_id;
                    $advertise = $entranceLog->advertise();
                    $conversion->reward = ceil($conversion->total * $advertise->reward_rate / 100) + $advertise->reward_price;
                    $chargeRate = Vizualizer_Configure::get("affiliate_conversion_charge_rate") / 100;
                    if($chargeRate > 0){
                        $conversion->charge = ceil($conversion->reward * $chargeRate / (1 - $chargeRate));
                    }
                    $conversion->entrance_time = $entranceLog->create_time;
                    $conversion->conversion_status = 1;
                }else{
                    $conversion->conversion_status = 4;
                }
                $conversion->save();
                Vizualizer_Logger::writeInfo("Migrated conversion : ".$conversion->tracking_code);

                Vizualizer_Database_Factory::commit($connection);
            } catch (Exception $e) {
                Vizualizer_Database_Factory::rollback($connection);
                throw new Vizualizer_Exception_Database($e);
            }
        }

        return $data;
    }
}
