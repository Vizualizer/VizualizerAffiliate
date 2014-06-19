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
 * サイトのデータを保存する。
 *
 * @package VizualizerAffiliate
 * @author Naohisa Minagawa <info@vizualizer.jp>
 */
class VizualizerAffiliate_Module_Tracking_Complete extends Vizualizer_Plugin_Module
{

    function execute($params)
    {
        $post = Vizualizer::request();
        $loader = new Vizualizer_Plugin("affiliate");

        // アクセスしたアフィリエイトコードからサイトと広告の情報を取得する。
        if(!empty($post["aff"])){
            $advertiseCode = $post["aff"];
            $total = $post["total"];
            if(!empty($post["oid"])){
                $orderCode = $post["oid"];
            }else{
                $orderCode = null;
            }

            // 渡されたアフィリエイトコードが有効か調べる。
            $advertise = $loader->loadModel("Advertise");
            $advertise->findByAdvertiseCode($advertiseCode);
            if($advertise->advertise_id > 0){
                try{
                    $this->processConversion($advertise, $total, $orderCode);
                }catch(Exception $e){
                    print_r($e);
                    throw $e;
                }
            }
        }
    }

    /**
     * エントランスの処理を実行
     * @param $site
     * @param $advertise
     */
    private function processConversion($advertise, $total, $orderCode = null){
        if($advertise->active_flg != "1"){
            // 有効で無い場合は実施を中断
            throw new Vizualizer_Exception_Invalid("advertise", "有効ではありません");
        }
        if($advertise->start_date != "0000-00-00" && date("Y-m-d") < $advertise->start_date){
            // 開始日前の場合は実施を中断
            throw new Vizualizer_Exception_Invalid("advertise", "この広告は開始されていません");
        }
        if($advertise->end_date != "0000-00-00" && $advertise->end_date < date("Y-m-d")){
            // 終了日後の場合は実施を中断
            throw new Vizualizer_Exception_Invalid("advertise", "この広告は終了しました");
        }
        // Cookieからトラッキングコードを取得
        $trackingCode = $_COOKIE["VZ_AFF_TRACKING_CODE"];
        if(empty($trackingCode)){
            // トラッキングコード取得できない場合は実施を中断
            throw new Vizualizer_Exception_Invalid("advertise", "トラッキングコードが取得できません");
        }
        // 成果金額を計算
        $reward = floor($total * $advertise->reward_rate / 100) + $advertise->reward_price;
        if(!($reward > 0)){
            // 終了日後の場合は実施を中断
            throw new Vizualizer_Exception_Invalid("advertise", "成果金額が1円以上になりません");
        }

        // コンバージョンログを作成
        $loader = new Vizualizer_Plugin("affiliate");
        $log = $loader->loadModel("ConversionLog");
        $log->tracking_code = $trackingCode;
        $log->total = $total;
        if($orderCode != null){
            $log->order_code = $orderCode;
        }

        // エントランスログを保存
        $connection = Vizualizer_Database_Factory::begin("affiliate");
        try {
            $log->save();
            Vizualizer_Database_Factory::commit($connection);
        } catch (Exception $e) {
            Vizualizer_Database_Factory::rollback($connection);
            throw $e;
        }
    }
}
