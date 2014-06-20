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
 * 自動で提携申請するためのバッチです。
 *
 * @package VizualizerAffiliate
 * @author Naohisa Minagawa <info@vizualizer.jp>
 */
class VizualizerAffiliate_Batch_AutoContract extends Vizualizer_Plugin_Batch
{

    public function getDaemonName()
    {
        return "autoContract";
    }

    public function getName()
    {
        return "Auto Contract";
    }

    public function getFlows()
    {
        return array("autoContract");
    }

    public function getDaemonInterval()
    {
        return 600;
    }

    /**
     * 自動提携処理を実施。
     *
     * @param $params バッチ自体のパラメータ
     * @param $data バッチで引き回すデータ
     * @return バッチで引き回すデータ
     */
    protected function autoContract($params, $data)
    {
        $loader = new Vizualizer_Plugin("Admin");
        $company = $loader->loadModel("Company");
        $companys = $company->findAllBy(array());
        $loader = new Vizualizer_Plugin("Member");
        $customer = $loader->loadModel("Customer");
        $customers = $customer->findAllBy(array());

        foreach($customers as $customer){
            foreach($companys as $company){
                // コンバージョンデータを更新
                $connection = Vizualizer_Database_Factory::begin("affiliate");
                try {
                    $loader = new Vizualizer_Plugin("Affiliate");
                    $contract = $loader->loadModel("Contract");
                    $contract->findBy(array("company_id" => $company->company_id, "customer_id" => $customer->customer_id));
                    $contract->company_id = $company->company_id;
                    $contract->customer_id = $customer->customer_id;
                    $contract->contract_status = 2;
                    $contract->save();

                    Vizualizer_Database_Factory::commit($connection);
                } catch (Exception $e) {
                    Vizualizer_Database_Factory::rollback($connection);
                    throw new Vizualizer_Exception_Database($e);
                }
            }
        }

        return $data;
    }
}
