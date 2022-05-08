/* eslint-disable */
define([
        'jquery',
        'core/str',
        'core/modal_factory',
        'core/modal_events',
        'core/fragment',
        'core/ajax',
        'core/notification',
        'core/templates'],
    function($, Str, ModalFactory, ModalEvents, Fragment, Ajax, Notification, Template) {

        var CreateChapter = function(params) {
            const {contextid, blockid, courseid} = params;

            this.contextid = contextid;

            document.addEventListener('click', (event) => {
                if (
                    (event.target && event.target.classList.contains('edit_chapter')) ||
                    event.target.id == 'add_chapter'
                ) {
                    event.preventDefault();

                    this.init(event, blockid, courseid);
                }
            });
        };

        /**
         * @var {Modal} modal
         * @private
         */
        CreateChapter.prototype.modal = null;

        /**
         * @var {int} contextid
         * @private
         */
        CreateChapter.prototype.contextid = -1;

        /**
         * @var {Modal} modal
         * @private
         */
        CreateChapter.prototype.modal = null;

        CreateChapter.prototype.init = function(event, blockid, courseid) {
            const formdata = {
                chapterid: event.target.dataset.cid,
                blockid: blockid,
                courseid: courseid,
                name: event.target.dataset.cname,
                has_lock: event.target.dataset.haslock,
                unlocking_date: event.target.dataset.unlock,
            };

            return Str.get_string('add_chapter', 'block_mission_map').then(function(title) {
                // Create the modal.
                return ModalFactory.create({
                    type: ModalFactory.types.SAVE_CANCEL,
                    title: title,
                    body: this.getBody(formdata)
                });
            }.bind(this)).then(function(modal) {
                // Keep a reference to the modal.
                this.modal = modal;

                // We want to reset the form every time it is opened.
                this.modal.getRoot().on(ModalEvents.hidden, function() {
                    this.modal.setBody(this.getBody(formdata));
                }.bind(this));

                // We want to hide the submit buttons every time it is opened.
                this.modal.getRoot().on(ModalEvents.shown, function() {
                    this.modal.getRoot().append('<style>[data-fieldtype=submit] { display: none ! important; }</style>');
                }.bind(this));

                // We catch the modal save event, and use it to submit the form inside the modal.
                // Triggering a form submission will give JS validation scripts a chance to check for errors.
                this.modal.getRoot().on(ModalEvents.save, this.submitForm.bind(this));
                // We also catch the form submit event and use it to submit the form with ajax.
                this.modal.getRoot().on('submit', 'form', this.submitFormAjax.bind(this));

                this.modal.show();

                return this.modal;
            }.bind(this));
        };

        CreateChapter.prototype.getBody = function(formdata) {
            if (typeof formdata === "undefined") {
                formdata = {};
            }

            // Get the content of the modal.
            var params = {jsonformdata: JSON.stringify(formdata)};

            return Fragment.loadFragment('block_mission_map', 'chapter_form', this.contextid, params);
        };

        CreateChapter.prototype.handleFormSubmissionResponse = function(data) {
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
                        this.modal.hide();
                    })
                    .fail((ex) => {
                        Notification.alert('Warning', ex, 'Continue');
                    });
            }

            this.modal.hide();
        };

        CreateChapter.prototype.handleFormSubmissionFailure = function(data) {
            // Oh noes! Epic fail :(
            // Ah wait - this is normal. We need to re-display the form with errors!
            this.modal.setBody(this.getBody(data));
        };

        /**
         * Private method
         *
         * @method submitFormAjax
         *
         * @private
         *
         * @param {Event} e Form submission event.
         */
        CreateChapter.prototype.submitFormAjax = function(e) {
            // We don't want to do a real form submission.
            e.preventDefault();

            var changeEvent = document.createEvent('HTMLEvents');
            changeEvent.initEvent('change', true, true);

            // Prompt all inputs to run their validation functions.
            // Normally this would happen when the form is submitted, but
            // since we aren't submitting the form normally we need to run client side
            // validation.
            this.modal.getRoot().find(':input').each(function(index, element) {
                element.dispatchEvent(changeEvent);
            });

            // Now the change events have run, see if there are any "invalid" form fields.
            var invalid = $.merge(
                this.modal.getRoot().find('[aria-invalid="true"]'),
                this.modal.getRoot().find('.error')
            );

            // If we found invalid fields, focus on the first one and do not submit via ajax.
            if (invalid.length) {
                invalid.first().focus();
                return;
            }

            // Convert all the form elements values to a serialised string.
            var formData = this.modal.getRoot().find('form').serialize();

            // Now we can continue...
            Ajax.call([{
                methodname: 'block_mission_map_create_chapter',
                args: {contextid: this.contextid, jsonformdata: JSON.stringify(formData)},
                done: this.handleFormSubmissionResponse.bind(this),
                fail: this.handleFormSubmissionFailure.bind(this, formData)
            }]);
        };

        /**
         * This triggers a form submission, so that any mform elements can do final tricks before the form submission is processed.
         *
         * @method submitForm
         * @param {Event} e Form submission event.
         * @private
         */
        CreateChapter.prototype.submitForm = function(e) {
            e.preventDefault();

            this.modal.getRoot().find('form').submit();
        };

        return {
            init: function(params) {
                return new CreateChapter(params);
            }
        };
    }
);
