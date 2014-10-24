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
class VizualizerAffiliate_Batch_TransferConversion extends Vizualizer_Plugin_Batch
{

    public function getDaemonName()
    {
        return "transferConversion";
    }

    public function getName()
    {
        return "Transfer Conversion";
    }

    public function getFlows()
    {
        return array("transferConversion");
    }

    public function getDaemonInterval()
    {
        return 600;
    }

    /**
     * コンバージョンログで移動していないものを移動。
     *
     * @param $params バッチ自体のパラメータ
     * @param $data バッチで引き回すデータ
     * @return バッチで引き回すデータ
     */
    protected function transferConversion($params, $data)
    {
        $loader = new Vizualizer_Plugin("Affiliate");
        $conversionLog = $loader->loadModel("ConversionLog");

        $conversionLogs = $conversionLog->findAllBy(array("log_status" => "0"));
        foreach($conversionLogs as $conversionLog){
            $conversion = $loader->loadModel("Conversion");
            $conversion->findByPrimaryKey($conversionLog->conversion_id);
            if(!($conversion->conversion_id > 0)){
                $conversion = $loader->loadModel("Conversion", array("conversion_id" => $conversionLog->conversion_id));
            }

            // コンバージョンデータを登録
            $connection = Vizualizer_Database_Factory::begin("affiliate");
            try {
                $conversion->tracking_code = $conversionLog->tracking_code;
                $conversion->order_code = $conversionLog->order_code;
                $conversion->total = $conversionLog->total;
                $conversion->conversion_time = $conversionLog->create_time;
                $conversion->save();
                Vizualizer_Logger::writeInfo("Created conversion : ".$conversion->tracking_code);

                $conversionLog->log_status = 1;
                $conversionLog->save();

                Vizualizer_Database_Factory::commit($connection);
            } catch (Exception $e) {
                Vizualizer_Database_Factory::rollback($connection);
                throw new Vizualizer_Exception_Database($e);
            }
        }

        return $data;
    }
}
