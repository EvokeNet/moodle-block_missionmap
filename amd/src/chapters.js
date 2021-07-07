import ModalFactory from 'core/modal_factory';
import ModalEvents from 'core/modal_events';
import Fragment from 'core/fragment';
import Ajax from 'core/ajax';

// The function called from the Mustache template.
export const init = (contextid) => {
    // Set up a SAVE_CANCEL modal.
    ModalFactory.create({
        type: ModalFactory.types.SAVE_CANCEL,
        title: 'Add Chapter',
        body: get_form(null, contextid)
    })
    // Set up the actions.
    .then(function(modal) {
        const trigger = document.getElementById('add_chapter');
        const root = modal.getRoot();
        const form = root.find('form');
        //const output = document.getElementById('mission_map');
        trigger.addEventListener('click', (event) => showModal(event, modal));
        root.on(ModalEvents.save, (event) => submitForm(event, form));
        form.on('submit', (event) => submitFormAjax(event, form, contextid));

    })
    // Close modal
    .then(function(modal) {
        modal.close();
    });
};

const get_form = (formdata, contextid) => {
    if (typeof formdata === "undefined") {
        formdata = {};
    }
    var params = {jsonformdata: JSON.stringify(formdata)};
    return Fragment.loadFragment('block_mission_map', 'chapter_form', contextid, params);

};

const showModal = (event, modal) => {
    event.preventDefault();
    modal.show();
};

const submitForm = (event, form) => {
    event.preventDefault();
    form.submit();
};

const submitFormAjax = (event, form, contextid) => {
    event.preventDefault();
    let formData = form.serialize();
    window.console.log(formData);
    Ajax.call([{
        methodname: 'block_mission_map_create',
        args: {contextid: contextid, jsonformdata: JSON.stringify(formData)}
    }]);
};
/* eslint-disable */
/* eslint-enable */