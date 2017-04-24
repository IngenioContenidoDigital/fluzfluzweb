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

class OrderInvoice extends OrderInvoiceCore
{
    public function getInvoiceNumberFormatted($id_lang, $id_shop = null)
    {
        $invoice_formatted_number = Hook::exec('actionInvoiceNumberFormatted', array(
            get_class($this) => $this,
            'id_lang' => (int)$id_lang,
            'id_shop' => (int)$id_shop,
            'number' => (int)$this->number
        ));

        if (!empty($invoice_formatted_number)) {
            return $invoice_formatted_number;
        }

        $format = '%1$s%2$d';

        if (Configuration::get('PS_INVOICE_USE_YEAR')) {
            $format = Configuration::get('PS_INVOICE_YEAR_POS') ? '%1$s%3$s/%2$06d' : '%1$s%2$06d/%3$s';
        }

        return sprintf($format, Configuration::get('PS_INVOICE_PREFIX', (int)$id_lang, null, (int)$id_shop), $this->number, date('Y', strtotime($this->date_add)));
    }
}

?>