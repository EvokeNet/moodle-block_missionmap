import ModalFactory from 'core/modal_factory';
import ModalEvents from 'core/modal_events';
import Fragment from 'core/fragment';
import Ajax from 'core/ajax';
import Notification from 'core/notification';
import Template from 'core/templates';

// The function called from the Mustache template.
export const init = (params) => {
    const { contextid, blockid, courseid } = params;

    document.addEventListener('click', (event) => {
        if (
            (event.target && event.target.classList.contains('edit_chapter')) ||
            event.target.id == 'add_chapter'
        ) {
            event.preventDefault();
            create_modal(event, contextid, blockid, courseid);
        }
    });
};

const create_modal = (event, contextid, blockid, courseid) => {
    const formdata = {
        chapterid: event.target.dataset.cid,
        blockid: blockid,
        courseid: courseid,
        name: event.target.dataset.cname,
        has_lock: event.target.dataset.haslock,
        unlocking_date: event.target.dataset.unlock,
    };

    // Set up a SAVE_CANCEL modal.
    ModalFactory.create({
        type: ModalFactory.types.SAVE_CANCEL,
        title: 'Add Chapter',
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
        'chapter_form',
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
