/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */                
function addmsg(type, msg){
    $('#notification_count').html(msg); 
}
 
function waitForMsg(){

    var getUrl = window.location;
    var baseUrl = getUrl .protocol + "//" + getUrl.host + "/" + getUrl.pathname.split('/')[1];
    var hash = $('#csrf_hash').val();
            
    $.ajax({
        type: "POST",
        url: baseUrl+"/home/unread_leave_request/",
        async: true,
        cache: false,
        timeout:50000,
        data: {'csrf_test_name':hash},
        success: function(data){
            if(data=='0'){
                $("#notification_count").hide();
            }else{
                $("#notification_count").show();
            }
            addmsg("new", data);
            setTimeout(
                waitForMsg,
            1000
            );
        },
        error: function(XMLHttpRequest, textStatus, errorThrown){
            addmsg("error", textStatus + " (" + errorThrown + ")");
            setTimeout(
                waitForMsg,
            15000);
        }
    });
};

function addmsg1(type, msg){
    $('#notification_count1').html(msg); 
}
 
function waitForMsg1(){

    var getUrl = window.location;
    var baseUrl = getUrl .protocol + "//" + getUrl.host + "/" + getUrl.pathname.split('/')[1];
    var hash = $('#csrf_hash').val();
            
    $.ajax({
        type: "POST",
        url: baseUrl+"/home/processed_leave_request/1",
        async: true,
        cache: false,
        timeout:50000,
        data: {'csrf_test_name':hash},
        success: function(data){
//            if(data=='0'){
//                $("#notification_count1").hide();
//            }else{
//                $("#notification_count1").show();
//            }
            addmsg1("new", data);
            setTimeout(
                waitForMsg1,
            1000
            );
        },
        error: function(XMLHttpRequest, textStatus, errorThrown){
            addmsg1("error", textStatus + " (" + errorThrown + ")");
            setTimeout(
                waitForMsg1,
            15000);
        }
    });
};

function addmsg2(type, msg){
    $('#notification_count2').html(msg); 
}
 
function waitForMsg2(){

    var getUrl = window.location;
    var baseUrl = getUrl .protocol + "//" + getUrl.host + "/" + getUrl.pathname.split('/')[1];
    var hash = $('#csrf_hash').val();
            
    $.ajax({
        type: "POST",
        url: baseUrl+"/home/processed_leave_request/2",
        async: true,
        cache: false,
        timeout:50000,
        data: {'csrf_test_name':hash},
        success: function(data){
//            if(data=='0'){
//                $("#notification_count2").hide();
//            }else{
//                $("#notification_count2").show();
//            }
            addmsg2("new", data);
            setTimeout(
                waitForMsg2,
            1000
            );
        },
        error: function(XMLHttpRequest, textStatus, errorThrown){
            addmsg2("error", textStatus + " (" + errorThrown + ")");
            setTimeout(
                waitForMsg2,
            15000);
        }
    });
};

function addmsg_recruit(type, msg){
    $('#notification_count_recruit').html(msg); 
}
 
function waitForMsgRecruit(){

    var getUrl = window.location;
    var baseUrl = getUrl .protocol + "//" + getUrl.host + "/" + getUrl.pathname.split('/')[1];
    var hash = $('#csrf_hash').val();
            
    $.ajax({
        type: "POST",
        url: baseUrl+"/home/unread_recruitment_request/",
        async: true,
        cache: false,
        timeout:50000,
        data: {'csrf_test_name':hash},
        success: function(data){
            if(data=='0'){
                $("#notification_count_recruit").hide();
            }else{
                $("#notification_count_recruit").show();
            }
            addmsg_recruit("new", data);
            setTimeout(
                waitForMsgRecruit,
            1000
            );
        },
        error: function(XMLHttpRequest, textStatus, errorThrown){
            addmsg_recruit("error", textStatus + " (" + errorThrown + ")");
            setTimeout(
                waitForMsgRecruit,
            15000);
        }
    });
};
