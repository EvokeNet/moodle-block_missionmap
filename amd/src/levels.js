import ModalFactory from 'core/modal_factory';
import ModalEvents from 'core/modal_events';
import Fragment from 'core/fragment';
import Ajax from 'core/ajax';
import Notification from 'core/notification';
import Template from 'core/templates';
import $ from 'jquery';

// The function called from the Mustache template to render the ADD_LEVEL modal
export const init_add = (contextid) => {
    // Set up a SAVE_CANCEL modal.
    ModalFactory.create({
        type: ModalFactory.types.SAVE_CANCEL,
        title: 'Add Level',
        body: get_form(null, contextid),
    })
        // Set up the listeners
        .then((modal) => {
            document.addEventListener('click', (event) => {
                if (
                    event.target &&
                    event.target.classList.contains('add_level')
                ) {
                    showModal(event, modal);
                }
            });
            document.addEventListener('mousedown', (event) => {
                if (
                    event.target &&
                    event.target.classList.contains('mission')
                ) {
                    dragstart(event, event.target, contextid);
                }
            });
            const triggers = document.querySelectorAll('.add_level');
            const missions = document.querySelectorAll('.mission');
            const root = modal.getRoot();
            const form = root.find('form');

            // Adds click event listeners to all buttons already added to the DOM
            for (let i = 0; i < triggers.length; i++) {
                triggers[i].addEventListener('click', (event) =>
                    showModal(event, modal)
                );
            }

            // Adds dragging event listeners to all missions already added to the DOM
            for (let i = 0; i < missions.length; i++) {
                missions[i].addEventListener('mousedown', (event) =>
                    dragstart(event, missions[i], contextid)
                );
            }

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

// The function called from the Mustache template to render the EDIT_LEVEL modal
export const init_edit = (contextid) => {
    document.addEventListener('click', (event) => {
        if (event.target && event.target.classList.contains('edit_level')) {
            create_modal(event, contextid);
        }
    });
};

// Assembles EDIT_LEVEL modal with prefilled data
const create_modal = (event, contextid) => {
    // Pass data to the modal
    const formdata = {
        chapterid: event.target.parentNode.parentNode.dataset.cid,
        levelid: event.target.dataset.lid,
    };

    // Set up a SAVE_CANCEL modal.
    ModalFactory.create({
        type: ModalFactory.types.SAVE_CANCEL,
        title: 'Edit Level',
        body: get_form(formdata, contextid),
    })
        // Set up the modal events
        .then((modal) => {
            const root = modal.getRoot();
            const form = root.find('form');

            root.on(ModalEvents.save, (event) => submitForm(event, form));
            form.on('submit', (event) =>
                submitEditFormAjax(event, modal, form, contextid)
            );

            modal.show();
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

const submitAddFormAjax = (event, modal, form, contextid) => {
    event.preventDefault();

    let formData = form.serialize();
    Ajax.call([
        {
            methodname: 'block_mission_map_create_level',
            args: {
                contextid: contextid,
                jsonformdata: JSON.stringify(formData),
            },
            done: (data) => handleAddFormSubmissionResponse(data, modal),
            fail: (data) => handleFormSubmissionFailure(data, modal),
        },
    ]);
};

const submitEditFormAjax = (event, modal, form, contextid) => {
    event.preventDefault();

    let formData = form.serialize();
    Ajax.call([
        {
            methodname: 'block_mission_map_edit_level',
            args: {
                contextid: contextid,
                jsonformdata: JSON.stringify(formData),
            },
            done: (data) => handleEditFormSubmissionResponse(data, modal),
            fail: (data) => handleFormSubmissionFailure(data, modal),
        },
    ]);
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
const handleAddFormSubmissionResponse = (data, modal) => {
    let level = JSON.parse(data.data);
    let context = {
        id: level.id,
        chapterid: level.chapterid,
        name: level.name,
        url: level.url,
    };

    const chapter = document.querySelector(`[data-cid="${level.chapterid}"]`);

    Template.render('block_mission_map/level', context)
        .then((html, js) => {
            Template.appendNodeContents(chapter, html, js);
            modal.hide();
        })
        .fail((ex) => {
            Notification.alert('Warning', ex, 'Continue');
        });
};

const handleEditFormSubmissionResponse = (data, modal) => {
    let level_data = JSON.parse(data.data);
    const level = document.querySelector(`[data-lid="${level_data.levelid}"]`);
    level.href = level_data.url;
    modal.hide();
};

const dragstart = (event, element, contextid) => {
    event.preventDefault();

    element.classList.add('dimmed');
    element.style.cursor = 'move';

    let cOffX = event.clientX - element.offsetLeft;
    let cOffY = event.clientY - element.offsetTop;

    element.onmousemove = (event) => {
        element.style.top = (event.clientY - cOffY).toString() + 'px';
        element.style.left = (event.clientX - cOffX).toString() + 'px';
    };

    document.onmouseup = () => {
        element.onmousemove = null;
        element.style.cursor = 'pointer';
        element.classList.remove('dimmed');

        const level_edit_form = $('.block_mission_map_level_edit_form');
        document.getElementById('levelid').value = element.dataset.lid;
        document.getElementById('chapterid').value =
            element.parentNode.dataset.cid;
        document.getElementById('posx').value = element.style.top;
        document.getElementById('posy').value = element.style.left;
        submitLevelEditFormAjax(level_edit_form, contextid);
    };

    element.ondragstart = () => {
        return false;
    };
};

const submitLevelEditFormAjax = (form, contextid) => {
    let formData = form.serialize();
    Ajax.call([
        {
            methodname: 'block_mission_map_edit_level',
            args: {
                contextid: contextid,
                jsonformdata: JSON.stringify(formData),
            },
            done: (data) => handleLevelEditFormSubmissionResponse(data),
            fail: (data) => handleLevelEditFormSubmissionFailure(data),
        },
    ]);
};

const handleLevelEditFormSubmissionFailure = (data) => {
    Notification.alert('Warning', JSON.stringify(data), 'Continue');
    return false;
};

const handleLevelEditFormSubmissionResponse = (data) => {
    Notification.alert('Success', data, 'Continue');
};
/* eslint-disable */
/* eslint-enable */
