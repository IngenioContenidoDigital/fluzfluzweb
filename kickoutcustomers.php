<?php 
include_once('./config/defines.inc.php');
include_once('./config/config.inc.php');
include_once('./modules/allinone_rewards/models/RewardsSponsorshipModel.php');
include_once('./modules/allinone_rewards/allinone_rewards.php');

$kickoutCustomers = new kickoutCustomers();
$kickoutCustomers->init();

class kickoutCustomers {
    public $updatesNetwork = array();
    
    public function init() {
        $customers = $this->getCustomersKickOut();
        foreach ($customers as $customer) {
            $this->kickOut( $customer );
            $this->insertCustomerKickOut($customer);
            $this->deleteCustomerNetwork($customer);
            $this->finallykickOut();
        }
    }

    public function getCustomersKickOut() {
        $query = "SELECT rs.id_customer id, rs.*
                    FROM "._DB_PREFIX_."customer c
                    INNER JOIN "._DB_PREFIX_."rewards_sponsorship rs ON ( c.id_customer = rs.id_customer)
                    WHERE c.kick_out = 1";
        return ( Db::getInstance()->executeS($query) );
    }

    public function kickOut( $customer_kick_out ) {
        $sponsorships = $this->geTreeInformation($customer_kick_out['id']);

        // tomar clientes que estan debajo del cliente kick out
        $sponsors = array();
        foreach ( $sponsorships as $sponsorship ) {
            if ( $sponsorship['id_sponsor'] == $customer_kick_out['id'] ) {
                $sponsors[] = $sponsorship;
            }
        }

        // validar subida cliente
        $up = 2;
        $numSponsors = count($sponsors);
        if ( $numSponsors == 2 ) {
            if ( $sponsors[0]['points'] == $sponsors[1]['points'] ) {
                if ( $sponsors[0]['date_add_unix_customer'] == $sponsors[1]['date_add_unix_customer'] ) {
                    if ( $sponsors[0]['date_add_unix_sponsorship'] == $sponsors[1]['date_add_unix_sponsorship'] ) {
                        $up = rand(0, 1);
                    } elseif( $sponsors[0]['date_add_unix_sponsorship'] < $sponsors[1]['date_add_unix_sponsorship'] ) {
                        $up = 0;
                    } else {
                        $up = 1;
                    }
                } elseif( $sponsors[0]['date_add_unix_customer'] < $sponsors[1]['date_add_unix_customer'] ) {
                    $up = 0;
                } else {
                    $up = 1;
                }
            } elseif( $sponsors[0]['points'] > $sponsors[1]['points'] ) {
                $up = 0;
            } else {
                $up = 1;
            }
        } elseif ( $numSponsors == 1 ) {
            $up = 0;
        }

        if ( $up != 2 ) {
            if ( count($this->updatesNetwork) == 0 ) {
                $this->updateCustomerNetwork($sponsors[$up]['id'], $customer_kick_out['id_sponsor']);
            } else {
                $this->updateCustomerNetwork($sponsors[$up]['id'], $customer_kick_out['id']);
            }
            
            if ( $numSponsors == 2 ) {
                $down = ($up == 1) ? 0 : 1;
                $this->updateCustomerNetwork($sponsors[$down]['id'], $sponsors[$up]['id']);
            }
            
            $this->kickOut( $sponsors[$up] );
        } else {
            $this->notificationSponsor($customer_kick_out['id_sponsor']);
        }
    }

    public function geTreeInformation($id_customer) {
        $sponsorships = RewardsSponsorshipModel::_getTreeComplete($id_customer);
        foreach ($sponsorships as &$sponsorship) {
            $query = "SELECT c.email, rs.id_sponsor, c.firstname, c.lastname, IFNULL(SUM(r.credits),0) points, c.date_add date_add_customer, UNIX_TIMESTAMP(c.date_add) date_add_unix_customer, rs.date_add date_add_sponsorship, UNIX_TIMESTAMP(rs.date_add) date_add_unix_sponsorship
                        FROM "._DB_PREFIX_."rewards_sponsorship rs
                        LEFT JOIN "._DB_PREFIX_."customer c ON ( rs.id_customer = c.id_customer )
                        LEFT JOIN "._DB_PREFIX_."rewards r ON ( rs.id_customer = r.id_customer )
                        WHERE rs.id_customer = ".$sponsorship['id'];
            $informationValidation = Db::getInstance()->executeS($query);
            $sponsorship = array_merge($sponsorship, $informationValidation[0]);
        }
        return $sponsorships;
    }

