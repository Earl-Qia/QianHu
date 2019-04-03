/*
 * version 1.0
 * author handler
 * date 2017-01-13
 * 拖动图片验证码
 */
(function($){
    $.fn.drag = function(options){
        var x, drag = this, isMove = false, defaults = {
        };
        var options = $.extend(defaults, options);
        //添加背景，文字，滑块
        var html = '<div class="drag_bg"></div>'+
                    '<div class="drag_text" onselectstart="return false;" unselectable="on">拖动图片验证登陆</div>'+
                    '<div class="handler handler_bg"></div>';
        this.append(html);
        
        var handler = drag.find('.handler');
        var drag_bg = drag.find('.drag_bg');
        var text = drag.find('.drag_text');
        var maxWidth = drag.width() - handler.width();  //能滑动的最大间距
        
        //鼠标按下时候的x轴的位置
        handler.mousedown(function(e){
            $xy_img.show();
            isMove = true;
            x = e.pageX - parseInt(handler.css('left'), 10);
        });
        var $xy_img = $("#xy_img");
        //鼠标指针在上下文移动时，移动距离大于0小于最大间距，滑块x轴位置等于鼠标移动距离
        $("#drag").mousemove(function(e){
            var _x = e.pageX - x;
            if(isMove){
                $xy_img.css({'left': _x});
                if(_x > 0 && _x <= maxWidth){
                    handler.css({'left': _x});
                    drag_bg.css({'width': _x});
                }else if(_x > maxWidth){  //鼠标指针移动距离达到最大时清空事件
                  //  dragOk();
                }
            }
        }).mouseup(function(e){
            isMove = false;
            var _x = e.pageX - x;

            $.ajax({
                type:"POST",
                url:"/login/XSValidation.html",
                dataType:"JSON",
                async: false,
                data:{point:_x,if_admin_name:$("#if_admin_name").val(),if_admin_password:$("#if_admin_password").val()},
                success:function(result){
                    result['state'] == 4603? window.location.reload() :'';
                    if(result['state'] == 0 ) {
                        for(var i=1; 4>=i; i++){
                            $xy_img.animate({left:_x-(30-10*i)},50);
                            $xy_img.animate({left:_x+2*(30-10*i)},50,function(){
                                $xy_img.css({'left':result['data']});
                            });
                        }
                        handler.css({'left': maxWidth});
                        drag_bg.css({'width': maxWidth});
                        $xy_img.removeClass('xy_img_bord');
                        dragOk();
                        save_project();
                    } else {
                        layer.msg(result[1],{icon: 5,time:1000});
                        $xy_img.css({'left': 0});
                        handler.css({'left': 0});
                        drag_bg.css({'width': 0});
                    }
                },
                beforeSend:function(){
                    //$(".text-c").children('td').html('<i style="color: lightseagreen">加载中...</i>');
                }
            });
        });

        function save_project(){
           var  $url = '/login/get_data.json';
            var $data  = $("#form-login").serialize();
            $.ajax({
                type: "POST",
                url:$url,
                data:$data,
                dataType:'json',
                beforeSend:function(){
                    layer.msg('登录中....');
                },
                success:function(o){
                    if( o[0] != 0 ) {
                        layer.msg(o[1],{icon: 5,time:1000},function(){
                            window.location.href='/';
                        });
                    } else {
                        layer.msg('操作成功！',{icon: 6,time:1000},function(){
                            window.location.href='/';
                        });
                    }
                }
            });
        }
        //清空事件
        function dragOk(){
            handler.removeClass('handler_bg').addClass('handler_ok_bg');
            text.text('验证通过');
            drag.css({'color': '#fff'});
            handler.unbind('mousedown');
            $(document).unbind('mousemove');
            $(document).unbind('mouseup');
        }
    };
})(jQuery);


