export const init = () => {
    const chapters = document.querySelectorAll('.chapter');

    for (var chapter of chapters) {
        const missions = chapter.querySelectorAll('.mission');
        const curves = chapter.querySelectorAll('.curve');

        const width = chapter.offsetWidth;
        const height = chapter.offsetHeight;
        const division = width / missions.length;
        const offsetLeft = division / 2;
        const offsetHeight = height / 2;

        for (var mission of missions) {
            mission.style.top = offsetHeight - 25 + 'px';
            mission.style.left = offsetLeft - 25 + 'px';
            offsetLeft += division;
        }

        for (var i = 0; i < missions.length; i++) {
            var curve = curves[i];
            if (i == missions.length - 1) {
                break;
            }
            connectCurve(missions[i], missions[i + 1], curve);
        }
    }
};

// const connectLine = (from, to, line) => {
//     const x1 = from.offsetLeft + from.offsetWidth / 2;
//     const y1 = from.offsetTop - from.offsetHeight / 2;

//     const x2 = to.offsetLeft + to.offsetWidth / 2;
//     const y2 = to.offsetTop - to.offsetHeight / 2;

//     line.setAttribute('x1', x1);
//     line.setAttribute('y1', y1);
//     line.setAttribute('x2', x2);
//     line.setAttribute('y2', y2);
// };

const connectCurve = (from, to, curve) => {
    const start = {
        x: from.offsetLeft + from.offsetWidth / 2,
        y: from.offsetTop - from.offsetHeight / 2,
    };

    const middle_1 = {
        x: start.x,
        y: start.y - 20,
    };

    const end = {
        x: to.offsetLeft + to.offsetWidth / 2,
        y: to.offsetTop - to.offsetHeight / 2,
    };

    const middle_2 = {
        x: end.x,
        y: middle_1.y,
    };

    curve.setAttribute(
        'd',
        `M${start.x},${start.y} C${middle_1.x},${middle_1.y} ${middle_2.x},${middle_2.y} ${end.x},${end.y}`
    );
};

// const connect = (from, to, line) => {
//     var fT = from.offsetTop + from.offsetHeight / 2;
//     var tT = to.offsetTop + to.offsetHeight / 2;
//     var fL = from.offsetLeft + from.offsetWidth / 2;
//     var tL = to.offsetLeft + to.offsetWidth / 2;

//     var CA = Math.abs(tT - fT);
//     var CO = Math.abs(tL - fL);
//     var H = Math.sqrt(CA * CA + CO * CO);
//     var ANG = (180 / Math.PI) * Math.acos(CA / H);

//     if (tT > fT) {
//         var top = (tT - fT) / 2 + fT;
//     } else {
//         var top = (fT - tT) / 2 + tT;
//     }
//     if (tL > fL) {
//         var left = (tL - fL) / 2 + fL;
//     } else {
//         var left = (fL - tL) / 2 + tL;
//     }

//     if (
//         (fT < tT && fL < tL) ||
//         (tT < fT && tL < fL) ||
//         (fT > tT && fL > tL) ||
//         (tT > fT && tL > fL)
//     ) {
//         ANG *= -1;
//     }
//     top -= H / 2;

//     line.style['-webkit-transform'] = 'rotate(' + ANG + 'deg)';
//     line.style['-moz-transform'] = 'rotate(' + ANG + 'deg)';
//     line.style['-ms-transform'] = 'rotate(' + ANG + 'deg)';
//     line.style['-o-transform'] = 'rotate(' + ANG + 'deg)';
//     line.style['-transform'] = 'rotate(' + ANG + 'deg)';
//     line.style.top = top + '%';
//     line.style.left = left + '%';
//     line.style.height = H + '%';
// };
