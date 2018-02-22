<?php 

include_once('../config/defines.inc.php');
include_once('../config/config.inc.php');

$network = Db::getInstance()->ExecuteS("SELECT id_sponsor, id_customer
                                        FROM ps_rewards_sponsorship");

echo json_encode($network);
die();
<<<<<<< HEAD
=======

>>>>>>> 22fd723bf61629b7e47221de4b87b9d187264bf4
