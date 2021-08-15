export const init = () => {
    const screen_size = window.screen.width;

    window.console.log(screen_size);

    if (screen_size <= 600) {
        return;
    }

    const chapters = document.querySelectorAll('.chapter');

    const seed = xmur3('avocadotoast');
    const rand = sfc32(seed(), seed(), seed(), seed());

    for (let i = 0; i < chapters.length; i++) {
        let chapter_width = chapters[i].offsetWidth;
        let chapter_height = chapters[i].offsetHeight;
        let missions = chapters[i].querySelectorAll('.mission');

        let division = chapter_width / missions.length;
        let offsetLeft = division / 2;

        for (let j = 0; j < missions.length; j++) {
            let mission_width = missions[j].offsetWidth;
            let mission_height = missions[j].offsetHeight;
            let posx = offsetLeft - mission_width / 2;
            var posy =
                rand() * (chapter_height - (mission_height + 20)).toFixed();

            missions[j].style.left = `${posx}px`;
            missions[j].style.top = `${posy}px`;
            offsetLeft += division;
        }
    }
};

/* eslint-disable */
const xmur3 = (str) => {
    for (var i = 0, h = 1779033703 ^ str.length; i < str.length; i++)
        (h = Math.imul(h ^ str.charCodeAt(i), 3432918353)),
            (h = (h << 13) | (h >>> 19));
    return function () {
        h = Math.imul(h ^ (h >>> 16), 2246822507);
        h = Math.imul(h ^ (h >>> 13), 3266489909);
        return (h ^= h >>> 16) >>> 0;
    };
};

const sfc32 = (a, b, c, d) => {
    return function () {
        a >>>= 0;
        b >>>= 0;
        c >>>= 0;
        d >>>= 0;
        var t = (a + b) | 0;
        a = b ^ (b >>> 9);
        b = (c + (c << 3)) | 0;
        c = (c << 21) | (c >>> 11);
        d = (d + 1) | 0;
        t = (t + d) | 0;
        c = (c + t) | 0;
        return (t >>> 0) / 4294967296;
    };
};
