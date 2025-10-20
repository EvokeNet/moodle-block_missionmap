define(['jquery', 'core/ajax', 'core/notification', 'core/modal_factory', 'core/modal_events', 'core/templates'],
    function($, ajax, notification, ModalFactory, ModalEvents, Templates) {

    return {
        init: function(blockid, courseid) {

            // Create modal using Moodle's ModalFactory with template
            ModalFactory.create({
                type: ModalFactory.types.SAVE_CANCEL,
                title: 'Add Mission',
                body: Templates.render('block_mission_map/add_mission_modal', {
                    blockid: blockid,
                    courseid: courseid
                })
            }).then(function(modal) {

                // Show/hide URL/Activity fields based on mission type
                modal.getRoot().on('change', '#missionType', function() {
                    var type = $(this).val();
                    if (type == '1') { // URL
                        $('#urlGroup').show();
                        $('#activityGroup').hide();
                    } else if (type == '2') { // Activity
                        $('#urlGroup').hide();
                        $('#activityGroup').show();
                        loadCourseActivities();
                    } else { // Voting
                        $('#urlGroup').hide();
                        $('#activityGroup').hide();
                    }
                });

                // Handle activity search and filtering
                modal.getRoot().on('input', '#missionActivitySearch', function() {
                    var searchTerm = $(this).val().toLowerCase();
                    filterActivities(searchTerm);
                });

                // Handle clear search button
                modal.getRoot().on('click', '#clearSearchBtn', function() {
                    $('#missionActivitySearch').val('');
                    filterActivities('');
                });

                // Handle activity selection
                modal.getRoot().on('change', '#missionActivitySelect', function() {
                    var selectedOption = $(this).find('option:selected');
                    var cmid = selectedOption.val();
                    var url = selectedOption.data('url');

                    if (cmid) {
                        $('#selectedActivityUrl').val(url);
                    } else {
                        $('#selectedActivityUrl').val('');
                    }
                });

                /**
                 * Load course activities into dropdown
                 */
                function loadCourseActivities() {
                    ajax.call([{
                        methodname: 'block_mission_map_get_course_activities',
                        args: { courseid: courseid }
                    }])[0].then(function(response) {
                        if (response.success && response.activities) {
                            populateActivityDropdown(response.activities);
                        }
                    }).catch(function(error) {
                        notification.addNotification({
                            message: 'Error loading activities: ' + error.message,
                            type: 'error'
                        });
                    });
                }

                /**
                 * Populate the activity dropdown with grouped sections
                 * @param {Array} activities
                 */
                function populateActivityDropdown(activities) {
                    var dropdown = $('#missionActivitySelect');
                    dropdown.empty();
                    dropdown.append('<option value="">{{#str}}select_activity, block_mission_map{{/str}}</option>');

                    activities.forEach(function(section) {
                        if (section.activities.length > 0) {
                            // Add section header (disabled option)
                            dropdown.append('<optgroup label="' + section.section_name + '">');

                            section.activities.forEach(function(activity) {
                                var option = $('<option></option>')
                                    .attr('value', activity.id)
                                    .data('url', activity.url)
                                    .text(activity.name + ' (' + activity.type + ')');
                                dropdown.append(option);
                            });

                            dropdown.append('</optgroup>');
                        }
                    });
                }

                /**
                 * Filter activities based on search term
                 * @param {string} searchTerm
                 */
                function filterActivities(searchTerm) {
                    var dropdown = $('#missionActivitySelect');
                    var options = dropdown.find('option');

                    options.each(function() {
                        var option = $(this);
                        var text = option.text().toLowerCase();

                        if (option.val() === '') {
                            // Always show the placeholder option
                            option.show();
                        } else if (searchTerm === '' || text.includes(searchTerm)) {
                            option.show();
                        } else {
                            option.hide();
                        }
                    });

                    // Hide/show optgroups based on visible children
                    dropdown.find('optgroup').each(function() {
                        var optgroup = $(this);
                        var visibleOptions = optgroup.find('option:visible').not('[value=""]');

                        if (visibleOptions.length === 0) {
                            optgroup.hide();
                        } else {
                            optgroup.show();
                        }
                    });
                }

                // Handle save event using Moodle's standard save event
                modal.getRoot().on(ModalEvents.save, function(e) {
                    e.preventDefault();

                    var missionName = $('#missionName').val().trim();
                    var missionType = $('#missionType').val();
                    var missionUrl = $('#missionUrl').val();
                    var selectedActivityId = $('#missionActivitySelect').val();
                    var missionColor = $('#missionColor').val();
                    var missionDescription = $('#missionDescription').val();

                    if (!missionName) {
                        notification.addNotification({
                            message: 'Mission name is required',
                            type: 'error'
                        });
                        return;
                    }

                    // Validate based on type
                    if (missionType == '1' && !missionUrl) {
                        notification.addNotification({
                            message: 'Mission URL is required for URL type missions',
                            type: 'error'
                        });
                        return;
                    }

                    if (missionType == '2' && !selectedActivityId) {
                        notification.addNotification({
                            message: 'Please select a course activity',
                            type: 'error'
                        });
                        return;
                    }

                    var editingChapterId = modal.getRoot().data('editing-chapter-id');
                    var editingMissionId = modal.getRoot().data('editing-mission-id');

                    // Prepare data
                    var data = {
                        blockid: blockid,
                        courseid: courseid,
                        chapterid: editingChapterId,
                        name: missionName,
                        description: missionDescription,
                        type: parseInt(missionType),
                        color: missionColor,
                        url: missionType == '1' ? missionUrl : (missionType == '2' ? $('#selectedActivityUrl').val() : null),
                        cmid: missionType == '2' ? selectedActivityId : null
                    };

                    // Add mission ID if editing
                    if (editingMissionId) {
                        data.levelid = editingMissionId;
                    }

                    // Show loading state
                    modal.getRoot().find('[data-action="save"]').prop('disabled', true).text('Saving...');

                    // Make AJAX call to create or update mission
                    ajax.call([{
                        methodname: 'block_mission_map_create_level',
                        args: data
                    }])[0].then(function(response) {
                        if (response.success) {
                            notification.addNotification({
                                message: editingMissionId ? 'Mission updated successfully!' : 'Mission created successfully!',
                                type: 'success'
                            });

                            // Close modal and reload page
                            modal.hide();
                            setTimeout(function() {
                                window.location.reload();
                            }, 1000);
                        } else {
                            var errorMsg = editingMissionId ? 'Error updating mission' : 'Error creating mission';
                            notification.addNotification({
                                message: response.message || errorMsg,
                                type: 'error'
                            });
                        }
                    }).catch(function(error) {
                        notification.addNotification({
                            message: 'Error saving mission: ' + error.message,
                            type: 'error'
                        });
                    }).always(function() {
                        // Reset button state
                        modal.getRoot().find('[data-action="save"]').prop('disabled', false).text('Save');
                    });
                });

                // Reset form when modal is closed
                modal.getRoot().on(ModalEvents.hidden, function() {
                    $('#addMissionForm')[0].reset();
                    $('#urlGroup').show();
                    $('#activityGroup').hide();
                    $('#missionActivitySearch').val('');
                    $('#missionActivitySelect').val('');
                });

                // Show modal when "Add Mission" button is clicked
                $(document).on('click', '.add-mission-btn', function(e) {
                    e.preventDefault();
                    var chapterId = $(this).data('chapter-id');
                    var chapterName = $(this).data('chapter-name');

                    // Store chapter ID for mission creation
                    modal.getRoot().data('editing-chapter-id', chapterId);
                    modal.getRoot().data('editing-mission-id', null); // Clear any existing mission ID

                    // Update modal title with chapter name
                    modal.getRoot().find('.modal-title').text('Add Mission to: ' + chapterName);

                    // Reset form
                    $('#addMissionForm')[0].reset();
                    $('#urlGroup').show();
                    $('#activityGroup').hide();

                    modal.show();
                });

                // Handle mission clicks to show/hide action buttons
                $(document).on('click', '.mission', function(e) {
                    e.preventDefault();
                    
                    // Hide all other action buttons
                    $('.mission-actions').hide();
                    
                    // Show action buttons for this mission
                    $(this).siblings('.mission-actions').show();
                });

                // Handle view button clicks
                $(document).on('click', '.mission-view-btn', function(e) {
                    e.preventDefault();
                    var url = $(this).data('url');
                    window.open(url, '_blank');
                });

                // Handle edit mission buttons
                $(document).on('click', '.mission-edit-btn', function(e) {
                    e.preventDefault();
                    var missionId = $(this).data('mission-id');
                    var missionName = $(this).data('mission-name');
                    var missionDescription = $(this).data('mission-description');
                    var missionType = $(this).data('mission-type');
                    var missionUrl = $(this).data('mission-url');
                    var missionColor = $(this).data('mission-color');
                    var chapterId = $(this).data('chapter-id');

                    // Store mission ID for update
                    modal.getRoot().data('editing-mission-id', missionId);
                    modal.getRoot().data('editing-chapter-id', chapterId);

                    // Update modal title
                    modal.getRoot().find('.modal-title').text('Edit Mission: ' + missionName);

                    // Populate form with existing data
                    $('#missionName').val(missionName);
                    $('#missionDescription').val(missionDescription);
                    $('#missionColor').val(missionColor);
                    $('#missionType').val(missionType);

                    // Show/hide appropriate fields based on type
                    if (missionType == '1') { // URL
                        $('#urlGroup').show();
                        $('#activityGroup').hide();
                        $('#missionUrl').val(missionUrl);
                    } else if (missionType == '2') { // Activity
                        $('#urlGroup').hide();
                        $('#activityGroup').show();
                        loadCourseActivities();
                        // Set the activity URL in the hidden field
                        $('#selectedActivityUrl').val(missionUrl);
                    } else { // Voting
                        $('#urlGroup').hide();
                        $('#activityGroup').hide();
                    }

                    modal.show();
                });

                // Hide action buttons when clicking elsewhere
                $(document).on('click', function(e) {
                    if (!$(e.target).closest('.mission-container').length) {
                        $('.mission-actions').hide();
                    }
                });

            }).catch(function(error) {
                notification.addNotification({
                    message: 'Error creating mission modal: ' + error.message,
                    type: 'error'
                });
            });
        }
    };
});