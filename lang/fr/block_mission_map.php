<?php
$string['pluginname'] = 'Carte des missions';
$string['mission_map'] = 'Carte des missions';
$string['mission_map:addinstance'] = 'Ajouter un nouveau bloc de carte des missions';
$string['mission_map:myaddinstance'] = 'Ajouter un nouveau bloc de carte des missions à la page "Mon Moodle"';

$string['block_title'] = 'Vos missions';

// SETTINGS
$string['map_format'] = 'Format d\'affichage de la carte des missions';
$string['map_format_header'] = 'Paramètres de format de la carte';
$string['map_format_grid'] = 'Grille';
$string['map_format_row'] = 'Ligne';
$string['map_format_label'] = 'Afficher une étiquette sur la carte';

// GENERAL
$string['back_map'] = 'Retour à la carte';
$string['edit_map'] = 'Modifier la carte';

// CHAPTERS
$string['add_page'] = 'Configurer les chapitres';
$string['edit_chapters'] = 'Configuration des chapitres';
$string['view_chapters'] = 'Chapitres de campagne';
$string['chapter_settings'] = 'Configuration des chapitres';
$string['chapter_view'] = 'Chapitre : {$a}';
$string['chapter_locked'] = 'Verrouillé';
$string['chapter_helper'] = 'Cliquez sur les points pour accéder à la mission';
$string['chapter_countdown'] = '--j --h --m --s';

$string['add_chapter'] = 'Ajouter un chapitre';
$string['campaign_add_chapter'] = 'Nom du chapitre';
$string['campaign_locked_chapter'] = 'Verrouiller le chapitre en fonction de la date ?';
$string['campaign_unlock_chapter'] = 'Date de déverrouillage du chapitre';
$string['campaign_add_chapter_error'] = 'Le nom du chapitre ne peut pas être vide';
$string['create_chapter_success'] = 'Chapitre créé !';
$string['campaign_add_level_name'] = 'Nom du niveau';
$string['campaign_add_level_description'] = 'Description du niveau';
$string['campaign_add_level_color'] = 'Couleur du niveau';
$string['campaign_add_level_url'] = 'URL du niveau';
$string['campaign_add_level_error_name'] = 'Le nom du niveau ne peut pas être vide';
$string['campaign_add_level_error_url'] = 'L\'URL du niveau ne peut pas être vide';
$string['campaign_add_level_hassublevel'] = 'Possède des sous-niveaux ?';
$string['campaign_add_level_hasvoting'] = 'A des votes ?';

// LEVEL
$string['view_level'] = 'Paramètres du niveau';
$string['level_type'] = 'Type de redirection de niveau';
$string['level_option_url'] = 'Redirige vers l\'URL';
$string['level_option_section'] = 'Redirige vers la section du cours';
$string['level_option_voting'] = 'Redirige vers une session de vote';
$string['level_option_sublevel'] = 'Redirige vers un sous-niveau';
$string['level_select_course'] = 'Choisir le cours';
$string['level_select_section'] = 'Choisir une section';
$string['level_course'] = 'Cours';
$string['level_section'] = 'Section de cours pour les informations de complétion';

// LEVEL COLORS
$string['level_color_blue'] = 'Bleu';
$string['level_color_green'] = 'Vert';
$string['level_color_orange'] = 'Orange';
$string['level_color_red'] = 'Rouge';
$string['level_color_purple'] = 'Violet';
$string['level_color_yellow'] = 'Jaune';
$string['level_color_pink'] = 'Rose';
$string['level_color_brown'] = 'Marron';
$string['level_color_grey'] = 'Gris';
$string['level_color_black'] = 'Noir';
$string['level_color_white'] = 'Blanc';
$string['level_color_gray'] = 'Gris';

// LEVEL LEGEND
$string['legend_information'] = 'Informations et documents de soutien';
$string['legend_optional'] = 'Activités facultatives';
$string['legend_required'] = 'Activités obligatoires';

// VOTING
$string['view_voting'] = 'Session de vote';
$string['edit_voting'] = 'Modifier le vote';
$string['mission_map_voting_settings'] = 'Configuration du vote';

$string['voting_notready'] = 'Cette session de vote n\'est pas encore ouverte !';

$string['voting_select_course'] = 'Sélectionnez un cours pour récupérer les sections';
$string['voting_select_section'] = 'Sélectionnez une section pour rediriger';

