import ModalFactory from 'core/modal_factory';
import ModalEvents from 'core/modal_events';
import Fragment from 'core/fragment';
import Ajax from 'core/ajax';
import Notification from 'core/notification';
import Template from 'core/templates';

// The function called from the Mustache template.
export const init = (contextid) => {
    // Set up a SAVE_CANCEL modal.
    ModalFactory.create({
        type: ModalFactory.types.SAVE_CANCEL,
        title: 'Add Level',
        body: get_form(null, contextid)
    })
    // Set up the listeners
    .then(modal => {
        const triggers = document.querySelectorAll('.add_level');
        const root = modal.getRoot();
        const form = root.find('form');
        for (let i = 0; i < triggers.length; i++) {
            triggers[i].addEventListener('click', (event) => showModal(event, modal));
        }
        root.on(ModalEvents.save, (event) => submitForm(event, form));
        form.on('submit', (event) => submitFormAjax(event, modal, form, contextid));

    })
    // Close modal
    .then(modal => {
        modal.close();
    });
};

const showModal = (event, modal) => {
    event.preventDefault();
    let root = modal.getRoot();
    modal.show();
    // Adds chapter ID to invoked modal form so we can save to DB
    root.find('form').find('input[name="chapterid"]').val(event.target.parentNode.dataset.cid);
};

const get_form = (formdata, contextid) => {
    if (typeof formdata === "undefined") {
        formdata = {};
    }
    var params = {jsonformdata: JSON.stringify(formdata)};
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
    Ajax.call([{
        methodname: 'block_mission_map_create_level',
        args: {contextid: contextid, jsonformdata: JSON.stringify(formData)},
        done: (data) => handleFormSubmissionResponse(data, modal),
        fail: (data) => handleFormSubmissionFailure(data, modal),
    }]);
};

const handleFormSubmissionFailure = (data, modal) => {
    modal.hide();
    Notification.alert('Warning', JSON.parse(data), 'Continue');
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
        url: level.url
    };

    const chapter = document.querySelectorAll(`[data-cid="${level.chapterid}"]`);

    Template
    .render('block_mission_map/level', context)
    .then((html, js) => {
        Template.appendNodeContents(chapter, html, js);
        modal.hide();
    })
    .fail((ex) => {
        Notification.alert('Warning', ex, 'Continue');
    });
};