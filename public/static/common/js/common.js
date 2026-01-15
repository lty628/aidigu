
$(function(){
    if ($("#messageRemind").css('display') == 'block') {
        return
    }
    if ($("#checkRemind").css('display') == 'none') {
        // return
        var temindTimer = setInterval(() => {
            $.ajax({
                type: "GET",
                url: "/index/ajax/checkRemind",
                dataType: "json",
                success: function (response) {
                    if (response.status ==1) {
                        if (response.data.chatRemind && $("checkRemind").length > 0) {
                            $("#checkRemind").show()    
                        }
                        if (response.data.messageRemind && $("checkmessageRemind").length > 0) {
                            $("#checkmessageRemind").show()    
                        }
                        if ($("#chatAudio").length <= 0 ) {
                            $('<audio id="chatAudio"><source src="/static/chat/voices/notify.ogg" type="audio/ogg"><source src="/static/chat/voices/notify.mp3" type="audio/mpeg"><source src="/static/chat/voices/notify.wav" type="audio/wav"></audio>').appendTo('body');
                        }
                        $('#chatAudio')[0].play();
                        clearInterval(temindTimer)
                    }
                }
            });
        }, 30000);
    }
})