$(function(){
    $('#signupForm').on('submit', function(e){
        // default submit allowed (server side handles)
    });
    $('#loginForm').on('submit', function(e){});
    $('.edit-btn').on('click', function(){
        var field = $(this).data('field');
        var cur = $('#'+field).text();
        var inp = $('<input type="text">').val(cur);
        $('#'+field).replaceWith(inp);
        $(this).text('Save').off('click').on('click', function(){
            var val = inp.val();
            $.post('ajax/user.php',{action:'update_field',field:field,value:val}, function(res){
                if(res.status==='ok'){
                    inp.replaceWith('<span id="'+field+'">'+val+'</span>');
                    $('.edit-btn[data-field="'+field+'"]').text('✎');
                }
            },'json');
        });
    });
    $('#avatarForm').on('submit', function(e){
        e.preventDefault();
        var fd = new FormData(this);
        fd.append('action','upload_avatar');
        $.ajax({url:'ajax/user.php',type:'POST',data:fd,contentType:false,processData:false,dataType:'json',success:function(res){
            if(res.status==='ok'){ $('#profileAvatar').attr('src',res.avatar); }
        }});
    });
    $('#postForm').on('submit', function(e){
        e.preventDefault();
        var fd = new FormData(this);
        fd.append('action','create');
        $.ajax({url:'ajax/post.php',type:'POST',data:fd,contentType:false,processData:false,dataType:'json',success:function(res){
            if(res.status==='ok'){ location.reload(); }
        }});
    });
    $(document).on('click','.delete-post', function(){
        if(!confirm('Delete post?')) return;
        var id = $(this).data('id');
        $.post('ajax/post.php',{action:'delete',post_id:id}, function(res){ if(res.status==='ok') location.reload(); },'json');
    });
    $(document).on('click','.like-btn', function(){
        var id = $(this).data('id');
        var btn = $(this);
        $.post('ajax/post.php',{action:'react',post_id:id,type:'like'}, function(res){
            if(res.status==='ok'){ var v = parseInt(btn.find('.likes').text())+1; btn.find('.likes').text(v); }
        },'json');
    });
    $(document).on('click','.dislike-btn', function(){
        var id = $(this).data('id');
        var btn = $(this);
        $.post('ajax/post.php',{action:'react',post_id:id,type:'dislike'}, function(res){
            if(res.status==='ok'){ var v = parseInt(btn.find('.dislikes').text())+1; btn.find('.dislikes').text(v); }
        },'json');
    });
});