$string['voting_option_name'] = 'Nom de l\'option';
$string['voting_option_description'] = 'Description de l\'option';
$string['voting_option_type'] = 'Type d\'option';
$string['voting_option_url'] = 'Rediriger vers une URL';
$string['voting_option_course'] = 'Cours pour récupérer les sections';
$string['voting_option_section'] = 'Rediriger vers cette section de cours';
$string['voting_option_sublevel'] = 'Rediriger vers un sous-niveau';
$string['voting_option_title'] = 'Option de vote {no}';

$string['voting_description'] = 'Titre du vote';

$string['voting_type'] = 'Modèle de vote';
$string['voting_type_help'] = '<b>Tous les participants</b>: Chaque membre inscrit au cours a la possibilité de voter.<br/><br/><b>Participants de groupe</b>: Les votes seront calculés dans chaque groupe du cours.';

$string['voting_type_all'] = 'Tous les participants';
$string['voting_type_groups'] = 'Participants de groupe';

$string['voting_algorithm'] = 'Mécanique de vote';
$string['voting_algorithm_help'] = '<b>Majorité simple</b>: Chaque membre de la population doit voter. L\'option gagnante est décidée par majorité simple (50% + 1).<br/><br/> <b>Vote limité dans le temps</b>: Tous les membres de la population n\'ont pas besoin de voter. Les votes valides seront pris en compte s\'ils sont émis avant la date limite. L\'option ayant la majorité des votes l\'emporte.<br/><br/><b>Vote seuil</b>: N% des membres choisis de la population doivent voter. L\'option ayant la majorité simple (50% de N% + 1) l\'emporte.<br/><br/><b>Sélection aléatoire</b>: L\'option gagnante est immédiatement choisie au hasard (50% de chance de sélection).';

$string['voting_al_simplemajority'] = 'Majorité simple';
$string['voting_al_timebound'] = 'Vote limité dans le temps';
$string['voting_al_threshold'] = 'Vote seuil';
$string['voting_al_random'] = 'Sélection aléatoire';
$string['voting_deadline'] = 'Date limite pour voter';
$string['voting_threshold'] = 'Seuil (%)';

$string['voting_tiebreak'] = 'Stratégie de départage';
$string['voting_tiebreak_help'] = '<b>Deuxième tour</b>: Chaque membre de la population doit voter une deuxième fois avant une date limite donnée. L\'option gagnante est décidée par majorité simple (50% + 1) ou, s\'il n\'y a aucun vote, sélectionnée aléatoirement (50% de chance de sélection).<br/><br/><b>Vote de Minerva</b>: Le "votant Minerva" désigné doit émettre un vote décisif avant une date limite donnée. Si aucun vote n\'est émis, l\'option est sélectionnée au hasard (50% de chances de sélection).<br/><br/><b>Sélection aléatoire</b>: L\'option gagnante sera choisie immédiatement au hasard (50% de chances de sélection).';

$string['voting_tiebreak_secondround'] = 'Second tour';
$string['voting_tiebreak_minerva'] = 'Vote de Minerve';
$string['voting_tiebreak_random'] = 'Sélection aléatoire';

$string['voting_minerva'] = 'Votant de Minerve désigné';
$string['voting_tiebreak_deadline'] = 'Date limite pour la décision de la résolution d\'égalité';

$string['voting_save'] = 'Enregistrer la session de vote';

$string['voting_choose_path'] = 'Choisissez votre chemin';
$string['voting_totalizing'] = 'Nous attendons les votes de votre groupe';
$string['voting_tie'] = 'C\'était une égalité !';
$string['voting_tie_info'] = 'Voici le dénouement de l\'égalité';
$string['voting_completed'] = 'Votre chemin est choisi!';
$string['vote_intro'] = 'Introduction';
$string['vote_save'] = 'Sélectionner';
$string['vote_continue'] = 'Passer à la mission choisie';
$string['single_vote'] = 'vote';
$string['votes'] = 'votes';

// FORMS
$string['form_settings'] = 'Paramètres de la campagne';
$string['form_course'] = 'Cours à extraire des sections de';
$string['form_chapters_header'] = 'Configuration du chapitre {no}';
$string['form_chapter'] = 'Nom du chapitre';
$string['form_sections_blank'] = 'Missions du chapitre';
$string['form_course_blank'] = 'Sélectionner un cours';
$string['form_add_chapter'] = 'Ajouter un chapitre';
$string['form_supermissions'] = 'Nom de la supermission';
$string['form_missions'] = 'Nombre de missions dans le chapitre';
$string['mission_no'] = 'mission-{no}';

$string['create_level_success'] = 'Niveau créé avec succès !';
