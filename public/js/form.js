function handleInputDisplayOnSelect(selectID, inputID, selectTriggers) {
    document.getElementById(inputID).style.display = 'none';
    var objList = document.getElementById(selectID);

    if (objList.type == 'select-multiple') {
        for (i in objList.options) {
            if (objList.options[i].selected) {
                for (x in selectTriggers) {
                    if (selectTriggers[x] == objList.options[i].value) {
                        document.getElementById(inputID).style.display = 'block';
                    }
                }
            }
        }
    } else {
        var intListNumber = objList[objList.selectedIndex].value;
        for (x in selectTriggers) {
            if (selectTriggers[x] == intListNumber) {
                document.getElementById(inputID).style.display = 'block';
            }
        }
    }
}

function padZero(num, size) {
    var str = num.toString();
    while (str.length < size) {
        str = '0' + str;
    }
    return str;
}