// register.js

window.onload = function() {
    inputClass = new inputClass();
    inputClass.startEvent();
}

// リロード時の非同期通信
// 入力済みのフィールドの記憶用
window.onunload = function(){

    var bodyId = $$("body")[0].id;
    var actionUrl = bodyId.gsub("_","/");
    new Ajax.Request("http://logico.lab.in.nttpc.co.jp/php_front/" + actionUrl,
    {
        method: "post",
        asynchronous: true,
        postBody: Form.serialize($("stForm")) + "&reFlag=1",
        onSuccess: function(httpObj) {
            return;
        }.bind(this)
    });
}

function changeAppendTag(event) {
    //s inputClass = new inputClass();
    inputClass.FormSelectEvent(event);
}

inputClass = Class.create();

inputClass.prototype = {

    // ラベルを格納
    label: new Array(),

    // バリデーションルールを格納
    rules: new Array(),

    // クラス属性input を格納するプロパティ
    inputTags: new Array(),

    // ボタンアクティブ　フラグ
    validAllowFlags: new Array(),

    // スライダークラス要素
    allActionClass: null,

    // スライダークラス要素数
    actionlength: 0,

    targetElement: null,

    // イベントオブジェクト
    eventObj: null,

    // イベントフラグ
    eventFlag: false,

    // テンプレートエリアオブジェクト
    tempAreas: new Array(),

    // バリデーションインスタンス
    validInstanse: null,

    // フォームタグエレメント
    formElement: new Array(),

    // actionURL
    actionUrl: null,

    // baseUrl
    baseUrl: "http://logico.lab.in.nttpc.co.jp/php_front/",
    
    // eval済みのXHRオブジェクト
    responseJson: null,

    currentChangeTemplate: null,

    // コンストラクタ
    initialize: function() {

        // validation formElement init
        this.formElement = $("stForm");

        this.setActionUrl();

        // this page all input tags
        if (this.inputTags.length == 0) {
            this.inputTags = $$(".inputs");
        }

        // set label array
        if (this.label.length == 0) {
            this.setLabels();
        }

        // set Validation rules
        if (this.rules.length == 0) {
            this.setValidRules();
        }

        // side menu slide init
        if ($("side_menu")) {
            this.allActionClass = $$(".action");
            this.actionlength = this.allActionClass.length;
        }

    },

    startEvent: function() {
        if (this.eventFlag == false) {
            this.eventObserves();
        }
    },

    eventObserves: function() {

        this.eventFlag = true;

        if (this.actionlength > 0) {
            // slide event start
            this.allActionClass.each(function(obj){
                Event.observe(obj,"click",this.action.bindAsEventListener(this));
            }.bind(this));
        }

        // validaton event start
        this.inputTags.each(function(obj){
            if (obj.tagName == "SELECT") {
                Event.observe(obj,"change", this.FormSelectEvent.bindAsEventListener(this));
                return;
            }
            if (obj.tagName == "INPUT") {
                Event.observe(obj,"blur", this.FormInputEvent.bindAsEventListener(this));
                return;
            }

        }.bind(this));


        this.setInitializeLoad();
    },

    setInitializeLoad: function(){

        this.tempAreas.each(function(obj){
           if (obj.className.indexOf("staticTable") == -1) {
                Element.setStyle(obj,{display: "none"});
           }
        });

        this.ajaxLoad();

    },

    ajaxLoad: function() {

        new Ajax.Request(this.baseUrl + this.actionUrl,
        {
            method: "post",
            asynchronous: true,
            postBody: Form.serialize(this.formElement.id),
            onSuccess: function(httpObj) {
               this.responseJson = eval("("+httpObj.responseText+")");
               if (this.responseJson.post != null) {
                   this.loadAppendSelectBox();
               }
               this.changeText();
               this.setFormsValue();
               this.changeTemplate();
               if (this.responseJson.post != null) {
                   this.allValidationCheck();
               }
               this.checkAllowFlag();
            }.bind(this)
        });

    },

    ajaxChageLoad: function() {

        new Ajax.Request(this.baseUrl + this.actionUrl,
        {
            method: "post",
            asynchronous: true,
            postBody: Form.serialize(this.formElement.id) + "&reFlag=1",
            onSuccess: function(httpObj) {
               this.responseJson = eval("("+httpObj.responseText+")");
               //this.currentChangeTemplate = this.responseJson.post.template;
               // changeText
               if (this.responseJson.changeText) {
                   this.changeText();
               }
               this.setFormsValue();
               this.changeTemplate();
               this.allValidationCheck();
               this.checkAllowFlag();
            }.bind(this)
        });

    },


    setFormsValue: function() {

        var json = this.responseJson.post;
        for (keys in json) {
            if ($(keys)) {
                var elem = $(keys);
                switch (elem.tagName){
                    case "INPUT" :
                        elem.value = json[keys];
                        break;
                    case "SELECT" :
                        var index = 0;
                        tmpArray = new Array();
                        resPonseIndex = String(json[keys]);
                        if (resPonseIndex.indexOf(':') != -1) {
                            tmpArray = json[keys].split(':');
                            index = tmpArray[0];
                        } else {
                            for (var i=0; i < elem.options.length; i++) {
                                if (elem.options[i].value == resPonseIndex) {
                                    index = i;
                                    break;
                                }
                            }
                            // exception
                            if (!index) index = 0;
                        }
                        elem.selectedIndex = index;
                        break;
                    default:
                        break;
                }

            }
        }

    },

    changeText: function() {
        var resText = this.responseJson.changeText;
        var mixText = null;
        for (key in resText) {
            if (mixText == null) {
                mixText = resText[key];
            } else {
                mixText += "&nbsp;" + resText[key];
            }
        }
        Element.update("changeText",mixText);
    },

    changeTemplate: function() {

        // テンプレート切り替え
        // 一旦テンプレートを全て非表示
        this.tempAreas.each(function(temp){
            if (temp.className.indexOf("staticTable") == -1) {
                Element.setStyle(temp,{display: "none"});
            }
        });

        if (this.responseJson.post == null) {
            var defaults = ($$(".default"));
            defaults.each(function(obj) {
                //if (!$(obj.id).visible) {
                Element.setStyle($(obj.id),{display: "block"});
                //}
            });
            return;
        }

        var template = this.responseJson.post.template;

        template = String(template);
        temps = template.split(',');

        if (temps instanceof Array) {

            temps.each(function(val){
                var element = $(val);
                if (!Element.visible(element)) {
                    Element.setStyle(element,{display: "block"});
                }
            });
        } else {
            var element = $(temps);
            if (!Element.visible(temps)) {
                Element.setStyle(element,{display: "block"});
            }
        }
    },

    setActionUrl: function() {
        // bodyタグからajax通信時のurlを取得
        var bodyId = $$("body")[0].id;
        this.actionUrl = bodyId.gsub("_","/");
    },

    FormSelectEvent: function(event) {

        var resFlag = false;
        var tmpAreaId = null;
        this.eventObj = Event.element(event);

        // read validation class
        this.validInstanse = new ValidateClass(this.eventObj);

        this.tempAreas.each(function(obj) {
            if (Element.childOf(this.eventObj,obj)) {
                tmpAreaId = obj.id;
                resFlag = this.validInstanse.factory(this.rules[this.eventObj.id],this.label[obj.id][this.eventObj.id],false);
            }
        }.bind(this));

        if (resFlag) {
            this.validAllowFlags[tmpAreaId][this.eventObj.id] = true;
        } else {
            this.validAllowFlags[tmpAreaId][this.eventObj.id] = false;
        }

        // 特殊セレクトボックス
        if (this.eventObj.hasClassName("pullDownAction")) {

            this.ajaxChageLoad();
        }
        
        if (this.eventObj.hasClassName("tagAppend")) {
            // タグ生成
            this.appendSelectBox(tmpAreaId);
        }
        this.checkAllowFlag();
        this.eventFlag = false;

    },

    FormInputEvent: function(event) {

        var resFlag = false;
        var tmpAreaId = null;
        this.eventObj = Event.element(event);

        // read validation class
        this.validInstanse = new ValidateClass(this.eventObj);

        this.tempAreas.each(function(obj) {
            if (Element.childOf(this.eventObj,obj)) {
                tmpAreaId = obj.id;
                resFlag = this.validInstanse.factory(this.rules[this.eventObj.id],this.label[obj.id][this.eventObj.id],false);
            }
        }.bind(this));


        if (resFlag) {
            this.validAllowFlags[tmpAreaId][this.eventObj.id] = true;
        } else {
            this.validAllowFlags[tmpAreaId][this.eventObj.id] = false;
        }
        this.checkAllowFlag();
        this.eventFlag = false;
    },

    // 確認ボタンの切り替え　アクティブ:非アクティブ
    checkAllowFlag: function()
    {
        $('submitBtn').enable();

        this.tempAreas.each(function(obj) {
            if (Element.visible(obj.id)) {
            //if (template.indexOf(obj.id) != -1 || obj.className.indexOf("staticTable") != -1) {
                for(keys in this.validAllowFlags[obj.id]) {
                    //alert(keys + ":" + this.validAllowFlags[obj.id][keys]);
                    if (!$(keys)) break;
                    if (!this.validAllowFlags[obj.id][keys]) {
                        $('submitBtn').disable();
                        return;
                    }
                }
            }
        }.bind(this));

    },

    allValidationCheck: function() {

        var template = this.responseJson.post.template;
        template = String(template);
        this.tempAreas.each(function(obj) {
            if (Element.visible(obj.id)) {
                for(keys in this.label[obj.id]) {
                    // read validation class
                    if (!$(keys)) break;
                    this.validInstanse = new ValidateClass($(keys));
                    this.validAllowFlags[obj.id][keys] = this.validInstanse.factory(this.rules[keys],this.label[obj.id][keys],true);
                }
            }
        }.bind(this));

    },

    loadAppendSelectBox: function() {

        var Month = 0;
        if (this.responseJson.post.creditCardExpMonth) {
            Month = this.responseJson.post.creditCardExpMonth;
        }

        this.rules['creditCardExpMonth'] = "validNotEmpty";
        this.label['payment_10012']['creditCardExpMonth'] = this.label['payment_10012']['creditCardExpYear']

        var creditExpMonthTag = "<select name='creditCardExpMonth' id='creditCardExpMonth' class='inputs validNotEmpty' onChange='changeAppendTag(event)'>";

        if (Month) {
            this.validAllowFlags['payment_10012']['creditCardExpMonth'] = true;
            creditExpMonthTag += "<option value=''>選択して下さい</option>";
        } else {
            this.validAllowFlags['payment_10012']['creditCardExpMonth'] = false;
            creditExpMonthTag += "<option value='' selected='selected'>選択して下さい</option>";
        }


        var value = this.responseJson.post.creditCardExpYear;

        var spValue = value.split(':');

        var count = this.getCurrentMonth(spValue[1]);
        do{

            creditExpMonthTag += "<option value=" + count + ">" + count + "月</option>";
            count += 1;

        }while(count != 13);

        creditExpMonthTag += "</select>";


        if ($('creditCardExpMonth')) {
            Element.remove('creditCardExpMonth');
        }

        new Insertion.After("creditCardExpYear",creditExpMonthTag);

    },

    appendSelectBox: function(tmpAreaId) {

        var value = this.eventObj.getValue();

        var monthId = this.eventObj.id.sub("Year","Month");

        if (!value) {
            if ($(monthId)) {
                Element.remove(monthId);
            }
            return;
        }

        this.rules[monthId] = "validNotEmpty";

        this.validAllowFlags[tmpAreaId][monthId] = false;

        this.label[tmpAreaId][monthId] = this.label[tmpAreaId][this.eventObj.id];

        var spValue = value.split(":");
        var current = this.getCurrentMonth(spValue[1]);

        var creditExpMonthTag = "<select name=" + monthId + " id=" + monthId + " class='inputs validNotEmpty' onChange='changeAppendTag(event)'>";

        creditExpMonthTag += "<option value='' selected='selected'>選択して下さい</option>";

        //this.validAllowFlags[]

        do{

            creditExpMonthTag += "<option value=" + current + ">" + current + "月</option>";
            current += 1;

        }while(current != 13);

        creditExpMonthTag += "</select>";


        if ($(monthId)) {
            Element.remove(monthId);
        }

        new Insertion.After("creditCardExpYearError",creditExpMonthTag);

    },

    getCurrentMonth: function(value) {
        dateObj = new Date();
        var current = 1;
        if (parseInt(value) == parseInt(dateObj.getFullYear())) {
            current = dateObj.getMonth() + 1;
        }
        return current;
    },

    // ラベル,許可フラグをセット
    setLabels: function()
    {

        var labels = $$("label");
        var elements = $$("div","table");

        elements.each(function(obj){

            if (Element.hasClassName(obj,"registTable")) {
                this.tempAreas.push(obj);
                this.label[obj.id] = new Array();
                this.validAllowFlags[obj.id] = new Array();

                labels.each(function(obj2){
                    if (Element.childOf(obj2,obj.id)) {
                        this.label[obj.id][obj2.htmlFor] = obj2.firstChild.nodeValue;
                        this.validAllowFlags[obj.id][obj2.htmlFor] = false;
                    }
                }.bind(this));

            }
        }.bind(this));

    },

    // バリデーションルールをセット
    setValidRules: function() {

        var tmpRules = null;

        this.inputTags.each(function(obj) {
            tmpRules = obj.className.split(" ");
            result = tmpRules.grep(/valid/i,function(value,index) {
                //return value.substring(5);
                return value
            });

            this.rules[obj.id] = result;

        }.bind(this));

    },

    // side menu toggle controll
    action: function(e) {
        this.targetElement = Event.element(e);
        var node = this.targetElement;
        count = 0;
        // 該当クラスが見当たらず無限ループしそうになったら、
        // カウント20で強制終了する
        do {
            count++;
            node = node.nextSibling;
        }while(node.className != "disp" || count > 20);

        try {

            if (!node) {
                throw "disp class not found";
            }

            clearInterval(node.timer);

            if (Element.visible(node)) {
                node.style.height = "auto";
                node.style.overflow = "hidden";
                node.timer = setInterval(function() {
                    this.effectUp(node)
                    }.bind(this),10);
            } else {
                if (node.maxh && node.maxh <= node.offsetHeight) {
                    return;
                }
                node.style.display = "block";
                node.style.height = "auto";
                node.maxh = node.offsetHeight;
                node.style.height = 0 + "px";
                node.timer = setInterval(function() {
                    this.effectDown(node)
                    }.bind(this),10);
            }
        } catch (er) {
            alert(er);
        }

        Event.stop(e);
    },

    effectUp: function(node) {
        var clunt = node.offsetHeight;
        var dist = (Math.round(clunt / 10));
        dist = (dist <= 1) ? 1 : dist;
        node.style.height = clunt - dist + 'px';
        if (clunt < 2) {
            node.style.display = "none";
            clearInterval(node.timer);
        }
    },

    effectDown: function(node) {
        var clunt = node.offsetHeight;
        var dist = (Math.round((node.maxh - clunt) / 10));
        dist = (dist <= 1) ? 1 : dist;
        node.style.height = clunt + dist + 'px';
        if (clunt > (node.maxh - 2)) {
            node.style.display = "block";
            clearInterval(node.timer);
        }
    }

}