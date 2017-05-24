<?php 
include_once('./config/defines.inc.php');
include_once('./config/config.inc.php');
include_once('./modules/allinone_rewards/models/RewardsSponsorshipModel.php');
require_once(_PS_MODULE_DIR_ . 'allinone_rewards/models/RewardsModel.php');

$encrypt = new encryptphone();
$encrypt->init();

class encryptphone {
    public $updatesNetwork = array();
    
    public function init() {
        $sql ="SELECT
"._DB_PREFIX_."product_code.`code`,
"._DB_PREFIX_."product_code.id_order, 
"._DB_PREFIX_."webservice_external_log.mobile_phone     
FROM
"._DB_PREFIX_."webservice_external_log
INNER JOIN "._DB_PREFIX_."product_code ON "._DB_PREFIX_."product_code.`code` = "._DB_PREFIX_."webservice_external_log.mobile_phone
ORDER BY `code`";
        $result=DB::getInstance()->executeS($sql);
        foreach($result as $r){
         
                $chainsql="SELECT "._DB_PREFIX_."customer.id_customer, "._DB_PREFIX_."customer.secure_key 
                    FROM "._DB_PREFIX_."webservice_external_log INNER JOIN "._DB_PREFIX_."orders ON "._DB_PREFIX_."webservice_external_log.id_order = "._DB_PREFIX_."orders.id_order INNER JOIN "._DB_PREFIX_."customer ON "._DB_PREFIX_."orders.id_customer = "._DB_PREFIX_."customer.id_customer 
                        WHERE "._DB_PREFIX_."webservice_external_log.id_order =".$r['id_order'];
                $chainrow= Db::getInstance()->getRow($chainsql);
                            
                $chain=Encrypt::encrypt($chainrow['secure_key'] , $r['mobile_phone']);
                $sqlupdate="UPDATE "._DB_PREFIX_."product_code SET "._DB_PREFIX_."product_code.`code`='".$chain."' WHERE "._DB_PREFIX_."product_code.id_order='".$r['id_order']."' AND "._DB_PREFIX_."product_code.`code`='".$r['code']."'";
                Db::getInstance()->execute($sqlupdate);
        }
    }

}