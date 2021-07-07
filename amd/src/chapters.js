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
        //const output = document.getElementById('mission_map');

        trigger.addEventListener('click', confirm);

        function confirm(event) {
            event.preventDefault();
            modal.show();
        }

        root.on(ModalEvents.save, function(event) {
            submitFormAjax(event, root, contextid);
        });
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
    // Get the content of the modal.
    var params = {jsonformdata: JSON.stringify(formdata)};
    return Fragment.loadFragment('block_mission_map', 'chapter_form', contextid, params);

};

/* eslint-disable */
const submitFormAjax = (event, root, contextid) => {
    event.preventDefault();
    let formData = root.find('form').serialize();
    Ajax.call([{
        methodname: 'block_mission_map_create_chapter',
        args: {contextid: contextid, jsonformdata: JSON.stringify(formData)}
    }]);
};
/* eslint-enable */