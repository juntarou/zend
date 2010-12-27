// httpxml.js

// constants
var base_url = "http://logico.lab.in.nttpc.co.jp/php_front/";
var domainSearchAction = "http://logico.lab.in.nttpc.co.jp/php_front/signup/sdomain/format/json"; 

// window.onload
window.onload = function() {
    Event.observe("searchBt","click", xmlSearchDomain);
}

// call back functions
function xmlSearchDomain() {
    var searchDomainWord = $("domainName").getValue();
    var checkBoxFlag = false;
    var checkBoxValus = '';
    
    Form.getInputs("domainSearch", "checkbox").each(function(obj){
	if (obj.checked == true) {
	    if (checkBoxValus == '') {
		checkBoxValus = obj.name + "=" + obj.value;
	    } else {
		checkBoxValus += "&" + obj.name + "=" + obj.value;
	    }
	    checkBoxFlag = true;
	}
    });
    var errorMessage = "";
    if (searchDomainWord == "" || searchDomainWord == null) {
	errorMessage = "<p>ご希望のドメイン名を入力して下さい</p>";
	//$("responseDomain").innerHTML = "ご希望のドメイン名を入力して下さい";
    } 
    if (checkBoxFlag == false) {
	errorMessage += "<p>ご希望のドメインを選択して下さい</p>";
	//$("responseDomain").innerHTML = "ご希望のドメインを選択して下さい";
    } 
    if (errorMessage == ""){
	new Ajax.Request(domainSearchAction, {
	    method: "post",
	    asynchronous: true,
	    //postBody: checkBoxValus,
	    postBody: Form.serialize("domainSearch"),
	    //requestHeaders: "application/json",
	    onComplete: function (httpObj){
		createLink(httpObj);
	    },
	    onFailure:function (httpObj) {
		alert("http error");
	    }
	});
    } else {
	$("responseDomain").innerHTML = errorMessage;
	errorMessage = "";
    }
}

function createLink(httpObj)
{
    var response = eval("("+httpObj.responseText+")");
    var appendHtml = "<ul>";
    response.json.each(function(obj) {
	var enableStr = (obj.enable == 1) ? "○ 取得できます" : "× 取得できません";
	appendHtml += "<li><a href="+ base_url + "signup/index?id=" + obj.token + "&enable=" + obj.enable +  ">" + obj.url + "</a>" + enableStr + "</li>";
    });
    appendHtml += "</ul>";
    $("responseDomain").innerHTML = appendHtml;
}
