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

class MyAccountControllerCore extends FrontController
{
    public $auth = true;
    public $php_self = 'my-account';
    public $authRedirection = 'my-account';
    public $ssl = true;
    
    public function setMedia()
    {
        parent::setMedia();
        $this->addCSS(_THEME_CSS_DIR_.'my-account.css');
        $this->addCSS(_THEME_CSS_DIR_.'cardsview.css');
    }

    /**
     * Assign template vars related to page content
     * @see FrontController::initContent()
     */
    public function initContent()
    {
        parent::initContent();
        
        
        $totals = RewardsModel::getAllTotalsByCustomer((int)$this->context->customer->id);
        $totalAvailable = round(isset($totals[RewardsStateModel::getValidationId()]) ? (float)$totals[RewardsStateModel::getValidationId()] : 0);
        $this->context->smarty->assign('totalAvailable', $totalAvailable);
        
        $customerProfile = $this->getProfileCustomer($this->context->customer->id);
        $this->context->smarty->assign('customerProfile', $customerProfile);
        $this->context->smarty->assign('profile', $this->context->customer->id);
        
        $datePoint = $this->getPointsLastDays($this->context->customer->id);
        $lastPoint = round($datePoint, $precision=0);
        $this->context->smarty->assign('lastPoint', $lastPoint);
        $has_address = $this->context->customer->getAddresses($this->context->language->id);
        $members = $this->numberMembers();
        $this->context->smarty->assign(array(
            'manufacturers'=> $this->getProductsByManufacturer($this->context->customer->id),
            'has_customer_an_address' => empty($has_address),
            'members'=> $members,
            'voucherAllowed' => (int)CartRule::isFeatureActive(),
            'topPoint'=> $this->TopNetworkUnique(),
            'worstPoint'=> $this->WorstNetworkUnique(),
            'returnAllowed' => (int)Configuration::get('PS_ORDER_RETURN')
        ));

        $imgprofile = "";
        if ( file_exists(_PS_IMG_DIR_."profile-images/".$this->context->customer->id.".png") ) {
            $imgprofile = "/img/profile-images/".$this->context->customer->id.".png";
        }
        $this->context->smarty->assign('imgprofile',$imgprofile);

        $this->context->smarty->assign('HOOK_CUSTOMER_ACCOUNT', Hook::exec('displayCustomerAccount'));
        
        // my messaging
        $tree = RewardsSponsorshipModel::_getTree($this->context->customer->id);
        $members = array();
        foreach ($tree as $sponsor) {
            if ( $this->context->customer->id != $sponsor['id'] ) {
                $points = Db::getInstance()->ExecuteS("SELECT credits, date_add
                                                        FROM "._DB_PREFIX_."rewards
                                                        WHERE  id_customer = ".$this->context->customer->id."
                                                        AND plugin = 'sponsorship'
                                                        AND id_order IN (
                                                                SELECT id_order
                                                                FROM "._DB_PREFIX_."rewards
                                                                WHERE  id_customer = ".$sponsor['id']."
                                                                AND plugin = 'loyalty'
                                                        )
                                                        ORDER BY date_add DESC
                                                        LIMIT 1");
                if ( !empty($points) ) {
                    $customer = new Customer($sponsor['id']);
                    $name = strtolower($customer->firstname." ".$customer->lastname);
                    $members[$sponsor['id']]['name'] = $name;
                    $members[$sponsor['id']]['points'] = $points[0]['credits'];
                    $members[$sponsor['id']]['dateaddorder'] = date_format( date_create($points[0]['date_add']) ,"d/m/y");
                    $members[$sponsor['id']]['dateaddorderunformat'] = $points[0]['date_add'];
                    $imgprofile = "";
                    if ( file_exists(_PS_IMG_DIR_."profile-images/".$sponsor['id'].".png") ) {
                        $imgprofile = "/img/profile-images/".$sponsor['id'].".png";
                    }
                    $members[$sponsor['id']]['img'] = $imgprofile;
                }
            }
        }
        /* ORGANIZAR POR FECHA ORDEN */
        usort($members, function($a, $b) {
            return strtotime($b['dateaddorderunformat']) - strtotime($a['dateaddorderunformat']);
        });
        $this->context->smarty->assign('members', array_slice($members, 0, 5));

        $this->setTemplate(_PS_THEME_DIR_.'my-account.tpl');
    }
    
    public function getProductsByManufacturer($id_customer){
        
        $query='SELECT
                PM.id_manufacturer AS id_manufacturer,
                PM.`name` AS manufacturer_name,
                Count(OD.product_id) AS products,
                Sum(PP.price) AS total
                FROM
                ps_orders AS PO
                INNER JOIN ps_order_state_lang AS OSL ON PO.current_state = OSL.id_order_state
                INNER JOIN ps_order_detail AS OD ON PO.id_order = OD.id_order
                INNER JOIN ps_product AS PP ON OD.product_id = PP.id_product
                INNER JOIN ps_supplier AS PS ON PS.id_supplier = PP.id_supplier
                INNER JOIN ps_manufacturer AS PM ON PP.id_manufacturer = PM.id_manufacturer
                WHERE
                ((OSL.id_order_state = 2 OR
                OSL.id_order_state = 5) AND
                (PO.id_customer ='.$id_customer.') AND (PP.reference<>"MFLUZ") AND (OSL.id_lang='.$this->context->language->id.'))
                GROUP BY
                 id_manufacturer,
                manufacturer_name
                ORDER BY
                manufacturer_name ASC';
        
        $supplier = Db::getInstance()->executeS($query);
        return $supplier;
    }
    
    public function getProfileCustomer($id_customer){
        
        
        $query = 'SELECT firstname FROM '._DB_PREFIX_.'customer WHERE id_customer='.(int)$id_customer;
        
        $row= Db::getInstance()->getRow($query);
        $name = $row['firstname'];
        return $name;
    }
    
    public function getPointsLastDays(){
     
        $tree = RewardsSponsorshipModel::_getTree($this->context->customer->id);
            $sum=0;
            foreach ($tree as $valor){
                $queryTop = 'SELECT SUM(n.credits) AS points
                             FROM '._DB_PREFIX_.'rewards n 
                             LEFT JOIN '._DB_PREFIX_.'customer c ON (c.id_customer = n.id_customer) 
                             LEFT JOIN '._DB_PREFIX_.'order_detail s ON (s.id_order = n.id_order) WHERE n.id_customer='.$valor['id'].' AND s.product_reference != "MFLUZ" AND n.date_add >= curdate() + interval -30 day'.' AND n.id_reward_state = 2 AND '.$valor['level'].'!=1';
                
                $result = Db::getInstance()->executeS($queryTop);
                
                if ($result[0]['points'] != "" ) {
                    $top[] = $result[0];
                }
                
            }
            
            usort($top, function($a, $b) {
                return $b['points'] - $a['points'];
            });
            
            foreach ($top as $x){
                $sum += $x['points'];
            }
            
            return $sum;    
            
        }
        
    public function getCustomerSponsorship($id_customer){
            
            $childs = array();
            array_push($childs,$id_customer);
            $childs2=array();
          
            $query =Db::getInstance()->executeS('SELECT id_customer FROM '._DB_PREFIX_.'rewards_sponsorship WHERE id_sponsor IN ('.implode(',', $childs).')');
                if($query != ""){
                    foreach ($query as $valor){
                        array_push($childs2,$valor['id_customer']);
                    }
                    
                }
             $childs=array_merge($childs, $childs2);   
             
           return $query;
        }
    
    public function numberMembers(){
        
        $tree = RewardsSponsorshipModel::_getTree($this->context->customer->id);
        $memberCount = 0;
            foreach ($tree as $valor){
                $query = 'SELECT COUNT(id_customer) as members FROM '._DB_PREFIX_.'rewards WHERE id_customer='.$valor['id'];
                $sqlmember = Db::getInstance()->getRow($query);
                $members = $sqlmember['members'];
                $memberCount++;
            }
            
         return $memberCount;
    }    
        
    public function TopNetworkUnique() {
            $tree = RewardsSponsorshipModel::_getTree($this->context->customer->id);
            
            foreach ($tree as $valor){
                $queryTop = 'SELECT c.username AS username, s.product_reference AS reference, c.firstname AS name, c.lastname AS lastname, SUM(n.credits) AS points
                            FROM '._DB_PREFIX_.'rewards n 
                            LEFT JOIN '._DB_PREFIX_.'customer c ON (c.id_customer = n.id_customer) 
                            LEFT JOIN '._DB_PREFIX_.'order_detail s ON (s.id_order = n.id_order) WHERE n.id_customer='.$valor['id'].' AND s.product_reference != "MFLUZ" AND '.$valor['level'].'!=1';
                $result = Db::getInstance()->executeS($queryTop);
                
                if ($result[0]['points'] != "" ) {
                    $top[] = $result[0];
                }                
            }
            usort($top, function($a, $b) {
                return $b['points'] - $a['points'];
            });
            
            return array_slice($top, 0, 1);    
            
        }
        
        public function WorstNetworkUnique() {
            $tree = RewardsSponsorshipModel::_getTree($this->context->customer->id);
            foreach ($tree as $valor){
                $queryTop = 'SELECT c.username AS username, s.product_reference AS reference, c.firstname AS name, c.lastname AS lastname, SUM(n.credits) AS points
                            FROM '._DB_PREFIX_.'rewards n 
                            LEFT JOIN '._DB_PREFIX_.'customer c ON (c.id_customer = n.id_customer) 
                            LEFT JOIN '._DB_PREFIX_.'order_detail s ON (s.id_order = n.id_order) WHERE n.id_customer='.$valor['id'].' AND s.product_reference != "MFLUZ" AND '.$valor['level'].'!=1';
                $result = Db::getInstance()->executeS($queryTop);
                
                if ($result[0]['points'] != "" ) {
                    $top[] = $result[0];
                }                
            }
            usort($top, function($a, $b) {
                return $a['points'] - $b['points'];
            });
            
            return array_slice($top, 0, 1);    
            
        }
        
}
