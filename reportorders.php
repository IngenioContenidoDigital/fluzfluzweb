<?php 
include_once('./config/defines.inc.php');
include_once('./config/config.inc.php');
include_once('./modules/allinone_rewards/models/RewardsSponsorshipModel.php');

$reportOrders = new reportOrders();
$reportOrders->init();

class reportOrders {
    public function init() {
        Order::saveExportOrders();
    }
}