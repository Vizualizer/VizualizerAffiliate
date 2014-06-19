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
 * エントランスログのモデルです。
 *
 * @package VizualizerAffiliate
 * @author Naohisa Minagawa <info@vizualizer.jp>
 */
class VizualizerAffiliate_Model_ConversionLog extends Vizualizer_Plugin_Model
{

    /**
     * コンストラクタ
     *
     * @param $values モデルに初期設定する値
     */
    public function __construct($values = array())
    {
        $loader = new Vizualizer_Plugin("affiliate");
        parent::__construct($loader->loadTable("ConversionLogs"), $values);
    }

    /**
     * 主キーでデータを取得する。
     *
     * @param $conversion_id コンバージョンID
     */
    public function findByPrimaryKey($conversion_id)
    {
        $this->findBy(array("conversion_id" => $conversion_id));
    }

    /**
     * トラッキングコードでデータを取得する。
     *
     * @param $tracking_code トラッキングコード
     */
    public function findByAdvertiseCode($tracking_code)
    {
        $this->findBy(array("tracking_code" => $tracking_code));
    }

    /**
     * 広告のデータを取得する。
     */
    public function entranceLog()
    {
        $loader = new Vizualizer_Plugin("affiliate");
        $entranceLog = $loader->loadModel("EntranceLog");
        $entranceLog->findByTrackingCode($this->tracking_code);
        return $entranceLog;
    }

    /**
     * 成果のデータを取得する。
     */
    public function conversion()
    {
        $loader = new Vizualizer_Plugin("affiliate");
        $conversion = $loader->loadModel("Conversion");
        $conversion->findByTrackingCode($this->tracking_code);
        return $conversion;
    }
}
