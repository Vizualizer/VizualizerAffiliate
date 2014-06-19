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
 * 広告のモデルです。
 *
 * @package VizualizerAffiliate
 * @author Naohisa Minagawa <info@vizualizer.jp>
 */
class VizualizerAffiliate_Model_Advertise extends Vizualizer_Plugin_Model
{

    /**
     * コンストラクタ
     *
     * @param $values モデルに初期設定する値
     */
    public function __construct($values = array())
    {
        $loader = new Vizualizer_Plugin("affiliate");
        parent::__construct($loader->loadTable("Advertises"), $values);
    }

    /**
     * 主キーでデータを取得する。
     *
     * @param $advertise_id 広告ID
     */
    public function findByPrimaryKey($advertise_id)
    {
        $this->findBy(array("advertise_id" => $advertise_id));
    }

    /**
     * 広告コードでデータを取得する。
     *
     * @param $advertise_code 広告コード
     */
    public function findByAdvertiseCode($advertise_code)
    {
        $this->findBy(array("advertise_code" => $advertise_code));
    }

    /**
     * 組織IDでデータを取得する。
     *
     * @param $company_id 組織ID
     */
    public function findAllByCompanyId($company_id, $sort = "", $reverse = false)
    {
        return $this->findAllBy(array("company_id" => $company_id), $sort, $reverse);
    }

    /**
     * 広告に含まれる商品を取得する。
     *
     * @return 商品
     */
    public function products()
    {
        $loader = new Vizualizer_Plugin("admin");
        $product = $loader->loadModel("Product");
        return $product->findAllByAdvertiseId($this->advertise_id);
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
        return $conversion->findAllByAdvertiseId($this->advertise_id);
    }
}
