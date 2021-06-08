// import * as Str from 'core/str';

export const init = (courses) => {
    var campaign_select = document.getElementById('campaign_select');

    campaign_select.addEventListener('change', (e) => {
        var course_id = e.target.value;
        var dropdowns = document.querySelectorAll("[data-type='sections']");

        clearOptions(dropdowns);

        for (var course of courses) {
            if (course.id == 0) {
                break;
            }
            if (course.id == course_id) {
                for (var dropdown of dropdowns) {
                    for (var section of course.sections) {
                        var el = document.createElement('option');
                        el.text =
                            section.name !== null ? section.name : section.id;
                        el.value = section.id;
                        dropdown.add(el);
                    }
                }
            }
        }
    });
};

const clearOptions = (dropdowns) => {
    for (var dropdown of dropdowns) {
        for (var option of dropdown.options) {
            dropdown.options.remove(option);
        }
    }
};
