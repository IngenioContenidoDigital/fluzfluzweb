<?php
/**
* 2014 PAYU LATAM
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
*  @author    PAYU LATAM <sac@payulatam.com>
*  @copyright 2014 PAYU LATAM
*  @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*/

if (!defined('_PS_VERSION_'))
	exit;

class PayuLatam extends PaymentModule {

private $_postErrors = array();

public function __construct()
{
	$this->name = 'payulatam';
	$this->tab = 'payments_gateways';
	$this->version = '3.1.4';
	$this->author = 'Electroge32';
	$this->need_instance = 0;
	$this->currencies = true;
	$this->currencies_mode = 'checkbox';
	parent::__construct();

	$this->displayName = $this->l('PayU Web Service Integration');
	$this->description = $this->l('Payment gateway for PayU');

	$this->confirmUninstall = $this->l('Are you sure you want to uninstall?');
	/* Backward compatibility */
	if (_PS_VERSION_ < '1.5')
		require(_PS_MODULE_DIR_.$this->name.'/backward_compatibility/backward.php');
        
       //Configuration::updateValue('PAYU_LATAM_PUBLIC_KEY', "");
       //Configuration::updateValue('PAYU_LATAM_API_LOGIN', "");

	$this->checkForUpdates();
}

public function install()
{
	$this->_createStates();
	require_once(_PS_MODULE_DIR_.'payulatam/config.php');
	$conf = new ConfPayu();
	$conf->addTables();

	if (!parent::install()
		|| !$this->registerHook('payment')
		|| !$this->registerHook('paymentReturn'))
		return false;
	return true;
}

public function uninstall()
{ 
	if (!parent::uninstall()
		|| !Configuration::deleteByName('PAYU_LATAM_MERCHANT_ID')
		|| !Configuration::deleteByName('PAYU_LATAM_ACCOUNT_ID')
		|| !Configuration::deleteByName('PAYU_LATAM_API_KEY')
		|| !Configuration::deleteByName('PAYU_LATAM_TEST')
		|| !Configuration::deleteByName('PAYU_OS_PENDING')
		|| !Configuration::deleteByName('PAYU_OS_FAILED')
		|| !Configuration::deleteByName('PAYU_OS_REJECTED')
                || !Configuration::deleteByName('PAYU_LATAM_PUBLIC_KEY')
                || !Configuration::deleteByName('PAYU_LATAM_API_LOGIN'))
		return false;
	return true;
}

public function getContent()
{
	$html = '';

	if (isset($_POST) && isset($_POST['submitPayU']))
	{
		$this->_postValidation();
		if (!count($this->_postErrors))
		{
			$this->_saveConfiguration();
			$html .= $this->displayConfirmation($this->l('Settings updated'));
		}
		else
			foreach ($this->_postErrors as $err)
				$html .= $this->displayError($err);
	}
	return $html.$this->_displayAdminTpl();
}

private function _displayAdminTpl()
{
	$this->context->smarty->assign(array(
		'tab' => array(
			'intro' => array(
				'title' => $this->l('How to configure'),
				'content' => $this->_displayHelpTpl(),
				'icon' => '../modules/payulatam/img/info-icon.gif',
				'tab' => 'conf',
				'selected' => (Tools::isSubmit('submitPayU') ? false : true),
				'style' => 'config_payu'
			),
			'credential' => array(
				'title' => $this->l('Credentials'),
				'content' => $this->_displayCredentialTpl(),
				'icon' => '../modules/payulatam/img/credential.png',
				'tab' => 'crendeciales',
				'selected' => (Tools::isSubmit('submitPayU') ? true : false),
				'style' => 'credentials_payu'
			),
		),
		'tracking' => 'http://www.prestashop.com/modules/pagosonline.png?url_site='.Tools::safeOutput($_SERVER['SERVER_NAME']).'&id_lang='.
		(int)$this->context->cookie->id_lang,
		'img' => '../modules/payulatam/img/',
		'css' => '../modules/payulatam/css/',
		'lang' => ($this->context->language->iso_code != 'en' || $this->context->language->iso_code != 'es' ? 'en' : $this->context->language->iso_code)
	));

	return $this->display(__FILE__, 'views/templates/admin/admin.tpl');
}

private function _displayHelpTpl()
{
	return $this->display(__FILE__, 'views/templates/admin/help.tpl');
}

private function _displayCredentialTpl()
{
	$this->context->smarty->assign(array(
		'formCredential' => './index.php?tab=AdminModules&configure=payulatam&token='.Tools::getAdminTokenLite('AdminModules').
		'&tab_module='.$this->tab.'&module_name=payulatam',
		'credentialTitle' => $this->l('Log in'),
		'credentialInputVar' => array(
			'merchant_id' => array(
				'name' => 'merchant_id',
				'required' => true,
				'value' => (Tools::getValue('merchant_id') ? Tools::safeOutput(Tools::getValue('merchant_id')) :
				Tools::safeOutput(Configuration::get('PAYU_LATAM_MERCHANT_ID'))),
				'type' => 'text',
				'label' => $this->l('Merchant Id'),
				'desc' => $this->l('You will find the Merchant ID in the section â€œTechnical Informationâ€?').'<br>'.$this->l('of the Administrative Module.'),
			),
			'api_key' => array(
				'name' => 'api_key',
				'required' => true,
				'value' => (Tools::getValue('api_key') ? Tools::safeOutput(Tools::getValue('api_key')) :
				Tools::safeOutput(Configuration::get('PAYU_LATAM_API_KEY'))),
				'type' => 'text',
				'label' => $this->l('Api Key'),
				'desc' => $this->l('You will find the API Key in the section â€œTechnical Informationâ€?').'<br>'.$this->l('of the Administrative Module.'),
			),
			'account_id' => array(
				'name' => 'account_id',
				'required' => false,
				'value' => (Tools::getValue('account_id') ? (int)Tools::getValue('account_id') : (int)Configuration::get('PAYU_LATAM_ACCOUNT_ID')),
				'type' => 'text',
				'label' => $this->l('Account ID'),
				'desc' => $this->l('You will find the Account ID in the section â€œAccountâ€?').'<br>'.$this->l('of the Administrative Module.'),
			),
                    	'api_login' => array(
				'name' => 'api_login',
				'required' => false,
				'value' => (Tools::getValue('api_login') ? (int)Tools::getValue('api_login') : Configuration::get('PAYU_LATAM_API_LOGIN')),
				'type' => 'text',
				'label' => $this->l('Api Login'),
				'desc' => $this->l('You will find the Api Login in the section â€œAccountâ€?').'<br>'.$this->l('of the Administrative Module.'),
			),
                        'public_key' => array(
				'name' => 'public_key',
				'required' => false,
				'value' => (Tools::getValue('public_key') ? (int)Tools::getValue('public_key') : Configuration::get('PAYU_LATAM_PUBLIC_KEY')),
				'type' => 'text',
				'label' => $this->l('Public Key'),
				'desc' => $this->l('You will find the Public Key in the section â€œAccountâ€?').'<br>'.$this->l('of the Administrative Module.'),
			),
			'test' => array(
				'name' => 'test',
				'required' => false,
				'value' => (Tools::getValue('test') ? Tools::safeOutput(Tools::getValue('test')) : Tools::safeOutput(Configuration::get('PAYU_LATAM_TEST'))),
				'type' => 'radio',
				'values' => array('true', 'false'),
				'label' => $this->l('Mode Test'),
				'desc' => $this->l(''),
			))));
	return $this->display(__FILE__, 'views/templates/admin/credential.tpl');
}


public function hookPayment($params)
{
	if (!$this->active)
		return;
		
	$this->context->smarty->assign(array(
		'css' => '../modules/payulatam/css/',
		'module_dir' => _PS_MODULE_DIR_.$this->name.'/'
	));

	$year = date('Y-m-j');
    $year_select='<select id="year"  class="form-control" name="year" >
                  <option value="">aÃ±o</option>';
    for($i=0; $i<=15; $i++){
        $str_year = strtotime ( '+'.$i.' year' , strtotime ( $year ) ); 
        $new_year = date( 'Y' , $str_year); 
        $year_select.='<option value="'.$new_year.'">'.$new_year.'</option>';
    }
    $year_select.='</select>';

    //$session_id=$this->context->smarty->tpl_vars['token']->value;
    $deviceSessionId = NULL;    
    $session_id = $this->context->customer->secure_key;
    $timestamp = microtime();
    setcookie(md5($timestamp), 'payu', time() + 1800);
    if ($session_id != NULL && !empty($session_id) && $session_id != 0) {
        $deviceSessionId = md5($session_id . $timestamp);
    } else {
        $deviceSessionId = md5($timestamp . $timestamp);
    }
    $this->context->cookie->__set('timestamp',$timestamp);
	$this->context->cookie->__set('deviceSessionId',$deviceSessionId);
	$this->context->smarty->assign('deviceSessionId', $deviceSessionId);

if(isset($this->context->cookie->{'error_pay'}) && !empty($this->context->cookie->{'error_pay'}) ) {
	$error_pay = json_decode($this->context->cookie->{'error_pay'},true);
	$this->context->smarty->assign('errors_pay','true');
	$this->context->smarty->assign('errors_msgs',$error_pay);
	unset($this->context->cookie->{'error_pay'});
}else{
  		$this->context->smarty->assign('errors_pay','false');
  }

    $this->context->smarty->assign('year_select',$year_select);

	return $this->display(__FILE__, 'views/templates/hook/payulatam_payment.tpl');
}

private function _postValidation()
{
	if (!Validate::isCleanHtml(Tools::getValue('merchant_id'))
		|| !Validate::isGenericName(Tools::getValue('merchant_id')))
		$this->_postErrors[] = $this->l('You must indicate the merchant id');

	if (!Validate::isCleanHtml(Tools::getValue('account_id'))
		|| !Validate::isGenericName(Tools::getValue('account_id')))
		$this->_postErrors[] = $this->l('You must indicate the account id');

	if (!Validate::isCleanHtml(Tools::getValue('api_key'))
		|| !Validate::isGenericName(Tools::getValue('api_key')))
		$this->_postErrors[] = $this->l('You must indicate the API key');

	if (!Validate::isCleanHtml(Tools::getValue('test'))
		|| !Validate::isGenericName(Tools::getValue('test')))
		$this->_postErrors[] = $this->l('You must indicate if the transaction mode is test or not');

}

private function _saveConfiguration()
{
	Configuration::updateValue('PAYU_LATAM_MERCHANT_ID', (string)Tools::getValue('merchant_id'));
	Configuration::updateValue('PAYU_LATAM_ACCOUNT_ID', (string)Tools::getValue('account_id'));
	Configuration::updateValue('PAYU_LATAM_API_KEY', (string)Tools::getValue('api_key'));
	Configuration::updateValue('PAYU_LATAM_TEST', Tools::getValue('test'));
        Configuration::updateValue('PAYU_LATAM_PUBLIC_KEY', (string)Tools::getValue('public_key'));
	Configuration::updateValue('PAYU_LATAM_API_LOGIN', (string)Tools::getValue('api_login'));
}

private function _createStates()
{
	if (!Configuration::get('PAYU_OS_PENDING'))
	{
		$order_state = new OrderState();
		$order_state->name = array();
		foreach (Language::getLanguages() as $language)
			$order_state->name[$language['id_lang']] = 'Pending';

		$order_state->send_email = false;
		$order_state->color = '#FEFF64';
		$order_state->hidden = false;
		$order_state->delivery = false;
		$order_state->logable = false;
		$order_state->invoice = false;

		if ($order_state->add())
		{
			$source = dirname(__FILE__).'/img/logo.jpg';
			$destination = dirname(__FILE__).'/../../img/os/'.(int)$order_state->id.'.gif';
			copy($source, $destination);
		}
		Configuration::updateValue('PAYU_OS_PENDING', (int)$order_state->id);
	}

	if (!Configuration::get('PAYU_OS_FAILED'))
	{
		$order_state = new OrderState();
		$order_state->name = array();
		foreach (Language::getLanguages() as $language)
			$order_state->name[$language['id_lang']] = 'Failed Payment';

		$order_state->send_email = false;
		$order_state->color = '#8F0621';
		$order_state->hidden = false;
		$order_state->delivery = false;
		$order_state->logable = false;
		$order_state->invoice = false;

		if ($order_state->add())
		{
			$source = dirname(__FILE__).'/img/logo.jpg';
			$destination = dirname(__FILE__).'/../../img/os/'.(int)$order_state->id.'.gif';
			copy($source, $destination);
		}
		Configuration::updateValue('PAYU_OS_FAILED', (int)$order_state->id);
	}

	if (!Configuration::get('PAYU_OS_REJECTED'))
	{
		$order_state = new OrderState();
		$order_state->name = array();
		foreach (Language::getLanguages() as $language)
			$order_state->name[$language['id_lang']] = 'Rejected Payment';

		$order_state->send_email = false;
		$order_state->color = '#8F0621';
		$order_state->hidden = false;
		$order_state->delivery = false;
		$order_state->logable = false;
		$order_state->invoice = false;

		if ($order_state->add())
		{
			$source = dirname(__FILE__).'/img/logo.jpg';
			$destination = dirname(__FILE__).'/../../img/os/'.(int)$order_state->id.'.gif';
			copy($source, $destination);
		}
		Configuration::updateValue('PAYU_OS_REJECTED', (int)$order_state->id);
	}
}

private function checkForUpdates()
{
	// Used by PrestaShop 1.3 & 1.4
	if (version_compare(_PS_VERSION_, '1.5', '<') && self::isInstalled($this->name))
		foreach (array('2.0') as $version)
		{
			$file = dirname(__FILE__).'/upgrade/upgrade-'.$version.'.php';
			if (Configuration::get('PAYU_LATAM') < $version && file_exists($file))
			{
				include_once($file);
				call_user_func('upgrade_module_'.str_replace('.', '_', $version), $this);
			}
		}
}

public function validationws() {
    require_once(_PS_MODULE_DIR_.'payulatam/config.php');
       
        $conf = new ConfPayu();
  
        $keysPayu= $conf->keys();
 
        $currency_iso_code='';
        if($conf->isTest()){
          $currency_iso_code='USD';
          }else{
           $currency_iso_code=$params[9]['currency'];
          }


        if (!isset($_POST['sign']) && !isset($_POST['signature']))
            Logger::AddLog('[Payulatam] the signature is missing.', 2, null, null, null, true);
        else
            $token = isset($_POST['sign']) ? $_POST['sign'] : $_POST['signature'];
        if (!isset($_POST['reference_sale']) && !isset($_POST['referenceCode']))
            Logger::AddLog('[Payulatam] the reference is missing.', 2, null, null, null, true);
        else
            $ref = isset($_POST['reference_sale']) ? $_POST['reference_sale'] : $_POST['referenceCode'];
        if (!isset($_POST['value']) && !isset($_POST['amount']))
            Logger::AddLog('[Payulatam] the amount is missing.', 2, null, null, null, true);
        else
            $amount = isset($_POST['value']) ? $_POST['value'] : $_POST['amount'];

        if (!isset($_POST['merchant_id']) && !isset($_POST['merchantId']))
            Logger::AddLog('[Payulatam] the merchantId is missing.', 2, null, null, null, true);
        else
            $merchantId = isset($_POST['merchant_id']) ? $_POST['merchant_id'] : $_POST['merchantId'];

        if (!isset($_POST['lap_state']) && !isset($_POST['state_pol']))
            Logger::AddLog('[Payulatam] the lap_state is missing.', 2, null, null, null, true);
        else
            $statePol = isset($_POST['lap_state']) ? $_POST['lap_state'] : $_POST['state_pol'];
        $idCart = explode('_', $ref)[2];
        $this->context->cart = new Cart((int) $idCart);
        $total_order = $this->context->cart->getOrderTotal();
        if (!$this->context->cart->OrderExists()) {
            Logger::AddLog('[Payulatam] The shopping card ' . (int) $idCart . ' doesn\'t have any order created', 2, null, null, null, true);
            return false;
        }
        if (Validate::isLoadedObject($this->context->cart)) {
            $id_orders = Db::getInstance()->ExecuteS('SELECT `id_order` FROM `' . _DB_PREFIX_ . 'orders` WHERE `id_cart` = ' . (int) $this->context->cart->id . '');
            foreach ($id_orders as $val) {
                $order = new Order((int) $val['id_order']);
                if ($this->context->cart->getOrderTotal() != $amount)
                    Logger::AddLog('[Payulatam] The shopping card ' . (int) $idCart . ' doesn\'t have the correct amount expected during payment validation.' . $keysPayu['apiKey'] . '~' . Tools::safeOutput($keysPayu['merchantId']) . '~payU_' . Configuration::get('PS_SHOP_NAME') . '_' . (int) $this->context->cart->id . '~' . number_format((float) $this->context->cart->getOrderTotal(), 2, '.', '') . '~' . $currency->iso_code . '~' . $statePol . "---" . $amount, 2, null, null, null, true);
                else {
                    $currency = new Currency((int) $this->context->cart->id_currency);
                    if ($token == md5($keysPayu['apiKey'] . '~' . Tools::safeOutput($keysPayu['merchantId']) . '~payU_' . Configuration::get('PS_SHOP_NAME') . '_' . (int) $this->context->cart->id.'_'.$conf->get_intentos($this->context->cart->id) . '~' . number_format((float) $total_order, 2, '.', '') . '~' . $currency_iso_code . '~' . $statePol) || $token == md5($keysPayu['apiKey'] . '~' . Tools::safeOutput($keysPayu['merchantId']) . '~payU_' . Configuration::get('PS_SHOP_NAME') . '_' . (int) $this->context->cart->id .'_'.$conf->get_intentos($this->context->cart->id). '~' . number_format((float) $total_order, 1, '.', '') . '~' . $currency_iso_code . '~' . $statePol) || $token == md5($keysPayu['apiKey'] . '~' . Tools::safeOutput($keysPayu['merchantId']) . '~payU_' . Configuration::get('PS_SHOP_NAME') . '_' . (int) $this->context->cart->id .'_'.$conf->get_intentos($this->context->cart->id). '~' . number_format((float) $total_order, 0, '.', '') . '~' . $currency_iso_code . '~' . $statePol)) { // CUANDO SE ENVIAN # ENTEROS EN EL PAGO A PAYU, ESTE RETORNA 1 DECIMAL, CUANDO SE ENVIAN DECIMALES, PAYU RETORNA 2 DECIMALES. SE VALIDA TAMBIEN SIN DECIMALES EVG GPB
                        if ($statePol == 7){
                            if($order-> getCurrentState() != (int) Configuration::get('PAYU_WAITING_PAYMENT') )
                            $order->setCurrentState((int) Configuration::get('PAYU_WAITING_PAYMENT'));
                            }
                        else if ($statePol == 4){
                            if($order-> getCurrentState() != (int) Configuration::get('PS_OS_PAYMENT') )
                            $order->setCurrentState((int) Configuration::get('PS_OS_PAYMENT'));
                            }
                        else {
                            if($order-> getCurrentState() != (int) Configuration::get('PS_OS_ERROR') )
                            $order->setCurrentState((int) Configuration::get('PS_OS_ERROR'));
                            Logger::AddLog('[PayU] (payulatam) The shopping card ' . (int) $idCart . ' has been rejected by PayU state pol=' . (int) $statePol, 2, null, null, null, true);
                        }
                    } else
                        Logger::AddLog('[PayU] The shopping card ' . (int) $idCart . ' has an incorrect token given from payU during payment validation.' . $keysPayu['apiKey'] . '~' . Tools::safeOutput($keysPayu['merchantId']) . '~payU_' . Configuration::get('PS_SHOP_NAME') . '_' . (int) $this->context->cart->id . '~' . number_format((float) $total_order, 2, '.', '') . '~' . $currency->iso_code . '~' . $statePol . "--" . number_format((float) $total_order, 1, '.', '') . "--" . $token, 2, null, null, null, true);
                }
                if (_PS_VERSION_ >= 1.5) {
                    $payment = $order->getOrderPaymentCollection();
                    if (isset($payment[0])) {
                        $payment[0]->transaction_id = pSQL("payU_".md5(Configuration::get('PS_SHOP_NAME'))."_".$idCart);
                        $payment[0]->save();
                    }
                }
            }
        } else {
            Logger::AddLog('[PayU] The shopping card ' . (int) $idCart . ' was not found during the payment validation step', 2, null, null, null, true);
        }
    }


}
?>
