export const init = () => {
    let countdowns = document.querySelectorAll('[data-countdown]');

    for (const countdown of countdowns) {
        const countDownDate = new Date(countdown.dataset.countdown * 1000);
        const timerValue = countdown.querySelector('.timer-value');
        
        if (!timerValue) continue;
        
        window.console.log(countDownDate);
        setInterval(function () {
            var now = new Date().getTime();
            var distance = countDownDate - now;

            var days = Math.floor(distance / (1000 * 60 * 60 * 24));
            var hours = Math.floor(
                (distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60)
            );
            var minutes = Math.floor(
                (distance % (1000 * 60 * 60)) / (1000 * 60)
            );
            var seconds = Math.floor((distance % (1000 * 60)) / 1000);

            timerValue.innerHTML =
                zeroPad(days, 2) +
                'd ' +
                zeroPad(hours, 2) +
                'h ' +
                zeroPad(minutes, 2) +
                'm ' +
                zeroPad(seconds, 2) +
                's ';

            // If the count down is finished, write some text
            if (distance < 0) {
                clearInterval();
                timerValue.innerHTML = '00d 00h 00m 00s';
            }
        }, 1000);
    }
};

const zeroPad = (num, places) => String(num).padStart(places, '0');
