$(function(){
    $('.proceedVoteNextBtn').on('click', function(e){
        e.preventDefault();

        $(this).hide();
        $('.proceedVote').show();
    });

    $('input[name="new_address"]').on('keyup', function(){
        //checkSubmitReady();
    });

    function initEvents(){
        //checkSubmitReady();
    }
    initEvents();

    function checkSubmitReady(){
        var val = $.trim($('input[name="new_address"]').val());

        if(val && val.length === 34) {
            $('.proceedVoteNextBtn').removeAttr('disabled');
            $('.proceedVote').removeAttr('disabled');
        } else {
            $('.proceedVoteNextBtn').attr('disabled', 'disabled');
            $('.proceedVote').attr('disabled', 'disabled');
        }
    }
});
