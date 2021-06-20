export const init = () => {
    const chapters = document.querySelectorAll('.chapter');

    for (var chapter of chapters) {
        const missions = chapter.querySelectorAll('.mission');
        const curves = chapter.querySelectorAll('.curve');

        const chapter_width = chapter.offsetWidth;
        const chapter_height = chapter.offsetHeight;
        const division = chapter_width / missions.length;
        const offsetLeft = division / 2;
        const offsetHeight = chapter_height / 2;

        const m_no = missions.length;
        const height_step = chapter_height / (m_no * 1.5);
        let height_add = 0;

        for (var i = 0; i < m_no; i++) {
            const mission_width = missions[i].offsetWidth;
            const mission_height = missions[i].offsetHeight;
            missions[i].style.top =
                offsetHeight + mission_height + height_add + 'px';
            missions[i].style.left = offsetLeft - mission_width / 2 + 'px';
            missions[i].style.transform = `rotate(-15deg)`;
            offsetLeft += division;
            height_add -= height_step;
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
        x: end.x - 60,
        y: middle_1.y,
    };

    curve.setAttribute(
        'd',
        `M${start.x},${start.y} C${middle_1.x},${middle_1.y} ${middle_2.x},${middle_2.y} ${end.x},${end.y}`
    );
};
