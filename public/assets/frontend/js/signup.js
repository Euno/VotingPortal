$(function(){

    var nodes = window.nodes;
    var sigErrors = [];
    $('.ipSelect').select2();


    $('.proceedVoteNextBtn').on('click', function(e){
        e.preventDefault();

        $(this).hide();
        $('.proceedVote').show();
    });

    $('.answersSelect').select2({minimumResultsForSearch: -1});

    function initEvents(){
        $('.voteRow').each(function(){
            var voteRow = $(this);

            voteRow.find('input[name="telegram_username"]').on('change', function () {
                checkSignedMessage(voteRow);
                checkSubmitReady();
            });

            voteRow.find('.ipSelect').on('change', function () {
                var val = $(this).val();

                voteRow.find('input.address').val(nodes[val]).change();
                checkSubmitReady();
            });

            voteRow.find('textarea, input.address').off('change').on('change', function () {
                checkSignedMessage(voteRow);
                checkSubmitReady();
            });
        });

        checkSubmitReady();
    }
    initEvents();

    function checkSignedMessage(voteRow){
        var val = voteRow.find('textarea').val();
        var address = voteRow.find('input.address').val();
        var telegram_username = voteRow.find('input.telegram_username').val();

        if($.trim(val) && $.trim(address) && $.trim(telegram_username)) {
            $.ajax({
                type: 'POST',
                url: '/signup/signedMsgCheck',
                data: {
                    signedMessage: val,
                    address: address,
                    telegram_username: telegram_username
                },
                dataType: 'JSON',
                success: function(res){
                    var span = voteRow.find('span.invalidSignedMsgWarning');
                    switch (res.status){
                        case true:
                            span.hide();

                            if(typeof sigErrors[address] !== 'undefined'){
                                delete sigErrors[address];
                            }

                            break;

                        case false:
                            span.show();
                            sigErrors[address] = false;
                            break;
                    }

                    checkSubmitReady();
                }
            });
        }
    }

    function checkSubmitReady(){
        var countVoteRows = $('.voteRow').length;
        var countedValidRowVotes = 0;

        $('.voteRow').each(function(){
            var telegram_username = $(this).find('.telegram_username');
            var select = $(this).find('select');
            var textarea = $(this).find('textarea');

            if(select.val() && $.trim(textarea.val()) && telegram_username.val()){
                countedValidRowVotes++;
            }
        });

        if(countedValidRowVotes === countVoteRows && Object.keys(sigErrors).length === 0) {
            $('.proceedVoteNextBtn').removeAttr('disabled');
            $('.proceedVote').removeAttr('disabled');
        } else {
            $('.proceedVoteNextBtn').attr('disabled', 'disabled');
            $('.proceedVote').attr('disabled', 'disabled');
        }
    }
});