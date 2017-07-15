<?php 
include_once('./config/defines.inc.php');
include_once('./config/config.inc.php');

//**************************************************************************************************//

$tree = RewardsSponsorshipModel::_getTree(2,'EfrainArmentaF');
//echo "<pre>"; print_r($tree); die();

$report = "<table>
                <tr>
                    <th>id</th>
                    <th>level</th>
                    <th>username</th>
                </tr>";
foreach ($tree as $user) {
    $report .= "<tr>
                    <td>".$user['id']."</td>
                    <td>".$user['level']."</td>
                    <td>".$user['username']." </td>
                <tr>";
}
$report .= "</table>";
//echo $report; die();

header("Content-Type: application/vnd.ms-excel");
header("Expires: 0");
header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
header("content-disposition: attachment;filename=network.xls");
die($report);

class RewardsSponsorshipModel {
    public static function _getTree($idSponsor, $username) {
        $result = array('maxlevel' => 1, 'rewards1' => 0, 'direct_nb1' => 0, 'direct_nb2' => 0, 'direct_nb3' => 0, 'direct_nb4' => 0, 'direct_nb5' => 0, 'indirect_nb' => 0,
                                            'indirect_nb_orders' => 0, 'nb_orders_channel1' => 0, 'nb_orders_channel2' => 0, 'nb_orders_channel3' => 0, 'nb_orders_channel4' => 0, 'nb_orders_channel5' => 0,
                                            'direct_rewards_orders1' => 0, 'direct_rewards_orders2' => 0, 'direct_rewards_orders3' => 0, 'direct_rewards_orders4' => 0, 'direct_rewards_orders5' => 0, 'indirect_rewards' => 0,
                                            'direct_rewards_registrations1' => 0, 'direct_rewards_registrations2' => 0, 'direct_rewards_registrations3' => 0, 'direct_rewards_registrations4' => 0, 'direct_rewards_registrations5' => 0,
                                            'sponsored1' => array(), 'total_direct_rewards' => 0, 'total_indirect_rewards' => 0, 'total_direct_orders' => 0, 'total_indirect_orders' => 0,
                                            'total_orders' => 0, 'total_registrations' => 0, 'total_global' => 0);
        $sponsor_tree = array();
        $sponsor_tree[] = array(
                                "id" => $idSponsor,
                                "username" => $username,
                                "level" => 0,
                            );
        self::_getRecursiveDescendantsTree($idSponsor, $result, $sponsor_tree);
        return $sponsor_tree;
    }

    public static function _getRecursiveDescendantsTree($idSponsor, &$result, &$sponsor_tree, $level=1, $father=null) {
            $query = '
                    SELECT *
                    FROM `'._DB_PREFIX_.'rewards_sponsorship` AS rs
                    WHERE rs.id_sponsor = '.(int)$idSponsor.'
                    AND rs.id_customer > 0';
            $rows = Db::getInstance()->ExecuteS($query);

            if (is_array($rows) && count($rows) > 0) {
                    if ($level > $result['maxlevel']) {
                            $result['maxlevel'] = $level;
                            $result['rewards'.$result['maxlevel']] = 0;
                    }

                    foreach ($rows AS $row)	{
                            
                            $query = '
                                    SELECT username
                                    FROM `'._DB_PREFIX_.'customer` AS c
                                    WHERE c.id_customer = '.(int)$row['id_customer'];
                            $username = Db::getInstance()->getValue($query);
                        
                            if ($level == 1) {
                                    $result['direct_nb'.$row['channel']]++;
                                    $father = $row['id_customer'];
                            } else {
                                    $result['indirect_nb']++;
                            }

                            if ( $level <= 15 ) {
                                $sponsor_tree[] = array(
                                    "id" => $row['id_customer'],
                                    "username" => $username,
                                    "level" => $level,
                                );
                            }

                            // nb direct or indirect friends for each level 1 sponsored
                            if (!isset($result['direct_customer'.$idSponsor])) {
                                    $result['direct_customer'.$idSponsor] = 0;
                            }

                            $result['direct_customer'.$idSponsor]++;

                            if (isset($father) && $level > 1 && $father != $idSponsor) {
                                    if (!isset($result['indirect_customer'.$father])) {
                                            $result['indirect_customer'.$father] = 0;
                                    }
                                    $result['indirect_customer'.$father]++;
                            }

                            // nb sponsored by level
                            if (!isset($result['nb'.$level])) {
                                    $result['nb'.$level] = 0;
                            }

                            $result['nb'.$level]++;
                            self::_getRecursiveDescendantsTree($row['id_customer'], $result, $sponsor_tree, $level+1, $father);
                    }
            }
    }
}
