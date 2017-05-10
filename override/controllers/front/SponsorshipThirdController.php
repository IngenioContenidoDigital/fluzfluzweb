<?php
error_reporting(0);

class SponsorshipThirdController extends FrontController {
    public function initContent() {
        parent::initContent();

        $error = "";
        $invitation_sent = false;
        
        if ( Tools::isSubmit('submitSponsorFriendsThird') ) {
            $friendFirstNameThird = Tools::getValue('friendsFirstNameThird');
            $friendLastNameThird = Tools::getValue('friendsLastNameThird');
            $friendEmailThird = Tools::getValue('friendsEmailThird');

            if (empty($friendFirstNameThird) || empty($friendLastNameThird) || !Validate::isName($friendFirstNameThird) || !Validate::isName($friendLastNameThird)) {
                $error = 'name invalid';
            } elseif (Tools::isSubmit('submitSponsorFriendsThird') && !Validate::isEmail($friendEmailThird) ) {
                $error = 'email invalid';
            } elseif (RewardsSponsorshipModel::isEmailExists($friendEmailThird) || Customer::customerExists($friendEmailThird)) {
                $customerKickOut = Db::getInstance()->getValue("SELECT kick_out FROM "._DB_PREFIX_."customer WHERE email = '".$friendEmailThird."'");
                if ( $customerKickOut == 0 ) {
                    $error = 'email exists';
                    $mails_exists[] = $friendEmailThird;
                }
            }

            if ( $error == "" ) {
                $sponsor = Db::getInstance()->getRow("SELECT c.id_customer, c.username, c.firstname, c.lastname, c.email, (2-COUNT(rs.id_sponsorship) ) sponsoships
                                                        FROM "._DB_PREFIX_."customer c
                                                        LEFT JOIN "._DB_PREFIX_."rewards_sponsorship rs ON ( c.id_customer = rs.id_sponsor )
                                                        WHERE c.id_customer = ".Tools::getValue("user"));

                if ( !empty($sponsor) ) {
                    $sponsorship = new RewardsSponsorshipModel();
                    $sponsorship->id_sponsor = $sponsor['id_customer'];
                    $sponsorship->id_customer = $this->generateIdTemporary($friendEmailThird);
                    $sponsorship->firstname = $friendFirstNameThird;
                    $sponsorship->lastname = $friendLastNameThird;
                    $sponsorship->email = $friendEmailThird;
                    $sponsorship->channel = 1;
                    $send = "";
                    if ($sponsorship->save()) {
                        $vars = array(
                            '{message}' => Tools::nl2br(Tools::getValue('message')),
                            '{email}' => $sponsor['id_customer'],
                            '{firstname_invited}'=> $sponsorship->firstname,
                            '{inviter_username}' => $sponsor['username'],
                            '{username}' => $sponsor['username'],
                            '{lastname}' => $sponsor['lastname'],
                            '{firstname}' => $sponsor['firstname'],
                            '{email_friend}' => $sponsorship->email,
                            '{Expiration}'=> $send,
                            '{link}' => $sponsorship->getSponsorshipMailLink()
                        );
                        
                        $template = 'sponsorship-invitation-novoucher';
                        $allinone_rewards = new allinone_rewards();
                        $allinone_rewards->sendMail((int)$this->context->language->id, $template, 'Invitacion de su amigo', $vars, $sponsorship->email, $sponsorship->firstname.' '.$sponsorship->lastname);
                        /*Db::getInstance()->execute("INSERT INTO "._DB_PREFIX_."rewards_sponsorship_third(id_customer,id_rewards_sponsorship,email_third,date_add)
                                                    VALUES (".$this->context->customer->id.",".$sponsorship->id.",'".$sponsorship->email."',NOW())");*/
                        $invitation_sent = true;
                    }
                } else {
                    $error = 'no sponsor';
                }
            }
        }

        $smarty_values = array(
            'user' => Tools::getValue("user"),
            'error' => $error,
            'invitation_sent' => $invitation_sent
        );
        $this->context->smarty->assign($smarty_values);
        
        $this->setTemplate(_PS_THEME_DIR_.'sponsorship_third.tpl');
    }
    
    public function generateIdTemporary($email) {
        $idTemporary = '1';
        for ($i = 0; $i < strlen($email); $i++) {
            $idTemporary .= (string) ord($email[$i]);
        }
        return substr($idTemporary, 0, 10);
    }
}
