<?php
$string['pluginname'] = 'Mapa de Misiones';
$string['mission_map'] = 'Mapa de Misiones';
$string['mission_map:addinstance'] = 'Añadir un nuevo bloque del Mapa de Misiones';
$string['mission_map:myaddinstance'] = 'Añadir un nuevo bloque del Mapa de Misiones a la página de Mi Moodle';

$string['block_title'] = 'Tus misiones';

// GENERAL
$string['back_map'] = 'Volver al mapa';
$string['edit_map'] = 'Editar mapa';

// CHAPTERS 
$string['add_page'] = 'Editar capítulos';
$string['edit_chapters'] = 'Edición de capítulos';
$string['view_chapters'] = 'Capítulos de la Campaña';
$string['chapter_settings'] = 'Configuración de los capítulos';
$string['chapter_view'] = 'Capítulo: {$a}';
$string['chapter_locked'] = 'Bloqueado';
$string['chapter_helper'] = 'Click on the dots to access the mission'; //
$string['chapter_countdown'] = '--d --h --m --s';

$string['campaign_add_chapter'] = 'Nombre del capítulo';
$string['campaign_locked_chapter'] = 'Bloquear capítulo en función de la fecha?';
$string['campaign_unlock_chapter'] = 'Fecha para desbloquear el capítulo';
$string['campaign_add_chapter_error'] = 'El nombre del capítulo no puede dejarse en blanco';
$string['create_chapter_success'] = '¡Capítulo creado!';
$string['campaign_add_level_name'] = 'Nombre del nivel';
$string['campaign_add_level_description'] = 'Descripción del nivel';
$string['campaign_add_level_url'] = 'URL del nivel';
$string['campaign_add_level_error_name'] = 'En nombre del nivel no puede dejarse en blanco';
$string['campaign_add_level_error_url'] = 'La URL del nivel no puede dejarse en blanco';
$string['campaign_add_level_hassublevel'] = 'Tiene sub-niveles?';
$string['campaign_add_level_hasvoting'] = 'Tiene votación?';

// LEVEL //NIVEL
$string['view_level'] = 'Configuración de nivel';
$string['level_type'] = 'Tipo de redirección de nivel';
$string['level_option_url'] = 'Redirige a la URL';
$string['level_option_section'] =  'Redirige a la sección curso';
$string['level_option_voting'] = 'Redirige a la sesión de votación';
$string['level_option_sublevel'] = 'Redirige a Subnivel';
$string['level_select_course'] =  'Selecciona la Campaña';
$string['level_course'] = 'Campaña';
$string['level_section'] = 'Sección';

// VOTING //VOTACIÓN
$string['view_voting'] = 'Sesión de votación';
$string['edit_voting'] = 'Editar votación';
$string['mission_map_voting_settings'] =  'Configurar la votacion';

$string['voting_notready'] = 'Esta votación aún no está abierta!';

$string['voting_select_course'] = 'Seleccionar un curso para buscar secciones';
$string['voting_select_section'] = 'Seleccionar una sección a la que redirigir';

$string['voting_option_name'] =  'Nombre de la opción';
$string['voting_option_description'] = 'Descripción de la opción';
$string['voting_option_type'] = 'Tipo de opción';
$string['voting_option_url'] = 'Redirige a la URL';
$string['voting_option_course'] = 'Campaña en el que buscar secciones';
$string['voting_option_section'] = 'Redirige a esta sección del campaña';
$string['voting_option_sublevel'] =  'Redirige al subnivel';
$string['voting_option_title'] = 'Opción de votación {no}';

$string['voting_description'] = 'Descripción de esta sesión de votación: propósito, método, resultados, etc.';

$string['voting_type'] = 'Modelo de votación';
$string['voting_type_help'] =  '<b>Todos los participantes</b>: Todo miembro alistados en la campaña tiene la opción de participar en la votación.<br/><br/><b>Participantes del grupos</b>: Los votos serán contados dentro de cada equipo en esta campaña.';

