/**
 * JavaScript Document JS消息国际化，不同语言环境对应不同提示语言
 * @author FANGYF
 * 2012-11-29 
 */
/*
 * 提示信息Json对象
 */
var $locale_message = {};

/**
 * 1.拿取页尾语言种类
 */
if(typeof(locale_message_language) == 'undefined'){
	locale_message_language='';
}

var locale_language = locale_message_language;

/**
 * 2.判断语言种类，为加载提示信息做准备
 */
$locale_message = getLanguage(locale_language);

/**
 * 测试提示信息能否取出
 */
function testMessage(){
	var tmp1 = $.getMessage("test",["这里用一个数组就行了","猴子不一定是大圣"]);
	var tmp2 = $.getMessage("test");
	alert(tmp2);
}

/**
 * 获得语言环境配置的加载路径
 * @param locale_language
 * @returns
 */
function getLanguage(locale_language){
	//语言环境配置前缀标识
	var oms_lang = "lang-";
	//已有的语言环境配置后缀
	var arrLanguage = ["en","zh_CN","zh_TW","zh-CN","en-US"];
	/*
	 * 查看是否存在于已有的配置中，-1表示没有找到
	 */
	var index = $.inArray(locale_language,arrLanguage);
	var bol = (index == -1)?false:true;
	//检查哪些特殊的,比如"zh"、"zh_SG"、"zh_Hk"、"en_US"、"en_CA"等
	var languageEN = "en";
	var languageZH = "zh";
	if(bol){
		if(index == 0 || index == 4){
			//存在en，使用英文语言环境
			return $locale_message_en;
		}else if(index == 1 || index == 3){
			//等于zh，使用中文简体语言环境
			return $locale_message_cn;
		}else if(index == 2){
			//存在zh，使用中文繁体语言环境
			return $locale_message_tw;
		}
	}else{
		if(languageZH == locale_language){
			//等于zh，使用中文简体语言环境
			return $locale_message_cn;
		}else if(locale_language.indexOf(languageZH) != -1){
			//存在zh，使用中文繁体语言环境
			return $locale_message_tw;
		}else if(locale_language.indexOf(languageEN) != -1){
			//存在en，使用英文语言环境
			return $locale_message_en;
		}else{
			//为空或匹配不到时，使用中文
			return $locale_message_cn;
		}
	}
}


/**
 * 替换字符串中的占位符
 * @param str	可能还有占位符的字符串
 * @param array	替换占位符的数组
 * @returns String
 * 调用方式：
 * var str = "表情{0}，表情{1}，表情{2}";
 * var ary = ['灭哈哈哈','呜呜呜','哈哈哈哈'];
 * var tmp = formatStr(str ,ary));
 */
$.formatStr = function (){

	//传入数组为空时直接返回
	if(!arguments[1]){
		return arguments[0];
	}
	
	//存放替换占位符的数组
	var ary = [];
	for(i = 0 ; i < arguments[1].length ; i++){
		ary.push(arguments[1][i]);
	}

	return arguments[0].replace(/\{(\d+)\}/g,function(m ,i){
		//对于溢出的占位符，替换为空字符串
		return (ary[i])?ary[i]:"";
	});
};

/**
 * 查找指定的提示信息
 */
$.getMessage = function(){
	
	//指定的消息的key
	var key = arguments[0];
	key = (key)?$.trim(key):key;
	
	//对应占位符的参数
	var param = arguments[1];
	
	if(!$locale_message){
		return "提示信息加载异常,请联系客服！";
	}else{
		var bol;
		for(var i = 0; i < $locale_message.length; i++) {
			bol = ($locale_message[i].key === key)?true:false;
			if(bol){
				return $.formatStr($locale_message[i].value,param);
			}
		}
	}
	//找不到指定的提示信息,返回异常信息
	return key;
	//return $.getMessage("sys_message_error");
	
};

/**
 * 是否为英文环境
 * @returns {Boolean}
 */
function isEnLocale(){
	var locale = $.getMessage("sys_web_locale");
	if(locale == 'en'){
		return true;
	}
	return false;
}

/**
 *切换语言
 */
function changeLan(res) {
	var language = $("select.language").val();
	if(res == ''){
        window.location.href = '?language='+language;
	}else{
        window.location.href = '?language='+language+'&content='+res;
	}

}

