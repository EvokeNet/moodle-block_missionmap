export const init = (courses, is_editing, selected_course) => {
    const campaign_select = document.getElementById('campaign_select');
    const dropdowns = document.querySelectorAll("[data-type='sections']");

    if (!is_editing) {
        for (var dropdown of dropdowns) {
            dropdown.disabled = true;
        }
    } else {
        campaign_select.selected = selected_course;
        clearOptions();
        addOptions(selected_course, courses, dropdowns, is_editing);
    }

    campaign_select.addEventListener('change', (e) => {
        var course_id = e.target.value;

        clearOptions();
        addOptions(course_id, courses, dropdowns);

        dropdowns.disabled = false;
    });
};

const clearOptions = () => {
    var dropdowns = document.querySelectorAll("[data-type='sections']");
    for (var dropdown of dropdowns) {
        while (dropdown.options.length > 0) {
            dropdown.remove(0);
        }
        dropdown.disabled = false;
    }
};

const addOptions = (course_id, courses, dropdowns, is_editing) => {
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
                    if (is_editing) {
                        el.selected = true;
                    }
                    dropdown.add(el);
                }
            }
        }
    }
};
