{*
* 2007-2015 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
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
    <a href="{$link->getPageLink('my-account', true)|escape:'html':'UTF-8'}">
        {l s='My account'}
    </a>
    <span class="navigation-pipe">
        {$navigationPipe}
    </span>
    <span class="navigation_page">
        {l s='Your personal information'}
    </span>
{/capture}

<div class="box">
    <div class="headinformation">
        <h1 class="titleInfo page-subheading">
            {l s='Your personal information'}
        </h1>
        <h1 class="deactivate">{l s='Deactivate account'}</h1>
    </div>

    {include file="$tpl_dir./errors.tpl"}

    {if (isset($confirmation) && $confirmation) || (isset($confirmationcard) && $confirmationcard) }
        <p class="alert alert-success">
            {l s='Your personal information has been successfully updated.'}
            {if isset($pwd_changed)}<br />{l s='Your password has been sent to your email:'} {$email}{/if}
        </p>
    {else}
        {*<p class="info-title">{l s='Please be sure to update your personal information if it has changed.'}</p>*}
        <div class="bodyinformation">
            <form action="{$link->getPageLink('identity', true)|escape:'html':'UTF-8'}" enctype="multipart/form-data" method="post" class="std">
                <div class="profile">
                    <h1 class="title">{l s='Profile'}</h1>
                    <h1 class="edit" id="editProfile">{l s='Edit'}</h1>
                    <div class="fieldInfo">
                        <p class="required requiredinfo"><sup>*</sup>{l s='Required field'}</p>
                        <div class="required form-group">
                            {if $imgprofile != ""}
                                <img src="{$imgprofile}">
                            {else}
                                <img src="{$img_dir}icon/profile.png">
                            {/if}
                        </div>
                        <div class="form-group block-profileimg">
                            <label for="profileimg">{l s="Change Image"}</label>
                            <input class="inputform enabled" type="file" disabled id="profileimg" name="profileimg"/>
                        </div>
                        <div class="clearfix">
                            <label>{l s='Social title'}:</label>
                            {foreach from=$genders key=k item=gender}
                                <div class="radio-inline">
                                    <label for="id_gender{$gender->id}" class="top">
                                    <input class="inputform enabled" type="radio" name="id_gender" id="id_gender{$gender->id}" value="{$gender->id|intval}" {if isset($smarty.post.id_gender) && $smarty.post.id_gender == $gender->id}checked="checked"{/if} />
                                    {$gender->name}</label>
                                </div>
                            {/foreach}
                        </div>
                        <div class="required form-group">
                            <label for="firstname" class="required">
                                {l s='First name'}:
                            </label>
                            <input class="is_required validate form-control inputform enabled" disabled data-validate="isName" type="text" id="firstname" name="firstname" value="{$smarty.post.firstname}" />
                        </div>
                        <div class="required form-group">
                            <label for="lastname" class="required">
                                {l s='Last name'}:
                            </label>
                            <input class="is_required validate form-control inputform enabled" disabled data-validate="isName" type="text" name="lastname" id="lastname" value="{$smarty.post.lastname}" />
                        </div>
                        <div class="required form-group">
                            <label for="email" class="required">
                                {l s='E-mail address'}:
                            </label>
                            <input class="is_required validate form-control inputform enabled" disabled data-validate="isEmail" type="email" name="email" id="email" value="{$smarty.post.email}" />
                        </div>
                        <div class="required form-group">
                            <label for="government" class="required">
                                {l s='Government Id #'}:
                            </label>
                            <input class="is_required validate form-control inputform enabled" disabled readonly data-validate="isDniLite" type="password" name="government" id="government" value="{$customerGovernment}" />
                        </div>
                        <div class="form-group dateBirth">
                            <label>
                                {l s='Date of Birth'}:
                            </label>
                            <div class="row dateBirthText">&nbsp;{$sl_day}/{$sl_month}/{$sl_year}</div>
                            <div class="row dateBirthInput">
                                <div class="col-xs-4">
                                    <select name="days" id="days" class="form-control inputform enabled" disabled>
                                        <option value="">-</option>
                                        {foreach from=$days item=v}
                                            <option value="{$v}" {if ($sl_day == $v)}selected="selected"{/if}>{$v}&nbsp;&nbsp;</option>
                                        {/foreach}
                                    </select>
                                </div>
                                <div class="col-xs-4">
                                    <select id="months" name="months" class="form-control inputform enabled" disabled>
                                        <option value="">-</option>
                                        {foreach from=$months key=k item=v}
                                            <option value="{$k}" {if ($sl_month == $k)}selected="selected"{/if}>{l s=$v}&nbsp;</option>
                                        {/foreach}
                                    </select>
                                </div>
                                <div class="col-xs-4">
                                    <select id="years" name="years" class="form-control inputform enabled" disabled>
                                        <option value="">-</option>
                                        {foreach from=$years item=v}
                                            <option value="{$v}" {if ($sl_year == $v)}selected="selected"{/if}>{$v}&nbsp;&nbsp;</option>
                                        {/foreach}
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="required form-group">
                            <label for="phone" class="required">
                                {l s='Phone number'}:
                            </label>
                            <input class="is_required validate form-control inputform enabled" disabled data-validate="isPhoneNumber" type="text" name="phone" id="phone" value="{$customerPhone}" />
                        </div>
                        <div class="required form-group">
                            <label for="old_passwd" class="required">
                                {l s='Current Password'}:
                            </label>
                            <input class="is_required validate form-control inputform enabled" disabled type="password" data-validate="isPasswd" name="old_passwd" id="old_passwd" value="*****" />
                        </div>
                        <div class="password form-group newPassword">
                            <label for="passwd">
                                {l s='New Password'}:
                            </label>
                            <input class="is_required validate form-control inputform enabled" disabled type="password" data-validate="isPasswd" name="passwd" id="passwd" />
                        </div>
                        <div class="password form-group newPassword">
                            <label for="confirmation">
                                {l s='Confirmation'}:
                            </label>
                            <input class="is_required validate form-control inputform enabled" disabled type="password" data-validate="isPasswd" name="confirmation" id="confirmation" />
                        </div>
                        {if isset($newsletter) && $newsletter}
                            <div class="checkbox">
                                <label for="newsletter">
                                    <input class="inputform enabled" type="checkbox" id="newsletter" name="newsletter" value="1" {if isset($smarty.post.newsletter) && $smarty.post.newsletter == 1} checked="checked"{/if}/>
                                    {l s='Sign up for our newsletter!'}
                                    {if isset($required_fields) && array_key_exists('newsletter', $field_required)}
                                      <sup> *</sup>
                                    {/if}
                                </label>
                            </div>
                        {/if}
                        {if isset($optin) && $optin}
                            <div class="checkbox">
                                <label for="optin">
                                    <input class="inputform enabled" type="checkbox" name="optin" id="optin" value="1" {if isset($smarty.post.optin) && $smarty.post.optin == 1} checked="checked"{/if}/>
                                    {l s='Receive special offers from our partners!'}
                                    {if isset($required_fields) && array_key_exists('optin', $field_required)}
                                      <sup> *</sup>
                                    {/if}
                                </label>
                            </div>
                        {/if}
                        {*if $b2b_enable}
                            <h1 class="page-subheading">
                                    {l s='Your company information'}
                            </h1>
                            <div class="form-group">
                                    <label for="">{l s='Company'}</label>
                                    <input type="text" class="form-control" id="company" name="company" value="{if isset($smarty.post.company)}{$smarty.post.company}{/if}" />
                            </div>
                            <div class="form-group">
                                <label for="siret">{l s='SIRET'}</label>
                                <input type="text" class="form-control" id="siret" name="siret" value="{if isset($smarty.post.siret)}{$smarty.post.siret}{/if}" />
                            </div>
                            <div class="form-group">
                                <label for="ape">{l s='APE'}</label>
                                <input type="text" class="form-control" id="ape" name="ape" value="{if isset($smarty.post.ape)}{$smarty.post.ape}{/if}" />
                            </div>
                            <div class="form-group">
                                <label for="website">{l s='Website'}</label>
                                <input type="text" class="form-control" id="website" name="website" value="{if isset($smarty.post.website)}{$smarty.post.website}{/if}" />
                            </div>
                        {/if*}
                        {if isset($HOOK_CUSTOMER_IDENTITY_FORM)}
                            {$HOOK_CUSTOMER_IDENTITY_FORM}
                        {/if}
                        <div class="formInfo form-group">
                            <button type="submit" name="submitIdentity" class="btnInfo">
                                <span>{l s='Save'}<i class="icon-briefcase right"></i></span>
                            </button>
                        </div>
                        <div class="formInfo form-group">
                            <button type="submit" name="submitDeactivate" class="btnDeactivate">
                                <span>{l s='Deactivate account'}<i class="icon-briefcase right"></i></span>
                            </button>
                        </div>
                    </div>
                </div>
            </form>
            <form action="{$link->getPageLink('identity', true)|escape:'html':'UTF-8'}" method="post" class="stdcard">
                <div class="payment">
                    <h1 class="title">{l s='Payment information'}</h1>
                    <h1 class="edit" id="editPayment">{l s='Edit'}</h1>
                    <div class="fieldInfo">
                        <p class="required requiredinfocard"><sup>*</sup>{l s='Required field'}</p>
                        <div class="form-group">
                            <label for="typecard">
                                {l s='Type'}:
                            </label>
                            <input class="form-control enabled" readonly data-validate="isName" type="text" id="typecard" name="typecard" value="{$card.name_creditCard}" />
                        </div>
                        <div class="required form-group">
                            <label for="numbercard" class="required">
                                {l s='Number'}:
                            </label>
                            <input class="is_required validate form-control inputformcard enabled" disabled data-validate="isCard" type="password" id="numbercard" name="numbercard" value="{$card.num_creditCard}" /><span class="card_digits">{$card_digits}</span>
                        </div>
                        <div class="form-group dateExpiration">
                            <label>
                                {l s='Expiration Date'}:
                            </label>
                            {assign var=dateData value="/"|explode:$card.date_expiration}
                            <div class="row dateBirthTextCard">&nbsp;{$card.date_expiration}</div>
                            <div class="row dateBirthInputCard">
                                <div class="col-xs-6">
                                    <select id="monthsCard" name="monthsCard" class="form-control inputformcard enabled" disabled>
                                        <option value="">-</option>
                                        {foreach from=$months key=k item=v}
                                            {if $k <= 9 }
                                                <option value="0{$k}" {if ($dateData.0 == $k)}selected="selected"{/if}>{l s=$v}&nbsp;</option>
                                            {else}
                                                <option value="{$k}" {if ($dateData.0 == $k)}selected="selected"{/if}>{l s=$v}&nbsp;</option>
                                            {/if}
                                        {/foreach}
                                    </select>
                                </div>
                                <div class="col-xs-6">
                                    {$year_select}
                                </div>
                            </div>
                        </div>
                        <div class="required form-group">
                            <label for="holdernamecard" class="required">
                                {l s='Cardholder Name'}:
                            </label>
                            <input class="is_required validate form-control inputformcard enabled" disabled data-validate="isName" type="text" id="holdernamecard" name="holdernamecard" value="{$card.nameOwner}" />
                        </div>
                        <div class="formInfo form-group">
                            <button type="submit" name="submitCard" class="btnCard">
                                <span>{l s='Save'}<i class="icon-briefcase right"></i></span>
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    {/if}
</div>
<ul class="footer_links clearfix">
    <li>
        <a class="btn btn-default button button-small" href="{$link->getPageLink('my-account', true)}">
            <span>
                <i class="icon-chevron-left"></i>{l s='Back to your account'}
            </span>
        </a>
    </li>
    <li>
        <a class="btn btn-default button button-small" href="{if isset($force_ssl) && $force_ssl}{$base_dir_ssl}{else}{$base_dir}{/if}">
            <span>
                <i class="icon-chevron-left"></i>{l s='Home'}
            </span>
        </a>
    </li>
</ul>

{literal}
    <script>
        $(".deactivate").click(function(){
            if ( confirm("Desea desactivar su cuenta?") ) {
                $(".btnDeactivate").click();
            }
        });
        $('#editProfile').click(function(){
            $(".inputform").is(":disabled") ? $('#editProfile').html("Cancel") : $('#editProfile').html("Edit");
            $(".inputform").is(":disabled") ? $(".inputform").removeClass("enabled") : $(".inputform").addClass("enabled");
            $(".inputform").is(":disabled") ? true : $(".inputform").parent().removeClass("form-ok form-error");
            $(".inputform").is(":disabled") ? $(".btnInfo").css('display', "block") : $(".btnInfo").css('display', "none");
            $(".inputform").is(":disabled") ? $(".requiredinfo").css('display', "block") : $(".requiredinfo").css('display', "none");
            $(".inputform").is(":disabled") ? $("#government").prop("type", "text") : $("#government").prop("type", "password");
            $(".inputform").is(":disabled") ? $(".newPassword").css('display', "block") : $(".newPassword").css('display', "none");
            $(".inputform").is(":disabled") ? $(".block-profileimg").css('display', "block") : $(".block-profileimg").css('display', "none");
            $(".inputform").is(":disabled") ? $(".dateBirthText").css('display', "none") : $(".dateBirthText").css('display', "block");
            $(".inputform").is(":disabled") ? $(".dateBirthInput").css('display', "block") : $(".dateBirthInput").css('display', "none");
            $(".inputform").is(":disabled") ? $(".inputform").removeAttr('disabled') : $(".inputform").attr('disabled', 'disabled');
            $(".checker").removeClass('disabled');
            $('.std')[0].reset();
            $(".inputform").is(":disabled") ? $("#old_passwd").val('*****') : $("#old_passwd").val('');
        });
        $('#editPayment').click(function(){
            $(".inputformcard").is(":disabled") ? $('#editPayment').html("Cancel") : $('#editPayment').html("Edit");
            $(".inputformcard").is(":disabled") ? $(".inputformcard").removeClass("enabled") : $(".inputformcard").addClass("enabled");
            $(".inputformcard").is(":disabled") ? true : $(".inputformcard").parent().removeClass("form-ok form-error");
            $(".inputformcard").is(":disabled") ? $(".btnCard").css('display', "block") : $(".btnCard").css('display', "none");
            $(".inputformcard").is(":disabled") ? $(".requiredinfocard").css('display', "block") : $(".requiredinfocard").css('display', "none");
            $(".inputformcard").is(":disabled") ? $(".dateBirthTextCard").css('display', "none") : $(".dateBirthTextCard").css('display', "block");
            $(".inputformcard").is(":disabled") ? $(".dateBirthInputCard").css('display', "block") : $(".dateBirthInputCard").css('display', "none");
            $(".inputformcard").is(":disabled") ? $("#numbercard").prop("type", "text") : $("#numbercard").prop("type", "password");
            $(".inputformcard").is(":disabled") ? $(".card_digits").css("display", "none") : $(".card_digits").css("display", "inline-block");
            $(".inputformcard").is(":disabled") ? $("#numbercard").css("width", "271px") : $("#numbercard").css("width", "100px");
            $(".inputformcard").is(":disabled") ? $(".inputformcard").removeAttr('disabled') : $(".inputformcard").attr('disabled', 'disabled');
            $('.stdcard')[0].reset();
        });
    </script>
{/literal}
<script src="{$js_dir}jquery.creditCardValidator.js"></script>
{literal}
    <script>
        $(function() {
            $("#numbercard").validateCreditCard(function(result) {
                switch ( result.card_type.name ) {
                    case 'visa':
                        $("#typecard").addClass('visa');
                        $("#typecard").val("visa");
                        break;
                    case 'mastercard':
                        $("#typecard").addClass('mastercard');
                        $("#typecard").val("mastercard");
                        break;
                    case 'amex':
                        $("#typecard").addClass('amex');
                        $("#typecard").val("amex");
                        break;
                    case 'discover':
                        $("#typecard").addClass('discover');
                        $("#typecard").val("discover");
                        break;
                    default:
                        $("#typecard").removeClass('visa');
                        $("#typecard").removeClass('mastercard');
                        $("#typecard").removeClass('amex');
                        $("#typecard").removeClass('discover');
                        $("#typecard").val("");
                        break;
                }
            });
        });
        
        $('#numbercard').on('keyup',function(){
            $("#typecard").removeClass('visa');
            $("#typecard").removeClass('mastercard');
            $("#typecard").removeClass('amex');
            $("#typecard").removeClass('discover');
            $("#typecard").val("");
            if ( $(this).val() === "" ) {
                $("#typecard").removeClass('visa');
                $("#typecard").val("");
            } else {
                $(this).validateCreditCard(function(result) {
                    switch ( result.card_type.name ) {
                        case 'visa':
                            $("#typecard").addClass('visa');
                            $("#typecard").val("visa");
                            break;
                        case 'mastercard':
                            $("#typecard").addClass('mastercard');
                            $("#typecard").val("mastercard");
                            break;
                        case 'amex':
                            $("#typecard").addClass('amex');
                            $("#typecard").val("amex");
                            break;
                        case 'discover':
                            $("#typecard").addClass('discover');
                            $("#typecard").val("discover");
                            break;
                        default:
                            $("#typecard").removeClass('visa');
                            $("#typecard").removeClass('mastercard');
                            $("#typecard").removeClass('amex');
                            $("#typecard").removeClass('discover');
                            $("#typecard").val("");
                            break;
                    }
                });
            }
        });
    </script>
{/literal}