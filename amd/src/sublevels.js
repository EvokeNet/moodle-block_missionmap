import ModalFactory from 'core/modal_factory';
import ModalEvents from 'core/modal_events';
import Fragment from 'core/fragment';
import Ajax from 'core/ajax';
import Notification from 'core/notification';
import Template from 'core/templates';

// The function called from the Mustache template to render the ADD_LEVEL modal
export const init_add = (contextid) => {
    // Set up a SAVE_CANCEL modal.
    ModalFactory.create({
        type: ModalFactory.types.SAVE_CANCEL,
        title: 'Add Sublevel',
        body: get_form(null, contextid),
    })
        // Set up the listeners
        .then((modal) => {
            document.addEventListener('click', (event) => {
                if (
                    event.target &&
                    event.target.classList.contains('add_sublevel')
                ) {
                    showModal(event, modal);
                }
            });

            const root = modal.getRoot();
            const form = root.find('form');

            root.on(ModalEvents.save, (event) => submitForm(event, form));
            form.on('submit', (event) =>
                submitAddFormAjax(event, modal, form, contextid)
            );
        })
        // Close modal
        .then((modal) => {
            modal.close();
        });
};

const showModal = (event, modal) => {
    event.preventDefault();
    let root = modal.getRoot();
    modal.show();
    // Adds chapter ID to invoked modal form so we can save to DB
    root.find('form')
        .find('input[name="chapterid"]')
        .val(event.target.parentNode.dataset.cid);

    // Adds parentlevel ID to invoked modal form so we can save to DB
    root.find('form')
        .find('input[name="parentlevelid"]')
        .val(event.target.parentNode.dataset.plid);
};

const get_form = (formdata, contextid) => {
    if (typeof formdata === 'undefined') {
        formdata = {};
    }
    var params = { jsonformdata: JSON.stringify(formdata) };
    return Fragment.loadFragment(
        'block_mission_map',
        'sublevel_form',
        contextid,
        params
    );
};

const submitForm = (event, form) => {
    event.preventDefault();
    form.submit();
};

const submitAddFormAjax = (event, modal, form, contextid) => {
    event.preventDefault();

    let formData = form.serialize();
    Ajax.call([
        {
            methodname: 'block_mission_map_create_sublevel',
            args: {
                contextid: contextid,
                jsonformdata: JSON.stringify(formData),
            },
            done: (data) => handleAddFormSubmissionResponse(data, modal),
            fail: (data) => handleFormSubmissionFailure(data, modal),
        },
    ]);
};

const handleFormSubmissionFailure = (data, modal) => {
    modal.hide();
    Notification.alert('Warning', JSON.parse(data), 'Continue');
};

const handleAddFormSubmissionResponse = (data, modal) => {
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
