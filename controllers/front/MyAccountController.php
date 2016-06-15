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
    }

    /**
     * Assign template vars related to page content
     * @see FrontController::initContent()
     */
    public function initContent()
    {
        parent::initContent();
        
        $totals = RewardsModel::getAllTotalsByCustomer((int)$this->context->customer->id);
        $totalAvailable = isset($totals[RewardsStateModel::getValidationId()]) ? (float)$totals[RewardsStateModel::getValidationId()] : 0;
        $this->context->smarty->assign('totalAvailable', $totalAvailable);
        
        $customerProfile = $this->getProfileCustomer($this->context->customer->id);
        $this->context->smarty->assign('customerProfile', $customerProfile);
        
        $datePoint = $this->getPointsLastDays($this->context->customer->id);
        $lastPoint = round($datePoint, $precision=0);
        $this->context->smarty->assign('lastPoint', $lastPoint);
        
        /*$arrayCustomer = $this->getCustomerSponsorship($this->context->customer->id);
        $this->context->smarty->assign('arrayCustomer', $arrayCustomer[0]);*/
        
        $has_address = $this->context->customer->getAddresses($this->context->language->id);
        $this->context->smarty->assign(array(
            'manufacturers'=> $this->getProductsByManufacturer($this->context->customer->id),
            'has_customer_an_address' => empty($has_address),
            'voucherAllowed' => (int)CartRule::isFeatureActive(),
            'returnAllowed' => (int)Configuration::get('PS_ORDER_RETURN')
        ));
        $this->context->smarty->assign('HOOK_CUSTOMER_ACCOUNT', Hook::exec('displayCustomerAccount'));

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
                (PO.id_customer ='.$id_customer.'))
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
    
    public function getPointsLastDays($id_customer){
        
        $query = 'SELECT sum(credits) FROM '._DB_PREFIX_.'rewards WHERE id_customer='.(int)$id_customer.' AND date_add >= curdate() + interval -30 day';
        
        $row= Db::getInstance()->getRow($query);
        $datePoint = $row['sum(credits)'];
        return $datePoint;
        
    }
    
    /*static public function getCustomerSponsorship($id_customer){
            
            $seguir = true;
            $childs = array();
            array_push($childs,$id_customer);
            $childs2=array();
            $childs3=array();
            
           while ($seguir){
               
                $query = 'SELECT id_customer FROM '._DB_PREFIX_.'rewards_sponsorship WHERE id_sponsor IN ('.implode(',', $childs).')';
                if($row1=Db::getInstance()->executeS($query)){
                    foreach ($row1 as $valor){
                        array_push($childs2,$valor['id_customer']);
                    }
                    //print_r(array_values($childs2));
                    
                    array_push($childs3,$childs);
                    
                    empty($childs);
                    
                    print_r(array_values($childs3));
                    $childs=  array_diff($childs2, $childs);
                    print_r(array_values($childs));
                    
                    empty($childs2);
                    
                }
                else{
                    $seguir=false;
                }
           }
           return $childs3[0];
        }*/
}
