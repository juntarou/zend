// helper.js


// テンプレートの切り替え
function changeTemplate(template)
{
    template = String(template);
    temps = template.split(',');
    if (temps instanceof Array) {
        temps.each(function(val){
            var element = $(val);
            if (!Element.visible(element)) {
                Element.toggle(element);
            }
        });
    } else {
        var element = $(temps);
        if (!Element.visible(temps)) {
            Element.toggle(temps);
        }
    }
    return temps;
}

// フィールドへ記憶している値を挿入
function setFormsValue(json)
{
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
                        index = json[keys];
                        if (elem.options[0].value != "") {
                            index = index - 1;
                        }
                    }
                    elem.selectedIndex = index;
                    break;
                default:
                    break;
            }

        }
    }
    createMonthSelectBox(json.creditCardExpYear,json.creditCardExpMonth);
}

// 確認ボタンの切り替え　アクティブ:非アクティブ
function checkAllowFlag(submitBtn,disable)
{
    if (disable) {
        $(submitBtn).disable();
        return;
    }

    for(keys in validAllowFlags) {
        if (!validAllowFlags[keys]) {
            $(submitBtn).disable();
            return;
        }
    }
    $(submitBtn).enable();
}