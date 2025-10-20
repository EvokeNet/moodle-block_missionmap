define(['jquery', 'core/ajax', 'core/notification', 'core/modal_factory', 'core/modal_events', 'core/templates'], function($, ajax, notification, ModalFactory, ModalEvents, Templates) {

    return {
        init: function(blockid, courseid) {
            console.log('Chapter modal init called with blockid:', blockid, 'courseid:', courseid);

            // Create modal using Moodle's ModalFactory with template
            ModalFactory.create({
                type: ModalFactory.types.SAVE_CANCEL,
                title: 'Add Chapter',
                body: Templates.render('block_mission_map/add_chapter_modal', {
                    blockid: blockid,
                    courseid: courseid
                })
            }).then(function(modal) {
                console.log('Modal created successfully');

                // Show/hide unlock date field based on checkbox
                modal.getRoot().on('change', '#hasLock', function() {
                    console.log('HasLock checkbox changed');
                    if ($(this).is(':checked')) {
                        $('#unlockDateGroup').show();
                    } else {
                        $('#unlockDateGroup').hide();
                    }
                });

                // Handle save event using Moodle's standard save event
                modal.getRoot().on(ModalEvents.save, function(e) {
                    console.log('Save event triggered');
                    e.preventDefault();
                    
                    var chapterName = $('#chapterName').val().trim();

                    if (!chapterName) {
                        notification.addNotification({
                            message: 'Chapter name is required',
                            type: 'error'
                        });
                        return;
                    }

                    var hasLock = $('#hasLock').is(':checked');
                    var unlockingDate = hasLock ? $('#unlockDate').val() : null;
                    var editingChapterId = modal.getRoot().data('editing-chapter-id');

                    // Prepare data
                    var data = {
                        blockid: blockid,
                        courseid: courseid,
                        name: chapterName,
                        has_lock: hasLock ? 1 : 0,
                        unlocking_date: unlockingDate
                    };

                    // Add chapter ID if editing
                    if (editingChapterId) {
                        data.chapterid = editingChapterId;
                    }

                    console.log('Sending data:', data);

                    // Show loading state
                    modal.getRoot().find('[data-action="save"]').prop('disabled', true).text('Saving...');

                    // Make AJAX call to create or update chapter
                    ajax.call([{
                        methodname: 'block_mission_map_create_chapter',
                        args: data
                    }])[0].then(function(response) {
                        console.log('AJAX response:', response);
                        if (response.success) {
                            notification.addNotification({
                                message: editingChapterId ? 'Chapter updated successfully!' : 'Chapter created successfully!',
                                type: 'success'
                            });

                            // Close modal and reload page
                            modal.hide();
                            setTimeout(function() {
                                window.location.reload();
                            }, 1000);
                        } else {
                            notification.addNotification({
                                message: response.message || 'Error saving chapter',
                                type: 'error'
                            });
                        }
                    }).catch(function(error) {
                        console.log('AJAX error:', error);
                        notification.addNotification({
                            message: 'Error saving chapter: ' + error.message,
                            type: 'error'
                        });
                    }).always(function() {
                        // Reset button state
                        modal.getRoot().find('[data-action="save"]').prop('disabled', false).text('Save');
                    });
                });

                // Reset form when modal is closed
                modal.getRoot().on(ModalEvents.hidden, function() {
                    console.log('Modal closed');
                    $('#addChapterForm')[0].reset();
                    $('#unlockDateGroup').hide();
                });

                // Show modal when button is clicked (both main button and header button)
                $('button[data-target="#addChapterModal"], .editing_add_chapter').on('click', function(e) {
                    e.preventDefault();
                    console.log('Button clicked, showing modal');
                    modal.show();
                });

                // Handle edit chapter buttons
                $(document).on('click', '.edit-chapter-btn', function(e) {
                    e.preventDefault();
                    var chapterId = $(this).data('chapter-id');
                    var chapterName = $(this).data('chapter-name');
                    var hasLock = $(this).data('has-lock');
                    var unlockDate = $(this).data('unlock-date');
                    
                    console.log('Edit chapter clicked:', chapterId);
                    
                    // Populate modal with existing data
                    $('#chapterName').val(chapterName);
                    $('#hasLock').prop('checked', hasLock == 1);
                    if (hasLock == 1) {
                        $('#unlockDateGroup').show();
                        if (unlockDate) {
                            $('#unlockDate').val(unlockDate);
                        }
                    } else {
                        $('#unlockDateGroup').hide();
                    }
                    
                    // Store chapter ID for update
                    modal.getRoot().data('editing-chapter-id', chapterId);
                    
                    modal.show();
                });

                console.log('Modal setup complete');
            }).catch(function(error) {
                console.error('Error creating modal:', error);
                notification.addNotification({
                    message: 'Error creating modal: ' + error.message,
                    type: 'error'
                });
            });
        }
    };
});
