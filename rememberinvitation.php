<?php 
include_once('./config/defines.inc.php');
include_once('./config/config.inc.php');
include_once('./modules/allinone_rewards/allinone_rewards.php');
include_once('./modules/allinone_rewards/models/RewardsSponsorshipModel.php');

$query = "SELECT
            rs.id_sponsorship,
            rs.email,
            rs.lastname,
            rs.firstname,
            rs.id_customer,
            rs.date_add,
            DATEDIFF(NOW(), rs.date_add) AS days,
            c1.id_customer sponsorid,
            c1.username sponsorusername, 
            c1.email sponsoremail,
            c1.firstname sponsorfirstname,
            c1.lastname sponsorlastname
        FROM "._DB_PREFIX_."rewards_sponsorship rs
        LEFT JOIN "._DB_PREFIX_."customer c1 ON ( rs.id_sponsor = c1.id_customer )
        LEFT JOIN "._DB_PREFIX_."customer c2 ON ( rs.id_customer = c2.id_customer )
        WHERE c2.id_customer IS NULL
        HAVING ( days IN (1,5,6,7) OR days > 7 )";

$invitations = Db::getInstance()->executeS($query);

//echo '<pre>'; print_r($invitations); die();

foreach ($invitations as $key => &$invitation) {
    $send = "";
    $sixDays = "<label>Este es un recordatorio de que tiene 6 d&iacute;as antes de que expire tu invitaci&oacute;n.</label>";
    $twoDays = "<label>Este es un recordatorio de que tiene 2 d&iacute;as antes de que expire tu invitaci&oacute;n.</label>";
    $oneDays = "<label>Este es un recordatorio de que tiene 1 d&iacute;a antes de que expire tu invitaci&oacute;n.</label>";
    $sixHour = "<label>Este es un recordatorio de que tiene 6 horas antes de que expire tu invitaci&oacute;n.</label>";

    if ( $invitation['sponsorusername'] == "" ) {
        $invitation['sponsorusername'] = "Usuario Patrocinador";
    }
    
    $days = $invitation['days'];
    if ($days == 1) {

        $friendEmail = $invitation['email'];
        $friendLastName = $invitation['lastname'];
        $friendFirstName = $invitation['firstname'];

        $template = 'sponsorship-invitation-novoucher';
        //$template = 'sponsorship-invitation';

        $idTemporary = '1';
        for ($i = 0; $i < strlen($friendEmail); $i++) {
            $idTemporary .= (string) ord($friendEmail[$i]);
        }

        $sponsorship = new RewardsSponsorshipModel($invitation['id_sponsorship']);
        $sponsorship->id_sponsor = $invitation['sponsorid'];
        $sponsorship->id_customer = substr($idTemporary, 0, 10);
        $sponsorship->firstname = $friendFirstName;
        $sponsorship->lastname = $friendLastName;
        $sponsorship->channel = 1;
        $sponsorship->email = $friendEmail;

        $vars = array(
                    '{message}' => "PRUEBA",
                    '{firstname_invited}'=> $friendFirstName,
                    '{email}' => $invitation['sponsoremail'],
                    '{username}' => $invitation['sponsorusername'],
                    '{lastname}' => $invitation['sponsorlastname'],
                    '{firstname}' => $invitation['sponsorfirstname'],
                    '{email_friend}' => $friendEmail,
                    '{Expiration}'=> $sixDays,
                    '{link}' => $sponsorship->getSponsorshipMailLink()
                );
        
        $prefix_template = '16-sponsorship-invitation-novoucher';

        $query_subject = 'SELECT subject_mail FROM '._DB_PREFIX_.'mail_send WHERE name_mail ="'.$prefix_template.'"';
        $row_subject = Db::getInstance()->getRow($query_subject);
        $message_subject = $row_subject['subject_mail'];
        
        $allinone_rewards = new allinone_rewards();
        $allinone_rewards->sendMail(1, $template, $allinone_rewards->getL($message_subject), $vars, $friendEmail, $friendFirstName.' '.$friendLastName);
    }

    else if ($days == 5) {
        $friendEmail = $invitation['email'];
        $friendLastName = $invitation['lastname'];
        $friendFirstName = $invitation['firstname'];

        $template = 'sponsorship-invitation-novoucher';
        //$template = 'sponsorship-invitation';

        $idTemporary = '1';
        for ($i = 0; $i < strlen($friendEmail); $i++) {
            $idTemporary .= (string) ord($friendEmail[$i]);
        }

        $sponsorship = new RewardsSponsorshipModel($invitation['id_sponsorship']);
        $sponsorship->id_sponsor = $invitation['sponsorid'];
        $sponsorship->id_customer = substr($idTemporary, 0, 10);
        $sponsorship->firstname = $friendFirstName;
        $sponsorship->lastname = $friendLastName;
        $sponsorship->channel = 1;
        $sponsorship->email = $friendEmail;

        $vars = array(
                    '{message}' => "PRUEBA",
                    '{firstname_invited}'=> $friendFirstName,
                    '{email}' => $invitation['sponsoremail'],
                    '{username}' => $invitation['sponsorusername'],
                    '{lastname}' => $invitation['sponsorlastname'],
                    '{firstname}' => $invitation['sponsorfirstname'],
                    '{email_friend}' => $friendEmail,
                    '{Expiration}'=> $twoDays,
                    '{link}' => $sponsorship->getSponsorshipMailLink()
                );
        $prefix_template = '16-sponsorship-invitation-novoucher';

        $query_subject = 'SELECT subject_mail FROM '._DB_PREFIX_.'mail_send WHERE name_mail ="'.$prefix_template.'"';
        $row_subject = Db::getInstance()->getRow($query_subject);
        $message_subject = $row_subject['subject_mail'];
        
        $allinone_rewards = new allinone_rewards();
        $allinone_rewards->sendMail(1, $template, $allinone_rewards->getL($message_subject), $vars, $friendEmail, $friendFirstName.' '.$friendLastName);
    }

    else if ($days == 6) {
        $friendEmail = $invitation['email'];
        $friendLastName = $invitation['lastname'];
        $friendFirstName = $invitation['firstname'];

        $template = 'sponsorship-invitation-novoucher';
        //$template = 'sponsorship-invitation';

        $idTemporary = '1';
        for ($i = 0; $i < strlen($friendEmail); $i++) {
            $idTemporary .= (string) ord($friendEmail[$i]);
        }

        $sponsorship = new RewardsSponsorshipModel($invitation['id_sponsorship']);
        $sponsorship->id_sponsor = $invitation['sponsorid'];
        $sponsorship->id_customer = substr($idTemporary, 0, 10);
        $sponsorship->firstname = $friendFirstName;
        $sponsorship->lastname = $friendLastName;
        $sponsorship->channel = 1;
        $sponsorship->email = $friendEmail;

        $vars = array(
                    '{message}' => "PRUEBA",
                    '{firstname_invited}'=> $friendFirstName,
                    '{email}' => $invitation['sponsoremail'],
                    '{username}' => $invitation['sponsorusername'],
                    '{lastname}' => $invitation['sponsorlastname'],
                    '{firstname}' => $invitation['sponsorfirstname'],
                    '{email_friend}' => $friendEmail,
                    '{Expiration}'=> $oneDays,
                    '{link}' => $sponsorship->getSponsorshipMailLink()
                );

         $prefix_template = '16-sponsorship-invitation-novoucher';

        $query_subject = 'SELECT subject_mail FROM '._DB_PREFIX_.'mail_send WHERE name_mail ="'.$prefix_template.'"';
        $row_subject = Db::getInstance()->getRow($query_subject);
        $message_subject = $row_subject['subject_mail'];
        
        $allinone_rewards = new allinone_rewards();
        $allinone_rewards->sendMail(1, $template, $allinone_rewards->getL($message_subject), $vars, $friendEmail, $friendFirstName.' '.$friendLastName);
    }

    else if ($days == 7) {
        $friendEmail = $invitation['email'];
        $friendLastName = $invitation['lastname'];
        $friendFirstName = $invitation['firstname'];

        $template = 'sponsorship-invitation-novoucher';
        //$template = 'sponsorship-invitation';

        $idTemporary = '1';
        for ($i = 0; $i < strlen($friendEmail); $i++) {
            $idTemporary .= (string) ord($friendEmail[$i]);
        }

        $sponsorship = new RewardsSponsorshipModel($invitation['id_sponsorship']);
        $sponsorship->id_sponsor = $invitation['sponsorid'];
        $sponsorship->id_customer = substr($idTemporary, 0, 10);
        $sponsorship->firstname = $friendFirstName;
        $sponsorship->lastname = $friendLastName;
        $sponsorship->channel = 1;
        $sponsorship->email = $friendEmail;

        $vars = array(
                    '{message}' => "PRUEBA",
                    '{firstname_invited}'=> $friendFirstName,
                    '{email}' => $invitation['sponsoremail'],
                    '{username}' => $invitation['sponsorusername'],
                    '{lastname}' => $invitation['sponsorlastname'],
                    '{firstname}' => $invitation['sponsorfirstname'],
                    '{email_friend}' => $friendEmail,
                    '{Expiration}'=> $sixHour,
                    '{link}' => $sponsorship->getSponsorshipMailLink()
                );

        $prefix_template = '16-sponsorship-invitation-novoucher';

        $query_subject = 'SELECT subject_mail FROM '._DB_PREFIX_.'mail_send WHERE name_mail ="'.$prefix_template.'"';
        $row_subject = Db::getInstance()->getRow($query_subject);
        $message_subject = $row_subject['subject_mail'];
        
        $allinone_rewards = new allinone_rewards();
        $allinone_rewards->sendMail(1, $template, $allinone_rewards->getL($message_subject), $vars, $friendEmail, $friendFirstName.' '.$friendLastName);
    }

    else if ($days > 7) {
        $friendEmail = $invitation['email'];
        $friendLastName = $invitation['lastname'];
        $friendFirstName = $invitation['firstname'];
        
        $template = "invitation_cancel";

        $idTemporary = '1';
        for ($i = 0; $i < strlen($friendEmail); $i++) {
            $idTemporary .= (string) ord($friendEmail[$i]);
        }

        $sponsorship = new RewardsSponsorshipModel($invitation['id_sponsorship']);
        $sponsorship->id_sponsor = $invitation['sponsorid'];
        $sponsorship->id_customer = substr($idTemporary, 0, 10);
        $sponsorship->firstname = $friendFirstName;
        $sponsorship->lastname = $friendLastName;
        $sponsorship->channel = 1;
        $sponsorship->email = $friendEmail;
 
        $vars = array(
                    '{message}' => "PRUEBA",
                    '{firstname_invited}'=> $sponsorship->firstname,
                    '{email}' => $invitation['sponsoremail'],
                    '{username}' => $invitation['sponsorusername'],
                    '{lastname}' => $sponsorship->lastname,
                    '{firstname}' => $sponsorship->firstname,
                    '{email_friend}' => $friendEmail,
                    '{link}' => $sponsorship->getSponsorshipMailLink()
                );

        $prefix_template = '16-invitation_cancel';

        $query_subject = 'SELECT subject_mail FROM '._DB_PREFIX_.'mail_send WHERE name_mail ="'.$prefix_template.'"';
        $row_subject = Db::getInstance()->getRow($query_subject);
        $message_subject = $row_subject['subject_mail'];
        
        $allinone_rewards = new allinone_rewards();
        $allinone_rewards->sendMail(1, $template, $allinone_rewards->getL($message_subject), $vars, $friendEmail, $friendFirstName.' '.$friendLastName);

        $insertnotaccepted = "INSERT INTO "._DB_PREFIX_."rewards_sponsorship_not_accepted (sponsor_id, sponsor_username, sponsor_email, sponsor_lastname, sponsor_firstname, email, lastname, firstname, date_end, date_add)
                                VALUES (".$invitation['sponsorid'].", '".$invitation['sponsorusername']."', '".$invitation['sponsoremail']."', '".$invitation['sponsorlastname']."', '".$invitation['sponsorfirstname']."', '".$invitation['email']."', '".$invitation['lastname']."', '".$invitation['firstname']."', NOW(), '".$invitation['date_add']."')";
        Db::getInstance()->execute($insertnotaccepted);
        
        $deletemail = "DELETE FROM "._DB_PREFIX_."rewards_sponsorship WHERE id_customer = ".$invitation['id_customer'];
        Db::getInstance()->execute($deletemail);
        
        $deletemailthird = "DELETE FROM "._DB_PREFIX_."rewards_sponsorship_third WHERE email_third = '".$invitation['email']."'";
        Db::getInstance()->execute($deletemailthird);

    }

    if ( $days == 1 || $days == 5 || $days == 6 || $days == 7 ) {
        Db::getInstance()->execute("INSERT INTO "._DB_PREFIX_."notification_history(id_customer, type_message, message, date_send)
                                VALUES (".$invitation['id_customer'].",'Recordatorio invitacion', 'Recordatorio de invitacion fluz fluz pendiente por responder', NOW())");
    }
}
