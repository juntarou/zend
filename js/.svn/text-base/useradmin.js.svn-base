// useradmin.js

// window.onload
window.onload = function() {
    adminClass = new adminClass('action','disp');
}

adminClass = Class.create();

// validation method
/*
 * 使用例　　field : [rule1,rule2,rule3...]
 * NotEmpty : 未入力禁止
 * Digit    : 数字のみ許可
 * max:num  : 最大文字数
 * email    : メールフォーマット
 * twoByte  : 全角のみ許可
 */
var validationRules = {
    'mailidinput' : {
        mailid: ["NotEmpty"],
        password: ["NotEmpty"]
    }
};

var lavel = {
    "mailid": "MailID",
    "password": "メールパスワード"
};

var validAllowFlags = {
    "mailidinput" : {
        mailid: null,
        password: null
    }
};

adminClass.prototype = {

    allActionClass: null,

    actionLength: 0,

    targetElement: null,

    childElement: null,

    validInstanse: null,

    slideMaxHeight: 0,

    eventEndFlag: false,

    formClasses: null,

    formElement: null,

    // init useradmin
    initialize: function(action,disp) {

        // side menu slide init
        this.allActionClass = $$("." + action);
        this.actionlength = this.allActionClass.length;

        // validation formElement init
        this.formElement = document.getElementsByTagName("form");

        // event controll method run
        this.eventObserves();
    },

    eventObserves: function() {
        // slide event start
        this.allActionClass.each(function(obj){
            Event.observe(obj,"click",this.action.bindAsEventListener(this));
        }.bind(this));

        // validaton event start
        //フォームの入力項目タグを全て取得
        this.formClasses = $$(".inputs");
        this.formClasses.each(function(obj){
            if (obj.tagName == "SELECT") {
                Event.observe(obj,"change", this.FormSelectEvent.bindAsEventListener(this));
                return;
            }
            if (obj.tagName == "INPUT") {
                Event.observe(obj,"blur", this.FormInputEvent.bindAsEventListener(this));
                return;
            }

        }.bind(this));
    },

    FormSelectEvent: function(e) {
        var resFlag = false;
        var eObj = Event.element(e);

        // read validation class
        this.validInstanse = new ValidateClass(eObj);
        
        resFlag = this.validInstanse.factory(validationRules[this.formElement.id][eObj.id],lavel).bind(this);

        if (resFlag) {
            validAllowFlags[eventObj.name] = true;
        } else {
            validAllowFlags[eventObj.name] = false;
        }
    },

    FormInputEvent: function(e) {
        var resFlag = false;
        var eObj = Event.element(e);

        // read validation class
        this.validInstanse = new ValidateClass(eObj,true);

        resFlag = this.validInstanse.factory(validationRules[this.formElement[0].id][eObj.id],lavel);

        if (resFlag) {
            validAllowFlags[eObj.name] = true;
        } else {
            validAllowFlags[eObj.name] = false;
        }
    },

    ajaxRequest: function(actionName) {

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
};