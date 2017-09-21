{*
* 2007-2015 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in not the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
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
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}
{capture name=path}
    {if !isset($email_create)}{l s='Sign In'}{else}
        <a href="{$link->getPageLink('authentication', true)|escape:'html':'UTF-8'}" rel="nofollow" title="{l s='Authentication'}">{l s='Authentication'}</a>
        <span class="navigation-pipe">{$navigationPipe}</span>{l s='Create your account'}
    {/if}
{/capture}
<h1 class="page-heading pag">{if !isset($email_create)}{l s='Sign In'}{else}{l s='Create an account'}{/if}</h1>
{if isset($back) && preg_match("/^http/", $back)}{assign var='current_step' value='login'}{include file="$tpl_dir./order-steps.tpl"}{/if}
{include file="$tpl_dir./errors.tpl"}
{assign var='stateExist' value=false}
{assign var="postCodeExist" value=false}
{assign var="dniExist" value=false}
{if !isset($email_create)}
	{*if isset($authentification_error)}
            <div class="alert alert-danger">
                {if {$authentification_error|@count} == -1}
                    <p>{l s='There\'s at least one error'} :</p>
                {else}
                    <p>{l s='There are %s errors' sprintf=[$account_error|@count]} :</p>
                {/if}
                <ol>
                    {foreach from=$authentification_error item=v}
                        <li>{$v}</li>
                    {/foreach}
                </ol>
            </div>
	{/if*}
        <div class="row banner-container">
            <div class="col-xs-12 col-sm-12 signup-account">
                <img src="{$img_dir}login/banner.jpg" id="banner_login" />
                <a href="#video-signup" class="myfancybox">
                    <h2 id="learn_more">APRENDE M&Aacute;S</h2>
                    <img src="{$img_dir}login/play.png" id="icon_play" />
                </a>
            </div>
        </div>
        <div class="video-fancy" style="display:none;">
            <div id="video-signup" class="videoWrapper">
                <iframe width="560" height="315" src="https://www.youtube.com/embed/VkPDA0YDMZQ" frameborder="0" allowfullscreen></iframe>
            </div>
        </div>
	<div class="row sign-account">
            <div class="col-xs-12 col-sm-6 signup-account">
                <div class="info-box">
                    <form action="{$link->getPageLink('authentication', true)|escape:'html':'UTF-8'}" method="post" id="login_form" class="box">
                        <h3 class="page-subheading borde-inf">{l s='Already registered?'}</h3>
                        <div class="line-separator"></div>
                        <div class="form_content clearfix">
                            <div class="form-group">
                                <label for="email">{l s='Email address'}</label>
                                <input class="is_required validate account_input form-control" data-validate="isEmail" type="email" id="email" name="email" value="{if isset($smarty.post.email)}{$smarty.post.email|stripslashes}{/if}" />
                            </div>
                            <div class="form-group">
                                <label for="passwd">{l s='Password'}</label>
                                <input class="is_required validate account_input form-control" type="password" data-validate="isPasswd" id="passwd" name="passwd" value="" />
                            </div>
                            <p class="lost_password form-group"><a href="{$link->getPageLink('password')|escape:'html':'UTF-8'}" title="{l s='Recover your forgotten password'}" rel="nofollow">{l s='Forgot your password?'}</a></p>
                            <div class='row'>
                            <div class="col-lg-7" style='padding-left:0px;'>    
                                <p class="submit col-lg-6 col-sm-6 col-md-6 col-xs-6" style='padding-left:0px;'>
                                    {if isset($back)}<input type="hidden" class="hidden" name="back" value="{$back|escape:'html':'UTF-8'}" />{/if}
                                    <button type="submit" id="SubmitLogin" name="SubmitLogin" class="button btn btn-default button-medium">
                                        <span>
                                            <i class="icon-lock left"></i>
                                            {l s='Log In'}
                                        </span>
                                    </button>
                                </p>
                                <p class="submit col-lg-6 col-sm-6 col-md-6 col-xs-6" style='text-align:right;padding-left:0px;'>
                                    <button class="button btn btn-default button-medium-business">
                                        <span>
                                            <i class="icon-briefcase left" style='font-size: 20px;'></i>
                                            <a href="{$link->getPageLink('authentication?back=business', true)|escape:'html':'UTF-8'}">
                                                {l s='Empresas'}
                                            </a>    
                                        </span>
                                    </button>
                                </p>
                            </div>
                            <!--<div class="col-lg-6" style="padding-right:0px; float: right;">
                                <p class="submit col-lg-12" style='text-align:right;'>
                                    <button class="button btn btn-default button-medium-business">
                                        <span>
                                            <i class="icon-briefcase left" style='font-size: 20px;'></i>
                                            <a href="{$link->getPageLink('authentication?back=business', true)|escape:'html':'UTF-8'}">
                                                {l s='Empresas'}
                                            </a>    
                                        </span>
                                    </button>
                                </p>
                            </div>-->
                            </div>
                            <div class='row'>
                                <h4 class="page-subheading borde-inf">Iniciar Sesi&oacute;n Con:</h4>
                                <div id="oneall_social_login"></div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <div class="col-xs-12 col-sm-6 signup-account">
                <div class="info-box">
                    <form action="{$link->getPageLink('authentication', true)|escape:'html':'UTF-8'}" method="post" id="login_form" class="box">
                        <h3 class="page-subheading borde-inf">{l s='Not Registered?'}</h3>
                        <div class="line-separator"></div>
                        <!--<p>Para realizar el registro, debes ser invitado por un miembro actual de Fluz Fluz. Una vez haz sido invitado por un Fluzzer, recibir&aacute;s un correo de confirmaci&oacute;n con instrucciones detalladas para finalizar el proceso de registro y maximizar los beneficios de Fluz Fluz. &iquest;No conoces a ning&uacute;n fluzzer activo y quieres ser parte de Fluz Fluz para construir tu red? Escr&iacute;benos un correo a info@fluzfluz.com y haremos todo lo posible para ayudarte.</p>-->
                        <p>Descarga aqu&iacute; nuestras aplicaciones</p>
                        <p><a href="https://play.google.com/store/apps/details?id=com.ionicframework.fluzfluz141172"><img src="{$img_dir}login/GooglePlay.png" id="google_play" /></a></p>
                        <p>
                            <a href="http://info.fluzfluz.co/miembros/" class="learn-buy">{l s="Aprende como comprar bonos"}</a>
                            <i class="icon icon-chevron-right" style="color:#EF4136;"></i>
                        </p>
                        <br><br><br>
                        <h4 class="page-subheading borde-inf">Registrarse Con:</h4>
                        <div id="oneall_social_login"></div>
                    </form>
                </div>
            </div>
	</div>
        <div class="row container_tips">
            <div class="col-xs-12 col-sm-4 col-md-4">
                <img src="{$img_dir}login/piggy-bank.jpg" class="icon_tip" />
                <h3>Gana!</h3>
                <span>Entre m&aacute;s compras, m&aacute;s gan&aacute;s y aumenta tus ingresos.</span>
            </div>
            <div class="col-xs-12 col-sm-4 col-md-4">
                <img src="{$img_dir}login/email.jpg" class="icon_tip" />
                <h3>Invita Amigos!</h3>
                <span>Mientras m&aacute;s amigos invitas, m&aacute;s Fluz ganas por las compras de ellos.</span>
            </div>
            <div class="col-xs-12 col-sm-4 col-md-4">
                <img src="{$img_dir}login/coins.jpg" class="icon_tip" />
                <h3>Redime!</h3>
                <span>Convierte tus Fluz en dinero en efectivo!</span>
            </div>
            <!--<div class="col-xs-12 col-sm-3">
                <img src="{$img_dir}login/pie-chart.jpg" class="icon_tip" />
                <h3>Estad&iacute;sticas!</h3>
                <span>Revisa tus estad&iacute;sticas para mejorar la obtenci&oacute;n de Fluz.</span>
            </div>-->
        </div>
	{if isset($inOrderProcess) && $inOrderProcess && $PS_GUEST_CHECKOUT_ENABLED}
		<form action="{$link->getPageLink('authentication', true, NULL, "back=$back")|escape:'html':'UTF-8'}" method="post" id="new_account_form" class="std clearfix">
			<div class="box">
				<div id="opc_account_form" style="display: block; ">
					<h3 class="page-heading bottom-indent">{l s='Instant checkout'}</h3>
					<p class="required"><sup>*</sup>{l s='Required field'}</p>
					<!-- Account -->
					<div class="required form-group">
						<label for="guest_email">{l s='Email address'} <sup>*</sup></label>
						<input type="text" class="is_required validate form-control" data-validate="isEmail" id="guest_email" name="guest_email" value="{if isset($smarty.post.guest_email)}{$smarty.post.guest_email}{/if}" />
					</div>
					<div class="cleafix gender-line">
						<label>{l s='Gender'}</label>
						{foreach from=$genders key=k item=gender}
							<div class="radio-inline">
								<label for="id_gender{$gender->id}" class="top">
									<input type="radio" name="id_gender" id="id_gender{$gender->id}" value="{$gender->id}"{if isset($smarty.post.id_gender) && $smarty.post.id_gender == $gender->id} checked="checked"{/if} />
									{$gender->name}
								</label>
							</div>
						{/foreach}
					</div>
                                        <div class="required form-group">
						<label for="username">{l s='Username'} <sup>*</sup></label>
						<input type="text" class="is_required validate form-control" data-validate="isName" id="username" name="username" value="{if isset($smarty.post.username)}{$smarty.post.username}{/if}" />
					</div>
					<div class="required form-group">
						<label for="firstname">{l s='First name'} <sup>*</sup></label>
						<input type="text" class="is_required validate form-control" data-validate="isName" id="firstname" name="firstname" value="{if isset($smarty.post.firstname)}{$smarty.post.firstname}{/if}" />
					</div>
					<div class="required form-group">
						<label for="lastname">{l s='Last name'} <sup>*</sup></label>
						<input type="text" class="is_required validate form-control" data-validate="isName" id="lastname" name="lastname" value="{if isset($smarty.post.lastname)}{$smarty.post.lastname}{/if}" />
					</div>
					<div class="form-group date-select">
						<label>{l s='Date of Birth'}</label>
						<div class="row">
							<div class="col-xs-4">
								<select id="days" name="days" class="form-control">
									<option value="">-</option>
									{foreach from=$days item=day}
										<option value="{$day}" {if ($sl_day == $day)} selected="selected"{/if}>{$day}&nbsp;&nbsp;</option>
									{/foreach}
								</select>
								{*
									{l s='January'}
									{l s='February'}
									{l s='March'}
									{l s='April'}
									{l s='May'}
									{l s='June'}
									{l s='July'}
									{l s='August'}
									{l s='September'}
									{l s='October'}
									{l s='November'}
									{l s='December'}
								*}
							</div>
							<div class="col-xs-4">
								<select id="months" name="months" class="form-control">
									<option value="">-</option>
									{foreach from=$months key=k item=month}
										<option value="{$k}" {if ($sl_month == $k)} selected="selected"{/if}>{l s=$month}&nbsp;</option>
									{/foreach}
								</select>
							</div>
							<div class="col-xs-4">
								<select id="years" name="years" class="form-control">
									<option value="">-</option>
									{foreach from=$years item=year}
										<option value="{$year}" {if ($sl_year == $year)} selected="selected"{/if}>{$year}&nbsp;&nbsp;</option>
									{/foreach}
								</select>
							</div>
						</div>
					</div>
					{if isset($newsletter) && $newsletter}
						<div class="checkbox">
							<label for="newsletter">
							<input type="checkbox" name="newsletter" id="newsletter" value="1" {if isset($smarty.post.newsletter) && $smarty.post.newsletter == '1'}checked="checked"{/if} />
							{l s='Sign up for our newsletter!'}</label>
						</div>
					{/if}
					{if isset($optin) && $optin}
						<div class="checkbox">
							<label for="optin">
							<input type="checkbox" name="optin" id="optin" value="1" {if isset($smarty.post.optin) && $smarty.post.optin == '1'}checked="checked"{/if} />
							{l s='Receive special offers from our partners!'}</label>
						</div>
					{/if}
					<h3 class="page-heading bottom-indent top-indent">{l s='Delivery address'}</h3>
					{foreach from=$dlv_all_fields item=field_name}
						{if $field_name eq "company"}
							<div class="form-group">
								<label for="company">{l s='Company'}{if in_array($field_name, $required_fields)} <sup>*</sup>{/if}</label>
								<input type="text" class="form-control" id="company" name="company" value="{if isset($smarty.post.company)}{$smarty.post.company}{/if}" />
							</div>
						{elseif $field_name eq "vat_number"}
							<div id="vat_number" style="display:none;">
								<div class="form-group">
									<label for="vat-number">{l s='VAT number'}{if in_array($field_name, $required_fields)} <sup>*</sup>{/if}</label>
									<input id="vat-number" type="text" class="form-control" name="vat_number" value="{if isset($smarty.post.vat_number)}{$smarty.post.vat_number}{/if}" />
								</div>
							</div>
							{elseif $field_name eq "dni"}
							{assign var='dniExist' value=true}
							<div class="required dni form-group">
								<label for="dni">{l s='Identification number'} <sup>*</sup></label>
								<input type="text" name="dni" id="dni" value="{if isset($smarty.post.dni)}{$smarty.post.dni}{/if}" />
								<span class="form_info">{l s='DNI / NIF / NIE'}</span>
							</div>
						{elseif $field_name eq "address1"}
							<div class="required form-group">
								<label for="address1">{l s='Address'} <sup>*</sup></label>
								<input type="text" class="form-control" name="address1" id="address1" value="{if isset($smarty.post.address1)}{$smarty.post.address1}{/if}" />
							</div>
						{elseif $field_name eq "address2"}
							<div class="form-group is_customer_param">
								<label for="address2">{l s='Address (Line 2)'}{if in_array($field_name, $required_fields)} <sup>*</sup>{/if}</label>
								<input type="text" class="form-control" name="address2" id="address2" value="{if isset($smarty.post.address2)}{$smarty.post.address2}{/if}" />
							</div>
						{elseif $field_name eq "postcode"}
							{assign var='postCodeExist' value=true}
							<div class="required postcode form-group">
								<label for="postcode">{l s='Zip/Postal Code'} <sup>*</sup></label>
								<input type="text" class="validate form-control" name="postcode" id="postcode" data-validate="isPostCode" value="{if isset($smarty.post.postcode)}{$smarty.post.postcode}{/if}"/>
							</div>
						{elseif $field_name eq "city"}
							<div class="required form-group">
								<label for="city">{l s='City'} <sup>*</sup></label>
								<input type="text" class="form-control" name="city" id="city" value="{if isset($smarty.post.city)}{$smarty.post.city}{/if}" />
							</div>
							<!-- if customer hasn't update his layout address, country has to be verified but it's deprecated -->
						{elseif $field_name eq "Country:name" || $field_name eq "country"}
							<div class="required select form-group">
								<label for="id_country">{l s='Country'} <sup>*</sup></label>
								<select name="id_country" id="id_country" class="form-control">
									{foreach from=$countries item=v}
										<option value="{$v.id_country}"{if (isset($smarty.post.id_country) AND  $smarty.post.id_country == $v.id_country) OR (!isset($smarty.post.id_country) && $sl_country == $v.id_country)} selected="selected"{/if}>{$v.name}</option>
									{/foreach}
								</select>
							</div>
						{elseif $field_name eq "State:name"}
							{assign var='stateExist' value=true}
							<div class="required id_state select form-group">
								<label for="id_state">{l s='State'} <sup>*</sup></label>
								<select name="id_state" id="id_state" class="form-control">
									<option value="">-</option>
								</select>
							</div>
						{/if}
					{/foreach}
					{if $stateExist eq false}
						<div class="required id_state select unvisible form-group">
							<label for="id_state">{l s='State'} <sup>*</sup></label>
							<select name="id_state" id="id_state" class="form-control">
								<option value="">-</option>
							</select>
						</div>
					{/if}
					{if $postCodeExist eq false}
						<div class="required postcode unvisible form-group">
							<label for="postcode">{l s='Zip/Postal Code'} <sup>*</sup></label>
							<input type="text" class="validate form-control" name="postcode" id="postcode" data-validate="isPostCode" value="{if isset($smarty.post.postcode)}{$smarty.post.postcode}{/if}"/>
						</div>
					{/if}
					{if $dniExist eq false}
						<div class="required form-group dni">
							<label for="dni">{l s='Identification number'} <sup>*</sup></label>
							<input type="text" class="text form-control" name="dni" id="dni" value="{if isset($smarty.post.dni) && $smarty.post.dni}{$smarty.post.dni}{/if}" />
							<span class="form_info">{l s='DNI / NIF / NIE'}</span>
						</div>
					{/if}
					<div class="{if isset($one_phone_at_least) && $one_phone_at_least}required {/if}form-group">
						<label for="phone_mobile">{l s='Mobile phone'}{if isset($one_phone_at_least) && $one_phone_at_least} <sup>*</sup>{/if}</label>
						<input type="text" class="form-control" name="phone_mobile" id="phone_mobile" value="{if isset($smarty.post.phone_mobile)}{$smarty.post.phone_mobile}{/if}" />
					</div>
					<input type="hidden" name="alias" id="alias" value="{l s='My address'}" />
					<input type="hidden" name="is_new_customer" id="is_new_customer" value="0" />
					<div class="checkbox">
						<label for="invoice_address">
						<input type="checkbox" name="invoice_address" id="invoice_address"{if (isset($smarty.post.invoice_address) && $smarty.post.invoice_address) || (isset($smarty.post.invoice_address) && $smarty.post.invoice_address)} checked="checked"{/if} autocomplete="off"/>
						{l s='Please use another address for invoice'}</label>
					</div>
					<div id="opc_invoice_address"  class="unvisible">
						{assign var=stateExist value=false}
						{assign var=postCodeExist value=false}
						{assign var=dniExist value=false}
						<h3 class="page-subheading top-indent">{l s='Invoice address'}</h3>
						{foreach from=$inv_all_fields item=field_name}
						{if $field_name eq "company"}
						<div class="form-group">
							<label for="company_invoice">{l s='Company'}{if in_array($field_name, $required_fields)} <sup>*</sup>{/if}</label>
							<input type="text" class="text form-control" id="company_invoice" name="company_invoice" value="{if isset($smarty.post.company_invoice) && $smarty.post.company_invoice}{$smarty.post.company_invoice}{/if}" />
						</div>
						{elseif $field_name eq "vat_number"}
						<div id="vat_number_block_invoice" style="display:none;">
							<div class="form-group">
								<label for="vat_number_invoice">{l s='VAT number'}{if in_array($field_name, $required_fields)} <sup>*</sup>{/if}</label>
								<input type="text" class="form-control" id="vat_number_invoice" name="vat_number_invoice" value="{if isset($smarty.post.vat_number_invoice) && $smarty.post.vat_number_invoice}{$smarty.post.vat_number_invoice}{/if}" />
							</div>
						</div>
						{elseif $field_name eq "dni"}
						{assign var=dniExist value=true}
						<div class="required form-group dni_invoice">
							<label for="dni_invoice">{l s='Identification number'} <sup>*</sup></label>
							<input type="text" class="text form-control" name="dni_invoice" id="dni_invoice" value="{if isset($smarty.post.dni_invoice) && $smarty.post.dni_invoice}{$smarty.post.dni_invoice}{/if}" />
							<span class="form_info">{l s='DNI / NIF / NIE'}</span>
						</div>
						{elseif $field_name eq "firstname"}
						<div class="required form-group">
							<label for="firstname_invoice">{l s='First name'} <sup>*</sup></label>
							<input type="text" class="form-control" id="firstname_invoice" name="firstname_invoice" value="{if isset($smarty.post.firstname_invoice) && $smarty.post.firstname_invoice}{$smarty.post.firstname_invoice}{/if}" />
						</div>
						{elseif $field_name eq "lastname"}
						<div class="required form-group">
							<label for="lastname_invoice">{l s='Last name'} <sup>*</sup></label>
							<input type="text" class="form-control" id="lastname_invoice" name="lastname_invoice" value="{if isset($smarty.post.lastname_invoice) && $smarty.post.lastname_invoice}{$smarty.post.lastname_invoice}{/if}" />
						</div>
						{elseif $field_name eq "address1"}
						<div class="required form-group">
							<label for="address1_invoice">{l s='Address'} <sup>*</sup></label>
							<input type="text" class="form-control" name="address1_invoice" id="address1_invoice" value="{if isset($smarty.post.address1_invoice) && $smarty.post.address1_invoice}{$smarty.post.address1_invoice}{/if}" />
						</div>
						{elseif $field_name eq "address2"}
						<div class="form-group is_customer_param">
							<label for="address2_invoice">{l s='Address (Line 2)'}{if in_array($field_name, $required_fields)} <sup>*</sup>{/if}</label>
							<input type="text" class="form-control" name="address2_invoice" id="address2_invoice" value="{if isset($smarty.post.address2_invoice) && $smarty.post.address2_invoice}{$smarty.post.address2_invoice}{/if}" />
						</div>
						{elseif $field_name eq "postcode"}
						{$postCodeExist = true}
						<div class="required postcode_invoice form-group">
							<label for="postcode_invoice">{l s='Zip/Postal Code'} <sup>*</sup></label>
							<input type="text" class="validate form-control" name="postcode_invoice" id="postcode_invoice" data-validate="isPostCode" value="{if isset($smarty.post.postcode_invoice) && $smarty.post.postcode_invoice}{$smarty.post.postcode_invoice}{/if}"/>
						</div>
						{elseif $field_name eq "city"}
						<div class="required form-group">
							<label for="city_invoice">{l s='City'} <sup>*</sup></label>
							<input type="text" class="form-control" name="city_invoice" id="city_invoice" value="{if isset($smarty.post.city_invoice) && $smarty.post.city_invoice}{$smarty.post.city_invoice}{/if}" />
						</div>
						{elseif $field_name eq "country" || $field_name eq "Country:name"}
						<div class="required form-group">
							<label for="id_country_invoice">{l s='Country'} <sup>*</sup></label>
							<select name="id_country_invoice" id="id_country_invoice" class="form-control">
								<option value="">-</option>
								{foreach from=$countries item=v}
								<option value="{$v.id_country}"{if (isset($smarty.post.id_country_invoice) && $smarty.post.id_country_invoice == $v.id_country) OR (!isset($smarty.post.id_country_invoice) && $sl_country == $v.id_country)} selected="selected"{/if}>{$v.name|escape:'html':'UTF-8'}</option>
								{/foreach}
							</select>
						</div>
						{elseif $field_name eq "state" || $field_name eq 'State:name'}
						{$stateExist = true}
						<div class="required id_state_invoice form-group" style="display:none;">
							<label for="id_state_invoice">{l s='State'} <sup>*</sup></label>
							<select name="id_state_invoice" id="id_state_invoice" class="form-control">
								<option value="">-</option>
							</select>
						</div>
						{/if}
						{/foreach}
						{if !$postCodeExist}
						<div class="required postcode_invoice form-group unvisible">
							<label for="postcode_invoice">{l s='Zip/Postal Code'} <sup>*</sup></label>
							<input type="text" class="form-control" name="postcode_invoice" id="postcode_invoice" value="{if isset($smarty.post.postcode_invoice) && $smarty.post.postcode_invoice}{$smarty.post.postcode_invoice}{/if}"/>
						</div>
						{/if}
						{if !$stateExist}
						<div class="required id_state_invoice form-group unvisible">
							<label for="id_state_invoice">{l s='State'} <sup>*</sup></label>
							<select name="id_state_invoice" id="id_state_invoice" class="form-control">
								<option value="">-</option>
							</select>
						</div>
						{/if}
						{if $dniExist eq false}
							<div class="required form-group dni_invoice">
								<label for="dni">{l s='Identification number'} <sup>*</sup></label>
								<input type="text" class="text form-control" name="dni_invoice" id="dni_invoice" value="{if isset($smarty.post.dni_invoice) && $smarty.post.dni_invoice}{$smarty.post.dni_invoice}{/if}" />
								<span class="form_info">{l s='DNI / NIF / NIE'}</span>
							</div>
						{/if}
						<div class="form-group is_customer_param">
							<label for="other_invoice">{l s='Additional information'}</label>
							<textarea class="form-control" name="other_invoice" id="other_invoice" cols="26" rows="3"></textarea>
						</div>
						{if isset($one_phone_at_least) && $one_phone_at_least}
							<p class="inline-infos required is_customer_param">{l s='You must register at least one phone number.'}</p>
						{/if}
						<div class="form-group is_customer_param">
							<label for="phone_invoice">{l s='Home phone'}</label>
							<input type="text" class="form-control" name="phone_invoice" id="phone_invoice" value="{if isset($smarty.post.phone_invoice) && $smarty.post.phone_invoice}{$smarty.post.phone_invoice}{/if}" />
						</div>
						<div class="{if isset($one_phone_at_least) && $one_phone_at_least}required {/if}form-group">
							<label for="phone_mobile_invoice">{l s='Mobile phone'}{if isset($one_phone_at_least) && $one_phone_at_least} <sup>*</sup>{/if}</label>
							<input type="text" class="form-control" name="phone_mobile_invoice" id="phone_mobile_invoice" value="{if isset($smarty.post.phone_mobile_invoice) && $smarty.post.phone_mobile_invoice}{$smarty.post.phone_mobile_invoice}{/if}" />
						</div>
						<input type="hidden" name="alias_invoice" id="alias_invoice" value="{l s='My Invoice address'}" />
					</div>
					<!-- END Account -->
				</div>
				{$HOOK_CREATE_ACCOUNT_FORM}
			</div>
			<p class="cart_navigation required submit clearfix">
				<span><sup>*</sup>{l s='Required field'}</span>
				<input type="hidden" name="display_guest_checkout" value="1" />
				<button type="submit" class="button btn btn-default button-medium" name="submitGuestAccount" id="submitGuestAccount">
					<span>
						{l s='Proceed to checkout'}
						<i class="icon-chevron-right right"></i>
					</span>
				</button>
			</p>
		</form>
	{/if}
{else}
	<!--{if isset($account_error)}
	<div class="error">
		{if {$account_error|@count} == 1}
			<p>{l s='There\'s at least one error'} :</p>
			{else}
			<p>{l s='There are %s errors' sprintf=[$account_error|@count]} :</p>
		{/if}
		<ol>
			{foreach from=$account_error item=v}
				<li>{$v}</li>
			{/foreach}
		</ol>
	</div>
	{/if}-->
       <form action="{$link->getPageLink('authentication', true)|escape:'html':'UTF-8'}" enctype="multipart/form-data" method="post" id="account-creation_form" class="col-lg-6 col-sm-12 col-md-6 col-xs-12 std box first-inscription">
		{$HOOK_CREATE_ACCOUNT_TOP}
        <div class="account_creation">
            <h2>{l s='Your personal information'}</h2>
            {*<p>{l s="Please be sure to update your personal information if it’s changed."}</p>*}
            <p class="required"><sup>*</sup>{l s='Required field'}</p>
            <div class="row containerForm">
                    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                        <fieldset class="fieldInfo"><br/>
                                    <div class="clearfix gender-responsive">
                                            <label>{l s='Gender'}</label>
                                            <br/>
                                            {foreach from=$genders key=k item=gender}
                                                    <div class="gender">
                                                            <label for="id_gender{$gender->id}" class="top">
                                                                    <input type="radio" name="id_gender" class="is_required validate form-control" id="id_gender{$gender->id}" value="{$gender->id}" {if $gender->id == 1}checked="checked"{/if} />
                                                            {$gender->name}
                                                            </label>
                                                    </div>
                                            {/foreach}
                                    </div>
                                    <div class="required form-group">
                                            <label class="required" for="username">{l s='Username'}</label>
                                            <input type="text" class="is_required validate form-control" data-validate="isUsername" id="username" name="username" value="{if isset($smarty.post.username)}{$smarty.post.username}{/if}" title="{l s='Enter only letters and numbers'}" />
                                            <span class="form_info">{l s='Enter only letters and numbers'}</span>
                                    </div>
                                    <div class="required form-group">
                                            <label class="required" for="customer_firstname">{l s='First name'}</label>
                                            <input type="text" class="is_required validate form-control" data-validate="isName" id="customer_firstname" name="customer_firstname" value="{if isset($smarty.post.customer_firstname)}{$smarty.post.customer_firstname}{/if}" />
                                    </div>
                                    <div class="required form-group">
                                            <label class="required" for="customer_lastname">{l s='Last name'}</label>
                                            <input type="text" class="is_required validate form-control" data-validate="isName" id="customer_lastname" name="customer_lastname" value="{if isset($smarty.post.customer_lastname)}{$smarty.post.customer_lastname}{/if}" />
                                    </div>
                                    <div class="required form-group">
                                            <label class="required" for="email">{l s='Email'}</label>
                                            <input type="email" class="is_required validate form-control" data-validate="isEmail" id="email" name="email" value="{if isset($smarty.post.email)}{$smarty.post.email}{/if}" OnFocus="this.blur()"/>
                                    </div>
                                    <div class="required password form-group">
                                            <label class="required" for="passwd">{l s='Password'} </label>
                                            <input type="password" class="is_required validate form-control" data-validate="isPasswd" name="passwd" id="passwd" />
                                            <span class="form_info">{l s='(Five characters minimum)'}</span>
                                    </div>
                                    <div class="required form-group">
                                            <label class="required" for="typedocument">{l s='Document type'}</label>
                                            <div style="max-width: 271px;" class="select-form">
                                                    <select id="typedocument" name="typedocument" class="form-control" style="max-width: 100%;">
                                                        <option value="0" selected="selected">Cedula de Ciudadan&iacute;a</option>
                                                            <option value="1">NIT</option>
                                                            <option value="2">Cedula de Extranjer&iacute;a</option>
                                                    </select>
                                            </div>
                                    </div>
                                    <div class="required form-group" style="height: 50px">
                                            <div class="form-group required" style="padding: 0;">
                                                    <label class="required" for="gover">{l s='Document number'}</label>
                                                    <input type="text" class="is_required validate form-control" data-validate="isGoverNumber" id="gover" name="gover" value="{if isset($smarty.post.gover)}{$smarty.post.gover}{/if}"/>                                            
                                            </div>
                                            <div class="form-group required col-lg-3 col-md-3 col-xs-3 blockcheckdigit" style="display: none;">
                                                    <label class="required" for="gover">d&iacute;gito de verificaci&oacute;n</label>
                                                    <input type="number" class="is_required validate form-control" data-validate="isCheckDigit" id="checkdigit" name="checkdigit" oninput="if(value.length>1)value=value.slice(0,1)" value="{if isset($smarty.post.checkdigit)}{$smarty.post.checkdigit}{/if}"/>
                                            </div>
                                    </div>
                                    <div class="form-group col-lg-12" style="padding-left:0px;">
                                        <div class="container-birth">
                                            <label class="col-lg-12 col-md-12 col-xs-12 required" style="padding-left:0px;">{l s='Date of Birth'}</label>
                                           
                                                    <div class="col-xs-4 col-lg-4 col-sm-4">
                                                            <select id="days" name="days" class="form-control">
                                                                    <option value="">-</option>
                                                                    {foreach from=$days item=day}
                                                                            <option value="{$day}" {if ($sl_day == $day)} selected="selected"{/if}>{$day}&nbsp;&nbsp;</option>
                                                                    {/foreach}
                                                            </select>
                                                            {*
                                                                    {l s='January'}
                                                                    {l s='February'}
                                                                    {l s='March'}
                                                                    {l s='April'}
                                                                    {l s='May'}
                                                                    {l s='June'}
                                                                    {l s='July'}
                                                                    {l s='August'}
                                                                    {l s='September'}
                                                                    {l s='October'}
                                                                    {l s='November'}
                                                                    {l s='December'}
                                                            *}
                                                    </div>
                                                    <div class="col-xs-4 col-lg-4 col-sm-4">
                                                            <select id="months" name="months" class="form-control">
                                                                    <option value="">-</option>
                                                                    {foreach from=$months key=k item=month}
                                                                            <option value="{$k}" {if ($sl_month == $k)} selected="selected"{/if}>{l s=$month}&nbsp;</option>
                                                                    {/foreach}
                                                            </select>
                                                    </div>
                                                    <div class="col-xs-4 col-lg-4 col-sm-4">
                                                            <select id="years" name="years" class="form-control">
                                                                    <option value="">-</option>
                                                                    {foreach from=$years item=year}
                                                                        {if ($smarty.now|date_format:"%Y" - {$year}) >= 18 }
                                                                            <option value="{$year}" {if ($sl_year == $year)} selected="selected"{/if}>{$year}&nbsp;&nbsp;</option>
                                                                        {/if}
                                                                    {/foreach}
                                                            </select>
                                                    </div>
                                        </div>
                                    </div>
                                    <div class="required form-group">
                                        <p class="{if isset($one_phone_at_least) && $one_phone_at_least}required {/if}form-group">
                                            <br/><label class="required col-lg-12" for="phone_mobile" style="padding:0px; margin-top: 5px;">{l s='Mobile phone'}{if isset($one_phone_at_least) && $one_phone_at_least} {/if}</label>
                                            <input type="number" class="is_required validate form-control" data-validate="isPhoneNumber" name="phone_mobile" id="phone_mobile" value="{if isset($smarty.post.phone_mobile)}{$smarty.post.phone_mobile}{/if}" />
                                        </p>
                                    </div>
                                    <div class="required form-group">
                                            <label class="required" for="address1">{l s='Address'}</label>
                                            <input type="text" class="is_required validate form-control" data-validate="isAddress" name="address1" id="address1" value="{if isset($smarty.post.address1)}{$smarty.post.address1}{/if}" />
                                            <span class="inline-infos">{l s='Street address, P.O. Box, Company name, etc.'}</span>
                                    </div>
                                    {*<div class="required form-group">
                                            <label class="required" for="address2">{l s='Address (Line 2)'}</label>
                                            <input type="text" class="is_required validate form-control" data-validate="isAddress" name="address2" id="address2" value="{if isset($smarty.post.address2)}{$smarty.post.address2}{/if}" />
                                            <span class="inline-infos">{l s='Apartment, suite, unit, building, floor, etc...'}</span>
                                    </div>*}
                                    <div class="required form-group">
                                        <label class="required" for="city">{l s='City'}</label>
                                        {*<input type="text" class="is_required validate form-control" data-validate="isCityName" name="city" id="city" value="{if isset($smarty.post.city)}{$smarty.post.city}{/if}" />*}
                                        <div style="max-width: 271px;" class="select-form">
                                        <select name="city" id="city" class="form-control" style="max-width: 100%;">
                                            {foreach from=$cities item=city}
                                                <option value="{$city.ciudad}">{$city.ciudad}</option>
                                            {/foreach}
                                        </select>
                                        </div>
                                    </div>
                                    <div class="required select form-group">
                                        <label class="required" for="id_country">{l s='Country'}</label>
                                        <div style="max-width: 271px;" class="select-form">
                                        <select name="id_country" id="id_country" class="form-control" style="max-width: 100%;">
                                                <option value="">-</option>
                                                {foreach from=$countries item=v}
                                                <option value="{$v.id_country}"{if (isset($smarty.post.id_country) AND $smarty.post.id_country == $v.id_country) OR (!isset($smarty.post.id_country) && $sl_country == $v.id_country)} selected="selected"{/if}>{$v.name}</option>
                                                {/foreach}
                                        </select>
                                        </div>
                                    </div>  
                                    <br/>
                                    {if isset($newsletter) && $newsletter}
                                            <div class="col-lg-12 col-md-12 checkbox">
                                                    <input type="checkbox" name="newsletter" id="newsletter" value="1" {if isset($smarty.post.newsletter) AND $smarty.post.newsletter == 1} checked="checked"{/if} />
                                                    <label for="newsletter">Reg&iacute;strate en nuestro bolet&iacute;n</label>
                                                    {if array_key_exists('newsletter', $field_required)}
                                                            <sup> *</sup>
                                                    {/if}
                                            </div>
                                    {/if}
                                    {*if isset($optin) && $optin}
                                            <div class="col-lg-12 col-md-12 checkbox">
                                                    <input type="checkbox" name="optin" id="optin" value="1" {if isset($smarty.post.optin) AND $smarty.post.optin == 1} checked="checked"{/if} />
                                                    <label for="optin">{l s='Receive special offers from our partners!'}</label>
                                                    {if array_key_exists('optin', $field_required)}
                                                            <sup> *</sup>
                                                    {/if}
                                            </div>
                                    {/if*}
                                    
                                    {if $b2b_enable}
                                        <div class="account_creation">
                                            <h3 class="page-subheading">{l s='Your company information'}</h3>
                                            <p class="form-group">
                                                    <label for="">{l s='Company'}</label>
                                                    <input type="text" class="form-control" id="company" name="company" value="{if isset($smarty.post.company)}{$smarty.post.company}{/if}" />
                                            </p>
                                            <p class="form-group">
                                                    <label for="siret">{l s='SIRET'}</label>
                                                    <input type="text" class="form-control" id="siret" name="siret" value="{if isset($smarty.post.siret)}{$smarty.post.siret}{/if}" />
                                            </p>
                                            <p class="form-group">
                                                    <label for="ape">{l s='APE'}</label>
                                                    <input type="text" class="form-control" id="ape" name="ape" value="{if isset($smarty.post.ape)}{$smarty.post.ape}{/if}" />
                                            </p>
                                            <p class="form-group">
                                                    <label for="website">{l s='Website'}</label>
                                                    <input type="text" class="form-control" id="website" name="website" value="{if isset($smarty.post.website)}{$smarty.post.website}{/if}" />
                                            </p>
                                        </div>
                                    {/if}
                                    {if isset($PS_REGISTRATION_PROCESS_TYPE) && $PS_REGISTRATION_PROCESS_TYPE}
                                    <div class="account_creation">
                                            <h3 class="page-subheading">{l s='Your address'}</h3>
                                            {foreach from=$dlv_all_fields item=field_name}
                                                    {if $field_name eq "company"}
                                                            {if !$b2b_enable}
                                                                    <p class="form-group">
                                                                            <label for="company">{l s='Company'}{if in_array($field_name, $required_fields)} <sup>*</sup>{/if}</label>
                                                                            <input type="text" class="form-control" id="company" name="company" value="{if isset($smarty.post.company)}{$smarty.post.company}{/if}" />
                                                                    </p>
                                                            {/if}
                                                    {elseif $field_name eq "vat_number"}
                                                            <div id="vat_number" style="display:none;">
                                                                    <p class="form-group">
                                                                            <label for="vat_number">{l s='VAT number'}{if in_array($field_name, $required_fields)} <sup>*</sup>{/if}</label>
                                                                            <input type="text" class="form-control" id="vat_number" name="vat_number" value="{if isset($smarty.post.vat_number)}{$smarty.post.vat_number}{/if}" />
                                                                    </p>
                                                            </div>
                                                    {elseif $field_name eq "firstname"}
                                                            <p class="required form-group">
                                                                    <label for="firstname">{l s='First name'} <sup>*</sup></label>
                                                                    <input type="text" class="form-control" id="firstname" name="firstname" value="{if isset($smarty.post.firstname)}{$smarty.post.firstname}{/if}" />
                                                            </p>
                                                    {elseif $field_name eq "lastname"}
                                                            <p class="required form-group">
                                                                    <label for="lastname">{l s='Last name'} <sup>*</sup></label>
                                                                    <input type="text" class="form-control" id="lastname" name="lastname" value="{if isset($smarty.post.lastname)}{$smarty.post.lastname}{/if}" />
                                                            </p>
                                                    {elseif $field_name eq "address1"}
                                                            <p class="required form-group">
                                                                    <label for="address1">{l s='Address'} <sup>*</sup></label>
                                                                    <input type="text" class="form-control" name="address1" id="address1" value="{if isset($smarty.post.address1)}{$smarty.post.address1}{/if}" />
                                                                    <span class="inline-infos">{l s='Street address, P.O. Box, Company name, etc.'}</span>
                                                            </p>
                                                    {elseif $field_name eq "address2"}
                                                            <p class="form-group is_customer_param">
                                                                    <label for="address2">{l s='Address (Line 2)'}{if in_array($field_name, $required_fields)} <sup>*</sup>{/if}</label>
                                                                    <input type="text" class="form-control" name="address2" id="address2" value="{if isset($smarty.post.address2)}{$smarty.post.address2}{/if}" />
                                                                    <span class="inline-infos">{l s='Apartment, suite, unit, building, floor, etc...'}</span>
                                                            </p>
                                                    {elseif $field_name eq "postcode"}
                                                            {assign var='postCodeExist' value=true}
                                                            <p class="required postcode form-group">
                                                                    <label for="postcode">{l s='Zip/Postal Code'} <sup>*</sup></label>
                                                                    <input type="text" class="validate form-control" name="postcode" id="postcode" data-validate="isPostCode" value="{if isset($smarty.post.postcode)}{$smarty.post.postcode}{/if}"/>
                                                            </p>
                                                    {elseif $field_name eq "city"}
                                                            <p class="required form-group">
                                                                    <label for="city">{l s='City'} <sup>*</sup></label>
                                                                    <input type="text" class="form-control" name="city" id="city" value="{if isset($smarty.post.city)}{$smarty.post.city}{/if}" />
                                                            </p>
                                                            <!-- if customer hasn't update his layout address, country has to be verified but it's deprecated -->
                                                    {elseif $field_name eq "Country:name" || $field_name eq "country"}
                                                            <p class="required select form-group">
                                                                    <label for="id_country">{l s='Country'} <sup>*</sup></label>
                                                                    <select name="id_country" id="id_country" class="form-control">
                                                                            <option value="">-</option>
                                                                            {foreach from=$countries item=v}
                                                                            <option value="{$v.id_country}"{if (isset($smarty.post.id_country) AND $smarty.post.id_country == $v.id_country) OR (!isset($smarty.post.id_country) && $sl_country == $v.id_country)} selected="selected"{/if}>{$v.name}</option>
                                                                            {/foreach}
                                                                    </select>
                                                            </p>
                                                    {elseif $field_name eq "State:name" || $field_name eq 'state'}
                                                            {assign var='stateExist' value=true}
                                                            <p class="required id_state select form-group">
                                                                    <label for="id_state">{l s='State'} <sup>*</sup></label>
                                                                    <select name="id_state" id="id_state" class="form-control">
                                                                            <option value="">-</option>
                                                                    </select>
                                                            </p>
                                                    {/if}
                                            {/foreach}
                                            {if $postCodeExist eq false}
                                                    <p class="required postcode form-group unvisible">
                                                            <label for="postcode">{l s='Zip/Postal Code'} <sup>*</sup></label>
                                                            <input type="text" class="validate form-control" name="postcode" id="postcode" data-validate="isPostCode" value="{if isset($smarty.post.postcode)}{$smarty.post.postcode}{/if}"/>
                                                    </p>
                                            {/if}
                                            {if $stateExist eq false}
                                                    <p class="required id_state select unvisible form-group">
                                                            <label for="id_state">{l s='State'} <sup>*</sup></label>
                                                            <select name="id_state" id="id_state" class="form-control">
                                                                    <option value="">-</option>
                                                            </select>
                                                    </p>
                                            {/if}
                                            <p class="textarea form-group">
                                                    <label for="other">{l s='Additional information'}</label>
                                                    <textarea class="form-control" name="other" id="other" cols="26" rows="3">{if isset($smarty.post.other)}{$smarty.post.other}{/if}</textarea>
                                            </p>
                                            <p class="form-group">
                                                    <label for="phone">{l s='Home phone'}{if isset($one_phone_at_least) && $one_phone_at_least} <sup>**</sup>{/if}</label>
                                                    <input type="text" class="form-control" name="phone" id="phone" value="{if isset($smarty.post.phone)}{$smarty.post.phone}{/if}" />
                                            </p>
                                            <p class="{if isset($one_phone_at_least) && $one_phone_at_least}required {/if}form-group">
                                                    <label for="phone_mobile">{l s='Mobile phone'}{if isset($one_phone_at_least) && $one_phone_at_least} <sup>**</sup>{/if}</label>
                                                    <input type="text" class="form-control" name="phone_mobile" id="phone_mobile" value="{if isset($smarty.post.phone_mobile)}{$smarty.post.phone_mobile}{/if}" />
                                            </p>
                                            {if isset($one_phone_at_least) && $one_phone_at_least}
                                                    {assign var="atLeastOneExists" value=true}
                                                    <p class="inline-infos required">** {l s='You must register at least one phone number.'}</p>
                                            {/if}
                                            <p class="required form-group" id="address_alias">
                                                    <label for="alias">{l s='Assign an address alias for future reference.'} <sup>*</sup></label>
                                                    <input type="text" class="form-control" name="alias" id="alias" value="{if isset($smarty.post.alias)}{$smarty.post.alias}{else}{l s='My address'}{/if}" />
                                            </p>
                                    </div>
                                    <div class="account_creation dni">
                                            <h3 class="page-subheading">{l s='Tax identification'}</h3>
                                            <p class="required form-group">
                                                    <label for="dni">{l s='Identification number'} <sup>*</sup></label>
                                                    <input type="text" class="form-control" name="dni" id="dni" value="{if isset($smarty.post.dni)}{$smarty.post.dni}{/if}" />
                                                    <span class="form_info">{l s='DNI / NIF / NIE'}</span>
                                            </p>
                                    </div>
                                {/if}
                                </fieldset>
                    </div>                
            </div>
            {if $PS_BUY_MEMBERSHIP}
                <div>
                    <label class="depoTitle page-subheading col-lg-12">{l s='DEPOSIT'}</label>
                    <div class="containerDepo">
                        <p>{l s="When you create a Fluz Fluz account, we ask that you "}<span class="stand_out">{l s="deposit a minimum of $20.000 in your account so we can validate it. "}</span>{l s="This is a firs time only required deposit and will be entirely at your disposal in your account so you can start."}</p>
                    <div class="row rangeSelect">
                        <span class="col-lg-2 rangePrice">$15.000</span><input class="rangeslider col-lg-8" type="range" id="rangeSlider" value="30000" min="15000" max="105000" step="15000" data-rangeslider><span class="col-lg-2 rangePrice">$105.000</span>
                    </div>
                    <div class="col-lg-12 col-md-12 finalDeposit">
                        <span class="col-lg-8 col-md-7" style="font-size:18px;">{l s="Final Deposit Amount:"}</span>
                        <div class="col-lg-4 col-md-5">
                            <span class="money">$</span>
                            <input class="output" type="text" name="valorSlider" id="valorSlider" value="" readonly />
                        </div>
                    </div>
                    </div>    
                </div>
                <div style="display: none">
                    <input type="hidden" id="psebank" name="psebank">
                    <input type="hidden" id="namebank" name="namebank">
                    <input type="hidden" id="psetypecustomer" name="psetypecustomer">
                    <input type="hidden" id="psetypedoc" name="psetypedoc">
                    <input type="hidden" id="psenumdoc" name="psenumdoc">
                </div>
                <div class="payublock">
                    {hook h="displayPayment"}
                </div>
            {/if}
                <div class="row">
                    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                        <div style="display:none;">{$HOOK_CREATE_ACCOUNT_FORM}</div>
                    <div class="formInfo submit clearfix">
			<input type="hidden" name="email_create" value="1" />
                        <div class="checkbox">
                                <input type="checkbox" name="acceptterms" id="acceptterms" value="1"/>
                                <label for="acceptterms">Acepto los t&eacute;rminos y condiciones de Fluz Fluz</label>
                        </div>
                        <button class="btnInfo" type="submit" name="submitAccount" id="submitAccount"/>
				<span>Registro Gratis<i class="icon-chevron-right right"></i></span>
			</button>
                    </div>
                    </div>
                </div>
        </div>
                                
        {literal}
        <script type="text/javascript">
            $(document).ready(function(){
                document.getElementById( 'valorSlider' ).value=15000 ;  
                
                $('#rangeSlider').change(function() 
                {
                    $('#valorSlider').val($(this).val());
                });
            });
        </script>
        {/literal}
	</form> 
       <form action="{$link->getPageLink('authentication', true)|escape:'html':'UTF-8'}"  method="post" id="infoaccount-creation_form" class="col-lg-6 col-md-6 col-sm-12 col-xs-12 std box">
		{$HOOK_CREATE_ACCOUNT_TOP}
        <div class="account_creation">
            <h2>&iquest;POR QU&Eacute; REGISTRARSE EN FLUZ FLUZ?</h2>
            
            <div class="row containerForm">
                    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                        <fieldset>
                            <div class="row rowAccount">
                                <img src="{$img_dir}icon/gift.png" class="imglock2 col-lg-4 col-md-4" />
                                <div class="col-lg-8 col-md-8 infRight">
                                    <h3 class="col-lg-12 title-text">&iexcl;Ganando como nunca!</h3>
                                    <p class="col-lg-12 col-sm-12 col-md-12 col-xs-12 p-auth">Gana dinero por tus compras habituales en tus marcas favoritas.</p>
                                </div>
                            </div>
                            <div class="row rowAccount">
                                <img src="{$img_dir}icon/save.png" class="imglock2 col-lg-4 col-md-4" />
                                <div class="col-lg-8 col-md-8 infRight">
                                    <h3 class="col-lg-12 title-text">&iexcl;Ahorra!</h3>
                                    <p class="col-lg-12 col-sm-12 col-md-12 col-xs-12 p-auth">Cada vez que compras, recibes Fluz</p>
                                </div>
                            </div>
                            <div class="row rowAccount">
                                <img src="{$img_dir}icon/invite.png" class="imglock2 col-lg-4 col-md-4" />
                                <div class="col-lg-8 col-md-8 infRight">
                                    <h3 class="col-lg-12 title-text">&iexcl;Invita a tus amigos!</h3>
                                    <p class="col-lg-12 col-sm-12 col-md-12 col-xs-12 p-auth">Entre m&aacute;s amigos se unen a tu network, m&aacute;s gan&aacute;s por los consumos de ellos</p>
                                </div>
                            </div>
                            <div class="row rowAccount">
                                <img src="{$img_dir}icon/cash.png" class="imglock2 col-lg-4 col-md-4" />
                                <div class="col-lg-8 col-md-8 infRight">
                                    <h3 class="col-lg-12 title-text">&iexcl;Redenci&oacute;n en Efectivo!</h3>
                                    <p class="col-lg-12 col-sm-12 col-md-12 col-xs-12 p-auth">Convierte tus Fluz en pesos</p>
                                </div>
                            </div>
                            {*<div class="row rowAccount">
                                <img src="{$img_dir}icon/diagram.png" class="imglock2 col-lg-4 col-md-4" />
                                <div class="col-lg-8 col-md-8 infRight">
                                    <h3 class="col-lg-12 title-text">{l s="View Network Statistics"}</h3>
                                    <p class="col-lg-12 col-sm-12 col-md-12 col-xs-12 p-auth">{l s="View your network statistics to improve your point tally. "}</p>
                                </div>
                            </div>*}
                        </fieldset>
                    </div>
            </div>
        </div>
            <div class="side-nav vdoTube row">
                <iframe class="vdo-auth" height="315" src="https://www.youtube.com/embed/bVmfZ-Iu-UY?rel=0&controls=0" frameborder="0" allowfullscreen="allowfullscreen"></iframe>    
                <div class="row bulletins">
                    <div class="col-sm-4 text-center style_prevu_kit">
                        <a target="_blank" href="http://www.economiaynegocios.co/index.php/negocios/item/3621-fluz-fluz-llega-a-colombia"><img src="https://s3.amazonaws.com/imagenes-fluzfluz/dev/admin/economia-y-negocios.png"/></a>
                    </div>
                    <div class="col-sm-4 text-center style_prevu_kit">
                        <a target="_blank" href="http://www.bluradio.com/tecnologia/fluz-fluz-una-nueva-alternativa-para-hacer-compras-virtuales-132044"><img src="https://s3.amazonaws.com/imagenes-fluzfluz/dev/admin/bluradio.png"/></a>
                    </div>
                    <div class="col-sm-4 text-center style_prevu_kit">
                        <a target="_blank" href="http://www.finanzaspersonales.com.co/consumo-inteligente/articulo/compras-como-convertir-las-compras-en-consumos-gratis/71597"><img src="https://s3.amazonaws.com/imagenes-fluzfluz/dev/admin/logo-finanzas.png"/></a>
                    </div>
                </div>
            </div>
	</form>
	<form action="{$link->getPageLink('authentication', true)|escape:'html':'UTF-8'}" enctype="multipart/form-data" method="post" id="account-creation_form" class="col-lg-6 col-sm-12 col-md-6 col-xs-12 std box second-inscription">
		{$HOOK_CREATE_ACCOUNT_TOP}
        <div class="account_creation">
            <h2>{l s='Your personal information'}</h2>
            {*<p>{l s="Please be sure to update your personal information if it’s changed."}</p>*}
            <p class="required"><sup>*</sup>{l s='Required field'}</p>
            <div class="row containerForm">
                    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                        <fieldset class="fieldInfo"><br/>
                                    <div class="clearfix">
                                            <label>{l s='Gender'}</label>
                                            <br/>
                                            {foreach from=$genders key=k item=gender}
                                                    <div class="gender">
                                                            <label for="id_gender{$gender->id}" class="top">
                                                                    <input type="radio" name="id_gender" class="is_required validate form-control" id="id_gender{$gender->id}" value="{$gender->id}" {if $gender->id == 1}checked="checked"{/if} />
                                                            {$gender->name}
                                                            </label>
                                                    </div>
                                            {/foreach}
                                    </div>
                                    <div class="required form-group">
                                            <label class="required" for="username">{l s='Username'}</label>
                                            <input type="text" class="is_required validate form-control" data-validate="isUsername" id="username" name="username" value="{if isset($smarty.post.username)}{$smarty.post.username}{/if}" title="{l s='Enter only letters and numbers'}" />
                                            <span class="form_info">{l s='Enter only letters and numbers'}</span>
                                    </div>
                                    <div class="required form-group">
                                            <label class="required" for="customer_firstname">{l s='First name'}</label>
                                            <input type="text" class="is_required validate form-control" data-validate="isName" id="customer_firstname" name="customer_firstname" value="{if isset($smarty.post.customer_firstname)}{$smarty.post.customer_firstname}{/if}" />
                                    </div>
                                    <div class="required form-group">
                                            <label class="required" for="customer_lastname">{l s='Last name'}</label>
                                            <input type="text" class="is_required validate form-control" data-validate="isName" id="customer_lastname" name="customer_lastname" value="{if isset($smarty.post.customer_lastname)}{$smarty.post.customer_lastname}{/if}" />
                                    </div>
                                    <div class="required form-group">
                                            <label class="required" for="email">{l s='Email'}</label>
                                            <input type="email" class="is_required validate form-control" data-validate="isEmail" id="email" name="email" value="{if isset($smarty.post.email)}{$smarty.post.email}{/if}" OnFocus="this.blur()"/>
                                    </div>
                                    <div class="required password form-group">
                                            <label class="required" for="passwd">{l s='Password'} </label>
                                            <input type="password" class="is_required validate form-control" data-validate="isPasswd" name="passwd" id="passwd" />
                                            <span class="form_info">{l s='(Five characters minimum)'}</span>
                                    </div>
                                    <div class="required form-group">
                                            <label class="required" for="typedocument">{l s='Document type'}</label>
                                            <div style="max-width: 271px;">
                                                    <select id="typedocument" name="typedocument" class="form-control" style="max-width: 100%;">
                                                        <option value="0" selected="selected">Cedula de Ciudadan&iacute;a</option>
                                                            <option value="1">NIT</option>
                                                            <option value="2">Cedula de Extranjer&iacute;a</option>
                                                    </select>
                                            </div>
                                    </div>
                                    <div class="required form-group" style="height: 50px">
                                            <div class="form-group required" style="padding: 0;">
                                                    <label class="required" for="gover">{l s='Document number'}</label>
                                                    <input type="text" class="is_required validate form-control" data-validate="isGoverNumber" id="gover" name="gover" value="{if isset($smarty.post.gover)}{$smarty.post.gover}{/if}"/>                                            
                                            </div>
                                            <div class="form-group required col-lg-3 col-md-3 col-xs-3 blockcheckdigit" style="display: none;">
                                                    <label class="required" for="gover">d&iacute;gito de verificaci&oacute;n</label>
                                                    <input type="number" class="is_required validate form-control" data-validate="isCheckDigit" id="checkdigit" name="checkdigit" oninput="if(value.length>1)value=value.slice(0,1)" value="{if isset($smarty.post.checkdigit)}{$smarty.post.checkdigit}{/if}"/>
                                            </div>
                                    </div>
                                    <div class="form-group col-lg-12" style="padding-left:0px;">
                                            <label class="col-lg-12 col-md-12 col-xs-12 required" style="padding-left:0px;">{l s='Date of Birth'}</label>
                                           
                                                    <div class="col-xs-4 col-lg-4">
                                                            <select id="days" name="days" class="form-control">
                                                                    <option value="">-</option>
                                                                    {foreach from=$days item=day}
                                                                            <option value="{$day}" {if ($sl_day == $day)} selected="selected"{/if}>{$day}&nbsp;&nbsp;</option>
                                                                    {/foreach}
                                                            </select>
                                                            {*
                                                                    {l s='January'}
                                                                    {l s='February'}
                                                                    {l s='March'}
                                                                    {l s='April'}
                                                                    {l s='May'}
                                                                    {l s='June'}
                                                                    {l s='July'}
                                                                    {l s='August'}
                                                                    {l s='September'}
                                                                    {l s='October'}
                                                                    {l s='November'}
                                                                    {l s='December'}
                                                            *}
                                                    </div>
                                                    <div class="col-xs-4 col-lg-4">
                                                            <select id="months" name="months" class="form-control">
                                                                    <option value="">-</option>
                                                                    {foreach from=$months key=k item=month}
                                                                            <option value="{$k}" {if ($sl_month == $k)} selected="selected"{/if}>{l s=$month}&nbsp;</option>
                                                                    {/foreach}
                                                            </select>
                                                    </div>
                                                    <div class="col-xs-4 col-lg-4">
                                                            <select id="years" name="years" class="form-control">
                                                                    <option value="">-</option>
                                                                    {foreach from=$years item=year}
                                                                        {if ($smarty.now|date_format:"%Y" - {$year}) >= 18 }
                                                                            <option value="{$year}" {if ($sl_year == $year)} selected="selected"{/if}>{$year}&nbsp;&nbsp;</option>
                                                                        {/if}
                                                                    {/foreach}
                                                            </select>
                                                    </div>
                                           
                                    </div>
                                    <div class="required form-group">
                                        <p class="{if isset($one_phone_at_least) && $one_phone_at_least}required {/if}form-group">
                                            <br/><label class="required col-lg-12" for="phone_mobile" style="padding:0px; margin-top: 5px;">{l s='Mobile phone'}{if isset($one_phone_at_least) && $one_phone_at_least} {/if}</label>
                                            <input type="number" class="is_required validate form-control" data-validate="isPhoneNumber" name="phone_mobile" id="phone_mobile" value="{if isset($smarty.post.phone_mobile)}{$smarty.post.phone_mobile}{/if}" />
                                        </p>
                                    </div>
                                    <div class="required form-group">
                                            <label class="required" for="address1">{l s='Address'}</label>
                                            <input type="text" class="is_required validate form-control" data-validate="isAddress" name="address1" id="address1" value="{if isset($smarty.post.address1)}{$smarty.post.address1}{/if}" />
                                            <span class="inline-infos">{l s='Street address, P.O. Box, Company name, etc.'}</span>
                                    </div>
                                    {*<div class="required form-group">
                                            <label class="required" for="address2">{l s='Address (Line 2)'}</label>
                                            <input type="text" class="is_required validate form-control" data-validate="isAddress" name="address2" id="address2" value="{if isset($smarty.post.address2)}{$smarty.post.address2}{/if}" />
                                            <span class="inline-infos">{l s='Apartment, suite, unit, building, floor, etc...'}</span>
                                    </div>*}
                                    <div class="required form-group">
                                        <label class="required" for="city">{l s='City'}</label>
                                        {*<input type="text" class="is_required validate form-control" data-validate="isCityName" name="city" id="city" value="{if isset($smarty.post.city)}{$smarty.post.city}{/if}" />*}
                                        <select name="city" id="city" class="form-control">
                                            {foreach from=$cities item=city}
                                                <option value="{$city.ciudad}">{$city.ciudad}</option>
                                            {/foreach}
                                        </select>
                                    </div>
                                    <div class="required select form-group">
                                        <label class="required" for="id_country">{l s='Country'}</label>
                                        <select name="id_country" id="id_country" class="form-control">
                                                <option value="">-</option>
                                                {foreach from=$countries item=v}
                                                <option value="{$v.id_country}"{if (isset($smarty.post.id_country) AND $smarty.post.id_country == $v.id_country) OR (!isset($smarty.post.id_country) && $sl_country == $v.id_country)} selected="selected"{/if}>{$v.name}</option>
                                                {/foreach}
                                        </select>
                                    </div>  
                                    <br/>
                                    {if isset($newsletter) && $newsletter}
                                            <div class="col-lg-12 col-md-12 checkbox">
                                                    <input type="checkbox" name="newsletter" id="newsletter" value="1" {if isset($smarty.post.newsletter) AND $smarty.post.newsletter == 1} checked="checked"{/if} />
                                                    <label for="newsletter">Reg&iacute;strate en nuestro bolet&iacute;n</label>
                                                    {if array_key_exists('newsletter', $field_required)}
                                                            <sup> *</sup>
                                                    {/if}
                                            </div>
                                    {/if}
                                    {*if isset($optin) && $optin}
                                            <div class="col-lg-12 col-md-12 checkbox">
                                                    <input type="checkbox" name="optin" id="optin" value="1" {if isset($smarty.post.optin) AND $smarty.post.optin == 1} checked="checked"{/if} />
                                                    <label for="optin">{l s='Receive special offers from our partners!'}</label>
                                                    {if array_key_exists('optin', $field_required)}
                                                            <sup> *</sup>
                                                    {/if}
                                            </div>
                                    {/if*}
                                    
                                    {if $b2b_enable}
                                        <div class="account_creation">
                                            <h3 class="page-subheading">{l s='Your company information'}</h3>
                                            <p class="form-group">
                                                    <label for="">{l s='Company'}</label>
                                                    <input type="text" class="form-control" id="company" name="company" value="{if isset($smarty.post.company)}{$smarty.post.company}{/if}" />
                                            </p>
                                            <p class="form-group">
                                                    <label for="siret">{l s='SIRET'}</label>
                                                    <input type="text" class="form-control" id="siret" name="siret" value="{if isset($smarty.post.siret)}{$smarty.post.siret}{/if}" />
                                            </p>
                                            <p class="form-group">
                                                    <label for="ape">{l s='APE'}</label>
                                                    <input type="text" class="form-control" id="ape" name="ape" value="{if isset($smarty.post.ape)}{$smarty.post.ape}{/if}" />
                                            </p>
                                            <p class="form-group">
                                                    <label for="website">{l s='Website'}</label>
                                                    <input type="text" class="form-control" id="website" name="website" value="{if isset($smarty.post.website)}{$smarty.post.website}{/if}" />
                                            </p>
                                        </div>
                                    {/if}
                                    {if isset($PS_REGISTRATION_PROCESS_TYPE) && $PS_REGISTRATION_PROCESS_TYPE}
                                    <div class="account_creation">
                                            <h3 class="page-subheading">{l s='Your address'}</h3>
                                            {foreach from=$dlv_all_fields item=field_name}
                                                    {if $field_name eq "company"}
                                                            {if !$b2b_enable}
                                                                    <p class="form-group">
                                                                            <label for="company">{l s='Company'}{if in_array($field_name, $required_fields)} <sup>*</sup>{/if}</label>
                                                                            <input type="text" class="form-control" id="company" name="company" value="{if isset($smarty.post.company)}{$smarty.post.company}{/if}" />
                                                                    </p>
                                                            {/if}
                                                    {elseif $field_name eq "vat_number"}
                                                            <div id="vat_number" style="display:none;">
                                                                    <p class="form-group">
                                                                            <label for="vat_number">{l s='VAT number'}{if in_array($field_name, $required_fields)} <sup>*</sup>{/if}</label>
                                                                            <input type="text" class="form-control" id="vat_number" name="vat_number" value="{if isset($smarty.post.vat_number)}{$smarty.post.vat_number}{/if}" />
                                                                    </p>
                                                            </div>
                                                    {elseif $field_name eq "firstname"}
                                                            <p class="required form-group">
                                                                    <label for="firstname">{l s='First name'} <sup>*</sup></label>
                                                                    <input type="text" class="form-control" id="firstname" name="firstname" value="{if isset($smarty.post.firstname)}{$smarty.post.firstname}{/if}" />
                                                            </p>
                                                    {elseif $field_name eq "lastname"}
                                                            <p class="required form-group">
                                                                    <label for="lastname">{l s='Last name'} <sup>*</sup></label>
                                                                    <input type="text" class="form-control" id="lastname" name="lastname" value="{if isset($smarty.post.lastname)}{$smarty.post.lastname}{/if}" />
                                                            </p>
                                                    {elseif $field_name eq "address1"}
                                                            <p class="required form-group">
                                                                    <label for="address1">{l s='Address'} <sup>*</sup></label>
                                                                    <input type="text" class="form-control" name="address1" id="address1" value="{if isset($smarty.post.address1)}{$smarty.post.address1}{/if}" />
                                                                    <span class="inline-infos">{l s='Street address, P.O. Box, Company name, etc.'}</span>
                                                            </p>
                                                    {elseif $field_name eq "address2"}
                                                            <p class="form-group is_customer_param">
                                                                    <label for="address2">{l s='Address (Line 2)'}{if in_array($field_name, $required_fields)} <sup>*</sup>{/if}</label>
                                                                    <input type="text" class="form-control" name="address2" id="address2" value="{if isset($smarty.post.address2)}{$smarty.post.address2}{/if}" />
                                                                    <span class="inline-infos">{l s='Apartment, suite, unit, building, floor, etc...'}</span>
                                                            </p>
                                                    {elseif $field_name eq "postcode"}
                                                            {assign var='postCodeExist' value=true}
                                                            <p class="required postcode form-group">
                                                                    <label for="postcode">{l s='Zip/Postal Code'} <sup>*</sup></label>
                                                                    <input type="text" class="validate form-control" name="postcode" id="postcode" data-validate="isPostCode" value="{if isset($smarty.post.postcode)}{$smarty.post.postcode}{/if}"/>
                                                            </p>
                                                    {elseif $field_name eq "city"}
                                                            <p class="required form-group">
                                                                    <label for="city">{l s='City'} <sup>*</sup></label>
                                                                    <input type="text" class="form-control" name="city" id="city" value="{if isset($smarty.post.city)}{$smarty.post.city}{/if}" />
                                                            </p>
                                                            <!-- if customer hasn't update his layout address, country has to be verified but it's deprecated -->
                                                    {elseif $field_name eq "Country:name" || $field_name eq "country"}
                                                            <p class="required select form-group">
                                                                    <label for="id_country">{l s='Country'} <sup>*</sup></label>
                                                                    <select name="id_country" id="id_country" class="form-control">
                                                                            <option value="">-</option>
                                                                            {foreach from=$countries item=v}
                                                                            <option value="{$v.id_country}"{if (isset($smarty.post.id_country) AND $smarty.post.id_country == $v.id_country) OR (!isset($smarty.post.id_country) && $sl_country == $v.id_country)} selected="selected"{/if}>{$v.name}</option>
                                                                            {/foreach}
                                                                    </select>
                                                            </p>
                                                    {elseif $field_name eq "State:name" || $field_name eq 'state'}
                                                            {assign var='stateExist' value=true}
                                                            <p class="required id_state select form-group">
                                                                    <label for="id_state">{l s='State'} <sup>*</sup></label>
                                                                    <select name="id_state" id="id_state" class="form-control">
                                                                            <option value="">-</option>
                                                                    </select>
                                                            </p>
                                                    {/if}
                                            {/foreach}
                                            {if $postCodeExist eq false}
                                                    <p class="required postcode form-group unvisible">
                                                            <label for="postcode">{l s='Zip/Postal Code'} <sup>*</sup></label>
                                                            <input type="text" class="validate form-control" name="postcode" id="postcode" data-validate="isPostCode" value="{if isset($smarty.post.postcode)}{$smarty.post.postcode}{/if}"/>
                                                    </p>
                                            {/if}
                                            {if $stateExist eq false}
                                                    <p class="required id_state select unvisible form-group">
                                                            <label for="id_state">{l s='State'} <sup>*</sup></label>
                                                            <select name="id_state" id="id_state" class="form-control">
                                                                    <option value="">-</option>
                                                            </select>
                                                    </p>
                                            {/if}
                                            <p class="textarea form-group">
                                                    <label for="other">{l s='Additional information'}</label>
                                                    <textarea class="form-control" name="other" id="other" cols="26" rows="3">{if isset($smarty.post.other)}{$smarty.post.other}{/if}</textarea>
                                            </p>
                                            <p class="form-group">
                                                    <label for="phone">{l s='Home phone'}{if isset($one_phone_at_least) && $one_phone_at_least} <sup>**</sup>{/if}</label>
                                                    <input type="text" class="form-control" name="phone" id="phone" value="{if isset($smarty.post.phone)}{$smarty.post.phone}{/if}" />
                                            </p>
                                            <p class="{if isset($one_phone_at_least) && $one_phone_at_least}required {/if}form-group">
                                                    <label for="phone_mobile">{l s='Mobile phone'}{if isset($one_phone_at_least) && $one_phone_at_least} <sup>**</sup>{/if}</label>
                                                    <input type="text" class="form-control" name="phone_mobile" id="phone_mobile" value="{if isset($smarty.post.phone_mobile)}{$smarty.post.phone_mobile}{/if}" />
                                            </p>
                                            {if isset($one_phone_at_least) && $one_phone_at_least}
                                                    {assign var="atLeastOneExists" value=true}
                                                    <p class="inline-infos required">** {l s='You must register at least one phone number.'}</p>
                                            {/if}
                                            <p class="required form-group" id="address_alias">
                                                    <label for="alias">{l s='Assign an address alias for future reference.'} <sup>*</sup></label>
                                                    <input type="text" class="form-control" name="alias" id="alias" value="{if isset($smarty.post.alias)}{$smarty.post.alias}{else}{l s='My address'}{/if}" />
                                            </p>
                                    </div>
                                    <div class="account_creation dni">
                                            <h3 class="page-subheading">{l s='Tax identification'}</h3>
                                            <p class="required form-group">
                                                    <label for="dni">{l s='Identification number'} <sup>*</sup></label>
                                                    <input type="text" class="form-control" name="dni" id="dni" value="{if isset($smarty.post.dni)}{$smarty.post.dni}{/if}" />
                                                    <span class="form_info">{l s='DNI / NIF / NIE'}</span>
                                            </p>
                                    </div>
                                {/if}
                                </fieldset>
                    </div>                
            </div>
            {if $PS_BUY_MEMBERSHIP}
                <div>
                    <label class="depoTitle page-subheading col-lg-12">{l s='DEPOSIT'}</label>
                    <div class="containerDepo">
                        <p>{l s="When you create a Fluz Fluz account, we ask that you "}<span class="stand_out">{l s="deposit a minimum of $20.000 in your account so we can validate it. "}</span>{l s="This is a firs time only required deposit and will be entirely at your disposal in your account so you can start."}</p>
                    <div class="row rangeSelect">
                        <span class="col-lg-2 rangePrice">$15.000</span><input class="rangeslider col-lg-8" type="range" id="rangeSlider" value="30000" min="15000" max="105000" step="15000" data-rangeslider><span class="col-lg-2 rangePrice">$105.000</span>
                    </div>
                    <div class="col-lg-12 col-md-12 finalDeposit">
                        <span class="col-lg-8 col-md-7" style="font-size:18px;">{l s="Final Deposit Amount:"}</span>
                        <div class="col-lg-4 col-md-5">
                            <span class="money">$</span>
                            <input class="output" type="text" name="valorSlider" id="valorSlider" value="" readonly />
                        </div>
                    </div>
                    </div>    
                </div>
                <div style="display: none">
                    <input type="hidden" id="psebank" name="psebank">
                    <input type="hidden" id="namebank" name="namebank">
                    <input type="hidden" id="psetypecustomer" name="psetypecustomer">
                    <input type="hidden" id="psetypedoc" name="psetypedoc">
                    <input type="hidden" id="psenumdoc" name="psenumdoc">
                </div>
                <div class="payublock">
                    {hook h="displayPayment"}
                </div>
            {/if}
                <div class="row">
                    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                        <div style="display:none;">{$HOOK_CREATE_ACCOUNT_FORM}</div>
                    <div class="formInfo submit clearfix">
			<input type="hidden" name="email_create" value="1" />
                        <div class="checkbox">
                                <input type="checkbox" name="acceptterms" id="acceptterms" value="1"/>
                                <label for="acceptterms">Acepto los t&eacute;rminos y condiciones de Fluz Fluz</label>
                        </div>
                        <button class="btnInfo" type="submit" name="submitAccount" id="submitAccount"/>
				<span>Registro Gratis<i class="icon-chevron-right right"></i></span>
			</button>
                    </div>
                    </div>
                </div>
        </div>
                                
        {literal}
        <script type="text/javascript">
            $(document).ready(function(){
                document.getElementById( 'valorSlider' ).value=15000 ;  
                
                $('#rangeSlider').change(function() 
                {
                    $('#valorSlider').val($(this).val());
                });
            });
        </script>
        {/literal}
	</form>
                       
