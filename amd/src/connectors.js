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
            connect(missions[i], missions[i + 1], curve);
        }
    }
};

const connect = (from, to, curve) => {
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
