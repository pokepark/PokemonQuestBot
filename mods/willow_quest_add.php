<?php
// Write to log.
debug_log('WILLOW_QUEST_ADD()');

// For debug.
//debug_log($update);
//debug_log($data);

// Check access.
bot_access_check($update, 'willow');

// Get quest type id.
$cb_id = $data['id'];

// Get the event id and quest id.
if(strpos($cb_id, '-') !== false) {
    // Split id.
    $idval = explode('-', $cb_id);
    $event = $idval[0];
    $id = $idval[1];
} else {
    $id = $data['id'];
    $event = 0;
}

// Write to log.
debug_log($event, 'EVENT:');
debug_log($id, 'ID:');

// Get data arg.
$arg = $data['arg'];

// Get data arg.
if(strpos($arg, '-') !== false) {
    // Split arg
    $argval = explode('-', $arg);
    $action = $argval[0];
    $qty = $argval[1];

    // Get quest action source
    if(substr_count($arg, '-') >= 2) {
        $action_source = $argval[2];
    }

    // Get quest action value
    if (substr_count($arg, '-') >= 3) {
        $action_value = $argval[3];
    }
} else {
    $action = $arg;
    $qty = 0;
}

// Event?
if(is_numeric($event) && $event == 0 && $id == 0 && $action == 0 && $qty == 0) {
    // Get all quest events from json.
    $msg = get_all_json_quest_event();
    $msg .= CR . '<b>' . getTranslation('quest') . ' — ' . getTranslation('select_event') . '</b>';
    $keys = get_all_json_quest_event_keys('willow_quest_add', 0);

    // Add abort navigation key.
    $nav_keys = [];
    $nav_keys[] = universal_inner_key($keys, '0', 'willow', 'quest', getTranslation('back'));
    $nav_keys[] = universal_inner_key($keys, '0', 'exit', '0', getTranslation('abort'));

    // Get the inline key array.
    $keys[] = $nav_keys;

// Quest type.
} else if ($id == 0 && $action == 0 && $qty == 0) {
    // Get all quest types from json.
    $msg = get_all_json_quest_type();
    // Event?
    if(is_numeric($event)) {
        $msg .= CR . '<b>' . getTranslation('event') . ': ' . getTranslation('quest_event_' . $event) . '</b>' . CR;
    }
    $msg .= CR . '<b>' . getTranslation('quest') . ' — ' . getTranslation('quest_select_type') . '</b>';
    $keys = get_all_json_quest_type_keys('willow_quest_add', 'add-0', $event);

    // Add abort navigation key.
    $nav_keys = [];
    $nav_keys[] = universal_inner_key($keys, '0', 'willow_quest_add', '0', getTranslation('back'));
    $nav_keys[] = universal_inner_key($keys, '0', 'exit', '0', getTranslation('abort'));

    // Get the inline key array.
    $keys[] = $nav_keys;

// Quantity
} else if ($id > 0 && $action == 'add' && (substr_count($arg, '-') == 1)) {
    // Set message.
    $msg = '<b> ' . getTranslation('add_this_quest') . ' </b>' . CR . CR;

    // Get quantity
    $quest_type = getTranslation('quest_type_' . $id);
    $msg .= getTranslation('quest') . ': <b>' . CR;
    // Event?
    if(is_numeric($event) && $event > 0) {
        $msg .= getTranslation('quest_event_' . $event) . ':' . SP;
    }
    $msg .= $quest_type . SP . $qty . SP . ' ...</b>';
    $msg .= CR . CR . '<b>' . getTranslation('quest_select_qty') . '</b>';

    // Keys to specify qty
    $keys = quantity_keys($cb_id, 'willow_quest_add', 'add-' . $qty, 'quest');

    // Add abort navigation key.
    $nav_keys = [];
    $nav_keys[] = universal_inner_key($keys, '0', 'willow_quest_add', '0', getTranslation('back'));
    $nav_keys[] = universal_inner_key($keys, '0', 'exit', '0', getTranslation('abort'));

    // Get the inline key array.
    $keys[] = $nav_keys;

// Action from language file, pokedex IDs or pokemon types
} else if ($id > 0 && $action == 'add' && (substr_count($arg, '-') == 2)) {
    // Set message.
    $msg = '<b> ' . getTranslation('add_this_quest') . ' </b>' . CR . CR;

    // Get quantity
    $quest_type = getTranslation('quest_type_' . $id);
    $msg .= getTranslation('quest') . ': <b>' . CR;
    // Event?
    if(is_numeric($event) && $event > 0) {
        $msg .= getTranslation('quest_event_' . $event) . ':' . SP;
    }
    $msg .= $quest_type . SP . $qty . SP . ' ...</b>' . CR . CR;
    $msg .= '<b>' . getTranslation('quest_select_type') . '</b>';

    // Init empty keys array.
    $keys = [];

    // Create keys array.
    $keys = [
        [
            [
                'text'          => getTranslation('pokemon'),
                'callback_data' => $cb_id . ':willow_quest_add:' . $action . '-' . $qty . '-' . 'dex-0#0,0,0'
            ]
        ],
        [
            [
                'text'          => getTranslation('pokemon_types'),
                'callback_data' => $cb_id . ':willow_quest_add:' . $action . '-' . $qty . '-' . 'type-0#0,0,0'
            ]
        ],
        [
            [
                'text'          => getTranslation('other'),
                'callback_data' => $cb_id . ':willow_quest_add:' . $action . '-' . $qty . '-' . 'file-0'
            ]
        ],
    ];

    // Add navigation keys.
    $nav_keys = [];
    $nav_keys[] = universal_inner_key($keys, $cb_id, 'willow_quest_add', 'add-0', getTranslation('back'));
    $nav_keys[] = universal_inner_key($keys, '0', 'exit', '0', getTranslation('abort'));

    // Get the inline key array.
    $keys[] = $nav_keys;

// Set quest action, pokedex IDs or pokemon types
} else if ($id > 0 && $action == 'add' && (substr_count($arg, '-') == 3)) {
    // Init empty keys.
    $keys = [];

    // Set message.
    $msg = '<b> ' . getTranslation('add_this_quest') . ' </b>' . CR . CR;

    // Get quantity
    $quest_type = getTranslation('quest_type_' . $id);

    // Pokedex IDs
    if($action_source == 'dex') {
        // Get start value
        $actval_split = explode('#', $action_value);
        $start = $actval_split[0];

        // Get first, second and third pokemon
        $sel_pokemons = explode(',', $actval_split[1]);
        $first = $sel_pokemons[0];
        $second = $sel_pokemons[1];
        $third = $sel_pokemons[2];

        // New args.
        $new_arg = $action . '-' . $qty . '-dex-' . $start . '#' . $first . ',' . $second . ',' . $third;
        $new_prev_25 = $action . '-' . $qty . '-dex-' . ($start - 25) . '#' . $first . ',' . $second . ',' . $third;
        $new_prev_50 = $action . '-' . $qty . '-dex-' . ($start - 50) . '#' . $first . ',' . $second . ',' . $third;
        $new_next_25 = $action . '-' . $qty . '-dex-' . ($start + 25) . '#' . $first . ',' . $second . ',' . $third;
        $new_next_50 = $action . '-' . $qty . '-dex-' . ($start + 50) . '#' . $first . ',' . $second . ',' . $third;
        $new_save = 'save-' . $qty . '-dex-0#' . $first . ',' . $second . ',' . $third;

        // Reset and currently selected pokemons.
        $msg_poke = '...';
        if($first > 0 && $second == 0) {
            $new_reset = $action . '-' . $qty . '-dex-' . $start . '#0,0,0';
            $msg_poke = getTranslation('pokemon_id_' . $first);
        } else if($first > 0 && $second > 0 && $third == 0) {
            $new_reset = $action . '-' . $qty . '-dex-' . $start . '#' . $first . ',0,0';
            $msg_poke = getTranslation('pokemon_id_' . $first) . SP . getTranslation('or') . SP . getTranslation('pokemon_id_' . $second);
        } else if($first > 0 && $second > 0 && $third > 0) {
            $new_reset = $action . '-' . $qty . '-dex-' . $start . '#' . $first . ',' . $second . ',0';
            $msg_poke = getTranslation('pokemon_id_' . $first) . ', ' . getTranslation('pokemon_id_' . $second) . SP . getTranslation('or') . SP . getTranslation('pokemon_id_' . $third);
        }

        // Set end value.
        $limit = count_all_json_pokemon();
        //$end = ($limit > $start + 50) ? ($start + 50) : $limit;

        // Get pokemon keys.
        if($third == 0) {
            $keys = get_all_json_pokemon_keys($cb_id, 'willow_quest_add', $new_arg, $start, $first, $second, $third);
        }

        // Add navigation keys.
        $n_25_keys = [];
        $n_50_keys = [];

        // Previous 25 key.
        if($start - 25 >= 0 && $third == 0) {
            $n_25_keys[] = universal_inner_key($keys, $cb_id, 'willow_quest_add', $new_prev_25, getTranslation('back') . ' (-25)');
        }

        // Next 25 key.
        if($limit > $start + 25 && $third == 0) {
            $n_25_keys[] = universal_inner_key($keys, $cb_id, 'willow_quest_add', $new_next_25, getTranslation('next') . ' (+25)');
        }

        // Previous 50 key.
        if($start - 50 >= 0 && $third == 0) {
            $n_50_keys[] = universal_inner_key($keys, $cb_id, 'willow_quest_add', $new_prev_50, getTranslation('back') . ' (-50)');
        }

        // Next 50 key.
        if($limit > $start + 50 && $third == 0) {
            $n_50_keys[] = universal_inner_key($keys, $cb_id, 'willow_quest_add', $new_next_50, getTranslation('next') . ' (+50)');
        }

        // Get the inline key array.
        $keys[] = $n_25_keys;
        $keys[] = $n_50_keys;
       
        // Add save and reset key.
        if($first > 0) {
            $s_keys = [];
            $s_keys[] = universal_inner_key($keys, $cb_id, 'willow_quest_add', $new_reset, getTranslation('reset'));
            $s_keys[] = universal_inner_key($keys, $cb_id, 'willow_quest_add', $new_save, EMOJI_DISK);
            $keys[] = $s_keys;
        }

        // Set message.
        $msg .= get_all_json_pokemon($start, $first, $second, $third);

        // Get quantity
        $quest_type = getTranslation('quest_type_' . $id);
        $msg .= getTranslation('quest') . ': <b>' . CR;
        // Event?
        if(is_numeric($event) && $event > 0) {
            $msg .= getTranslation('quest_event_' . $event) . ':' . SP;
        }
        $msg .= $quest_type . SP . $qty . SP . $msg_poke . '</b>' . CR . CR;
        $msg .= '<b>';
        $msg .= ($third == 0) ? (getTranslation('pokemon') . ' — ' . getTranslation('select_id_to_add')) : (getTranslation('add_this_quest'));
        $msg .= '</b>';

    // Pokemon Types
    } else if($action_source == 'type') {
        // Get start value
        $actval_split = explode('#', $action_value);
        $start = $actval_split[0];

        // Get first, second and third pokemon type
        $sel_poketypes = explode(',', $actval_split[1]);
        $first = $sel_poketypes[0];
        $second = $sel_poketypes[1];
        $third = $sel_poketypes[2];

        // New save.
        $new_save = 'save-' . $qty . '-type-0#' . $first . ',' . $second . ',' . $third;

        // Reset and currently selected pokemons.
        $msg_poke_type = '...';
        if($first > 0 && $second == 0) {
            $new_reset = $action . '-' . $qty . '-type-' . $start . '#0,0,0';
            $msg_poke_type = getTranslation('pokemon_type_' . $first);
        } else if($first > 0 && $second > 0 && $third == 0) {
            $new_reset = $action . '-' . $qty . '-type-' . $start . '#' . $first . ',0,0';
            $msg_poke_type = getTranslation('pokemon_type_' . $first) . SP . getTranslation('or') . SP . getTranslation('pokemon_type_' . $second);
        } else if($first > 0 && $second > 0 && $third > 0) {
            $new_reset = $action . '-' . $qty . '-type-' . $start . '#' . $first . ',' . $second . ',0';
            $msg_poke_type = getTranslation('pokemon_type_' . $first) . ', ' . getTranslation('pokemon_type_' . $second) . SP . getTranslation('or') . SP . getTranslation('pokemon_type_' . $third);
        }

        // Get pokemon keys.
        if($third == 0) {
            $keys = get_all_json_pokemon_type_keys($cb_id, 'willow_quest_add', $arg, $first, $second, $third);
        }

        // Add save and reset key.
        if($first > 0) {
            $s_keys = [];
            $s_keys[] = universal_inner_key($keys, $cb_id, 'willow_quest_add', $new_reset, getTranslation('reset'));
            $s_keys[] = universal_inner_key($keys, $cb_id, 'willow_quest_add', $new_save, EMOJI_DISK);
            $keys[] = $s_keys;
        }

        // Set message.
        $msg .= get_all_json_pokemon_type($first, $second, $third);

        // Get quantity
        $quest_type = getTranslation('quest_type_' . $id);
        $poke_types = str_replace('POKEMON_TYPE', $msg_poke_type, getTranslation('pokemon_of_type'));
        $msg .= getTranslation('quest') . ': <b>' . CR;
        // Event?
        if(is_numeric($event) && $event > 0) {
            $msg .= getTranslation('quest_event_' . $event) . ':' . SP;
        }
        $msg .= $quest_type . SP . $qty . SP . $poke_types . '</b>' . CR . CR;
        $msg .= '<b>';
        $msg .= ($third == 0) ? (getTranslation('pokemon_types') . ' — ' . getTranslation('add') . ':') : (getTranslation('add_this_quest'));
        $msg .= '</b>';

    // Actions from language files
    } else if($action_source == 'file') {
        // Ready to save quest?
        if($action_value > 0) {
            // Write to log.
            debug_log('Asking for confirmation to save the quest');

            // Get quest action.
            $quest_action = explode(":", getTranslation('quest_action_' . $action_value));
            $quest_action_singular = $quest_action[0];
            $quest_action_plural = $quest_action[1];
            $action_qty_text = (($qty > 1) ? ($quest_action_plural) : ($quest_action_singular));

            // Final quest.
            $msg .= getTranslation('quest') . ': <b>' . CR;
            // Event?
            if(is_numeric($event) && $event > 0) {
                $msg .= getTranslation('quest_event_' . $event) . ':' . SP;
            }
            $msg .= $quest_type . SP . $qty . SP . $action_qty_text . '</b>' . CR . CR;

            // Create keys array.
            $keys = [
                [
                    [
                        'text'          => getTranslation('yes'),
                        'callback_data' => $cb_id . ':willow_quest_add:' . 'save-' . $qty . '-' . $action_source . '-' . $action_value
                    ]
                ],
                [
                    [
                        'text'          => getTranslation('no'),
                        'callback_data' => '0:exit:0'
                    ]
                ]
            ];
        } else {
            // Get keys and message via json.
            $keys = get_all_json_quest_action_keys($event, $id, 'willow_quest_add', $arg);
            $msg .= get_all_json_quest_action($id) . CR;
           
            // Build message.
            $msg .= getTranslation('quest') . ': <b>' . CR;
            // Event?
            if(is_numeric($event) && $event > 0) {
                $msg .= getTranslation('quest_event_' . $event) . ':' . SP;
            }
            $msg .= $quest_type . SP . $qty . SP . ' ...</b>' . CR . CR;
            $msg .= '<b>' . getTranslation('quest_select_type') . '</b>';
        }
    }

    // Add navigation keys.
    $nav_keys = [];
    $nav_keys[] = universal_inner_key($keys, $cb_id, 'willow_quest_add', $action . '-' . $qty . '-0', getTranslation('back'));
    $nav_keys[] = universal_inner_key($keys, '0', 'exit', '0', getTranslation('abort'));

    // Get the inline key array.
    $keys[] = $nav_keys;

// Save quest.
} else if ($id > 0 && $action == 'save') {
    // Write to log.
    debug_log('Confirmation received to save the quest');

    // No Event?
    if($event == 'no') {
        $event = 0;
    }

    // Save quest.
    add_questlist_quest($event, $id, $qty, $action_source, $action_value);

    // Get inserted database id.
    $ql_id = my_insert_id();

    // Set message.
    $msg = getTranslation('quest_successfully_saved') . CR . CR;

    // Show saved encounter.
    $msg .= getTranslation('quest') . ':' . CR;
    $quest_entry = get_formatted_questlist_entry($ql_id);
    $msg .= $quest_entry . CR;

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