{/if}
    
{strip}
{if isset($smarty.post.id_state) && $smarty.post.id_state}
	{addJsDef idSelectedState=$smarty.post.id_state|intval}
{elseif isset($address->id_state) && $address->id_state}
	{addJsDef idSelectedState=$address->id_state|intval}
{else}
	{addJsDef idSelectedState=false}
{/if}
{if isset($smarty.post.id_state_invoice) && isset($smarty.post.id_state_invoice) && $smarty.post.id_state_invoice}
	{addJsDef idSelectedStateInvoice=$smarty.post.id_state_invoice|intval}
{else}
	{addJsDef idSelectedStateInvoice=false}
{/if}
{if isset($smarty.post.id_country) && $smarty.post.id_country}
	{addJsDef idSelectedCountry=$smarty.post.id_country|intval}
{elseif isset($address->id_country) && $address->id_country}
	{addJsDef idSelectedCountry=$address->id_country|intval}
{else}
	{addJsDef idSelectedCountry=false}
{/if}
{if isset($smarty.post.id_country_invoice) && isset($smarty.post.id_country_invoice) && $smarty.post.id_country_invoice}
	{addJsDef idSelectedCountryInvoice=$smarty.post.id_country_invoice|intval}
{else}
	{addJsDef idSelectedCountryInvoice=false}
{/if}
{if isset($countries)}
	{addJsDef countries=$countries}
{/if}
{if isset($vatnumber_ajax_call) && $vatnumber_ajax_call}
	{addJsDef vatnumber_ajax_call=$vatnumber_ajax_call}
{/if}
{if isset($email_create) && $email_create}
	{addJsDef email_create=$email_create|boolval}
{else}
	{addJsDef email_create=false}
{/if}
{/strip}
    {if !$logged}
        <script>
            
            $( ".parentMenu span:last-child" ).click(function(e){
                var btn = $(this).html();
                if(btn == 'Comprar'){
                    var url = '{$url}';
                    e.preventDefault();
                    window.location.replace(url);
                }
            });
            
        </script>
    {/if}
{literal}
    <script>
            $('#numCard').on('keyup',function(){
                $(this).removeClass('visa');
                $(this).removeClass('mastercard');
                $(this).removeClass('amex');
                $(this).removeClass('discover');
                if($(this).val()===""){
                    $(this).removeClass('visa');
                }else{
                    $(this).validateCreditCard(function(result) {    
                        switch(result.card_type.name){
                            case 'visa':
                                $(this).addClass('visa');
                                break;
                            case 'mastercard':
                                $(this).addClass('mastercard');
                                break;
                             case 'amex':
                                $(this).addClass('amex');
                                break;
                             case 'discover':
                                $(this).addClass('discover');
                                break;
                            default:
                                $(this).removeClass('visa');
                                $(this).removeClass('mastercard');
                                $(this).removeClass('amex');
                                $(this).removeClass('discover');
                                break;
                        }
                    })
                }
            });
    </script>
    <script>
        $("#account-creation_form").validate();
    </script>
{/literal}

{literal}
    <style>
       h2{ font-size: 20px !important; 
        color: #505050;
        font-family: 'Open Sans' !important;
        text-transform: uppercase;
       }
       
       div.account_creation {
        padding-top:  20px;
        }

        .page-subheading {
        font-weight: 400;
        color: #505050;
        border:none;
        }

        .box {
        border: none;
        font-family: 'Open Sans' !important;
        }

        label {
        margin-top:10px;
        font-weight:400;
        }
    </style>
{/literal}
{literal}
     <script>
         var sn = $(".side-nav");
         var pos = sn.position();
            $(window).scroll(function() {
                var windowPos = $(window).scrollTop();
                if (windowPos >= pos.top - 5) {
                    sn.addClass("stick");
                } else {
                    sn.removeClass("stick");
                }
            });
    </script>
{/literal}
  
