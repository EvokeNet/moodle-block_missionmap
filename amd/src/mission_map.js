// import * as Str from 'core/str';

export const init = (courses) => {
    var campaign_select = document.getElementById('campaign_select');

    campaign_select.addEventListener('change', (e) => {
        var course_id = e.target.value;

        clearOptions();
        var dropdowns = document.querySelectorAll("[data-type='sections']");

        for (var course of courses) {
            if (course.id == 0) {
                break;
            }
            if (course.id == course_id) {
                for (var dropdown of dropdowns) {
                    for (var section of course.sections) {
                        var el = document.createElement('option');
                        el.text =
                            section.name !== null || section.name == ''
                                ? section.name
                                : 'Mission ' + section.no;
                        el.value = section.id;
                        dropdown.add(el);
                    }
                }
            }
        }
    });
};

const clearOptions = () => {
    var dropdowns = document.querySelectorAll("[data-type='sections']");
    for (var dropdown of dropdowns) {
        while (dropdown.options.length > 0) {
            dropdown.remove(0);
        }
    }
};
