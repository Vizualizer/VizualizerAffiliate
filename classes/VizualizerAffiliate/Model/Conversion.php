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
 * 成果のモデルです。
 *
 * @package VizualizerAffiliate
 * @author Naohisa Minagawa <info@vizualizer.jp>
 */
class VizualizerAffiliate_Model_Conversion extends Vizualizer_Plugin_Model
{

    /**
     * コンストラクタ
     *
     * @param $values モデルに初期設定する値
     */
    public function __construct($values = array())
    {
        $loader = new Vizualizer_Plugin("affiliate");
        parent::__construct($loader->loadTable("Conversions"), $values);
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
     * サイトIDでデータを取得する。
     *
     * @param $site_id サイトID
     */
    public function findAllByCompanyId($site_id, $sort = "", $reverse = false)
    {
        return $this->findAllBy(array("site_id" => $site_id), $sort, $reverse);
    }

    /**
     * 広告IDでデータを取得する。
     *
     * @param $advertise_id 広告ID
     */
    public function findAllByAdvertiseId($advertise_id, $sort = "", $reverse = false)
    {
        return $this->findAllBy(array("advertise_id" => $advertise_id), $sort, $reverse);
    }

    /**
     * 商品IDでデータを取得する。
     *
     * @param $product_id 商品ID
     */
    public function findAllByProductId($product_id, $sort = "", $reverse = false)
    {
        return $this->findAllBy(array("product_id" => $product_id), $sort, $reverse);
    }

    /**
     * 広告のデータを取得する。
     */
    public function site()
    {
        $loader = new Vizualizer_Plugin("affiliate");
        $site = $loader->loadModel("Site");
        $site->findByPrimaryKey($this->site_id);
        return $site;
    }

    /**
     * 広告のデータを取得する。
     */
    public function advertise()
    {
        $loader = new Vizualizer_Plugin("affiliate");
        $advertise = $loader->loadModel("Advertise");
        $advertise->findByPrimaryKey($this->advertise_id);
        return $advertise;
    }

    /**
     * 商品のデータを取得する。
     */
    public function product()
    {
        $loader = new Vizualizer_Plugin("affiliate");
        $product = $loader->loadModel("Product");
        $product->findByPrimaryKey($this->product_id);
        return $product;
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
     * 成果ログのデータを取得する。
     */
    public function conversionLog()
    {
        $loader = new Vizualizer_Plugin("affiliate");
        $conversionLog = $loader->loadModel("Conversion");
        $conversionLog->findByTrackingCode($this->tracking_code);
        return $conversionLog;
    }

    /**
     * 保存処理を上書き
     */
    public function save(){
        if($this->conversion_status == "2" && empty($this->commit_time)){
            $this->commit_time = date("Y-m-d H:i:s");
        }
        parent::save();
    }
}
