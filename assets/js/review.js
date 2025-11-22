jQuery(document).ready(function($){

    $('#sh_review_submit').click(function(){

        let text = $('#sh_review_text').val();
        let email = $('#sh_review_email').val();
        let post_id = $('.sh-stars').data('post') || $('body').data('post-id');
        let photo = $('#sh_review_photo')[0].files[0];

        let formData = new FormData();
        formData.append('action','sh_add_review');
        formData.append('post_id',post_id);
        formData.append('text',text);
        formData.append('email',email);
        formData.append('photo',photo);
        formData.append('nonce',sh_review_vars.nonce);

        $.ajax({
            url: sh_review_vars.ajax_url,
            method:'POST',
            data: formData,
            processData:false,
            contentType:false,
            success: function(res){
                $('#sh_review_msg').text(res.success ? res.data : res.data);
                location.reload();
            }
        });

    });

});
