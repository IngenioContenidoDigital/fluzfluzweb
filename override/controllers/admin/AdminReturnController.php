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

/**
 * @property OrderReturn $object
 */
class AdminReturnController extends AdminReturnControllerCore
{
     public function postProcess()
    {
        $this->context = Context::getContext();
        if (Tools::isSubmit('deleteorder_return_detail')) {
            if ($this->tabAccess['delete'] === '1') {
                if (($id_order_detail = (int)(Tools::getValue('id_order_detail'))) && Validate::isUnsignedId($id_order_detail)) {
                    if (($id_order_return = (int)(Tools::getValue('id_order_return'))) && Validate::isUnsignedId($id_order_return)) {
                        $orderReturn = new OrderReturn($id_order_return);
                        if (!Validate::isLoadedObject($orderReturn)) {
                            die(Tools::displayError());
                        }
                        if ((int)($orderReturn->countProduct()) > 1) {
                            if (OrderReturn::deleteOrderReturnDetail($id_order_return, $id_order_detail, (int)(Tools::getValue('id_customization', 0)))) {
                                Tools::redirectAdmin(self::$currentIndex.'&conf=4token='.$this->token);
                            } else {
                                $this->errors[] = Tools::displayError('An error occurred while deleting the details of your order return.');
                            }
                        } else {
                            $this->errors[] = Tools::displayError('You need at least one product.');
                        }
                    } else {
                        $this->errors[] = Tools::displayError('The order return is invalid.');
                    }
                } else {
                    $this->errors[] = Tools::displayError('The order return content is invalid.');
                }
            } else {
                $this->errors[] = Tools::displayError('You do not have permission to delete this.');
            }
        } elseif (Tools::isSubmit('submitAddorder_return') || Tools::isSubmit('submitAddorder_returnAndStay')) {
            if ($this->tabAccess['edit'] === '1') {
                if (($id_order_return = (int)(Tools::getValue('id_order_return'))) && Validate::isUnsignedId($id_order_return)) {
                    $orderReturn = new OrderReturn($id_order_return);
                    $order = new Order($orderReturn->id_order);
                    $customer = new Customer($orderReturn->id_customer);
                    $orderReturn->state = (int)(Tools::getValue('state'));
                    if ($orderReturn->save()) {
                        $orderReturnState = new OrderReturnState($orderReturn->state);
                        $vars = array(
                        '{username}' => $customer->username,    
                        '{lastname}' => $customer->lastname,
                        '{firstname}' => $customer->firstname,
                        '{id_order_return}' => $id_order_return,
                        '{state_order_return}' => (isset($orderReturnState->name[(int)$order->id_lang]) ? $orderReturnState->name[(int)$order->id_lang] : $orderReturnState->name[(int)Configuration::get('PS_LANG_DEFAULT')]));
                        Mail::Send((int)$order->id_lang, 'order_return_state', Mail::l('Your order return status has changed', $order->id_lang),
                            $vars, $customer->email, $customer->firstname.' '.$customer->lastname, null, null, null,
                            null, _PS_MAIL_DIR_, true, (int)$order->id_shop);

                        if (Tools::isSubmit('submitAddorder_returnAndStay')) {
                            Tools::redirectAdmin(self::$currentIndex.'&conf=4&token='.$this->token.'&updateorder_return&id_order_return='.(int)$id_order_return);
                        } else {
                            Tools::redirectAdmin(self::$currentIndex.'&conf=4&token='.$this->token);
                        }
                    }
                } else {
                    $this->errors[] = Tools::displayError('No order return ID has been specified.');
                }
            } else {
                $this->errors[] = Tools::displayError('You do not have permission to edit this.');
            }
        }
        AdminController::postProcess();
    }
}

?>
}