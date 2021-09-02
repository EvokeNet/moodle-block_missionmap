import ModalFactory from 'core/modal_factory';
import ModalEvents from 'core/modal_events';
import Fragment from 'core/fragment';
import Ajax from 'core/ajax';
import Notification from 'core/notification';
import Template from 'core/templates';

// The function called from the Mustache template.
export const init = (params) => {
    const { contextid, blockid, courseid } = params;
    // Set up a SAVE_CANCEL modal.
    ModalFactory.create({
        type: ModalFactory.types.SAVE_CANCEL,
        title: 'Add Chapter',
        body: get_form(null, contextid),
    })
        // Set up the listeners
        .then((modal) => {
            const add_trigger = document.getElementById('add_chapter');

            document.addEventListener('click', (event) => {
                if (
                    event.target &&
                    event.target.classList.contains('edit_chapter')
                ) {
                    showModal(event, modal, blockid, courseid, true);
                }
            });

            const root = modal.getRoot();
            const form = root.find('form');
            add_trigger.addEventListener('click', (event) =>
                showModal(event, modal, blockid, courseid)
            );
            root.on(ModalEvents.save, (event) => submitForm(event, form));
            form.on('submit', (event) =>
                submitFormAjax(event, modal, form, contextid)
            );
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
        'chapter_form',
        contextid,
        params
    );
};

const showModal = (event, modal, blockid, courseid, isEditing = false) => {
    event.preventDefault();
    modal.show();
    let root = modal.getRoot();
    root.find('form').find('input[name="blockid"]').val(blockid);
    root.find('form').find('input[name="courseid"]').val(courseid);
    if (isEditing) {
        root.find('form')
            .find('input[name="id"]')
            .val(event.target.dataset.cid);
        root.find('form')
            .find('input[name="name"]')
            .val(event.target.dataset.cname);
        root.find('form')
            .find('select[name="islocked"]')
            .val(event.target.dataset.haslock);
        root.find('form')
            .find('input[name="unlocking_date"]')
            .val(event.target.dataset.unlock);
    }
};

const submitForm = (event, form) => {
    event.preventDefault();
    form.submit();
};

const submitFormAjax = (event, modal, form, contextid) => {
    event.preventDefault();

    // var changeEvent = document.createEvent('HTMLEvents');
    // changeEvent.initEvent('change', true, true);

    // form.find(':input').each((element) => {
    //     element.dispatchEvent(changeEvent);
    // });

    let formData = form.serialize();
    Ajax.call([
        {
            methodname: 'block_mission_map_create_chapter',
            args: {
                contextid: contextid,
                jsonformdata: JSON.stringify(formData),
            },
            done: (data) => handleFormSubmissionResponse(data, modal),
            fail: (data) => handleFormSubmissionFailure(data),
        },
    ]);
};

const handleFormSubmissionFailure = (data) => {
    Notification.alert('Warning', JSON.parse(data), 'Continue');
};

/**
 *   chapter {
 *       id: 0,
 *       name: 'ChapterName',
 *       timecreated: 0000000000,
 *       timemodified: 0000000000
 *   }
 **/
const handleFormSubmissionResponse = (data, modal) => {
    const map = document.getElementById('mission_map');
    let chapter = JSON.parse(data.data);

    if (chapter.updated == true) {
        // Ugly hack because TIME
        location.reload();
    } else {
        let context = {
            id: chapter.id,
            name: chapter.name,
        };

        Template.render('block_mission_map/chapter', context)
            .then((html, js) => {
                Template.appendNodeContents(map, html, js);
                modal.hide();
            })
            .fail((ex) => {
                Notification.alert('Warning', ex, 'Continue');
            });
    }
};
