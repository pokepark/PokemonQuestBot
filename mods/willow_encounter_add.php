<?php
// Write to log.
debug_log('WILLOW_ENCOUNTER_ADD()');

// For debug.
//debug_log($update);
//debug_log($data);

// Check access.
bot_access_check($update, 'willow');

// Get encounter id.
$id = $data['id'];

// Get data arg.
$arg = $data['arg'];

// add-50#0,0,0
// edit-50#0,0,0

// Get data arg.
if(strpos($arg, '-') !== false) {
    // Split arg
    $argval = explode("-", $arg);
    $action = $argval[0];
    $dex_ids = $argval[1];

// Add new encounter.
} else if($arg == 0) {
    $action = 'add';
    $dex_ids = 0;

// Edit existing encounter.
} else if($arg == 1) {
    $action = 'edit';
    $dex_ids = 0;
}

// Add encounter.
if ($id == 0 && $action == 'add') {
    // Build message.
    $msg = get_all_questlist_entries(true);
    $msg .= CR . '<b>' . getTranslation('encounter') . ' — ' . getTranslation('select_id_to_add') . '</b>';

    // Get keys.
    $keys = get_all_questlist_keys('willow_encounter_add', 'add-0#0,0,0', true);

    // Add abort navigation key.
    $nav_keys = [];
    $nav_keys[] = universal_inner_key($keys, '0', 'willow', 'encounter', getTranslation('back'));
    $nav_keys[] = universal_inner_key($keys, '0', 'exit', '0', getTranslation('abort'));

    // Get the inline key array.
    $keys[] = $nav_keys;

// Edit encounter.
} else if ($id == 0 && $action == 'edit') {
    // Get all encounters from json.
    $msg = get_all_encounterlist_entries();
    $msg .= CR . '<b>' . getTranslation('encounter') . ' — ' . getTranslation('select_id_to_edit') . '</b>';
    $keys = get_all_encounterlist_keys('willow_encounter_add', 'edit-0#0,0,0');

    // Add abort navigation key.
    $nav_keys = [];
    $nav_keys[] = universal_inner_key($keys, '0', 'willow', 'encounter', getTranslation('back'));
    $nav_keys[] = universal_inner_key($keys, '0', 'exit', '0', getTranslation('abort'));

    // Get the inline key array.
    $keys[] = $nav_keys;

// Set pokedex IDs
} else if ($id > 0 && (substr_count($arg, '-') == 1) && ($action == 'add' || $action == 'edit')) {
    // Init empty keys.
    $keys = [];

    // Set message.
    $msg = '<b> ' . getTranslation($action . '_this_encounter') . ' </b>' . CR . CR;

    // Get start value
    $actval_split = explode('#', $dex_ids);
    $start = $actval_split[0];

    // Get first, second and third pokemon
    $sel_pokemons = explode(',', $actval_split[1]);
    $first = $sel_pokemons[0];
    $second = $sel_pokemons[1];
    $third = $sel_pokemons[2];

    // New args.
    $new_arg = $action . '-' . $start . '#' . $first . ',' . $second . ',' . $third;
    $new_prev_25 = $action . '-' . ($start - 25) . '#' . $first . ',' . $second . ',' . $third;
    $new_prev_50 = $action . '-' . ($start - 50) . '#' . $first . ',' . $second . ',' . $third;
    $new_next_25 = $action . '-' . ($start + 25) . '#' . $first . ',' . $second . ',' . $third;
    $new_next_50 = $action . '-' . ($start + 50) . '#' . $first . ',' . $second . ',' . $third;

    // Save
    if($action == 'add') {
        $new_save = 'save-0#' . $first . ',' . $second . ',' . $third;
    } else if($action == 'edit') {
        $new_save = 'update-0#' . $first . ',' . $second . ',' . $third;
    }

    // Reset and currently selected pokemons.
    $msg_poke = '...';
    if($first > 0 && $second == 0) {
        $new_reset = $action . '-' . $start . '#0,0,0';
        $msg_poke = getTranslation('pokemon_id_' . $first);
    } else if($first > 0 && $second > 0 && $third == 0) {
        $new_reset = $action . '-' . $start . '#' . $first . ',0,0';
        $msg_poke = getTranslation('pokemon_id_' . $first) . SP . getTranslation('or') . SP . getTranslation('pokemon_id_' . $second);
    } else if($first > 0 && $second > 0 && $third > 0) {
        $new_reset = $action . '-' . $start . '#' . $first . ',' . $second . ',0';
        $msg_poke = getTranslation('pokemon_id_' . $first) . ', ' . getTranslation('pokemon_id_' . $second) . SP . getTranslation('or') . SP . getTranslation('pokemon_id_' . $third);
    }

    // Get pokemon.
    $limit = count_all_json_pokemon();
    //$end = ($limit > $start + 50) ? ($start + 50) : $limit;

    // Get pokemon keys.
    if($third == 0) {
        $keys = get_all_json_pokemon_keys($id, 'willow_encounter_add', $new_arg, $start, $first, $second, $third);
    }

    // Add navigation keys.
    $n_25_keys = [];
    $n_50_keys = [];

    // Previous 25 key.
    if($start - 25 >= 0 && $third == 0) {
        $n_25_keys[] = universal_inner_key($keys, $id, 'willow_encounter_add', $new_prev_25, getTranslation('back') . ' (-25)');
    }

    // Next 25 key.
    if($limit > $start + 25 && $third == 0) {
        $n_25_keys[] = universal_inner_key($keys, $id, 'willow_encounter_add', $new_next_25, getTranslation('next') . ' (+25)');
    }

    // Previous 50 key.
    if($start - 50 >= 0 && $third == 0) {
        $n_50_keys[] = universal_inner_key($keys, $id, 'willow_encounter_add', $new_prev_50, getTranslation('back') . ' (-50)');
    }

    // Next 50 key.
    if($limit > $start + 50 && $third == 0) {
        $n_50_keys[] = universal_inner_key($keys, $id, 'willow_encounter_add', $new_next_50, getTranslation('next') . ' (+50)');
    }

    // Get the inline key array.
    $keys[] = $n_25_keys;
    $keys[] = $n_50_keys;

    // Add save and reset key.
    if($first > 0) {
        $s_keys = [];
        $s_keys[] = universal_inner_key($keys, $id, 'willow_encounter_add', $new_reset, getTranslation('reset'));
        $s_keys[] = universal_inner_key($keys, $id, 'willow_encounter_add', $new_save, EMOJI_DISK);
        $keys[] = $s_keys;
    }

    // Set message.
    $msg .= get_all_json_pokemon($start, $first, $second, $third);

    // Get quest.
    $quest_enc = '';
    if($action == 'add') {
        $msg .= getTranslation('quest') . ':' . CR;
        $quest_enc = get_formatted_questlist_entry($id);
        $msg .= $quest_enc . CR . CR;
    } else if($action == 'edit') {
        $msg .= getTranslation('quest') . ':' . CR;
        $quest_enc = get_formatted_encounterlist_entry($id);
        $msg .= $quest_enc . CR;
    }

    // Build message.
    $msg .= '<b>';
    $msg .= getTranslation('pokemon') . ' — ';
    $msg .= ($third == 0) ? (getTranslation('select_id_to_' . $action)) : (getTranslation($action . '_this_encounter'));
    $msg .= '</b>';

    // Add navigation keys.
    $nav_keys = [];
    $nav_keys[] = universal_inner_key($keys, '0', 'willow_encounter_add', $action . '-0#0,0,0', getTranslation('back'));
    $nav_keys[] = universal_inner_key($keys, '0', 'exit', '0', getTranslation('abort'));

    // Get the inline key array.
    $keys[] = $nav_keys;

// Save encounter.
} else if ($id > 0 && $action == 'save') {
    // Write to log.
    debug_log('Confirmation received to save the encounter');

    // Save encounter.
    add_encounterlist_entry($id, $dex_ids);

    // Get inserted database id.
    $id = my_insert_id();

    // Set message.
    $msg = getTranslation('encounter_successfully_saved') . CR . CR;

    // Show saved encounter.
    $msg .= getTranslation('quest') . ':' . CR;
    $quest_enc = get_formatted_encounterlist_entry($id);
    $msg .= $quest_enc . CR;

    // Set empty keys.
    $keys = [];

// Update encounter.
} else if ($id > 0 && $action == 'update') {
    // Write to log.
    debug_log('Confirmation received to update the encounter');

    // Update encounter.
    update_encounterlist_entry($id, $dex_ids);

    // Set message.
    $msg = getTranslation('encounter_successfully_saved') . CR . CR;

    // Show saved encounter.
    $msg .= getTranslation('quest') . ':' . CR;
    $quest_enc = get_formatted_encounterlist_entry($id);
    $msg .= $quest_enc . CR;

    // Set empty keys.
    $keys = [];

}

// Telegram JSON array.
$tg_json = array();

// Edit message.
$tg_json[] = edit_message($update, $msg, $keys, ['disable_web_page_preview' => 'true'], true);

// Build callback message string.
$callback_response = 'OK';

// Answer callback.
$tg_json[] = answerCallbackQuery($update['callback_query']['id'], $callback_response, true);

// Telegram multicurl request.
curl_json_multi_request($tg_json);

exit();
