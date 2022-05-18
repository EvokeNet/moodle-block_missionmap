import ModalFactory from 'core/modal_factory';
import ModalEvents from 'core/modal_events';
import Fragment from 'core/fragment';
import Ajax from 'core/ajax';
import Notification from 'core/notification';
import Template from 'core/templates';

// The function called from the Mustache template.
export const init = (params) => {
    const { contextid, blockid, courseid, sections } = params;

    document.addEventListener('click', (event) => {
        if (event.target && event.target.classList.contains('add_level')) {
            event.preventDefault();
            create_modal(event, contextid, blockid, courseid, sections);
        }
    });
};

// The function called from the Mustache template to render the ADD_LEVEL modal
const create_modal = (event, contextid, blockid, courseid, sections) => {
    const formdata = {
        chapterid: event.target.dataset.cid,
        blockid: blockid,
        courseid: courseid,
        sections: sections,
    };

    // Set up a SAVE_CANCEL modal.
    ModalFactory.create({
        type: ModalFactory.types.SAVE_CANCEL,
        title: 'Add Level',
        body: get_form(formdata, contextid),
    })
        // Set up the listeners
        .then((modal) => {
            const root = modal.getRoot();
            const form = root.find('form');

            root.on(ModalEvents.save, (event) => submitForm(event, form));
            form.on('submit', (event) =>
                submitFormAjax(event, modal, form, contextid)
            );
            modal.show();
        })
        // Close modal
        .then((modal) => {
            modal.close();
        });
};

const get_form = (formdata, contextid) => {
    if (typeof formdata === 'undefined') {
        formdata = {};
    }
    var params = { jsonformdata: JSON.stringify(formdata) };
    return Fragment.loadFragment(
        'block_mission_map',
        'level_form',
        contextid,
        params
    );
};

const submitForm = (event, form) => {
    event.preventDefault();
    form.submit();
};

const submitFormAjax = (event, modal, form, contextid) => {
    event.preventDefault();

    let formData = form.serialize();
    Ajax.call([
        {
            methodname: 'block_mission_map_create_level',
            args: {
                contextid: contextid,
                jsonformdata: JSON.stringify(formData),
            },
            done: (data) => handleFormSubmissionResponse(data, modal),
            fail: (data) => handleFormSubmissionFailure(data, modal),
        },
    ]);
};

const handleFormSubmissionFailure = (data, modal) => {
    Notification.alert('Warning', data.backtrace, 'Continue');
    modal.hide();
};

/**
 *   level {
 *       id: 0,
 *       chapterid: 0,
 *       parentlevelid: 0,
 *       name: 'LevelName',
 *       url: 'https://levelurl',
 *       timecreated: 0000000000,
 *       timemodified: 0000000000
 *   }
 **/
const handleFormSubmissionResponse = (data, modal) => {
    let level = JSON.parse(data.data);
    let context = {
        id: level.id,
        chapterid: level.chapterid,
        name: level.name,
        url: level.url,
    };

    const chapter = document.querySelector(`[data-cid="${level.chapterid}"]`);

    Template.render('block_mission_map/dot', context)
        .then((html, js) => {
            Template.appendNodeContents(chapter, html, js);
            modal.hide();
        })
        .fail((ex) => {
            Notification.alert('Warning', ex, 'Continue');
        });
};
