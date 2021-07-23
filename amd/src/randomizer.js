export const init = () => {
    const chapters = document.querySelectorAll('.chapter');

    for (let i = 0; i < chapters.length; i++) {
        let chapter_width = chapters[i].offsetWidth;
        let chapter_height = chapters[i].offsetHeight;
        let missions = chapters[i].querySelectorAll('.mission');

        for (let j = 0; j < missions.length; j++) {
            let mission_width = missions[j].offsetWidth;
            let mission_height = missions[j].offsetHeight;
            let posx =
                Math.random() *
                (chapter_width - (mission_width + 20)).toFixed();
            var posy =
                Math.random() *
                (chapter_height - (mission_height + 20)).toFixed();

            missions[j].style.left = `${posx}px`;
            missions[j].style.top = `${posy}px`;
        }
    }
};
