<?php 
include_once('./config/defines.inc.php');
include_once('./config/config.inc.php');
include_once('./modules/allinone_rewards/models/RewardsSponsorshipModel.php');
require_once(_PS_MODULE_DIR_ . 'allinone_rewards/models/RewardsModel.php');

$stringidsponsors = "";
        $tree = RewardsSponsorshipModel::_getTree(8797);
        foreach ($tree as $sponsor) {
            $stringidsponsors .= $sponsor['id'].",";
        }
echo "<pre>";
print_r($tree);
echo "<hr>";
//die();
        $last_shopping_products = Db::getInstance()->ExecuteS("SELECT
                                                        o.id_order,
                                                        o.date_add,
                                                        o.id_customer,
                                                        c.username name_customer,
                                                        pl.id_product,
                                                        i.id_image,
                                                        m.name name_product,
                                                        m.id_manufacturer,
                                                        pl.link_rewrite,
                                                        p.price,
                                                        od.points as credits
                                                FROM "._DB_PREFIX_."orders o
                                                INNER JOIN "._DB_PREFIX_."rewards r ON ( o.id_order = r.id_order AND r.plugin = 'sponsorship' AND r.id_customer = 8797 )
                                                INNER JOIN "._DB_PREFIX_."customer c ON ( o.id_customer = c.id_customer )
                                                INNER JOIN "._DB_PREFIX_."order_detail od ON ( o.id_order = od.id_order )
                                                INNER JOIN "._DB_PREFIX_."product p ON ( od.product_id = p.id_product )
                                                INNER JOIN "._DB_PREFIX_."image i ON ( od.product_id = i.id_product AND i.cover = 1 )
                                                INNER JOIN "._DB_PREFIX_."product_lang pl ON ( od.product_id = pl.id_product AND pl.id_lang = 1 )
                                                INNER JOIN ps_manufacturer m ON ( p.id_manufacturer = m.id_manufacturer )
                                                WHERE o.id_customer IN ( ".substr($stringidsponsors, 0, -1)." ) AND o.current_state = 2
                                                ORDER BY o.date_add DESC ");


echo "<pre>";
print_r($last_shopping_products);

