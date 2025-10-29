define(['jquery', 'core/ajax', 'core/notification', 'core/modal_factory', 'core/modal_events', 'core/templates', 'core/str'],
    function($, ajax, notification, ModalFactory, ModalEvents, Templates, str) {

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

                // Show/hide URL/Activity/Section fields based on mission type
                modal.getRoot().on('change', '#missionType', function() {
                    var type = $(this).val();
                    if (type == '1') { // URL
                        $('#urlGroup').show();
                        $('#activityGroup').hide();
                        $('#sectionGroup').hide();
                    } else if (type == '2') { // Activity
                        $('#urlGroup').hide();
                        $('#activityGroup').show();
                        $('#sectionGroup').hide();
                        loadCourseActivities();
                    } else if (type == '3') { // Section
                        $('#urlGroup').hide();
                        $('#activityGroup').hide();
                        $('#sectionGroup').show();
                        loadCourseSections();
                    } else { // Voting
                        $('#urlGroup').hide();
                        $('#activityGroup').hide();
                        $('#sectionGroup').hide();
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
                 * @param {Number} selectedCmid Optional: CMID to select after loading
                 */
                function loadCourseActivities(selectedCmid) {
                    ajax.call([{
                        methodname: 'block_mission_map_get_course_activities',
                        args: { courseid: courseid }
                    }])[0].then(function(response) {
                        if (response.success && response.activities) {
                            populateActivityDropdown(response.activities, selectedCmid);
                        }
                    }).catch(function(error) {
                        notification.addNotification({
                            message: 'Error loading activities: ' + error.message,
                            type: 'error'
                        });
                    });
                }

                // Load course sections into dropdown
                /**
                 * Load course sections into dropdown
                 * @param {String} selectedSectionid Optional: Section ID to select after loading
                 */
                function loadCourseSections(selectedSectionid) {
                    ajax.call([{
                        methodname: 'block_mission_map_get_course_sections',
                        args: { courseid: courseid }
                    }])[0].then(function(response) {
                        if (response.success && response.sections) {
                            populateSectionDropdown(response.sections, selectedSectionid);
                        } else {
                            notification.addNotification({
                                message: 'Invalid response from server',
                                type: 'error'
                            });
                        }
                    }).catch(function(error) {
                        notification.addNotification({
                            message: 'Error loading sections: ' + (error.message || 'Unknown error'),
                            type: 'error'
                        });
                    });
                }

                /**
                 * Populate the activity dropdown with grouped sections
                 * @param {Array} activities
                 * @param {Number} selectedCmid Optional: CMID to select
                 */
                function populateActivityDropdown(activities, selectedCmid) {
                    var dropdown = modal.getRoot().find('#missionActivitySelect');
                    dropdown.empty();

                    // Get translated string for "Select an activity..."
                    str.get_string('select_activity', 'block_mission_map').then(function(selectActivityText) {
                        dropdown.append('<option value="">' + selectActivityText + '</option>');

                        activities.forEach(function(section) {
                            if (section.activities.length > 0) {
                                // Add section header (disabled option)
                                dropdown.append('<optgroup label="' + section.section_name + '">');

                                section.activities.forEach(function(activity) {
                                    var option = $('<option></option>')
                                        .attr('value', activity.id)
                                        .data('url', activity.url)
                                        .text(activity.name + ' (' + activity.type + ')');
                                    // Select if this is the selected activity
                                    if (selectedCmid && String(selectedCmid) == String(activity.id)) {
                                        option.attr('selected', 'selected');
                                        // Also update the hidden URL field
                                        modal.getRoot().find('#selectedActivityUrl').val(activity.url);
                                    }
                                    dropdown.append(option);
                                });

                                dropdown.append('</optgroup>');
                            }
                        });
                    });
                }

                /**
                 * Populate the section dropdown
                 * @param {Array} sections
                 * @param {String} selectedSectionid Optional: Section ID to select
                 */
                function populateSectionDropdown(sections, selectedSectionid) {
                    var dropdown = modal.getRoot().find('#missionSectionSelect');
                    dropdown.empty();

                    // Get translated string for "Select a section..."
                    str.get_string('select_section', 'block_mission_map').then(function(selectSectionText) {
                        dropdown.append('<option value="">' + selectSectionText + '</option>');

                        sections.forEach(function(section) {
                            var option = $('<option></option>')
                                .attr('value', section.id)
                                .data('url', section.url)
                                .text(section.name);
                            // Select if this is the selected section
                            if (selectedSectionid && String(selectedSectionid) == String(section.id)) {
                                option.attr('selected', 'selected');
                                // Also update the hidden URL field
                                modal.getRoot().find('#selectedSectionUrl').val(section.url);
                            }
                            dropdown.append(option);
                        });
                    });
                }

                /**
                 * Filter activities based on search term
                 * @param {string} searchTerm
                 */
                function filterActivities(searchTerm) {
                    var dropdown = modal.getRoot().find('#missionActivitySelect');
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

                // Handle section selection
                modal.getRoot().on('change', '#missionSectionSelect', function() {
                    var selectedOption = $(this).find('option:selected');
                    var sectionId = selectedOption.val();
                    var url = selectedOption.data('url');

                    if (sectionId) {
                        $('#selectedSectionUrl').val(url);
                    } else {
                        $('#selectedSectionUrl').val('');
                    }
                });

                // Handle save event using Moodle's standard save event
                modal.getRoot().on(ModalEvents.save, function(e) {
                    e.preventDefault();

                    var missionName = modal.getRoot().find('#missionName').val().trim();
                    var missionType = modal.getRoot().find('#missionType').val();
                    var missionUrl = modal.getRoot().find('#missionUrl').val();
                    var selectedActivityId = modal.getRoot().find('#missionActivitySelect').val();
                    var selectedSectionId = modal.getRoot().find('#missionSectionSelect').val();
                    var missionColor = modal.getRoot().find('#missionColor').val();
                    var missionDescription = modal.getRoot().find('#missionDescription').val();

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

                    if (missionType == '3' && !selectedSectionId) {
                        notification.addNotification({
                            message: 'Please select a course section',
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
                        url: missionType == '1' ? missionUrl :
                            (missionType == '2' ? modal.getRoot().find('#selectedActivityUrl').val() :
                            (missionType == '3' ? modal.getRoot().find('#selectedSectionUrl').val() : null)),
                        cmid: missionType == '2' ? selectedActivityId : null,
                        sectionid: missionType == '3' ? selectedSectionId : null
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
                    var form = modal.getRoot().find('#addMissionForm')[0];
                    if (form) {
                        form.reset();
                    }
                    modal.getRoot().find('#urlGroup').show();
                    modal.getRoot().find('#activityGroup').hide();
                    modal.getRoot().find('#sectionGroup').hide();
                    modal.getRoot().find('#missionActivitySearch').val('');
                    modal.getRoot().find('#missionActivitySelect').val('');
                    modal.getRoot().find('#missionSectionSelect').val('');
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
                    var form = modal.getRoot().find('#addMissionForm')[0];
                    if (form) {
                        form.reset();
                    }
                    modal.getRoot().find('#urlGroup').show();
                    modal.getRoot().find('#activityGroup').hide();
                    modal.getRoot().find('#sectionGroup').hide();

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
                    window.location.href = url;
                });

                // Handle delete mission buttons
                $(document).on('click', '.mission-delete-btn', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    var missionId = $(this).data('mission-id');
                    var missionName = $(this).data('mission-name');
                    var courseId = $(this).data('course-id');

                    // Show confirmation dialog
                    var confirmMsg = 'Are you sure you want to delete the mission "' + missionName +
                        '"? This action cannot be undone.';
                    if (!confirm(confirmMsg)) {
                        return;
                    }

                    // Call delete API
                    var request = {
                        methodname: 'block_mission_map_delete_level',
                        args: {
                            levelid: missionId,
                            courseid: courseId
                        }
                    };

                    ajax.call([request])[0].then(function(response) {
                        if (response.success) {
                            notification.addNotification({
                                message: response.message || 'Mission deleted successfully',
                                type: 'success'
                            });
                            // Reload page to reflect changes
                            window.location.reload();
                        } else {
                            notification.addNotification({
                                message: response.message || 'Failed to delete mission',
                                type: 'error'
                            });
                        }
                    }).catch(function() {
                        notification.addNotification({
                            message: 'An error occurred while deleting the mission',
                            type: 'error'
                        });
                    });
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
                    var missionCmid = $(this).data('mission-cmid');
                    var missionSectionid = $(this).data('mission-sectionid');

                    // Store mission ID for update
                    modal.getRoot().data('editing-mission-id', missionId);
                    modal.getRoot().data('editing-chapter-id', chapterId);

                    // Update modal title
                    modal.getRoot().find('.modal-title').text('Edit Mission: ' + missionName);

                    // Populate form with existing data
                    modal.getRoot().find('#missionName').val(missionName);
                    modal.getRoot().find('#missionDescription').val(missionDescription);
                    modal.getRoot().find('#missionColor').val(missionColor);
                    modal.getRoot().find('#missionType').val(missionType);

                    // Show/hide appropriate fields based on type
                    if (missionType == '1') { // URL
                        modal.getRoot().find('#urlGroup').show();
                        modal.getRoot().find('#activityGroup').hide();
                        modal.getRoot().find('#sectionGroup').hide();
                        modal.getRoot().find('#missionUrl').val(missionUrl);
                    } else if (missionType == '2') { // Activity
                        modal.getRoot().find('#urlGroup').hide();
                        modal.getRoot().find('#activityGroup').show();
                        modal.getRoot().find('#sectionGroup').hide();
                        loadCourseActivities(missionCmid);
                        // Set the activity URL in the hidden field
                        modal.getRoot().find('#selectedActivityUrl').val(missionUrl);
                    } else if (missionType == '3') { // Section
                        modal.getRoot().find('#urlGroup').hide();
                        modal.getRoot().find('#activityGroup').hide();
                        modal.getRoot().find('#sectionGroup').show();
                        loadCourseSections(missionSectionid);
                        // Set the section URL in the hidden field
                        modal.getRoot().find('#selectedSectionUrl').val(missionUrl);
                    } else { // Voting
                        modal.getRoot().find('#urlGroup').hide();
                        modal.getRoot().find('#activityGroup').hide();
                        modal.getRoot().find('#sectionGroup').hide();
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