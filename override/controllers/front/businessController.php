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

include_once('./config/defines.inc.php');
include_once('./config/config.inc.php');
include_once(_PS_MODULE_DIR_.'allinone_rewards/allinone_rewards.php');
include_once(_PS_MODULE_DIR_ . 'allinone_rewards/models/RewardsModel.php');

class businessController extends FrontController
{
    public $auth = true;
    public $php_self = 'business';
    public $authRedirection = 'business';
    public $ssl = true;
      
    
    public function setMedia(){
       parent::setMedia();
       $this->addCSS(_THEME_CSS_DIR_.'business.css');
    }
    
    public function initContent()
    {
        parent::initContent();
        
        $tree = RewardsSponsorshipModel::_getTree($this->context->customer->id);
        
        $totals = RewardsModel::getAllTotalsByCustomer((int)$this->context->customer->id);
        $pointsAvailable = round(isset($totals[RewardsStateModel::getValidationId()]) ? (float)$totals[RewardsStateModel::getValidationId()] : 0);
        $this->context->smarty->assign('pointsAvailable', $pointsAvailable);
        
        $distribute_fluz = floor($pointsAvailable / (count($tree)-1));
        $this->context->smarty->assign('all_fluz', $distribute_fluz);
        
        foreach ($tree as &$network){
            $sql = 'SELECT id_customer, firstname, lastname, username, email, dni FROM '._DB_PREFIX_.'customer 
                    WHERE id_customer='.$network['id'];
            $row_sql = Db::getInstance()->getRow($sql);
            
            $network['id_customer'] = $row_sql['id_customer'];
            $network['firstname'] = $row_sql['firstname'];
            $network['lastname'] = $row_sql['lastname'];
            $network['email'] = $row_sql['email'];
            $network['dni'] = $row_sql['dni'];
            $network['username'] = $row_sql['username'];
        }
        
        $this->context->smarty->assign('network',$tree);
        
        $this->setTemplate(_PS_THEME_DIR_.'business.tpl');
       
    }
    
    public function postProcess() {
        switch ( Tools::getValue('action') ) {
            case 'allFLuz':
                
                break;
            default:
                break;
        }
    }
}
?>