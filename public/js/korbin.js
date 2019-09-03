/**
 * 
 * @authors Korbin 280674094@qq.com
 * @date    2019-03-06 09:46:13
 * @version $Id$
 */

$(function(){

	$('.defaultTab').click(function(){
	    $(this).addClass('active');
	    $('#menu-list a').not('.defaultTab').removeClass('active');
	    $('.defaultIframe').addClass('active');
	    $('.iframe-content').not('.defaultIframe').removeClass('active');

	});

	$('body').on('click','.closeOther',function(){
	    $('#menu-list').animate({"margin-left":"0"});
	    $('#menu-list a').not('.active,.defaultTab').remove();
	    $('.iframe-content').not('.active,.defaultIframe').remove();
	    
	});
	$('body').on('click','.closeAll',function(){
	    $('#menu-list').animate({"margin-left":"0"});
	    $('#menu-list a').not('.defaultTab').remove();
	    $('.defaultTab').click();
	    $('.iframe-content').not('.defaultIframe').remove();

	});	

	$(".kbin_tab .schtitle .pe").click(function(){
	    $(this).addClass('curr').siblings().removeClass('curr');
	    var index = $(this).index();
	    var ip = $(this).parents('.kbin_tab');	    
	    ip.find('.Orderment .tim').hide();
	    ip.find('.Orderment .tim:eq('+index+')').show();
	});	

	
// 	$("body").bind("keydown",function(event){  
//        if (event.keyCode == 116) {  
// 	         event.preventDefault(); //阻止默认刷新  
// 	         location=location;  
// 	     }    
// 	 }) ;

	$('.multLable em').click(function(){
		$(this).addClass('curr').siblings('em').removeClass('curr');
	})

	$(document).on('click','.submenu-left .col a',function(){
		var i = $(this);
		var indx = i.index();
		var windo = $(window.parent.document).find('#menu-list a');
		
	})

	$(".kbin_tab .tab_title li").click(function(){
	    $(this).addClass('curr').siblings().removeClass('curr');
	    var index = $(this).index();
	    var ip = $(this).parents('.kbin_tab')
	    number = index;
	    ip.find('.tab_content .tab_item').hide();
	    ip.find('.tab_content .tab_item:eq('+index+')').show();
	});

	$('.liergodic li').each(function(index,item){
		$(item).attr('id','sert' + index);
		$(item).attr('relid','kb' + index);
	});

	$('.outWindow .lip .chekid').each(function(index,item){
		$(item).attr('che-id',index);
	});



	$(document).on('click','.logisname span',function(){
		if($(this).hasClass('active')){
			$(this).removeClass('active')
		}else{
			$(this).addClass('active');
		}
	});

	$(document).on('click','.logis_toFro .logTo',function(){
		var o = $(this).parents('.appLogis');
		var listAct = o.find('.logis_list .active');
		var settle = o.find('.logis_settle .col');
		if(o.find('.logis_list span').hasClass('active')){
			listAct.appendTo(settle);
			settle.find('span').removeClass('active');			
		}
		

	});
	$(document).on('click','.logis_toFro .logfro',function(){
		var o = $(this).parents('.appLogis');
		var listAct = o.find('.logis_list .col');
		var settle = o.find('.logis_settle .active');
		if(o.find('.logis_settle span').hasClass('active')){
			settle.appendTo(listAct);
			listAct.find('span').removeClass('active');			
		}
		
	});

	



});


function pgout(url, value){     
    var page = $('<iframe class="iframe-content inlayerUrl active" data-url="'+url+'" data-value="'+value+'" src="'+url+'"></iframe>');
    var tab = $('<a href="javascript:void(0);" data-url="'+url+'" data-value="'+value+'" class="inlayer active">' + value + '<i class="menu-close"></i>' + '</a>');

    $(window.parent.document).find('#menu-list a').removeClass('active');
    tab.appendTo($(window.parent.document).find('#menu-list'));
    $(window.parent.document).find('#page-content').find('.iframe-content').removeClass('active');
    page.appendTo($(window.parent.document).find('#page-content'));

}