$string['voting_type_all'] = 'Todos los participantes';
$string['voting_type_groups'] = 'Participantes del equipo';

$string['voting_algorithm'] = 'Mecánica de la votación';
$string['voting_algorithm_help'] = '<b>Mayoría simple</b>: Todos los miembros del equipo deben votar. La opción ganadora se decide por mayoría simple. (50% + 1).<br/><br/> <b>Votación limidata por tiempo</b>: No todos los miembros del equipo necesitan votar. Los votos válidos serán contados si se han emitido antes del límite de tiempo. La opción con la mayoría de votos gana.<br/><br/><b>Umbral de votación</b>: El % de miembros del equipo seleccionado deberán votar. La opción con mayoría simple (50% of N% + 1) gana.<br/><br/><b>Selección aleatoria</b>: La opción ganadora será elegida inmediatamente de manera aleatoria (50% chance of selection).';

$string['voting_al_simplemajority'] = 'Mayoría simple';
$string['voting_al_timebound'] = 'Votación limidata por tiempo';
$string['voting_al_threshold'] = 'Umbral de votación';
$string['voting_al_random'] = 'Selección aleatoria';
$string['voting_deadline'] = 'Límite de tiempo';
$string['voting_threshold'] = 'Umbral (%)';

$string['voting_tiebreak'] = 'Estrategia de desempate';
$string['voting_tiebreak_help'] = '<b>Segunda ronda</b>: Cada miembro del equipo deberá votar por segunda vez antes de un límite de tiempo establecido. La opción ganadora se decide por mayoría simple (50% + 1) o, si no hay votos emitidos, seleccionada de manera aleatoria (50% chance of selection).<br/><br/><b>Votación Minerva</b>: Un “emisor de voto Minerva" seleccionado debería emitir un voto de que decida el desempate antes de un límite de tiempo establecido. Si no hay voto emitido, la opción es elegida de manera aleatoria (50% chance of selection).<br/><br/><b>Selección aleatoria</b>: La opción ganadora será seleccionada inmediatamente de manera aleatoria (50% chance of selection).';

$string['voting_tiebreak_secondround'] = 'Segunda ronda';
$string['voting_tiebreak_minerva'] = 'Votación Minerva';
$string['voting_tiebreak_random'] = 'Selección aleatoria';

$string['voting_minerva'] = 'Emisor de voto Minerva elegido';
$string['voting_tiebreak_deadline'] = 'Límite de tiempo para el desempate';

$string['voting_save'] = 'Guardar la sesión de votación';

$string['voting_choose_path'] = 'Elige tu camino';
$string['voting_totalizing'] = 'Esperando los votos del equipo';
$string['voting_tie'] = 'Es un empate!';
$string['voting_tie_info'] = 'Aquí biene el desempate';
$string['voting_completed'] = 'Tu camino ha sido elegido!';
$string['vote_intro'] = 'Intro';
$string['vote_save'] = 'Elige';
$string['vote_continue'] = 'Continua a la mission elegida';
$string['single_vote'] = 'voto';
$string['votes'] = 'votos';

// FORMS
$string['form_settings'] = 'Configuración de la campaña';
$string['form_course'] = 'Curso desde el que sacar las secciones';
$string['form_chapters_header'] = 'Configuración del capítulo {no}';
$string['form_chapter'] = 'Nombre del capítulo';
$string['form_sections_blank'] = 'Misiones del capítulo';
$string['form_course_blank'] = 'Seleccionar campaña';
$string['form_add_chapter'] = 'Añadir 1 capítulo';
$string['form_supermissions'] = 'Nombre de la supermisión o sesión';
$string['form_missions'] = 'Número de misiones en el capítulo';
$string['mission_no'] = 'misión-{no}';

$string['create_level_success'] = 'Nivel creado de con éxito!';
