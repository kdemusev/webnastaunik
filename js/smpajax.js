/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

function SMPAjaxPost(path, data, func, xml) {
    SMPAjax(path, func, xml, true, data);
}

function SMPAjaxGet(path, func, xml) {
    SMPAjax(path, func, xml, false, null);
}

function SMPAjax(path, func, xml, post, data) {
    var request = new XMLHttpRequest();
    var timeout = null;
    request.onreadystatechange = function() {
        if (request.readyState===4 && request.status===200) {
            if(timeout) {
                clearTimeout(timeout);
            }
            if(xml && func) {
                func(request.responseXML);
            } else if (func) {
                func(request.responseText);
            }
        }
    };

    if(!post) {
        request.open("GET", path, true);
        request.send();
    } else {
        request.open("POST", path, true);
        request.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        request.send(data);
    }
    timeout = setTimeout(function() {
        request.close();
        var popup = new sc2Popup();
        popup.showMessage('Соединение потеряно', 'Невозможно получить ответ от сервера. Проверьте соединение с интернетом. Если остальные сайты открываются, то возможны технические перебои в работе сервиса. Попробуйте повторить запрос', 'Повторить');
    }, 10000);
}
