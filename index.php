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
/*
if (!empty($_POST)){
    $pass=$_POST['hwsp_motech'];
    if (($pass=="Bowerytech2.")){
        setcookie('validar',1,time()+43200);
        header("location: /");
    }
}

if(!isset($_COOKIE['validar'])){
    echo'<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <title>Fluz Fluz</title>
        <style>
            body {margin:0px; background: url(/logo.png) !important;background-size: 230px 291px;background-position: center !important; background-color: rgba(255,0,0,0.85) !important; font-family:Arial; background-repeat: no-repeat !important;}
            #custom_messaging_banner {background: #eb583c;padding: 7px 10px;color: white;border-bottom: solid 3px white;font-size:16px;position:relative;z-index:1;}
            div#form_wrap {background-color: #d1a47f;}
            div#the_hint_wrap {background-color: #d1a47f;}
            button {background-color: #f61731 !important;text-transform: uppercase;}
            #form_wrap button {background-color: #f61731 !important;}
            div#custom_messaging_banner {
                font-size:13px;
                background-color:  #ff0525 !important;;
                font-family: open sans !important;
                color: #fff;
                border-bottom: none !important;
            }
            div#custom_messaging_banner {display:none;}
        </style>
        <style>
            html, body {
                    text-align: center;
                    height: 100%;
            }
            #form_wrap { 
                background-image: none;background-color: #1493d1;display: block;
                margin: 0px auto;
                height: 68px;
                width: 275px;
                position: relative;
                top: 0px;
                margin-top: 0px;margin-right: 100px;
            }
            #form_wrap input[type=text], .enter_password {background-image: none; background: white;position: absolute;
                top: 13px;
                left: 25px;
                border: 0px;
                width: 150px;
                padding-left: 11px;
                font-size: 15px;
                line-height: 15px;
                padding-top: 9px;
                height: 30px;
                color: rgb(85, 86, 90);
                opacity: .9;
                padding-right: 10px;}

						#form_wrap input:active, #form_wrap input:focus {outline:0;opacity:1;}
						#form_wrap button {background: none;background-color: #1caff6;;
border: 0px;
height: 25px;
position: absolute;
top: 22px;
left: auto;
right:12px;
cursor: pointer;
opacity: 1;color:white;font-weight:bold;
}
						#form_wrap button:hover {opacity:.9}
						#form_wrap button:focus, #form_wrap button:active { outline:0;}
						#form_wrap button:active { opacity:1;}
						#the_hint_wrap {
position: absolute;background: #1493d1;
top: 68px;
color: white;
left: 0px;
width: 225px;
font-weight: normal;
font-family: Arial;
text-align: left;
font-size: 11px;
overflow: hidden;
max-height: 25px;
padding:0px 25px;
padding-bottom:13px;
}
						#the_hint_wrap div {display:inline-block;vertical-align: top;}
						#the_hint_title {padding-right:5px;}
						#the_hint {width: 142px;}

</head>
					
    <!--[if IE]>
    <style>
    #form_wrap input[type=text], .enter_password {
      line-height:30px;
    }
    </style>
    <![endif]-->
<body>
    <div id="custom_messaging_banner">Sitio en Desarrollo.</div>
        <div id="form_wrap">
            <form method="post" action="/index.php">
                <input type="password" id="hwsp_motech" name="hwsp_motech" placeholder="Password" class="enter_password">
                <button type="submit">Entrar</button>
        </form>
    </div>
</body></html>';
}else{*/
    require(dirname(__FILE__).'/config/config.inc.php');
    Dispatcher::getInstance()->dispatch();
//}
