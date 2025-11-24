$(function(){
    $('#signupForm').on('submit', function(e){
        
    });
    $('#loginForm').on('submit', function(e){});
    $('.edit-btn').on('click', function(){
        var field = $(this).data('field');
        var cur = $('#'+field).text();
        
        // Determine input type based on the field
        var inputType = (field === 'dob') ? 'date' : 'text';
        
        var inp = $('<input type="'+inputType+'">').val(cur);
        $('#'+field).replaceWith(inp);
        $(this).text('Save').off('click').on('click', function(){
            var val = inp.val();
            
            // Client-side validation for DOB
            if (field === 'dob' && !val) {
                alert('Date of Birth cannot be empty.');
                return;
            }

            $.post('ajax/user.php',{action:'update_field',field:field,value:val}, function(res){
                if(res.status==='ok'){
                    inp.replaceWith('<span id="'+field+'">'+val+'</span>');
                    $('.edit-btn[data-field="'+field+'"]').text('✎');
                } else if (res.message) {
                    alert('Error: ' + res.message);
                    // Revert input field back to span for better UX on error
                    inp.replaceWith('<span id="'+field+'">'+cur+'</span>');
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
            if(res.status==='ok'){ 
                location.reload(); 
            }
        }});
    });
    $(document).on('click','.delete-post', function(){
        if(!confirm('Delete post?')) return;
        var id = $(this).data('id');
        $.post('ajax/post.php',{action:'delete',post_id:id}, function(res){ if(res.status==='ok') location.reload(); },'json');
    });

    $(document).on('click','.like-btn, .dislike-btn', function(){
        var btn = $(this);
        var id = btn.data('id');
        var type = btn.hasClass('like-btn') ? 'like' : 'dislike';
        var otherType = type === 'like' ? 'dislike' : 'like';
        var otherBtn = btn.siblings('.'+otherType+'-btn');
        
        $.post('ajax/post.php',{action:'react',post_id:id,type:type}, function(res){
            if(res.status==='ok'){ 
                var likesSpan = btn.closest('.post-actions').find('.likes');
                var dislikesSpan = btn.closest('.post-actions').find('.dislikes');

                likesSpan.text(res.likes);
                dislikesSpan.text(res.dislikes);

                if (res.new_reaction === type) {
                    btn.addClass('active');
                    otherBtn.removeClass('active');
                } else if (res.new_reaction === null) {
                    btn.removeClass('active');
                    otherBtn.removeClass('active');
                } else if (res.new_reaction === otherType) {
                    btn.removeClass('active');
                    otherBtn.addClass('active');
                }
            }
        },'json');
    });
});