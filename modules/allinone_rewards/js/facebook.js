/**
 * All-in-one Rewards Module
 *
 * @category  Prestashop
 * @category  Module
 * @author    Yann BONNAILLIE - ByWEB
 * @copyright 2012-2015 Yann BONNAILLIE - ByWEB (http://www.prestaplugins.com)
 * @license   Commercial license see license.txt
 * Support by mail  : contact@prestaplugins.com
 * Support on forum : Patanock
 * Support on Skype : Patanock13
 */

jQuery(function($){
	if (window.location.href.indexOf('http://')===0) {
		url_allinone_facebook = url_allinone_facebook.replace('https://','http://');
    } else {
		url_allinone_facebook = url_allinone_facebook.replace('http://','https://');
    }
	FB.Event.subscribe('edge.create', function(response){
		likeFB(response, 1);
	});
	FB.Event.subscribe('edge.remove', function(response){
		likeFB(response, 0);
	});
});

function likeFB(url, like) {
	$.ajax({
		type: 'POST',
		url: url_allinone_facebook,
		async: true,
		cache: false,
		data: 'url=' + url + '&like=' + like,
		dataType: 'json',
		success: function(obj)	{
			if (obj && obj.result == 1) {
				if (obj.code) {
					$('#rewards_facebook_code').html(obj.code);
					if (typeof(ajaxCart) != 'undefined')
						ajaxCart.refresh();
				}
				$.fancybox({
					content	: $('#rewards_facebook_confirm').html(),
					// before presta 1.5.5.0
					enableEscapeButton: false,
					autoDimensions: true,
					hideOnContentClick: false,
					hideOnOverlayClick: false,
					titleShow: false,
					// since presta 1.5.5.0
					minHeight : 20,
					helpers : {
				        overlay : {
				            locked : true,
				            closeClick : false,
				        }
				    }
				});
				//$('.reward_facebook_block').hide();
			}
		}
	});
}