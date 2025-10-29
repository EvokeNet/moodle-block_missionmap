export const init = (courses) => {
    var course_selects = document.querySelectorAll(
        '[data-element="voting_course_select"]'
    );

    for (let i = 0; i < course_selects.length; i++) {
        course_selects[i].addEventListener('change', (event) =>
            populate_sections(event, courses)
        );
    }
};

const populate_sections = (event, courses) => {
    var course_id = event.target.value;
    var dropdowns = document.querySelectorAll(
        '[data-element="voting_course_sections"]'
    );

    clearOptions(dropdowns);

    for (var course of courses) {
        if (course.id == 0) {
            break;
        }
        if (course.id == course_id) {
            for (var dropdown of dropdowns) {
                for (var section of course.sections) {
                    var el = document.createElement('option');
                    el.text = section.name;
                    el.value = section.id;
                    dropdown.add(el);
                }
            }
        }
    }
};

const clearOptions = (dropdowns) => {
    for (var dropdown of dropdowns) {
        for (var option of dropdown.options) {
            dropdown.options.remove(option);
        }
    }
};
