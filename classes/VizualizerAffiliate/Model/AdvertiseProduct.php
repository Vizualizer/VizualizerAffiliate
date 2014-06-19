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
 * 広告商品のモデルです。
 *
 * @package VizualizerAffiliate
 * @author Naohisa Minagawa <info@vizualizer.jp>
 */
class VizualizerAffiliate_Model_AdvertiseProduct extends Vizualizer_Plugin_Model
{

    /**
     * コンストラクタ
     *
     * @param $values モデルに初期設定する値
     */
    public function __construct($values = array())
    {
        $loader = new Vizualizer_Plugin("affiliate");
        parent::__construct($loader->loadTable("AdvertiseProducts"), $values);
    }

    /**
     * 主キーでデータを取得する。
     *
     * @param $product_id 広告商品ID
     */
    public function findByPrimaryKey($product_id)
    {
        $this->findBy(array("product_id" => $product_id));
    }

    /**
     * 広告商品コードでデータを取得する。
     *
     * @param $product_code 広告商品コード
     */
    public function findByAdvertiseCode($product_code)
    {
        $this->findBy(array("product_code" => $product_code));
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
     * 広告に含まれる成果を取得する。
     *
     * @return 成果
     */
    public function conversions()
    {
        $loader = new Vizualizer_Plugin("admin");
        $conversion = $loader->loadModel("Conversion");
        return $conversion->findAllByProductId($this->product_id);
    }
}
