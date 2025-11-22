jQuery(document).ready(function($){

    let selectedRating = 0;

    $('.sh-star').on('click', function(){
        selectedRating = $(this).data('value');
        $('.sh-star').removeClass('active');
        $(this).prevAll().addBack().addClass('active');
    });

    $('#sh_rating_submit').on('click', function(){

        let email = $('#sh_rating_email').val();
        let post_id = $('.sh-stars').data('post');

        $.post(sh_rating_vars.ajax_url, {
            action: 'sh_rate',
            post_id: post_id,
            rating: selectedRating,
            email: email,
            nonce: sh_rating_vars.nonce
        }, function(res){
            if(res.success){
                $('.sh-rating-status').html(
                    `Thank you! New Rating: <strong>${res.data.avg}/5</strong> (${res.data.count} Votes)`
                );
            } else {
                alert(res.data);
            }
        });

    });

});