    public function updateCustomerNetwork($id_customer, $id_sponsor) {
        // almacenar actualizacion de la red
        $this->updatesNetwork[] = "UPDATE "._DB_PREFIX_."rewards_sponsorship
                                    SET id_sponsor = ".$id_sponsor.", date_upd = NOW()
                                    WHERE id_customer = ".$id_customer;
        // almacenar en la tabla de promovidos
        Db::getInstance()->execute("INSERT INTO "._DB_PREFIX_."promoted (id_customer, date_add)
                                    VALUES (".$id_customer.", NOW())");
        
        // alertar sponsor de su nuevo sponsorship
        $this->notificationSponsor($id_sponsor, $id_customer, true);
    }

    public function insertCustomerKickOut($customer) {
        $numSponsorships = RewardsSponsorshipModel::getSponsorshipAscendants($customer['id']);
        $level = count( array_slice($numSponsorships, 1, 15) );
        
        // Mover usuario a grupo de clientes
        Db::getInstance()->Execute("DELETE FROM "._DB_PREFIX_."customer_group WHERE id_customer = ".$customer['id']);
        Db::getInstance()->Execute("INSERT INTO "._DB_PREFIX_."customer_group VALUES (".$customer['id'].",1), (".$customer['id'].",2)");
        Db::getInstance()->Execute("UPDATE "._DB_PREFIX_."customer SET id_default_group = 2 WHERE id_customer = ".$customer['id']);

        $query = "INSERT INTO "._DB_PREFIX_."rewards_sponsorship_kick_out (id_sponsor, id_customer, email, lastname, firstname, date_add, date_kick_out, level)
                    VALUES (".$customer['id_sponsor'].", ".$customer['id'].", '".$customer['email']."', '".$customer['lastname']."', '".$customer['firstname']."', '".$customer['date_add']."', NOW(), ".$level.")";
        return Db::getInstance()->execute($query);
    }

    public function deleteCustomerNetwork($customer) {
        
        $query = "SELECT c.id_customer, c.username, c.email, SUM(r.credits) points
                    FROM "._DB_PREFIX_."customer c
                    LEFT JOIN "._DB_PREFIX_."rewards r ON ( c.id_customer = r.id_customer AND r.id_reward_state = 2 )
                    WHERE c.id_customer = ".$customer['id'];
        $customerdata = Db::getInstance()->getRow($query);
        
        $contributor_count = Db::getInstance()->getValue("SELECT COUNT(*) contributor_count
                                                            FROM "._DB_PREFIX_."customer
                                                            WHERE active = 1");
        
        $points_count = Db::getInstance()->getValue("SELECT SUM(credits) points_count
                                                        FROM "._DB_PREFIX_."rewards
                                                        WHERE id_reward_state = 2");
        
        $template = "cancellation_account";
        $subject = "Tu cuenta fue Cancelada.";
        $vars = array(
            '{username}' => $customerdata['username'],
            '{days_inactive}' => 60,
            '{contributor_count}' => $contributor_count,
            '{points}' => $customerdata['points'] == "" ? 0 : round($customerdata['points']),
            '{points_count}' => round($points_count),
            '{shop_name}' => Configuration::get('PS_SHOP_NAME'),
            '{shop_url}' => Context::getContext()->link->getPageLink('index', true, Context::getContext()->language->id, null, false, Context::getContext()->shop->id),
            '{learn_more_url}' => "http://reglas.fluzfluz.co"
        );
        $reward = new allinone_rewards();
        $reward->sendMail(Context::getContext()->language->id, $template, $subject, $vars, $customerdata['email'], $customerdata['username']);
            
        /*Mail::Send(
            Context::getContext()->language->id,
            "cancellation_account",
            "Tu cuenta fue Cancelada.",
            $vars,
            $customerdata['email'],
            $customerdata['username']
        );*/
        
        return Db::getInstance()->execute("DELETE FROM "._DB_PREFIX_."rewards_sponsorship
                                            WHERE id_customer = ".$customer['id']);
    }
    
    public function notificationSponsor($id_sponsor, $id_customer = "", $new_sponsorship = false) {
        /*$messagesponsorship = "";
        if ( $new_sponsorship ) {
            $usernamepromoted = Db::getInstance()->getValue("SELECT username FROM "._DB_PREFIX_."customer WHERE id_customer = ".$id_customer);
            $messagesponsorship = " ".$usernamepromoted." ha sido promovido en tu red.";
        }

        Db::getInstance()->execute("INSERT INTO "._DB_PREFIX_."message_sponsor (id_message_sponsor, id_customer_send, id_customer_receive, message, date_send)
                                    VALUES ('',".Configuration::get('CUSTOMER_MESSAGES_FLUZ').", ".$id_sponsor.", 'Uno de los usuarios en tu red ha abandonado.".$messagesponsorship."', NOW())");

        Db::getInstance()->execute("INSERT INTO "._DB_PREFIX_."notification_history(id_customer, type_message, message, date_send)
                                    VALUES (".$id_sponsor.",'Fluzzer promovido', 'Uno de los usuarios en tu red ha abandonado.".$messagesponsorship."', NOW())");

        $query = "SELECT c.username, c.email, SUM(r.credits) points
                    FROM ps_customer c
                    LEFT JOIN ps_rewards r ON (c.id_customer = r.id_customer AND r.id_reward_state = 2)
                    WHERE c.id_customer = ".$id_sponsor;
        $sponsorinformation = Db::getInstance()->executeS($query);

        $vars = array(
            '{username}' => $sponsorinformation[0]['username'],
            '{customer_promoted}' => $usernamepromoted,
            '{points}' => $sponsorinformation[0]['points'] == "" ? 0 : round($sponsorinformation[0]['points']),
            '{shop_name}' => Configuration::get('PS_SHOP_NAME'),
            '{shop_url}' => Context::getContext()->link->getPageLink('index', true, Context::getContext()->language->id, null, false, Context::getContext()->shop->id)
        );

        Mail::Send(
            Context::getContext()->language->id,
            'notificationSponsorKickout',
            'Se ha movido de posicion uno de tus Fluzzers',
            $vars,
            $sponsorinformation[0]['email'],
            $sponsorinformation[0]['username']
        );*/
    }
    
    public function finallykickOut() {
        foreach ( $this->updatesNetwork as $updateNetwork ) {
            Db::getInstance()->execute($updateNetwork);
        }
        $this->updatesNetwork = array();
    }
}