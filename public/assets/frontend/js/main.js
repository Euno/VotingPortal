$(function(){
    function isMobile() { return ('ontouchstart' in document.documentElement); }

    if(isMobile()){
        //$('.voting-container').hide();
        //$('.mobileDeviceModal').show();

        //alert('The votingportal does currently not support mobile devices. Please use this portal with a desktop computer or laptop!');
    }

    var nodes = window.nodes;
    var sigErrors = [];
    var foundDoubleAddress = false;

    $('.addressSelect').select2();

    $('.addVote').on('click', function(e){
        e.preventDefault();

        var clone = $('.votes').find('.voteRow').first().clone();

        clone.find('input').val('');
        clone.find('textarea').val('');
        clone.find('.select2-container').remove();
        clone.find('select').removeClass('select2-hidden-accessible');
        clone.find('span.invalidSignedMsgWarning').hide();

        $('.votes').append(clone);

        $('.votes').find('.voteRow').last().find('select').select2();

        initEvents();
    });

    $('.proceedVoteNextBtn').on('click', function(e){
        e.preventDefault();

        if($(this).hasClass('private'))
        {
            $('input[name="vote_anon"]').val(1);
        }
        else
        {
            $('input[name="vote_anon"]').val(0);
        }

        $('.proceedVoteNextBtn').hide();
        $('.proceedVote').show();
    });

    $('.answersSelect').select2({minimumResultsForSearch: -1});

    $('select[name="answer"]').on('change', function(){
        $('.voteRow').each(function(){
            checkSignedMessage($(this));
        });
        checkSubmitReady();
    });

    function initEvents(){
        $('.voteRow').each(function(){
            var voteRow = $(this);

            voteRow.find('textarea, select.addressSelect').off('change').on('change', function () {
                checkSignedMessage(voteRow);
                checkSubmitReady();
            });

            voteRow.find('.remove-vote').off('click').on('click', function () {
                voteRow.remove();
                initEvents();
            });

            voteRow.find('select.addressSelect').select2();
        });

        if( $('.voteRow').length > 1 ) {
            $('.remove-vote').show();
        } else {
            $('.remove-vote').hide();
        }

        checkSubmitReady();
    }
    initEvents();

    $('p.clipboard').find('a').on('click', function(){

        var anchor = $(this);
        var orgText = anchor.text();
        $(this).text('Copied vote answer to clipboard!');

        var answer = $('select[name="answer"]').val();
        $('.clipboardContainerInput').val(answer);

        var tempInput = document.createElement("input");
        tempInput.style = "position: absolute; left: -1000px; top: -1000px";
        tempInput.value = answer;
        document.body.appendChild(tempInput);
        tempInput.select();
        document.execCommand("copy");
        document.body.removeChild(tempInput);

        setTimeout(function(){
            anchor.text(orgText);
        }, 3000);
    });

    function checkSignedMessage(voteRow){
        var val = voteRow.find('textarea').val();
        var address = voteRow.find('select.addressSelect').val();
        var answer = $('select[name="answer"]').val();

        if($.trim(val) && $.trim(address) && $.trim(answer)) {
            $.ajax({
                type: 'POST',
                url: '/vote/signedMsgCheck',
                data: {
                    signedMessage: val,
                    address: address,
                    answer: answer
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
        collectChosenAdresses();
        var countVoteRows = $('.voteRow').length;
        var countedValidRowVotes = 0;

        $('.voteRow').each(function(){
            var select = $(this).find('select');
            var textarea = $(this).find('textarea');

            if(select.val() && $.trim(textarea.val())){
                countedValidRowVotes++;
            }
        });

        if(countedValidRowVotes === countVoteRows && Object.keys(sigErrors).length === 0 && foundDoubleAddress !== true) {
            $('.proceedVoteNextBtn').removeAttr('disabled');
            $('.proceedVote').removeAttr('disabled');
        } else {
            $('.proceedVoteNextBtn').attr('disabled', 'disabled');
            $('.proceedVote').attr('disabled', 'disabled');
        }
    }

    var chosenAddresses = [];
    function collectChosenAdresses() {
        chosenAddresses = [];
        foundDoubleAddress = false;

        $('.voteRow').each(function(){
            var address = $(this).find('select.addressSelect').val();
            if(address){

                if(chosenAddresses.indexOf(address) !== -1){
                    foundDoubleAddress = true;
                } else {
                    chosenAddresses.push(address);
                }
            }
        });

        if(foundDoubleAddress){
            $('.warning-row').show();
        } else {
            $('.warning-row').hide();
        }
    }
});
