<?php
$string['pluginname'] = 'Missions Map';
$string['mission_map'] = 'Missions Map';
$string['mission_map:addinstance'] = 'Add a new Mission Map block';
$string['mission_map:myaddinstance'] = 'Add a new Mission Map block to the My Moodle page';

$string['block_title'] = 'Your missions';

// SETTINGS
$string['map_format'] = 'Missions map display format';
$string['map_format_header'] = 'Map format settings';
$string['map_format_grid'] = 'Grid';
$string['map_format_row'] = 'Row';

// GENERAL
$string['back_map'] = 'Back to map';
$string['edit_map'] = 'Edit map';

// CHAPTERS
$string['add_page'] = 'Configure chapters';
$string['edit_chapters'] = 'Chapters configuration';
$string['view_chapters'] = 'Campaign Chapters';
$string['chapter_settings'] = 'Chapters configuration';
$string['chapter_view'] = 'Chapter: {$a}';
$string['chapter_locked'] = 'Locked';
$string['chapter_helper'] = 'Click on the dots to access the mission';
$string['chapter_countdown'] = '--d --h --m --s';

$string['campaign_add_chapter'] = 'Chapter name';
$string['campaign_locked_chapter'] = 'Lock chapter based on date?';
$string['campaign_unlock_chapter'] = 'Date to unlock chapter';
$string['campaign_add_chapter_error'] = 'Chapter name can\'t be null';
$string['create_chapter_success'] = 'Chapter created!';
$string['campaign_add_level_name'] = 'Level name';
$string['campaign_add_level_description'] = 'Level description';
$string['campaign_add_level_color'] = 'Level color';
$string['campaign_add_level_url'] = 'Level URL';
$string['campaign_add_level_error_name'] = 'Level name can\'t be null';
$string['campaign_add_level_error_url'] = 'Level URL can\'t be null';
$string['campaign_add_level_hassublevel'] = 'Has sub levels?';
$string['campaign_add_level_hasvoting'] = 'Has voting?';

// LEVEL
$string['view_level'] = 'Level settings';
$string['level_type'] = 'Level redirection type';
$string['level_option_url'] = 'Redirects to URL';
$string['level_option_section'] = 'Redirects to Course Section';
$string['level_option_voting'] = 'Redirects to a Voting Session';
$string['level_option_sublevel'] = 'Redirects to a Sublevel';
$string['level_select_course'] = 'Choose the course';
$string['level_select_section'] = 'Choose a section';
$string['level_course'] = 'Course';
$string['level_section'] = 'Course section for completion info';

// LEVEL COLORS
$string['level_color_blue'] = 'Blue';
$string['level_color_green'] = 'Green';
$string['level_color_orange'] = 'Orange';
$string['level_color_red'] = 'Red';
$string['level_color_purple'] = 'Purple';
$string['level_color_yellow'] = 'Yellow';
$string['level_color_pink'] = 'Pink';
$string['level_color_brown'] = 'Brown';
$string['level_color_grey'] = 'Grey';
$string['level_color_black'] = 'Black';
$string['level_color_white'] = 'White';
$string['level_color_gray'] = 'Gray';

// VOTING
$string['view_voting'] = 'Voting session';
$string['edit_voting'] = 'Voting edit';
$string['mission_map_voting_settings'] = 'Voting configuration';

$string['voting_notready'] = 'This voting session is not yet open!';

$string['voting_select_course'] = 'Select a course to fetch sections';
$string['voting_select_section'] = 'Select a section to redirect';

$string['voting_option_name'] = 'Option name';
$string['voting_option_description'] = 'Option description';
$string['voting_option_type'] = 'Option type';
$string['voting_option_url'] = 'Redirects to URL';
$string['voting_option_course'] = 'Course to fetch sections';
$string['voting_option_section'] = 'Redirects to this course section';
$string['voting_option_sublevel'] = 'Redirects to sublevel';
$string['voting_option_title'] = 'Voting option {no}';

$string['voting_description'] = 'Title of voting';

$string['voting_type'] = 'Model of voting';
$string['voting_type_help'] = '<b>All participants</b>: Every enrolled member of the course has a chance to cast a vote.<br/><br/><b>Group participants</b>: Votes will be computed inside every group in the course.';

$string['voting_type_all'] = 'All participants';
$string['voting_type_groups'] = 'Group participants';

$string['voting_algorithm'] = 'Voting mechanic';
$string['voting_algorithm_help'] = '<b>Simple majority</b>: Every member of population must vote. Winner option is decided upon simple majority (50% + 1).<br/><br/> <b>Time bound voting</b>: Not every member of population needs vote. Valid votes will be computed if casted before the deadline. Option with majority of votes wins.<br/><br/><b>Threshold voting</b>: Chosen N% of members from population must cast a vote. Option with simple majority (50% of N% + 1) wins.<br/><br/><b>Random selection</b>: Winner option will be immediately chosen randomly (50% chance of selection).';

$string['voting_al_simplemajority'] = 'Simple majority';
$string['voting_al_timebound'] = 'Time bound voting';
$string['voting_al_threshold'] = 'Threshold voting';
$string['voting_al_random'] = 'Random selection';
$string['voting_deadline'] = 'Deadline for voting';
$string['voting_threshold'] = 'Threshold (%)';

$string['voting_tiebreak'] = 'Tie breaking strategy';
$string['voting_tiebreak_help'] = '<b>Second round</b>: Every member of population must cast a second vote before a given deadline. Winner option is decided upon simple majority (50% + 1) or, if no vote is cast, randomly selected (50% chance of selection).<br/><br/><b>Minerva vote</b>: Appointed "Minerva voter" must cast a deciding vote if before a given deadline. If no vote is cast, option is selected randomly (50% chance of selection).<br/><br/><b>Random selection</b>: Winner option will be immediately chosen randomly (50% chance of selection).';

$string['voting_tiebreak_secondround'] = 'Second round';
$string['voting_tiebreak_minerva'] = 'Minerva vote';
$string['voting_tiebreak_random'] = 'Random selection';

$string['voting_minerva'] = 'Appointed Minerva voter';
$string['voting_tiebreak_deadline'] = 'Deadline for tie breaking decision';

$string['voting_save'] = 'Save voting session';

$string['voting_choose_path'] = 'Choose your path';
$string['voting_totalizing'] = 'We are waiting for your group\'s votes';
$string['voting_tie'] = 'It was a tie!';
$string['voting_tie_info'] = 'Here comes the tie breaker';
$string['voting_completed'] = 'Your path is chosen!';
$string['vote_intro'] = 'Intro';
$string['vote_save'] = 'Select';
$string['vote_continue'] = 'Proceed to chosen mission';
$string['single_vote'] = 'vote';
$string['votes'] = 'votes';

// FORMS
$string['form_settings'] = 'Campaign settings';
$string['form_course'] = 'Course to pull sections from';
$string['form_chapters_header'] = 'Chapter {no} config';
$string['form_chapter'] = 'Chapter name';
$string['form_sections_blank'] = 'Chapter missions';
$string['form_course_blank'] = 'Select course';
$string['form_add_chapter'] = 'Add 1 chapter';
$string['form_supermissions'] = 'Name of supermission';
$string['form_missions'] = 'Number of missons in chapter';
$string['mission_no'] = 'mission-{no}';

$string['create_level_success'] = 'Level created succesfuly!';
