export const init = () => {
    const chapters = document.querySelectorAll('.chapter');
    let invert_flow = false;
    let counter = 0;

    for (var i = 0; i < chapters.length; i++) {
        const missions = chapters[i].querySelectorAll('.mission');
        const mission_texts = chapters[i].querySelectorAll('.mission-text');
        place_missions(missions, mission_texts, chapters[i], i, invert_flow);
        if (counter == 2) {
            invert_flow = !invert_flow;
            counter = 0;
        }
        counter++;
    }
};

// const connect = (from, to, curve) => {
//     const start = {
//         x: from.offsetLeft + from.offsetWidth / 2,
//         y: from.offsetTop - from.offsetHeight / 2,
//     };

//     const middle_1 = {
//         x: start.x,
//         y: start.y - 20,
//     };

//     const end = {
//         x: to.offsetLeft + to.offsetWidth / 2,
//         y: to.offsetTop - to.offsetHeight / 2,
//     };

//     const middle_2 = {
//         x: end.x - 60,
//         y: middle_1.y,
//     };

//     curve.setAttribute(
//         'd',
//         `M${start.x},${start.y} C${middle_1.x},${middle_1.y} ${middle_2.x},${middle_2.y} ${end.x},${end.y}`
//     );
// };

/**
 *  Path along div
 **/
const place_missions = (
    missions,
    mission_texts,
    chapter,
    chapter_no,
    invert_flow
) => {
    let path = chapter.querySelector(`.path${chapter_no + 1}`);
    let pathLength = path.getTotalLength();
    let increment = 1 / missions.length;
    let percentage = 0.1;

    if (invert_flow) {
        for (let m = missions.length - 1; m >= 0; m--) {
            let p = path.getPointAtLength(percentage * pathLength);
            missions[m].setAttribute('transform', `translate(${p.x}, ${p.y})`);
            mission_texts[m].setAttribute(
                'transform',
                `translate(${p.x}, ${p.y + 12})`
            );
            percentage += increment;
        }
    } else {
        for (let m = 0; m < missions.length; m++) {
            let p = path.getPointAtLength(percentage * pathLength);
            missions[m].setAttribute('transform', `translate(${p.x}, ${p.y})`);
            mission_texts[m].setAttribute(
                'transform',
                `translate(${p.x}, ${p.y + 12})`
            );
            percentage += increment;
        }
    }
};
