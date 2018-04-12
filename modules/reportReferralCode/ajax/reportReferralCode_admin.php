<?php
include_once('../../../config/config.inc.php');
include_once('../../../config/defines.inc.php');


switch (Tools::getValue('action')) {
    case 'searchCode':
        $username_search = strtolower($_POST['username']);

        $tree = Db::getInstance()->executeS('SELECT rsc.code, rsc.id_sponsor, c.email 
                                    FROM '._DB_PREFIX_.'rewards_sponsorship_code rsc
                                    INNER JOIN '._DB_PREFIX_.'customer c ON (rsc.id_sponsor = c.id_customer)
                                    WHERE c.active = 1 AND c.kick_out != 1');

        if (!empty($username_search)){
            $usersFind = array();
            foreach ($tree as &$usertree){
                $username = strtolower($usertree['code']);
                $email = strtolower($usertree['email']);
                $dni = $usertree['id_sponsor'];

                $coincidenceusername = strpos($username,$username_search);
                $coincidenceemail = strpos($email,$username_search);
                $coincidendni = strpos($dni,$username_search);

                if ( $coincidenceusername !== false || $coincidenceemail !== false || $coincidendni !== false) {
                    $usersFind[] = $usertree;
                }
            }
            die (json_encode($usersFind));
        }
        break;
    case 'clickSearch':
        $code_referral = Tools::getValue('referral_code');

        $email_sponsors = Db::getInstance()->executeS('SELECT c.email, c.date_add 
                            FROM '._DB_PREFIX_.'rewards_sponsorship_code rsc
                            INNER JOIN '._DB_PREFIX_.'customer c ON (rsc.id_sponsor = c.id_customer)
                            WHERE rsc.code_sponsor = "'.$code_referral.'"');

        die (json_encode($email_sponsors));
        break; 
    case 'exportFunc':
        $code_referral = Tools::getValue('referral_code');
        $email_sponsors = Db::getInstance()->executeS('SELECT c.firstname, c.email, c.date_add 
                            FROM '._DB_PREFIX_.'rewards_sponsorship_code rsc
                            INNER JOIN '._DB_PREFIX_.'customer c ON (rsc.id_sponsor = c.id_customer)
                            WHERE rsc.code_sponsor = "'.$code_referral.'"');
        
        
        $report = "<html>
                        <head>
                            <meta http-equiv=?Content-Type? content=?text/html; charset=utf-8? />
                        </head>
                            <body>
                                <table>
                                    <tr>
                                        <th>Nombre</th>
                                        <th>Email</th> 
                                        <th>fecha</th>";
        
        $report .= "</tr>";
        
        foreach ( $email_sponsors as $customer ) {
            
            $report .= "<tr>
                            <td>".$customer['firstname']."</td>
                            <td>".$customer['email']."</td>
                            <td>".$customer['date_add']."</td>";
            
            $report .= "</tr>";
        }
        
        $report .= "         </table>
                        </body>
                    </html>";
        header("Content-Type: application/vnd.ms-excel");
        header("Expires: 0");
        header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
        header("content-disposition: attachment;filename=report_referralCodes.xls");
        die(true);
        break;
    default:
        break;
}