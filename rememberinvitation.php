<?php 
include_once('./config/defines.inc.php');
include_once('./config/config.inc.php');
include_once('./modules/allinone_rewards/allinone_rewards.php');
include_once('./modules/allinone_rewards/models/RewardsSponsorshipModel.php');

$query = "SELECT
                rs.email,
                rs.lastname,
                rs.firstname,
                c1.id_customer sponsorid,
                c1.username sponsorusername, 
                c1.email sponsoremail,
                c1.firstname sponsorfirstname,
                c1.lastname sponsorlastname
        FROM "._DB_PREFIX_."rewards_sponsorship rs
        INNER JOIN "._DB_PREFIX_."customer c1 ON ( rs.id_sponsor = c1.id_customer )
        LEFT JOIN "._DB_PREFIX_."customer c2 ON ( rs.id_customer = c2.id_customer )
        WHERE c2.id_customer IS NULL
        AND ( 
                NOW() >= ADDDATE(rs.date_add, INTERVAL 1 DAY) AND NOW() <= ADDDATE(rs.date_add, INTERVAL 2 DAY) 
                OR
                NOW() >= ADDDATE(rs.date_add, INTERVAL 3 DAY) AND NOW() <= ADDDATE(rs.date_add, INTERVAL 4 DAY)
        )";

$invitations = Db::getInstance()->executeS($query);
//echo '<pre>'; print_r($invitations); die();

foreach ($invitations as $key => $invitation)
{
    $friendEmail = $invitation['email'];
    $friendLastName = $invitation['lastname'];
    $friendFirstName = $invitation['firstname'];

    $template = 'sponsorship-invitation-novoucher';
    //$template = 'sponsorship-invitation';

    $idTemporary = '1';
    for ($i = 0; $i < strlen($friendEmail); $i++) {
        $idTemporary .= (string) ord($friendEmail[$i]);
    }

    $sponsorship = new RewardsSponsorshipModel();
    $sponsorship->id_sponsor = $invitation['sponsorid'];
    $sponsorship->id_customer = substr($idTemporary, 0, 10);
    $sponsorship->firstname = $friendFirstName;
    $sponsorship->lastname = $friendLastName;
    $sponsorship->channel = 1;
    $sponsorship->email = $friendEmail;

    $vars = array(
                '{message}' => "PRUEBA",
                '{email}' => $invitation['sponsoremail'],
                '{inviter_username}' => $invitation['sponsorusername'],
                '{username}' => $friendFirstName,
                '{lastname}' => $invitation['sponsorlastname'],
                '{firstname}' => $invitation['sponsorfirstname'],
                '{email_friend}' => $friendEmail,
                '{link}' => $sponsorship->getSponsorshipMailLink()
            );

    $allinone_rewards = new allinone_rewards();
    $allinone_rewards->sendMail(1, $template, $allinone_rewards->getL('invitation'), $vars, $friendEmail, $friendFirstName.' '.$friendLastName);
}