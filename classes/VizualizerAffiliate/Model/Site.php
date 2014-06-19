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
 * ユーザーサイトのモデルです。
 *
 * @package VizualizerAffiliate
 * @author Naohisa Minagawa <info@vizualizer.jp>
 */
class VizualizerAffiliate_Model_Site extends Vizualizer_Plugin_Model
{

    /**
     * コンストラクタ
     *
     * @param $values モデルに初期設定する値
     */
    public function __construct($values = array())
    {
        $loader = new Vizualizer_Plugin("affiliate");
        parent::__construct($loader->loadTable("Sites"), $values);
    }

    /**
     * 主キーでデータを取得する。
     *
     * @param $site_id サイトID
     */
    public function findByPrimaryKey($site_id)
    {
        $this->findBy(array("site_id" => $site_id));
    }

    /**
     * サイトコードでデータを取得する。
     *
     * @param $site_code サイトコード
     */
    public function findBySiteCode($site_code)
    {
        $this->findBy(array("site_code" => $site_code));
    }

    /**
     * 顧客IDでデータを取得する。
     *
     * @param $customer_id 顧客ID
     */
    public function findAllByCustomerId($customer_id, $sort = "", $reverse = false)
    {
        return $this->findAllBy(array("customer_id" => $customer_id), $sort, $reverse);
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
        return $conversion->findAllBySiteId($this->site_id);
    }
}
