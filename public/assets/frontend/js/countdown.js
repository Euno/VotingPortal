$(function(){

    var expireDate = new Date((window.votingEndDate + (8*60*60))*1000).getTime();

    function calculateTimeLeft(){
        var now = new Date().getTime();

        var secondsLeft = expireDate - now;

        var days = Math.floor(secondsLeft / (1000 * 60 * 60 * 24));
        var hours = Math.floor((secondsLeft % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
        var minutes = Math.floor((secondsLeft % (1000 * 60 * 60)) / (1000 * 60));
        var seconds = Math.floor((secondsLeft % (1000 * 60)) / 1000);

        if (secondsLeft < 0) {
            location.reload();
        } else {
            $("#votingCountdown").find('span').text( days + "d " + hours + "h " + minutes + "m " + seconds + "s " );
        }
    }
    calculateTimeLeft();

    setInterval(calculateTimeLeft, 1000);
});