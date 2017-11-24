<?php
/*
* 2007-2015 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Open Software License (OSL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/osl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*  @author PrestaShop SA <contact@prestashop.com>
*  @copyright  2007-2015 PrestaShop SA
*  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

class DiscountController extends DiscountControllerCore
{
     public function initContent()
    {
        parent::initContent();
        
        // SPONSORS
        $tree = RewardsSponsorshipModel::_getTree($this->context->customer->id);
        $members = array();
        $searchnetwork = strtolower(Tools::getValue('searchnetwork'));
        foreach ($tree as $sponsor) {
            if ( $this->context->customer->id != $sponsor['id'] ) {
                $customer = new Customer($sponsor['id']);
                $name = strtolower($customer->username);
                if ( $customer->firstname != "" ) {
                    if ( $searchnetwork != "" ) {
                        $coincidence = strpos($name, $searchnetwork);
                        if ( $coincidence !== false ) {
                            $members[$sponsor['id']]['name'] = $name;
                            $members[$sponsor['id']]['username'] = $customer->username;
                            $members[$sponsor['id']]['id'] = $sponsor['id'];
                            $members[$sponsor['id']]['dateadd'] = date_format( date_create($customer->date_add) ,"d/m/y");
                            $members[$sponsor['id']]['level'] = $sponsor['level'];
                            $imgprofile = "";
                            if ( file_exists(_PS_IMG_DIR_."profile-images/".$sponsor['id'].".png") ) {
                                $imgprofile = "/img/profile-images/".$sponsor['id'].".png";
                            }
                            $members[$sponsor['id']]['img'] = $imgprofile;
                            $points = Db::getInstance()->ExecuteS("SELECT SUM(credits) AS points
                                                                    FROM "._DB_PREFIX_."rewards
                                                                    WHERE  id_customer = ".$this->context->customer->id."
                                                                    AND plugin = 'sponsorship'
                                                                    AND id_order IN (
                                                                            SELECT id_order
                                                                            FROM "._DB_PREFIX_."rewards
                                                                            WHERE  id_customer = ".$sponsor['id']."
                                                                            AND plugin = 'loyalty'
                                                                    )
                                                                    GROUP BY id_customer");
                            $members[$sponsor['id']]['points'] = $points[0]['points'];
                            
                            $pendingsinvitation = Db::getInstance()->getValue("SELECT (2 - COUNT(*)) pendingsinvitation
                                                                        FROM "._DB_PREFIX_."rewards_sponsorship
                                                                        WHERE id_sponsor = ".$sponsor['id']);
                            $members[$sponsor['id']]['pendingsinvitation'] = $pendingsinvitation;
                        }
                    } else {
                        $members[$sponsor['id']]['name'] = $name;
                        $members[$sponsor['id']]['username'] = $customer->username;
                        $members[$sponsor['id']]['id'] = $sponsor['id'];
                        $members[$sponsor['id']]['dateadd'] = date_format( date_create($customer->date_add) ,"d/m/y");
                        $members[$sponsor['id']]['level'] = $sponsor['level'];
                        $imgprofile = "";
                        if ( file_exists(_PS_IMG_DIR_."profile-images/".$sponsor['id'].".png") ) {
                            $imgprofile = "/img/profile-images/".$sponsor['id'].".png";
                        }
                        $members[$sponsor['id']]['img'] = $imgprofile;
                        $points = Db::getInstance()->ExecuteS("SELECT SUM(credits) AS points
                                                                FROM "._DB_PREFIX_."rewards
                                                                WHERE  id_customer = ".$this->context->customer->id."
                                                                AND plugin = 'sponsorship'
                                                                AND id_order IN (
                                                                        SELECT id_order
                                                                        FROM "._DB_PREFIX_."rewards
                                                                        WHERE  id_customer = ".$sponsor['id']."
                                                                        AND plugin = 'loyalty'
                                                                )
                                                                GROUP BY id_customer");
                        $members[$sponsor['id']]['points'] = $points[0]['points'];
                        $pendingsinvitation = Db::getInstance()->getValue("SELECT (2 - COUNT(*)) pendingsinvitation
                                                                        FROM "._DB_PREFIX_."rewards_sponsorship
                                                                        WHERE id_sponsor = ".$sponsor['id']);
                        $members[$sponsor['id']]['pendingsinvitation'] = $pendingsinvitation;
                    }
                }
            }
        }
        /* ORGANIZAR POR NOMBRE */
        // asort($members);
        
        /* ORGANIZAR POR NIVEL */
        usort($members, function($a, $b) {
            return  $a['level'] - $b['level'];
        });
        
        $net = $this->getMyNetwork(1, $this->context->customer->id);
        $netview =  json_encode($net);
        
        if( file_exists(_PS_IMG_DIR_."profile-images/".$this->context->customer->id.".png") ){
            $profile['img'] = "http://".Configuration::get('PS_SHOP_DOMAIN')."/img/profile-images/".$this->context->customer->id.".png";
        }
        else {
            $profile['img'] = "false";
        }
        
        $this->context->smarty->assign('profile', $profile);
        $this->context->smarty->assign('netview', $netview);
        $this->context->smarty->assign('members', $members);
        $this->context->smarty->assign('searchnetwork', $searchnetwork);
        
        // MESSAGES
        $searchmessage = strtolower(Tools::getValue('searchmessage'));
        $messages = Db::getInstance()->ExecuteS("SELECT ms.id_customer_send, ms.id_customer_receive, ms.message, c.username, c.id_customer id
                                                FROM "._DB_PREFIX_."message_sponsor ms
                                                INNER JOIN "._DB_PREFIX_."customer c
                                                ON ( (ms.id_customer_send = c.id_customer AND c.id_customer <> ".$this->context->customer->id.") OR (ms.id_customer_receive = c.id_customer AND c.id_customer <> ".$this->context->customer->id.")  )
                                                WHERE ms.id_customer_send = ".$this->context->customer->id."
                                                OR ms.id_customer_receive = ".$this->context->customer->id."
                                                ORDER BY ms.date_send DESC");
        foreach ($messages AS $key => &$message) {
            $imgprofile = "";
            if ( file_exists(_PS_IMG_DIR_."profile-images/".$message['id'].".png") ) {
                $imgprofile = "/img/profile-images/".$message['id'].".png";
            }
            $message['img'] = $imgprofile;
            $name = strtolower($message['username']);
            if ( $searchmessage != "" ) {
                $coincidence = strpos($name, $searchmessage);
                if ( $coincidence !== false ) {
                    $message['coincidence'] = true;
                } else {
                    $message['coincidence'] = false;
                    unset($messages[$key]);
                }
            }
        }
        $this->context->smarty->assign('messages', $messages);
        $this->context->smarty->assign('searchmessage', $searchmessage);
        $this->context->smarty->assign('id_customer', $this->context->customer->id);
        $this->context->smarty->assign('autoaddnetwork', $this->context->customer->autoaddnetwork);
        
        $this->addJS(_THEME_JS_DIR_.'discount.js');
        $this->addCSS(_THEME_CSS_DIR_.'discount.css');
        $this->setTemplate(_PS_THEME_DIR_.'discount.tpl');
    }
    
    public function getMyNetwork( $id_lang, $id_customer ) {
    $tree = RewardsSponsorshipModel::_getTree( $id_customer );
    $members = array();
    $counter = 0;
    foreach ( $tree as $sponsor ) {
      if ( $id_customer != $sponsor['id'] ) {
        $customer = new Customer( $sponsor['id'] );
        $name = strtolower( $customer->firstname." ".$customer->lastname );
        if ( $customer->firstname != "" ) {
          $sql = "SELECT SUM(credits) AS points
                  FROM "._DB_PREFIX_."rewards
                  WHERE  id_customer = ".$id_customer."
                  AND plugin = 'sponsorship'
                  AND id_order IN (
                    SELECT id_order
                    FROM "._DB_PREFIX_."rewards
                    WHERE  id_customer = ".$sponsor['id']."
                    AND plugin = 'loyalty'
                  )
                  GROUP BY id_customer";
          
          $members[$counter]['name'] = $name;
          $members[$counter]['username'] = ($customer->username == null) ? 'Fluzzer' : $customer->username ;
          $members[$counter]['id'] = $sponsor['id'];
          $members[$counter]['dateadd'] = date_format( date_create( $customer->date_add ) ,"d/m/y");
          $members[$counter]['level'] = $sponsor['level'];
          if( file_exists(_PS_IMG_DIR_."profile-images/".(string)$sponsor['id'].".png") ){
            $members[$counter]['img'] = "http://".Configuration::get('PS_SHOP_DOMAIN')."/img/profile-images/".(string)$sponsor['id'].".png";
          }
          else {
            $members[$counter]['img'] = false;
          }
          $points = Db::getInstance()->ExecuteS($sql);
          $members[$counter]['points'] = round($points[0]['points']);
        }
        $counter++;
      }
    }
    
    foreach ($members as &$x){
      $sponsorship2 = Db::getInstance()->getRow('SELECT (CASE WHEN 
                                  NOT EXISTS (SELECT c.id_customer FROM ps_customer c WHERE c.id_customer = '.$x['id'].')
                                  THEN "Pendiente"
                                  ELSE "Confirmado"
                                  END) AS "status"
                                  FROM ps_rewards_sponsorship rs 
                                  WHERE rs.id_sponsor = '.$id_customer);

      $sponsorship = Db::getInstance()->getRow('SELECT rs.firstname as firstname
          FROM '._DB_PREFIX_.'rewards_sponsorship rs 
          WHERE id_customer = '.$x['id']);

      $x['status'] = $sponsorship2['status'];
    }
        
    /* ORGANIZAR POR NIVEL */
    usort($members, function($a, $b) {
        return  $a['level'] - $b['level'];
    });
    
    
    return array('result' => $members);
  }

}

?>