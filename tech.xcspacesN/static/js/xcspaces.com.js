/*
 * 注册
 */
$(function(){
    $('#LfsCaptcha').click(function(){
        var milliseconds = (new Date()).getTime();
        $(this).css('background-image', 'url(' + $('base').attr('href') + 'captcha/img?fn=' + milliseconds + ')');
    });

    $(window).on('scroll', function() {
        var st = $(document).scrollTop();
        if (st > 0) {
            // if ($('#xcs-scroll').length != 0) {
                // var w = $(window).width(),
                //     mw = $('#xcs-scroll').width();
                // if ((w - mw) / 2 > 70) $('#gotop').css({
                //     'left': (w - mw) / 2 + mw + 20
                // });
                // else {
                //     $('#gotop').css({
                //         'left': 'auto'
                //     })
                // }
            // }
            $('#gotop').fadeIn(function() {
                $(this).removeClass('hide')
            })
        } else {
            $('#gotop').fadeOut(function() {
                $(this).addClass('hide')
            })
        }
    });
    $('#gotop .go').on('click', function() {
        $('html,body').animate({
            'scrollTop': 0
        }, 600)
    });
    $('#gotop .th7-weixin').hover(function() {
        $('#gotop .th7-weixin-con').removeClass('hide')
    }, function() {
        $('#gotop .th7-weixin-con').addClass('hide')
    })
    
});

function moveEnd(obj){
    obj.focus();
    obj = obj.get(0);
    var len = obj.value.length;
    if (document.selection) {
        var sel = obj.createTextRange();
        sel.moveStart('character',len);
        sel.collapse();
        sel.select();
    } else if (typeof obj.selectionStart == 'number' && typeof obj.selectionEnd == 'number') {
        obj.selectionStart = obj.selectionEnd = len;
    }
}

// reply a reply
function replyOne(username){
	// 获取编辑器区域完整html代码
    var html = editor.$txt.html();
    // 初始化编辑器的内容
    prefix = "@" + username + " ";

    if( html == '' )
    {
	    editor.$txt.html( prefix );
        moveEnd( $("#content") );
    }
    else
    {
        if( html != prefix )
        {
            // 初始化编辑器的内容
            editor.$txt.html( '' );
        }
        moveEnd( $("#content") );
    }
	
}

/*
 * 发布话题
 */
$('.sel-node a').click(function(){
    var e = $(this);
    $('.sel-node a').each(function(){

        var $this = $(this);
        $this.removeClass('sel');

    })

    e.addClass('sel');
    var node = e.attr('data');
    var nodes = node.split(',');
    $('#zid').val(nodes[0]);
});