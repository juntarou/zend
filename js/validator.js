
ValidateClass = Class.create();

ValidateClass.prototype = {

    initialize: function(element,flag){
        if (element != null){
            this.element = element;
        }
        this.displayType = flag;
    },

    rules: null,

    rule: null,

    labels: null,

    param : null,

    tempFlag : true,

    element : null,

    messageTmplate: {
        "validNotEmpty": "#{field}は必須項目です。",
        "validDigit": "#{field}は数字で入力して下さい",
        "validMax" : "#{field}は#{parm}文字以下で入力して下さい",
        "validMin" : "#{field}は#[parm]文字以上で入力して下さい",
        "validEqLength" : "#{field}の文字数が不正です",
        "validMail" : "#{field}の形式が正しくありません",
        "validPhone" : "#{field}の形式が正しくありません"
    },

    factory: function(rules,label,allCheckFlag) {

        //alert(label + ":" + rules);
        this.rules = rules;
        this.labels = label;
        if (this.rules instanceof Array) {
            if (this.rules.length > 1) {
                for(var i=0; i < this.rules.length; i++) {
                    if (!this.execute(this.rules[i])) {
                        this.tempFlag = false;
                        break;
                    } 
                }
            } else {
                if (!this.execute(this.rules)) {
                    this.tempFlag = false;
                }              
            }
        } else {
            if (!this.execute(this.rules)) {
                this.tempFlag = false;
            }
        }

        // if allchecking validate
        if (allCheckFlag) {
            return this.tempFlag;
        }


        if (!this.tempFlag) {
            var message = this.setTemplate(this.labels);
            this.displayMessage(this.element.name,message);
        } else {
            this.removeMessage(this.element.name);
        }

        return this.tempFlag;
    },

    execute: function(rule) {
        resRule = this.scanRule(rule);
        if (resRule instanceof Array) {
            this.rule = resRule[0];
            this.param = resRule[1];
        } else {
            this.rule = resRule;
        }
        return this.getMethod(this.element,this.param);

    },

    allCheck: function(options,allowFlags,visableArea,Areas) {
        for(keys in options) {
            var element = ($(keys)) ? $(keys) : null;
            options[keys].each(function(rule){
                validClass = new ValidateClass(element);
                var resRule = validClass.scanRule(rule);
                var param = null;
                 if (resRule instanceof Array) {
                     rule = resRule[0];
                     param = resRule[1];
                 } else {
                     rule = resRule;
                 }
                var result = null;
                var checkFlag = false;
                checkFlag = validClass.areaCheck(keys,visableArea,Areas);
                if (element != null) {
                    if (checkFlag == false) {
                        result = validClass.getMethod(element,rule,param);
                    } else {
                        result = true;
                    }
                }
                if (!result) {
                    allowFlags[keys] = false;
                    return;
                } else {
                    allowFlags[keys] = true;
                }
                //alert(keys + ':' + allowFlags[keys]);
            });
        }
        return allowFlags;
    },

    areaCheck: function(field,visibleArea,Areas) {

        flag = null;
        visibleArea.each(function(val){
            if (Areas[val].indexOf(field) != -1) {
                flag = false;
            } else {
                flag = true;
            }
        });
        return flag;
    },


    getMessageTmplate: function() {
        return this.messageTmplate[this.rule];
    },

    scanRule: function(rule) {
        if (rule.indexOf("_") != -1) {
            var ruleArray = rule.split("_");
            return ruleArray;
        }
        return rule;
    },

    getMethod: function(element,parm) {

        valids = new validations(element,parm,this.rule);
        return valids.exec();

    },

    setTemplate: function(field) {
        var message = this.getMessageTmplate();
        var template = new Template(message);
        return template.evaluate({
            field: field,
            parm: this.param
        });
    },

    displayMessage: function(name,message) {
        $(name + "Error").innerHTML = message;
    },

    removeMessage: function(name,message) {
        $(name + "Error").innerHTML = "";
    }

};

var validations = Class.create();

validations.prototype = {

    initialize: function(element,params,rule) {

        this.element = element;
        this.params = params;
        this.rule = rule;
        //alert("init:" + this.element);

    },

    exec: function() {

        return validations.method[this.rule](this.element,this.params);

    },

    rule : null,

    element : null,

    params : null
}

validations.method = {

    validNotEmpty: function(element,params) {
	var isValid = Field.present(element);
        //var isValid = (element.getValue()) ? true : false;
        if (!isValid) {
            return false;
        }
        return true;
    },

    validDigit: function(element,params) {
	val = this.trimVal(element.value);
	if (val.match (/^[1-9][0-9]*$/)){
	   return true;
	}
	return false;
    },

    validMail: function(element,params) {
        val = this.trimVal(element.value);
        if (val.match(/^(?:(?:(?:(?:[a-zA-Z0-9_!#\$\%&'*+/=?\^`{}~|\-]+)(?:\.(?:[a-zA-Z0-9_!#\$\%&'*+/=?\^`{}~|\-]+))*)|(?:"(?:\\[^\r\n]|[^\\"])*")))\@(?:(?:(?:(?:[a-zA-Z0-9_!#\$\%&'*+/=?\^`{}~|\-]+)(?:\.(?:[a-zA-Z0-9_!#\$\%&'*+/=?\^`{}~|\-]+))*)|(?:\[(?:\\\S|[\x21-\x5a\x5e-\x7e])*\])))$/)) {
            return true;
        }
        return false;
    },

    validMax: function(element,params) {
        var val = element.getValue();
        if (val.length > params) {
            return false;
        }
        return true;
    },

    validMin: function(element,params) {
        var val = element.getValue();
        if (val.length < params) {
            return false;
        }
        return true;
    },

    validEq: function(element,params) {
        var val = element.getValue();
        if (val.length != params) {
            return false;
        }
        return true;
    },

    validPhone: function(element,params) {
        val = this.trimVal(element.value);
        if (val.match(/\d{2,4}-\d{2,4}-\d{4}/)) {
            return true;
        }
        return false;
    },

    validTwoBite: function() {

    },

    validRegix: function() {

    },

    trimVal: function(val) {
	return val.replace(/[ \t]+/g, '');
    }

};

