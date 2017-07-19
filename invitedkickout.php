<?php 
include_once('./config/defines.inc.php');
include_once('./config/config.inc.php');
include_once('./modules/allinone_rewards/allinone_rewards.php');
include_once('./modules/allinone_rewards/models/RewardsSponsorshipModel.php');
include_once('./modules/allinone_rewards/controllers/front/sponsorship.php');

$tree_s = array('1189','176','175','1417','467','2321','1066','635','780',
                '4940','4671','4614','4946','1095','4301','4085','936','375','341','1167');
$network = array();

foreach($tree_s as $net_k){
    $query = 'SELECT * FROM '._DB_PREFIX_.'customer WHERE id_customer='.$net_k.' AND warning_kick_out = 1';
    $remember_i = Db::getInstance()->executeS($query);
    
    array_push($network, $remember_i);
}
$list_kick = array_map('current',$network);

foreach ($list_kick as $x){
    
    $tree = Db::getInstance()->executeS('SELECT * FROM '._DB_PREFIX_.'customer');
    $array_sponsor = array();
    foreach ($tree as $network) {
        $sponsor = Db::getInstance()->getRow("SELECT c.id_customer, c.username, c.firstname, c.lastname, c.email, (2-COUNT(rs.id_sponsorship) ) sponsoships
                FROM " . _DB_PREFIX_ . "customer c
                LEFT JOIN " . _DB_PREFIX_ . "rewards_sponsorship rs ON ( c.id_customer = rs.id_sponsor )
                LEFT JOIN "._DB_PREFIX_."customer_group cg ON (c.id_customer = cg.id_customer)
                WHERE c.id_customer =" . (int) $network['id_customer'] . " AND cg.id_group = 4 AND c.id_customer != 1
                HAVING sponsoships > 0");
        
        if ($sponsor['id_customer'] != "" ) {
                array_push($array_sponsor, $sponsor);
            }
    }
    $sort_array = array_filter($array_sponsor);

    usort($sort_array, function($a, $b) {
        return $a['id_customer'] - $b['id_customer'];
    });
    
    $sponsor_a = reset($sort_array);
    
    if (!empty($sponsor_a) && ($sponsor_a['sponsoships'] > 0)) {
        
        $sponsorship = new RewardsSponsorshipModel();
        $sponsorship->id_sponsor = $sponsor_a['id_customer'];
        $sponsorship->id_customer = Allinone_rewardsSponsorshipModuleFrontController::generateIdTemporary($x['email']);
        $sponsorship->firstname = $x['firstname'];
        $sponsorship->lastname = $x['lastname'];
        $sponsorship->channel = 1;
        $sponsorship->email = $x['email'];
        $send = "";
        
        if ($sponsorship->save()) {
        
        $vars = array(
                '{email}' => $sponsor_a['email'],
                '{firstname_invited}'=> $sponsorship->firstname,
                '{inviter_username}' => $sponsor_a['username'],
                '{username}' => $sponsor_a['username'],
                '{lastname}' => $sponsor_a['lastname'],
                '{firstname}' => $sponsor_a['firstname'],
                '{email_friend}' => $sponsorship->email,
                '{Expiration}'=> $send,
                '{link}' => $sponsorship->getSponsorshipMailLink());
        
        $template = 'sponsorship-invitation-novoucher';
        $prefix_template = '16-sponsorship-invitation-novoucher';

        $query_subject = 'SELECT subject_mail FROM '._DB_PREFIX_.'mail_send WHERE name_mail ="'.$prefix_template.'"';
        $row_subject = Db::getInstance()->getRow($query_subject);
        $message_subject = $row_subject['subject_mail'];
        
        $allinone_rewards = new allinone_rewards();
        $allinone_rewards->sendMail(1, $template, $allinone_rewards->getL($message_subject), $vars, $sponsorship->email, $sponsorship->firstname.' '.$sponsorship->lastname);
    
        }
    }
}