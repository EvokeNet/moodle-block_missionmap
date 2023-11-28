export const init = (selector) => {
    let voting = document.querySelector(selector);

    let card = voting.closest('.card');

    card.classList.add('colored_background');
};
