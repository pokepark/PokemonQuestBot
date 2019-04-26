<?php

/**
 * Quest access check.
 * @param $update
 * @param $data
 * @return bool
 */
function quest_access_check($update, $data, $permission, $return_result = false)
{
    // Default: Deny access to quests
    $quest_access = false;

    // Build query.
    $rs = my_query(
        "
        SELECT    user_id
        FROM      quests
        WHERE     id = {$data['id']}
        "
    );

    $quest = $rs->fetch_assoc();

    // Check permissions
    if ($update['callback_query']['from']['id'] != $quest['user_id']) {
        // Check "-all" permission
        $permission = $permission . '-all';
        $quest_access = bot_access_check($update, $permission, $return_result);
    } else {
        // Check "-own" permission
        $permission = $permission . '-own';
        $quest_access = bot_access_check($update, $permission, $return_result);
    }

    // Return result
    return $quest_access;
}

/**
 * Quest duplication check.
 * @param $pokestop_id
 * @return array
 */
function quest_duplication_check($pokestop_id)
{
    // Check if quest already exists for this pokestop.
    // Exclude unnamed pokestops with pokestop_id 0.
    $rs = my_query(
        "
        SELECT    id, pokestop_id
        FROM      quests
          WHERE   quest_date = UTC_DATE() 
            AND   pokestop_id > 0
            AND   pokestop_id = {$pokestop_id}
        "
    );

    // Get the row.
    $quest = $rs->fetch_assoc();

    debug_log($quest);

    return $quest;
}


/**
 * Get local name of pokemon.
 * @param $pokedex_id
 * @param $override_language
 * @return string
 */
function get_local_pokemon_name($pokedex_id, $override_language = false)
{
    // Get translation type
    if($override_language == true) {
        $getTypeTranslation = 'getPublicTranslation';
    } else {
        $getTypeTranslation = 'getTranslation';
    }

    // Init pokemon name and get translation
    $pokemon_name = '';
    $pokemon_name = $getTypeTranslation('pokemon_id_' . $pokedex_id);

    return $pokemon_name;
}

/**
 * Get questlist entry.
 * @param $questlist_id
 * @return array
 */
function get_questlist_entry($questlist_id)
{
    // Get the questlist entry by id.
    $rs = my_query(
        "
        SELECT     *
        FROM       questlist
        WHERE      id = {$questlist_id}
        "
    );

    // Get the row.
    $ql_entry = $rs->fetch_assoc();

    debug_log($ql_entry);

    return $ql_entry;
}

/**
 * Get quest.
 * @param $quest_id
 * @return array
 */
function get_quest($quest_id)
{
    // Get the quest data by id.
    $rs = my_query(
        "
        SELECT     quests.*,
                   users.name,
                   pokestops.pokestop_name, pokestops.lat, pokestops.lon, pokestops.address,
                   questlist.quest_event, questlist.quest_type, questlist.quest_quantity, questlist.quest_action, questlist.quest_pokedex_ids, questlist.quest_poketypes,
                   rewardlist.reward_type, rewardlist.reward_quantity,
                   encounterlist.pokedex_ids
        FROM       quests
        LEFT JOIN  users
        ON         quests.user_id = users.user_id
        LEFT JOIN  pokestops
        ON         quests.pokestop_id = pokestops.id
        LEFT JOIN  questlist
        ON         quests.quest_id = questlist.id
        LEFT JOIN  rewardlist
        ON         quests.reward_id = rewardlist.id
        LEFT JOIN  encounterlist
        ON         quests.quest_id = encounterlist.quest_id
        WHERE      quests.id = {$quest_id}
        "
    );

    // Get the row.
    $quest = $rs->fetch_assoc();

    debug_log($quest);

    return $quest;
}

/**
 * Get formatted quest from questlist.
 * @param quest_id
 * @param hide_id
 * @return string
 */
function get_formatted_questlist_entry($quest_id, $hide_id = false)
{
    // Get quest from questlist.
    $ql_entry = get_questlist_entry($quest_id);

    // Init empty questlist message.
    $msg_questlist = '';

    // Build message.
    $qty_action = get_quest_action($ql_entry);
    if($hide_id == false) {
        $msg_questlist .= '<b>ID: ' . $ql_entry['id'] . '</b> — ';
    }

    // Event?
    if($ql_entry['quest_event'] > 0) {
        $msg_questlist .= getTranslation('quest_event_'. $ql_entry['quest_event']) . ':' . SP . getTranslation('quest_type_'. $ql_entry['quest_type']) . SP . $qty_action;
    } else {
        $msg_questlist .= getTranslation('quest_type_'. $ql_entry['quest_type']) . SP . $qty_action;
    }

    return $msg_questlist;
}

/**
 * Get all quests in questlist.
 * @param hide_encounter
 * @return string
 */
function get_all_questlist_entries($hide_encounter = false)
{
    // Get all quests from questlist.
    $rs = my_query(
        "
        SELECT     *
        FROM       questlist
        ORDER BY   quest_event, id
        "
    );

    // Init empty questlist message.
    $msg_questlist = '';

    // Build message.
    while ($questlist = $rs->fetch_assoc()) {
        // Hide quests with encounters if requested and found.
        $el_entry = get_encounterlist_entry($questlist['id']);
        if($hide_encounter == true && $el_entry) continue; 

        // Get quest action.
        $qty_action = get_quest_action($questlist);

        // Build questlist message.
        $msg_questlist .= '<b>ID: ' . $questlist['id'] . '</b> — '; 

        // Event?
        if($questlist['quest_event'] > 0) {
            $msg_questlist .= '<b>' . getTranslation('quest_event_'. $questlist['quest_event']) . ':</b>' . SP . getTranslation('quest_type_'. $questlist['quest_type']) . SP . $qty_action . CR;
        } else {
            $msg_questlist .= getTranslation('quest_type_'. $questlist['quest_type']) . SP . $qty_action . CR;
        }
    }

    return $msg_questlist;
}

/**
 * Get keys for all quests in questlist.
 * @param action
 * @param arg
 * @param hide_encounter
 * @return array
 */
function get_all_questlist_keys($action, $arg, $hide_encounter = false)
{
    // Get all quests from questlist.
    $rs = my_query(
        "
        SELECT     id
        FROM       questlist
        ORDER BY   quest_event, id
        "
    );

    // Init empty keys array.
    $keys = array();

    // Add key for each quest
    while ($ql_entry = $rs->fetch_assoc()) {
        // Hide quests with encounters if requested and found.
        $el_entry = get_encounterlist_entry($ql_entry['id']);
        if($hide_encounter == true && $el_entry) continue;

        $keys[] = array(
            'text'          => $ql_entry['id'],
            'callback_data' => $ql_entry['id'] . ':' . $action . ':' . $arg
        );   
    } 

    // Add quick selection keys.
    $keys = inline_key_array($keys, 5);

    //debug_log($keys);

    return $keys;
}

/**
 * Get all encounters in encounterlist.
 * @return string
 */
function get_all_encounterlist_entries()
{
    // Get all encounters from encounterlist.
    $rs = my_query(
        "
        SELECT     *
        FROM       encounterlist
        "
    );

    // Init empty encounterlist message.
    $msg_enclist = '';

    // Build message.
    while ($enclist = $rs->fetch_assoc()) {
        // Get quest.
        $msg_quest = get_formatted_questlist_entry($enclist['quest_id'], true);

        // Get encounters
        $quest_pokemons = explode(',', $enclist['pokedex_ids']);

        // Get local pokemon name
        $msg_poke = '';
        foreach($quest_pokemons as $pokedex_id) {
            $msg_poke .= get_local_pokemon_name($pokedex_id);
            $msg_poke .= ' / ';
        }
        // Trim last slash
        $msg_poke = rtrim($msg_poke,' / ');

        // Build encounterlist message.
        $msg_enclist .= '<b>ID: ' . $enclist['id'] . '</b> — ';
        $msg_enclist .= $msg_quest;
        $msg_enclist .= SP . '(<b>' . $msg_poke . '</b>)' . CR;
    }

    return $msg_enclist;
}

/**
 * Get formatted encounter from encounterlist.
 * @param encounter_id
 * @param hide_id
 * @param hide_quest
 * @return string
 */
function get_formatted_encounterlist_entry($encounter_id, $hide_id = false, $hide_quest = false)
{
    // Get encounters from encounterlist.
    $rs = my_query(
        "
        SELECT     *
        FROM       encounterlist
        WHERE      id = '{$encounter_id}'
        "
    );

    // Init empty encounterlist message.
    $msg_enclist = '';

    // Build message.
    while ($enclist = $rs->fetch_assoc()) {
        // Get quest.
        $msg_quest = get_formatted_questlist_entry($enclist['quest_id'], true);

        // Get encounters
        $quest_pokemons = explode(',', $enclist['pokedex_ids']);

        // Get local pokemon name
        $msg_poke = '';
        foreach($quest_pokemons as $pokedex_id) {
            $msg_poke .= get_local_pokemon_name($pokedex_id);
            $msg_poke .= ' / ';
        }
        // Trim last slash
        $msg_poke = rtrim($msg_poke,' / ');

        // Build encounterlist message.
        if($hide_id != true) {
            $msg_enclist .= '<b>ID: ' . $enclist['id'] . '</b> — ';
        }
        if($hide_quest != true) {
            $msg_enclist .= $msg_quest;
            $msg_enclist .= SP . '(<b>' . $msg_poke . '</b>)' . CR;
        } else {
            $msg_enclist .= '<b>' . $msg_poke . '</b>' . CR;
        }
    }

    return $msg_enclist;
}

/**
 * Get keys for all encounters in encounterlist.
 * @param $action
 * @param $arg
 * @return array
 */
function get_all_encounterlist_keys($action, $arg)
{
    // Get all encounters from encounterlist.
    $rs = my_query(
        "
        SELECT     id
        FROM       encounterlist
        "
    );

    // Init empty keys array.
    $keys = array();

    // Add key for each encounters
    while ($enc_entry = $rs->fetch_assoc()) {
        $keys[] = array(
            'text'          => $enc_entry['id'],
            'callback_data' => $enc_entry['id'] . ':' . $action . ':' . $arg
        );
    }

    // Set keys.
    $keys = inline_key_array($keys, 5);

    //debug_log($keys);

    return $keys;
}

/**
 * Get all rewards in rewardlist.
 * @param $skip
 * @param $show_hidden
 * @param $quest_id
 * @param $w_column
 * @param $w_operator
 * @param $w_value
 * @return string
 */
function get_all_rewardlist_entries($skip = false, $show_hidden = true, $quest_id = 0, $w_column = 'id', $w_operator = '>', $w_value = 0)
{
    // Build WHERE.
    $where = $w_column . SP . $w_operator . SP . $w_value;

    // Get all rewards.
    $rs = my_query(
        "
        SELECT     id, reward_type
        FROM       rewardlist
        WHERE      $where
        "
    );

    // Init empty message.
    $msg_rewardlist = '';

    // Hidden rewards
    $hide_rewards = array();
    $hide_rewards = (QUEST_HIDE_REWARDS == true && !empty(QUEST_HIDDEN_REWARDS)) ? (explode(',', QUEST_HIDDEN_REWARDS)) : '';

    // Build message.
    while ($rewardlist = $rs->fetch_assoc()) {
        // Skip reward pokemon to avoid deletion.
        if($skip == true && $rewardlist['id'] == 1) continue;

        // Skip hidden rewards.
        if($show_hidden == false && QUEST_HIDE_REWARDS == true && in_array($rewardlist['reward_type'], $hide_rewards)) continue;

        // Build message.
        $msg_rewardlist .= get_formatted_rewardlist_entry($rewardlist['id'], $quest_id) . CR;
    }

    return $msg_rewardlist;
}

/**
 * Get formatted reward from rewardlist.
 * @param reward_id
 * @param quest_id
 * @param hide_id
 * @return string
 */
function get_formatted_rewardlist_entry($reward_id, $quest_id = 0, $hide_id = false)
{
    // Get reward from rewardlist.
    $rl_entry = get_rewardlist_entry($reward_id);

    // Init empty rewardlist message.
    $msg_rewardlist = '';

    // No reward forecast.
    //if($quest_id > 0) {
    if($quest_id == 0) {
        // Reward type: Singular or plural?
        $reward_type = explode(":", getTranslation('reward_type_' . $rl_entry['reward_type']));
        $reward_type_singular = $reward_type[0];
        $reward_type_plural = $reward_type[1];
        $qty_reward = $rl_entry['reward_quantity'] . SP . (($rl_entry['reward_quantity'] > 1) ? ($reward_type_plural) : ($reward_type_singular));

        // Build message.
        if($hide_id == false) {
            $msg_rewardlist .= '<b>ID: ' . $rl_entry['id'] . '</b> — ';
        }
        $msg_rewardlist .= $qty_reward;

    // Get reward pokemon forecast.
    } else {
        $ql_entry = get_questlist_entry($quest_id);

        // Rewardlist entry.
        $rl_entry = get_rewardlist_entry($reward_id);

        // Reward type: Singular or plural?
        $reward_type = explode(":", getTranslation('reward_type_' . $rl_entry['reward_type']));
        $reward_type_singular = $reward_type[0];
        $reward_type_plural = $reward_type[1];
        $qty_reward = $rl_entry['reward_quantity'] . SP . (($rl_entry['reward_quantity'] > 1) ? ($reward_type_plural) : ($reward_type_singular));

        // Reward pokemon forecast?
        $msg_poke = '';

        if($rl_entry['reward_type'] == 1) {
            $el_entry = get_encounterlist_entry($ql_entry['id']);
            $quest_pokemons = explode(',', $el_entry['pokedex_ids']);
            // Get local pokemon name
            foreach($quest_pokemons as $pokedex_id) {
                $msg_poke .= get_local_pokemon_name($pokedex_id);
                $msg_poke .= ' / ';
            }
            // Trim last slash
            $msg_poke = rtrim($msg_poke,' / ');
            $msg_poke = (!empty($msg_poke) ? $msg_poke : '');
        }

        // Forecast reward text.
        if($hide_id == false) {
            $msg_rewardlist .= '<b>ID: ' . $rl_entry['id'] . '</b> — ';
        }
        $msg_rewardlist .= (!empty($msg_poke) ? $msg_poke : $qty_reward);
    }

    return $msg_rewardlist;
}

/**
 * Get keys for all rewards in rewardlist.
 * @param $action
 * @param $arg
 * @param $skip
 * @param $show_hidden
 * @return array
 */
function get_all_rewardlist_keys($action, $arg, $skip = false, $show_hidden = true)
{
    // Get all rewards from rewardlist.
    $rs = my_query(
        "
        SELECT     id, reward_type
        FROM       rewardlist
        "
    );

    // Init empty keys array.
    $keys = array();

    // Hidden rewards
    $hide_rewards = array();
    $hide_rewards = (QUEST_HIDE_REWARDS == true && !empty(QUEST_HIDDEN_REWARDS)) ? (explode(',', QUEST_HIDDEN_REWARDS)) : '';

    // Add key for each reward
    while ($rl_entry = $rs->fetch_assoc()) {
        // Skip reward pokemon to avoid deletion.
        if($skip == true && $rl_entry['id'] == 1) continue;

        // Skip hidden rewards.
        if($show_hidden == false && QUEST_HIDE_REWARDS == true && in_array($rl_entry['reward_type'], $hide_rewards)) continue;

        // Add keys.
        $keys[] = array(
            'text'          => $rl_entry['id'],
            'callback_data' => $rl_entry['id'] . ':' . $action . ':' . $arg
        );
    }

    // Add quick selection keys.
    $keys = inline_key_array($keys, 5);

    //debug_log($keys);

    return $keys;
}


/**
 * Get keys for all entries in quicklist.
 * @param action
 * @param arg
 * @param id_type
 * @return array
 */
function get_all_quicklist_keys($action, $arg, $id_type = 'quest_id')
{
    // Get all entries from quicklist.
    $rs = my_query(
        "
        SELECT     id, quest_id
        FROM       quick_questlist
        "
    );

    // Init empty keys array.
    $keys = array();

    // Get id type for keys text.
    if($id_type != 'quest_id') {
        $id_value = 'id';
    } else {
        $id_value = 'quest_id';
    }

    // Add key for each quicklist entry
    while ($ql_entry = $rs->fetch_assoc()) {
        $keys[] = array(
            'text'          => $ql_entry[$id_value],
            'callback_data' => $ql_entry['id'] . ':' . $action . ':' . $arg
        );
    }

    // Add quick selection keys.
    $keys = inline_key_array($keys, 5);

    //debug_log($keys);

    return $keys;
}

/**
 * Get all entries in quicklist.
 * @param reward
 * @param hide_id
 * @param show_qq_id
 * @return string
 */
function get_all_quicklist_entries($reward = false, $hide_id = false, $show_qq_id = false)
{
    // Get all quicklist entries.
    $rs = my_query(
        "
        SELECT     * 
        FROM       quick_questlist
        "
    );

    // Init empty message.
    $msg_quicklist = '';

    // Build message.
    while ($quicklist = $rs->fetch_assoc()) {
        // Show quick questlist id.
        if($show_qq_id == true) {
            $msg_quicklist .= '<b>ID: ' . $quicklist['id'] . '</b> — ';
        }

        $msg_quicklist .= get_formatted_questlist_entry($quicklist['quest_id'], $hide_id);
        // Get reward.
        if($reward == true) {
            $msg_quicklist .= SP . ' (' . get_formatted_rewardlist_entry($quicklist['reward_id'], $quicklist['quest_id'], true) . ')' . CR;
        } else {
            $msg_quicklist .= CR;
        }
    }

    return $msg_quicklist;
}

/**
 * Get quicklist entry.
 * @param $quicklist_id
 * @return array
 */
function get_quicklist_entry($quicklist_id)
{
    // Get the quicklist entry by id.
    $rs = my_query(
        "
        SELECT     *
        FROM       quick_questlist
        WHERE      id = {$quicklist_id}
        "
    );

    // Get the row.
    $ql_entry = $rs->fetch_assoc();

    debug_log($ql_entry);

    return $ql_entry;
}

/**
 * Get quest action.
 * @param $quest
 * @param $override_language
 * @return string
 */
function get_quest_action($quest, $override_language = false)
{
    // Get translation type
    if($override_language == true) {
        $getTypeTranslation = 'getPublicTranslation';
    } else {
        $getTypeTranslation = 'getTranslation';
    }

    // Init Quantity Action Var.
    $qty_action = '';

    // Quest action - Translation?
    if($quest['quest_action'] != '0') {
        // Quest action: Singular or plural?
        $quest_action = explode(":", $getTypeTranslation('quest_action_' . $quest['quest_action']));
        $quest_action_singular = $quest_action[0];
        $quest_action_plural = $quest_action[1];
        $qty_action = $quest['quest_quantity'] . SP . (($quest['quest_quantity'] > 1) ? ($quest_action_plural) : ($quest_action_singular));

    // Quest action - Pokemons?
    } else if($quest['quest_pokedex_ids'] != '0') {
        // Init pokemon names.
        $poke_names = '';
        $quest_pokemons = explode(',', $quest['quest_pokedex_ids']);

        // Get local pokemon names.
        foreach($quest_pokemons as $pokedex_id) {
            $poke_names .= get_local_pokemon_name($pokedex_id, $override_language);
            $poke_names .= ', ';
        }
        // Trim last comma
        $comma = ', ';
        $poke_names = rtrim($poke_names,$comma);
        
        // Get position of last comma and replace it with 'or' in case of multiple Pokemon names
        $pos = strrpos($poke_names, $comma);
        if($pos !== false)
        {
            $poke_names = substr_replace($poke_names, SP . $getTypeTranslation('or') . SP, $pos, strlen($comma));
        }
        $qty_action = $quest['quest_quantity'] . SP . (!empty($poke_names) ? $poke_names : '');

    // Quest action - Pokemon Types?
    } else if($quest['quest_poketypes'] != '0') {
        // Init pokemon types.
        $poke_types = '';
        $quest_poketypes = explode(',', $quest['quest_poketypes']);

        // Get local pokemon names.
        foreach($quest_poketypes as $poketype_id) {
            $poke_types .= $getTypeTranslation('pokemon_type_' . $poketype_id);
            $poke_types .= ', ';
        }
        // Trim last comma
        $comma = ', ';
        $poke_types = rtrim($poke_types,$comma);

        // Get position of last comma and replace it with 'or' in case of multiple Pokemon types
        $pos = strrpos($poke_types, $comma);
        if($pos !== false)
        {
            $poke_types = substr_replace($poke_types, SP . $getTypeTranslation('or') . SP, $pos, strlen($comma));
        }

        // Get translation for pokemon types and replace POKEMON_TYPE with actual pokemon types
        $poke_types = str_replace('POKEMON_TYPE', $poke_types, $getTypeTranslation('pokemon_of_type'));
        $qty_action = $quest['quest_quantity'] . SP . (!empty($poke_types) ? $poke_types : '');
    }
    
    return $qty_action;
}

/**
 * Get quest and reward as formatted string.
 * @param $quest array
 * @param $add_creator bool
 * @param $add_timestamp bool
 * @param $compact_format bool
 * @param $override_language bool
 * @return array
 */
function get_formatted_quest($quest, $add_creator = false, $add_timestamp = false, $compact_format = false, $override_language = false)
{
    /** Example:
     * Pokestop: Reward-Stop Number 1
     * Quest-Street 5, 13579 Poke-City
     * Quest: Eggxtra-Event: Hatch 1 Egg
     * Reward: Magikarp or Onix
    */

    /** Event-Example:
     * Pokestop: Reward-Stop Number 1
     * Quest-Street 5, 13579 Poke-City
     * Eggxtra-Event: Hatch 1 Egg
     * Reward: Magikarp or Onix
    */

    // Get translation type
    if($override_language == true) {
        $getTypeTranslation = 'getPublicTranslation';
    } else {
        $getTypeTranslation = 'getTranslation';
    }

    // Pokestop name and address.
    $pokestop_name = SP . '<b>' . (!empty($quest['pokestop_name']) ? ($quest['pokestop_name']) : ($getTypeTranslation('unnamed_pokestop'))) . '</b>' . CR;

    // Get pokestop info.
    $stop = get_pokestop($quest['pokestop_id']);

    // Add google maps link.
    if(!empty($quest['address'])) {
        $pokestop_address = '<a href="https://maps.google.com/?daddr=' . $quest['lat'] . ',' . $quest['lon'] . '">' . $quest['address'] . '</a>';
    } else if(!empty($stop['address'])) {
        $pokestop_address = '<a href="https://maps.google.com/?daddr=' . $stop['lat'] . ',' . $stop['lon'] . '">' . $stop['address'] . '</a>';
    } else {
        $pokestop_address = '<a href="http://maps.google.com/maps?q=' . $quest['lat'] . ',' . $quest['lon'] . '">http://maps.google.com/maps?q=' . $quest['lat'] . ',' . $quest['lon'] . '</a>';
    }

    $qty_action = get_quest_action($quest, $override_language);

    // Reward type: Singular or plural?
    $reward_type = explode(":", $getTypeTranslation('reward_type_' . $quest['reward_type']));
    $reward_type_singular = $reward_type[0];
    $reward_type_plural = $reward_type[1];
    $qty_reward = $quest['reward_quantity'] . SP . (($quest['reward_quantity'] > 1) ? ($reward_type_plural) : ($reward_type_singular));
    
    // Reward pokemon forecast?
    $msg_poke = '';

    if($quest['pokedex_ids'] != '0' && $quest['reward_type'] == 1) {
        $quest_pokemons = explode(',', $quest['pokedex_ids']);
        // Get local pokemon name
        foreach($quest_pokemons as $pokedex_id) {
            $msg_poke .= get_local_pokemon_name($pokedex_id, $override_language);
            $msg_poke .= ' / ';
        }
        // Trim last slash
        $msg_poke = rtrim($msg_poke,' / ');
        $msg_poke = (!empty($msg_poke) ? $msg_poke : '');
    }

    // Event?
    $msg_event = '';
    $msg_compact = '';
    if($quest['quest_event'] > 0) {
        $msg_event = '<b>' . $getTypeTranslation('quest_event_'. $quest['quest_event']) . ':' . SP;
        $msg_compact = $getTypeTranslation('quest_event_'. $quest['quest_event']) . ':' . SP;
    } else {
        $msg_event = $getTypeTranslation('quest_event_'. $quest['quest_event']) . ':' . SP;
    }

    // Build quest message
    $msg = '';
    if($compact_format == false) {
        $msg .= $getTypeTranslation('pokestop') . ':' . $pokestop_name . $pokestop_address . CR;
        $msg .= $msg_event . $getTypeTranslation('quest_type_' . $quest['quest_type']) . SP . $qty_action . '</b>' . CR;
        $msg .= $getTypeTranslation('reward') . ': <b>' . (!empty($msg_poke) ? $msg_poke : $qty_reward) . '</b>' . CR;
    } else {
        $msg .= $msg_compact . $getTypeTranslation('quest_type_' . $quest['quest_type']) . SP . $qty_action . ' — ' . (!empty($msg_poke) ? $msg_poke : $qty_reward);
    }

    //Add custom message from the config.
    if (defined('MAP_URL') && !empty(MAP_URL)) {
        $msg .= CR . MAP_URL ;
    }

    // Display creator.
    $msg .= ($quest['user_id'] && $add_creator == true) ? (CR . $getTypeTranslation('created_by') . ': <a href="tg://user?id=' . $quest['user_id'] . '">' . htmlspecialchars($quest['name']) . '</a>') : '';

    // Add update time and quest id to message.
    if($add_timestamp == true) {
        $msg .= CR . '<i>' . $getTypeTranslation('updated') . ': ' . dt2date($quest['quest_date']) . '</i>';
        $msg .= '  ' . substr(strtoupper(BOT_ID), 0, 1) . '-ID = ' . $quest['id']; // DO NOT REMOVE! --> NEEDED FOR CLEANUP PREPARATION!
    }

    return $msg;
}

/**
 * Get today's quests as formatted string.
 * @return string
 */
function get_todays_formatted_quests()
{
    // Get the quest data by id.
    $rs = my_query(
        "
        SELECT     id
        FROM       quests
        WHERE      quest_date = UTC_DATE() 
        "
    );

    // Init empty message and counter.
    $msg = '';
    $count = 0;

    // Get the quests.
    while ($todays_quests = $rs->fetch_assoc()) {
        $quest = get_quest($todays_quests['id']);
        $msg .= CR . '<b>' . (!empty($quest['pokestop_name']) ? ($quest['pokestop_name']) : (getTranslation('unnamed_pokestop'))) . '</b>' . CR;
        $msg .= get_formatted_quest($quest, false, false, true, false);
        $msg .= CR;
        $count = $count + 1;
    }

    // No quests today?
    if($count == 0) {
        $msg = getTranslation('no_quests_today');
    } else {
        // Add update time to message.
        $msg .= CR . '<i>' . getTranslation('updated') . ': ' . date('H:i:s') . '</i>';
    }

    return $msg;
}

/**
 * Get rewardlist entry.
 * @param $reward_id
 * @return array
 */
function get_rewardlist_entry($reward_id)
{
    // Get the reward data by id.
    $rs = my_query(
        "
        SELECT     *
        FROM       rewardlist
        WHERE      id = {$reward_id}
        "
    );

    // Get the row.
    $reward = $rs->fetch_assoc();

    debug_log($reward);

    return $reward;
}


/**
 * Get encounterlist entry.
 * @param quest_id
 * @return array
 */
function get_encounterlist_entry($quest_id)
{
    // Get the reward data by id.
    $rs = my_query(
        "
        SELECT     *
        FROM       encounterlist
        WHERE      quest_id = {$quest_id}
        "
    );

    // Get the row.
    $encounters = $rs->fetch_assoc();

    debug_log($encounters);

    return $encounters;
}

/**
 * Get entry count of all pokemons from json.
 * @param start
 * @return string
 */
function count_all_json_pokemon()
{
    // Init count.
    $count = 0;
    
    // Set language.
    $language = USERLANGUAGE;

    // Translation file.
    $tfile = CORE_LANG_PATH . '/pokemon_' . strtolower($language) . '.json';
    $str = file_get_contents($tfile);
    $json = json_decode($str, true);
    
    // Count entries.
    $count = count($json);

    // Write to log.
    debug_log($count, 'Pokemon count of ' . $tfile . ':');

    return $count;
}

/**
 * Find dex id of specific pokemon from json.
 * @param pokemon
 * @return string
 */
function get_dex_entry($pokemon)
{
    // Init empty message.
    $msg = '';

    // Set language.
    $language = USERLANGUAGE;

    // Translation file.
    $tfile = CORE_LANG_PATH . '/pokemon_' . strtolower($language) . '.json';
    $str = file_get_contents($tfile);
    $json = json_decode($str, true);

    // Find dex ids.
    foreach($json as $index => $pokemon_name) {
        // Lower for better comparison
        $find = strtolower($pokemon);
        $pokemon_name = strtolower($pokemon_name);

        // Find pokemon.
        if(strpos($pokemon_name, $find) !== FALSE) {
            $dex_id = $index + 1;
            $msg .= '<b>ID: ' . $dex_id . ' </b> — ' . getTranslation('pokemon_id_' . $dex_id) . CR;
        }
    }

    // Write to log.
    debug_log('Found pokemon: ' . $msg);

    // Empty msg?
    $msg = empty($msg) ? getTranslation('pokemon_not_found') : $msg;

    return $msg;
}

/**
 * Get strings for all pokemons from json.
 * @param start
 * @param first
 * @param second
 * @param third
 * @return string
 */
function get_all_json_pokemon($start, $first = 0, $second = 0, $third = 0)
{
    // Init empty message.
    $msg = '';

    // Set start and end values.
    $start = $start + 1;
    $limit = count_all_json_pokemon();
    $end = ($limit > $start + 24) ? ($start + 24) : $limit;

    // Get pokemons from json.
    for ($i = $start; $i <= $end; $i = $i + 1) {
        // Skip if already selected.
        if($first == $i || $second == $i) continue;

        // Build message.
        $msg .= '<b>ID: ' . $i . ' </b> — ' . getTranslation('pokemon_id_' . $i) . CR;
    }

    // Write selected pokemon to log.
    debug_log($first . ', ' . $second . ', ' . $third, 'Selected Pokemon IDs:');

    // Already selected pokemon.
    $msg .= CR;
    if($first > 0 && $second == 0) {
        $msg .= getTranslation('selected_pokemon');
        $msg .= CR . '<b>ID: ' . $first . ' </b> — ' . getTranslation('pokemon_id_' . $first) . CR . CR;
    } else if($first > 0 && $second > 0 && $third == 0) {
        $msg .= getTranslation('selected_pokemon');
        $msg .= CR . '<b>ID: ' . $first . ' </b> — ' . getTranslation('pokemon_id_' . $first);
        $msg .= CR . '<b>ID: ' . $second . ' </b> — ' . getTranslation('pokemon_id_' . $second) . CR . CR;
    } else if($first > 0 && $second > 0 && $third > 0) {
        $msg .= getTranslation('selected_pokemon');
        $msg .= CR . '<b>ID: ' . $first . ' </b> — ' . getTranslation('pokemon_id_' . $first);
        $msg .= CR . '<b>ID: ' . $second . ' </b> — ' . getTranslation('pokemon_id_' . $second);
        $msg .= CR . '<b>ID: ' . $third . ' </b> — ' . getTranslation('pokemon_id_' . $third) . CR . CR;
    }

    return $msg;
}

/**
 * Get keys for all pokemons from json.
 * @param id
 * @param action
 * @param arg
 * @param start
 * @param first
 * @param second
 * @param third
 * @return array
 */
function get_all_json_pokemon_keys($id, $action, $arg, $start, $first = 0, $second = 0, $third = 0)
{
    // Init empty keys array.
    $keys = array();

    // Set start and end values.
    $start = $start + 1;
    $limit = count_all_json_pokemon();
    $end = ($limit > $start + 24) ? ($start + 24) : $limit;

    // Get arg.
    $arg_split = explode('#', $arg);
    $old_arg = $arg_split[0];

    // Get pokemons from json.
    for ($i = $start; $i <= $end; $i = $i + 1) {
        // Set new arg.
        if($first == 0) {
            $new_arg = $i . ',0,0';
        } else if($first > 0 && $second == 0) {
            $new_arg = $first . ',' . $i . ',0';
        } else if($first > 0 && $second > 0 && $third == 0) {
            $new_arg = $first . ',' . $second . ',' . $i;
        }

        // Skip if already selected.
        if($first == $i || $second == $i) continue;

        // Add key for each pokemon id
        $keys[] = array(
            'text'          => $i,
            'callback_data' => $id . ':' . $action . ':' . $old_arg . '#' . $new_arg
        );
    }

    // Keys array.
    $keys = inline_key_array($keys, 5);

    return $keys;
}

/**
 * Get strings for all pokemon types from json.
 * @param first
 * @param second
 * @param third
 * @return string
 */
function get_all_json_pokemon_type($first, $second, $third)
{
    // Get all pokemon types from json.
    $tfile = CORE_LANG_PATH . '/pokemon_types.json';
    $str = file_get_contents($tfile);
    $json = json_decode($str, true);

    // Init empty message.
    $msg = '';

    // Build message.
    foreach($json as $type_id_string => $type_translation_array)
    {
        // Get ID from string via substring
        $type_id = substr($type_id_string, strrpos($type_id_string, '_') + 1);

        // Make sure ID is numeric
        if(!(is_numeric($type_id))) {
            // Fallback: Get ID from string via replace
            $type_id = str_replace('pokemon_type_', '', $type_id_string);
        }

        // Skip if already selected.
        if($first == $type_id || $second == $type_id) continue;

        // Set message 
        $msg .= '<b>ID: ' . $type_id . '</b> — ' . getTranslation($type_id_string) . CR;
    }

    // Write selected pokemon to log.
    debug_log($first . ', ' . $second . ', ' . $third, 'Selected Pokemon Type IDs:');

    // Already selected pokemon types.
    $msg .= CR;
    if($first > 0 && $second == 0) {
        $msg .= getTranslation('selected_pokemon_types');
        $msg .= CR . '<b>ID: ' . $first . ' </b> — ' . getTranslation('pokemon_type_' . $first) . CR . CR;
    } else if($first > 0 && $second > 0 && $third == 0) {
        $msg .= getTranslation('selected_pokemon');
        $msg .= CR . '<b>ID: ' . $first . ' </b> — ' . getTranslation('pokemon_type_' . $first);
        $msg .= CR . '<b>ID: ' . $second . ' </b> — ' . getTranslation('pokemon_type_' . $second) . CR . CR;
    } else if($first > 0 && $second > 0 && $third > 0) {
        $msg .= getTranslation('selected_pokemon');
        $msg .= CR . '<b>ID: ' . $first . ' </b> — ' . getTranslation('pokemon_type_' . $first);
        $msg .= CR . '<b>ID: ' . $second . ' </b> — ' . getTranslation('pokemon_type_' . $second);
        $msg .= CR . '<b>ID: ' . $third . ' </b> — ' . getTranslation('pokemon_type_' . $third) . CR . CR;
    }

    return $msg;
}

/**
 * Get keys for all pokemon types from json.
 * @param id
 * @param action
 * @param arg
 * @param first
 * @param second
 * @param third
 * @return array
 */
function get_all_json_pokemon_type_keys($id, $action, $arg, $first = 0, $second = 0, $third = 0)
{
    // Get all pokemon types from json.
    $tfile = CORE_LANG_PATH . '/pokemon_types.json';
    $str = file_get_contents($tfile);
    $json = json_decode($str, true);

    // Init empty keys array.
    $keys = array();

    // Get arg.
    $arg_split = explode('#', $arg);
    $old_arg = $arg_split[0];

    // Get pokemons from json.
    foreach($json as $type_id_string => $type_translation_array)
    {
        // Get ID from string via substring
        $type_id = substr($type_id_string, strrpos($type_id_string, '_') + 1);

        // Make sure ID is numeric
        if(!(is_numeric($type_id))) {
            // Fallback: Get ID from string via replace
            $type_id = str_replace('pokemon_type_', '', $type_id_string);
        }

        // Skip if already selected.
        if($first == $type_id || $second == $type_id) continue;

        // Set new arg.
        if($first == 0) {
            $new_arg = $type_id . ',0,0';
        } else if($first > 0 && $second == 0) {
            $new_arg = $first . ',' . $type_id . ',0';
        } else if($first > 0 && $second > 0 && $third == 0) {
            $new_arg = $first . ',' . $second . ',' . $type_id;
        }

        // Add key for each pokemon type
        $keys[] = array(
            'text'          => getTranslation('pokemon_type_' . $type_id),
            'callback_data' => $id . ':' . $action . ':' . $old_arg . '#' . $new_arg
        );
    }

    // Keys array.
    $keys = inline_key_array($keys, 3);

    return $keys;
}

/**
 * Get strings for all quest types from json.
 * @return string
 */
function get_all_json_quest_type()
{
    // Get all quest types from json.
    $tfile = BOT_LANG_PATH . '/quest_type.json';
    $str = file_get_contents($tfile);
    $json = json_decode($str, true);

    // Init empty message.
    $msg = '';

    // Build message.
    foreach($json as $type_id_string => $type_translation_array)
    {
        // Get ID from string via substring
        $type_id = substr($type_id_string, strrpos($type_id_string, '_') + 1);

        // Make sure ID is numeric
        if(!(is_numeric($type_id))) {
            // Fallback: Get ID from string via replace
            $type_id = str_replace('quest_type_', '', $type_id_string);
        }

        // Always use singular
        $quest_type = explode(":", getTranslation($type_id_string));
        $quest_type_singular = $quest_type[0];
        //$quest_type_plural = $quest_type[1];

        // Set message 
        $msg .= '<b>ID: ' . $type_id . '</b> — ' . $quest_type_singular . CR;
    }

    return $msg;
}

/**
 * Get keys for all quest events from json.
 * @param $action
 * @param $arg
 * @return array
 */
function get_all_json_quest_event_keys($action, $arg)
{
    // Get all quest types from json.
    $tfile = BOT_LANG_PATH . '/quest_event.json';
    $str = file_get_contents($tfile);
    $json = json_decode($str, true);

    // Init empty keys array.
    $keys = array();

    // Add key for no event.
    $keys[] = array(
        'text'          => getTranslation('no_event'),
        'callback_data' => 'no-0:' . $action . ':' . $arg
    );

    // Add keys.
    foreach($json as $event_id_string => $event_translation_array)
    {
        // Get ID from string via substring
        $event_id = substr($event_id_string, strrpos($event_id_string, '_') + 1);

        // Make sure ID is numeric
        if(!(is_numeric($event_id))) {
            // Fallback: Get ID from string via replace
            $event_id = str_replace('quest_event_', '', $event_id_string);
        }

        // Add key for each quest event
        $keys[] = array(
            'text'          => getTranslation('quest_event_' . $event_id),
            'callback_data' => $event_id . '-0:' . $action . ':' . $arg
        );
    }

    // Keys array.
    $keys = inline_key_array($keys, 1);

    return $keys;
}

/**
 * Get strings for all quest events from json.
 * @return string
 */
function get_all_json_quest_event()
{
    // Get all quest event from json.
    $tfile = BOT_LANG_PATH . '/quest_event.json';
    $str = file_get_contents($tfile);
    $json = json_decode($str, true);

    // Init empty message.
    $msg = '';

    // Build message.
    foreach($json as $event_id_string => $event_translation_array)
    {
        // Get ID from string via substring
        $event_id = substr($event_id_string, strrpos($event_id_string, '_') + 1);

        // Make sure ID is numeric
        if(!(is_numeric($event_id))) {
            // Fallback: Get ID from string via replace
            $event_id = str_replace('quest_event_', '', $event_id_string);
        }

        // Always use singular
        $quest_event = getTranslation($event_id_string);

        // Set message 
        $msg .= '<b>ID: ' . $event_id . '</b> — ' . $quest_event . CR;
    }

    return $msg;
}

/**
 * Get keys for all quest types from json.
 * @param $action
 * @param $arg
 * @return array
 */
function get_all_json_quest_type_keys($action, $arg, $event = 0)
{
    // Get all quest types from json.
    $tfile = BOT_LANG_PATH . '/quest_type.json';
    $str = file_get_contents($tfile);
    $json = json_decode($str, true);

    // Init empty keys array.
    $keys = array();

    foreach($json as $type_id_string => $type_translation_array)
    {
        // Get ID from string via substring
        $type_id = substr($type_id_string, strrpos($type_id_string, '_') + 1);

        // Make sure ID is numeric
        if(!(is_numeric($type_id))) {
            // Fallback: Get ID from string via replace
            $type_id = str_replace('quest_type_', '', $type_id_string);
        }

        // Event?
        if($event > 0) {
            $cb_id = $event . '-' . $type_id;
        } else {
            $cb_id = $type_id;
        }

        // Add key for each quest type
        $keys[] = array(
            'text'          => getTranslation('quest_type_' . $type_id),
            'callback_data' => $cb_id . ':' . $action . ':' . $arg
        );
    }

    // Keys array.
    $keys = inline_key_array($keys, 3);

    return $keys;
}

/**
 * Get strings for all quest actions of a specific quest type from json.
 * @param $quest_type_id
 * @return string
 */
function get_all_json_quest_action($quest_type_id)
{
    // Get all quest actions from json.
    $tfile = BOT_LANG_PATH . '/quest_action.json';
    $str = file_get_contents($tfile);
    $json = json_decode($str, true);

    // Init empty message.
    $msg = '';

    // Build message.
    foreach($json as $action_id_string => $action_translation_array)
    {
        // Get ID from string via substring
        $action_id = substr($action_id_string, strrpos($action_id_string, '_') + 1);

        // Make sure ID is numeric
        if(!(is_numeric($action_id))) {
            // Fallback: Get ID from string via replace
            $action_id = str_replace('quest_action_', '', $action_id_string);
        }

        // Make sure action_id and quest_type_id start with the same number
        if(strpos($action_id, $quest_type_id) !== 0) continue;

        // Every quest action is designed to be type_id + action_id and action_id is always 3 chars.
        // To avoid quest actions being shown from another quest type check the length.
        // Type_id + 3 should match the length of the complete quest action id length - continue if not.
        // Example: quest_action_1001 vs. quest_action_11001 - start both with 1, but one is quest type 1 and the other is 11
        //debug_log($action_id, 'ACTION ID:');
        //debug_log($quest_type_id, 'QUEST TYPE ID:');
        //debug_log(strlen($action_id), 'LENGTH ACTION ID:');
        //debug_log(strlen($quest_type_id), 'LENGTH QUEST TYPE ID:');
        $len_qt_id = strlen($quest_type_id);
        $len_a_id = strlen($action_id);
        if(($len_qt_id + 3) <> $len_a_id) continue;

        // Always use singular
        $quest_action = explode(":", getTranslation($action_id_string));
        $quest_action_singular = $quest_action[0];
        //$quest_action_plural = $quest_action[1];

        // Set message 
        $msg .= '<b>ID: ' . $action_id . '</b> — ' . $quest_action_singular . CR;
    }

    return $msg;
}

/**
 * Get keys for all quest actions of a specific quest type from json.
 * @param $event
 * @param $id
 * @param $action
 * @param $arg
 * @return array
 */
function get_all_json_quest_action_keys($event, $id, $action, $arg)
{
    // Get all quest actions from json.
    $tfile = BOT_LANG_PATH . '/quest_action.json';
    $str = file_get_contents($tfile);
    $json = json_decode($str, true);

    // Init empty keys array.
    $keys = array();

    foreach($json as $action_id_string => $action_translation_array)
    {
        // Get ID from string via substring
        $action_id = substr($action_id_string, strrpos($action_id_string, '_') + 1);

        // Make sure ID is numeric
        if(!(is_numeric($action_id))) {
            // Fallback: Get ID from string via replace
            $action_id = str_replace('quest_action_', '', $action_id_string);
        }

        // Make sure action_id and (quest_type)id start with the same number
        if(strpos($action_id, $id) !== 0) continue;

        // Every quest action is designed to be type_id + action_id and action_id is always 3 chars.
        // To avoid quest actions being shown from another quest type check the length.
        // Type_id + 3 should match the length of the complete quest action id length - continue if not.
        // Example: quest_action_1001 vs. quest_action_11001 - start both with 1, but one is quest type 1 and the other is 11
        //debug_log($action_id, 'ACTION ID:');
        //debug_log($id, 'QUEST TYPE ID:');
        //debug_log(strlen($action_id), 'LENGTH ACTION ID:');
        //debug_log(strlen($qid), 'LENGTH QUEST TYPE ID:');
        $len_qt_id = strlen($id);
        $len_a_id = strlen($action_id);
        if(($len_qt_id + 3) <> $len_a_id) continue;

        // Split arg.
        $argsplit = explode('-', $arg);

        // Set new arg for callback data.
        $newarg = $argsplit[0] . '-' . $argsplit[1] . '-' . $argsplit[2];

        // Get quantity for singular or plural action translation.
        $action_qty = $argsplit[1];
        $quest_action = explode(":", getTranslation('quest_action_' . $action_id));
        $quest_action_singular = $quest_action[0];
        $quest_action_plural = $quest_action[1];
        $action_qty_text = (($action_qty > 1) ? ($quest_action_plural) : ($quest_action_singular));

        // Add key for each quest action
        $keys[] = array(
            'text'          => $action_qty_text,
            'callback_data' => $event . '-' . $id . ':' . $action . ':' . $newarg . '-' . $action_id
        );
    }

    // Keys array.
    $keys = inline_key_array($keys, 1);

    return $keys;
}

/**
 * Get strings for all reward types from json.
 * @return string
 */
function get_all_json_reward()
{
    // Get all reward types from json.
    $tfile = BOT_LANG_PATH . '/reward_type.json';
    $str = file_get_contents($tfile);
    $json = json_decode($str, true);

    // Init empty message.
    $msg = '';

    // Hidden rewards
    $hide_rewards = array();
    $hide_rewards = (QUEST_HIDE_REWARDS == true && !empty(QUEST_HIDDEN_REWARDS)) ? (explode(',', QUEST_HIDDEN_REWARDS)) : '';

    // Build message.
    foreach($json as $type_id_string => $type_translation_array)
    {
        // Get ID from string via substring
        $type_id = substr($type_id_string, strrpos($type_id_string, '_') + 1);

        // Make sure ID is numeric
        if(!(is_numeric($type_id))) {
            // Fallback: Get ID from string via replace
            $type_id = str_replace('reward_type_', '', $type_id_string);
        }

        // Skip pokemon reward.
        if($type_id == 1) continue;

        // Skip hidden rewards.
        if(QUEST_HIDE_REWARDS == true && in_array($type_id, $hide_rewards)) continue;

        // Always use singular
        $reward_type = explode(":", getTranslation($type_id_string));
        $reward_type_singular = $reward_type[0];
        //$reward_type_plural = $reward_type[1];

        // Set message 
        $msg .= '<b>ID: ' . $type_id . '</b> — ' . $reward_type_singular . CR;
    }

    return $msg;
}

/**
 * Get keys for all reward types from json.
 * @param $action
 * @param $arg
 * @return array
 */
function get_all_json_reward_keys($action, $arg)
{
    // Get all reward types from json.
    $tfile = BOT_LANG_PATH . '/reward_type.json';
    $str = file_get_contents($tfile);
    $json = json_decode($str, true);

    // Init empty keys array.
    $keys = array();

    // Hidden rewards
    $hide_rewards = array();
    $hide_rewards = (QUEST_HIDE_REWARDS == true && !empty(QUEST_HIDDEN_REWARDS)) ? (explode(',', QUEST_HIDDEN_REWARDS)) : ''; 

    foreach($json as $type_id_string => $type_translation_array)
    {
        // Get ID from string via substring
        $type_id = substr($type_id_string, strrpos($type_id_string, '_') + 1);

        // Make sure ID is numeric
        if(!(is_numeric($type_id))) {
            // Fallback: Get ID from string via replace
            $type_id = str_replace('reward_type_', '', $type_id_string);
        }

        // Skip pokemon reward.
        if($type_id == 1) continue;

        // Skip hidden rewards.
        if(QUEST_HIDE_REWARDS == true && in_array($type_id, $hide_rewards)) continue;

        // Add key for each reward id
        $keys[] = array(
            'text'          => explode(':', getTranslation('reward_type_' . $type_id))[0],
            'callback_data' => $type_id . ':' . $action . ':' . $arg
        );
    }

    // Keys array.
    $keys = inline_key_array($keys, 3);

    return $keys;
}

/**
 * Delete quest.
 * @param $quest_id
 */
function delete_quest($quest_id)
{
    global $db;

    // Delete telegram messages for quest.
    $rs = my_query(
        "
        SELECT        *
            FROM      qleanup
            WHERE     quest_id = '{$quest_id}'
              AND     chat_id <> 0
        "
    );

    // Counter
    $counter = 0;

    // Delete every telegram message
    while ($row = $rs->fetch_assoc()) {
        // Delete telegram message.
        debug_log('Deleting telegram message ' . $row['message_id'] . ' from chat ' . $row['chat_id'] . ' for quest ' . $row['quest_id']);
        delete_message($row['chat_id'], $row['message_id']);
        $counter = $counter + 1;
    }

    // Nothing to delete on telegram.
    if ($counter == 0) {
        debug_log('Quest with ID ' . $quest_id . ' was not found in the cleanup table! Skipping deletion of telegram messages!');
    }

    // Delete quest from cleanup table.
    debug_log('Deleting quest ' . $quest_id . ' from the cleanup table:');
    $rs_cleanup = my_query(
        "
        DELETE FROM   qleanup
        WHERE   quest_id = '{$quest_id}' 
           OR   cleaned = '{$quest_id}'
        "
    );

    // Delete quest from quest table.
    debug_log('Deleting quest ' . $quest_id . ' from the quest table:');
    $rs_quests = my_query(
        "
        DELETE FROM   quests 
        WHERE   id = '{$quest_id}'
        "
    );
}

/**
 * Delete questlist quest.
 * @param questlist_id
 */
function delete_questlist_quest($questlist_id)
{
    global $db;

    // Delete quest from questlist table.
    debug_log('Deleting quest with ID ' . $questlist_id . ' from the questlist table');
    $rs_quests = my_query(
        "
        DELETE FROM   questlist 
        WHERE   id = '{$questlist_id}'
        "
    );
}

/**
 * Add questlist quest.
 * @param quest_event
 * @param quest_type
 * @param quest_qty
 * @param quest_action_type
 * @param quest_action_value
 */
function add_questlist_quest($quest_event, $quest_type, $quest_qty, $quest_action_type, $quest_action_value)
{
    global $db;

    // Log received values.
    debug_log('Received quest of type ' . $quest_type . ' with quantity ' . $quest_qty . ' and quest action ' . $quest_action_type . ' with value ' . $quest_action_value);

    // Get quest action type.
    if($quest_action_type == 'dex') {
        $quest_action_type = 'quest_pokedex_ids';
    } else if($quest_action_type == 'type') {
        $quest_action_type = 'quest_poketypes';
    } else {
        $quest_action_type = 'quest_action';
    }

    // Format quest action value.
    if($quest_action_type == 'quest_pokedex_ids' || $quest_action_type == 'quest_poketypes') {
        // Split position from quest action value
        $pos_qa_value = explode('#', $quest_action_value);
        
        // Split quest action value
        $qa_value = explode(',', $pos_qa_value[1]);
        $first = $qa_value[0];
        $second = $qa_value[1];
        $third = $qa_value[2];

        // Build quest action value - get rid of "0 values"
        if($second == 0 && $third == 0) {
            $quest_action_value = $first;
        } else if($second > 0 && $third == 0) {
            $quest_action_value = $first . ',' . $second;
        } else if($second > 0 && $third > 0) {
            $quest_action_value = $first . ',' . $second . ',' . $third;
        } else {
            // Fallback: Leave value as it is.
            $quest_action_value = $quest_action_value;
        }
    }

    // Add quest to questlist table.
    debug_log('Adding quest of type ' . $quest_type . ' with quantity ' . $quest_qty . ' and quest action ' . $quest_action_type . ' with value ' . $quest_action_value . ' to the questlist table');
    $rs = my_query(
        "
        INSERT INTO   questlist
        SET           quest_type = '{$quest_type}',
                      quest_quantity = '{$quest_qty}',
                      $quest_action_type = '{$quest_action_value}'
        "
    );
}

/**
 * Delete rewardlist reward.
 * @param $reward_id
 */
function delete_rewardlist_reward($reward_id)
{
    global $db;

    // Delete reward from rewardlist table.
    debug_log('Deleting reward ' . $reward_id . ' from the rewardlist table');
    $rs_quests = my_query(
        "
        DELETE FROM   rewardlist 
        WHERE   id = '{$reward_id}'
        "
    );
}

/**
 * Add rewardlist reward.
 * @param $reward_type
 * @param $reward_qty
 */
function add_rewardlist_reward($reward_type, $reward_quantity)
{
    global $db;

    // Add reward to rewardlist table.
    debug_log('Adding reward of type ' . $reward_type . ' with quantity ' . $reward_quantity . ' to the rewardlist table');
    $rs = my_query(
        "
        INSERT INTO   rewardlist
        SET           reward_type = '{$reward_type}',
                      reward_quantity = '{$reward_quantity}'
        "
    );
}

/**
 * Add encounterlist entry.
 * @param $quest_id
 * @param $pokedex_ids
 */
function add_encounterlist_entry($quest_id, $pokedex_ids)
{
    global $db;

    // Log received values.
    debug_log('Received encounters ' . $pokedex_ids . ' for quest with ID ' . $quest_id);

    // Split position from pokedex ids
    $pos_dex_value = explode('#', $pokedex_ids);

    // Split pokedex ids
    $dex_value = explode(',', $pos_dex_value[1]);
    $first = $dex_value[0];
    $second = $dex_value[1];
    $third = $dex_value[2];

    // Build quest action value - get rid of "0 values"
    if($second == 0 && $third == 0) {
        $dex_ids = $first;
    } else if($second > 0 && $third == 0) {
        $dex_ids = $first . ',' . $second;
    } else if($second > 0 && $third > 0) {
        $dex_ids = $first . ',' . $second . ',' . $third;
    } else {
        // Fallback: Leave value as it is.
        $dex_ids = $pokedex_ids;
    }

    // Add encounter to encounterlist table.
    debug_log('Adding encounters with IDs ' . $dex_ids . ' for quest ID ' . $quest_id . ' to the encounterlist table');
    $rs = my_query(
        "
        INSERT INTO   encounterlist
        SET           quest_id = '{$quest_id}',
                      pokedex_ids = '{$dex_ids}'
        "
    );

}

/**
 * Update encounterlist entry.
 * @param $id
 * @param $pokedex_ids
 */
function update_encounterlist_entry($id, $pokedex_ids)
{
    global $db;

    // Log received values.
    debug_log('Received encounters ' . $pokedex_ids . ' for encounter with ID ' . $id);

    // Split position from pokedex ids
    $pos_dex_value = explode('#', $pokedex_ids);

    // Split pokedex ids
    $dex_value = explode(',', $pos_dex_value[1]);
    $first = $dex_value[0];
    $second = $dex_value[1];
    $third = $dex_value[2];

    // Build quest action value - get rid of "0 values"
    if($second == 0 && $third == 0) {
        $dex_ids = $first;
    } else if($second > 0 && $third == 0) {
        $dex_ids = $first . ',' . $second;
    } else if($second > 0 && $third > 0) {
        $dex_ids = $first . ',' . $second . ',' . $third;
    } else {
        // Fallback: Leave value as it is.
        $dex_ids = $pokedex_ids;
    }   

    // Add encounter to encounterlist table.
    debug_log('Adding encounters with IDs ' . $dex_ids . ' for encounter ID ' . $id . ' to the encounterlist table');
    $rs = my_query(
        "
        UPDATE   encounterlist
        SET      pokedex_ids = '{$dex_ids}'
        WHERE    id = '{$id}'
        "
    );

}

/**
 * Delete encounterlist entry.
 * @param $id
 * @param $where
 */
function delete_encounterlist_entry($id, $where = 'id')
{
    global $db;

    // Delete entry from encounterlist table.
    debug_log('Deleting entry with ' . $where . ' = ' . $id . ' from the encounterlist table');
    $rs_quests = my_query(
        "
        DELETE FROM   encounterlist 
        WHERE  $where = '{$id}'
        "
    );
}

/**
 * Delete quicklist entry.
 * @param $id
 * @param $where
 */
function delete_quicklist_entry($id, $where = 'id')
{
    global $db;

    // Delete entry from quicklist table.
    debug_log('Deleting entry with ' . $where . ' = ' . $id . ' from the quicklist table');
    $rs_quests = my_query(
        "
        DELETE FROM   quick_questlist 
        WHERE  $where = '{$id}'
        "
    );
}

/**
 * Add quicklist entry.
 * @param $questlist_id
 * @param $rewardlist_id
 */
function add_quicklist_entry($questlist_id, $rewardlist_id)
{
    global $db;

    // Add entry to quicklist table.
    debug_log('Adding quest with ID ' . $questlist_id . ' and reward with ID ' . $rewardlist_id . ' to the quicklist table');
    $rs = my_query(
        "
        INSERT INTO   quick_questlist
        SET           quest_id = '{$questlist_id}',
                      reward_id = '{$rewardlist_id}'
        "
    );
}

/**
 * Get pokestop.
 * @param $pokestop_id
 * @return array
 */
function get_pokestop($pokestop_id, $update_pokestop = true)
{
    global $db;

    // Pokestop from database
    if($pokestop_id != 0) {
        // Get pokestop from database
        $rs = my_query(
                "
                SELECT    *
                FROM      pokestops
                WHERE     id = {$pokestop_id}
                "
            );

        $stop = $rs->fetch_assoc();

        // Get address and update address string.
        if($update_pokestop == true){
            // Get address.
            $lat = $stop['lat'];
            $lon = $stop['lon'];
            $addr = get_address($lat, $lon);
            $address = format_address($addr);

            // Update pokestop address.
            if(!empty($address)) {
                $rs = my_query(
                    "
                    UPDATE        pokestops
                    SET           address = '{$db->real_escape_string($address)}'
                       WHERE      id = '{$pokestop_id}'
                    "
                );
            }

            // Set pokestop address.
            $stop['address'] = $address;
        }

    // Unnamend pokestop
    } else {
        $stop = 0;
    }

    debug_log($stop);

    return $stop;
}

/**
 * Get pokestops starting with the searchterm.
 * @param $searchterm
 * @return bool|array
 */
function get_pokestop_list_keys($searchterm)
{
    // Make sure the search term is not empty
    if(!empty($searchterm)) {
        // Get pokestop from database
        $rs = my_query(
                "
                SELECT    id, pokestop_name
                FROM      pokestops
                WHERE     pokestop_name LIKE '$searchterm%'
                OR        pokestop_name LIKE '%$searchterm%'
                ORDER BY
                  CASE
                    WHEN  pokestop_name LIKE '$searchterm%' THEN 1
                    WHEN  pokestop_name LIKE '%$searchterm%' THEN 2
                    ELSE  3
                  END
                LIMIT     15
                "
            );

        // Init empty keys array.
        $keys = array();

        // Add key for each found pokestop
        while ($stops = $rs->fetch_assoc()) {
            // Pokestop name.
            $pokestop_name = (!empty($stops['pokestop_name']) ? ($stops['pokestop_name']) : (getTranslation('unnamed_pokestop')));

            // Add keys.
            $keys[] = array(
                'text'          => $pokestop_name,
                'callback_data' => $stops['id'] . ':quest_create:0'
            );
        }
        
        if($keys) {
            // Get the inline key array.
            $keys = inline_key_array($keys, 1);
        } else {
            $keys = true;
        }
    } else {
        // Return false.
        $keys = false;
    }

    return $keys;
}

/**
 * Get pokestops within radius around lat/lon.
 * @param $lat
 * @param $lon
 * @param $radius
 * @return array
 */
function get_pokestops_in_radius_keys($lat, $lon, $radius)
{
    $radius = $radius / 1000;
    // Get all pokestop within the radius
    $rs = my_query(
            " SELECT    id, pokestop_name,
                        (
                            6371 *
                            acos(
                                cos(radians({$lat})) *
                                cos(radians(lat)) *
                                cos(
                                    radians(lon) - radians({$lon})
                                ) +
                                sin(radians({$lat})) *
                                sin(radians(lat))
                            )
                        ) AS distance
              FROM      pokestops
              HAVING    distance < {$radius}
              ORDER BY  distance
              LIMIT     10
            "
        );

    // Init empty keys array.
    $keys = array();

    // Add key for each found pokestop
    while ($stops = $rs->fetch_assoc()) {
        // Pokestop name.
        $pokestop_name = (!empty($stops['pokestop_name']) ? ($stops['pokestop_name']) : (getTranslation('unnamed_pokestop')));

        // Add keys.
        $keys[] = array(
            'text'          => $pokestop_name,
            'callback_data' => $stops['id'] . ':quest_create:0'
        );
    }

    // Add unknown pokestop.
    //$unknown_keys = array();
    //$unknown_keys[] = universal_inner_key($keys, '0', 'quest_create', $lat . ',' . $lon, getTranslation('unnamed_pokestop'));

    // Inline keys.
    $keys = inline_key_array($keys, 1);
    //$keys[] = $unknown_keys;

    return $keys;
}

/**
 * Get weather icons.
 * @param $weather_value
 * @return string
 */
function get_weather_icons($weather_value)
{
    if($weather_value > 0) {
        // Get length of arg and split arg
        $weather_value_length = strlen((string)$weather_value);
        $weather_value_string = str_split((string)$weather_value);

        // Init weather icons string.
        $weather_icons = '';

        // Add icons to string.
        for ($i = 0; $i < $weather_value_length; $i = $i + 1) {
            // Get weather icon from constants
            $weather_icons .= $GLOBALS['weather'][$weather_value_string[$i]];
            $weather_icons .= ' ';
        }

        // Trim space after last icon
        $weather_icons = rtrim($weather_icons);
    } else {
        $weather_icons = '';
    }

    return $weather_icons;
}

/**
 * Get user.
 * @param $user_id
 * @return message
 */
function get_user($user_id)
{
    // Get user details.
    $rs = my_query(
        "
        SELECT    * 
                FROM      users
                  WHERE   user_id = {$user_id}
        "
    );

    // Fetch the row.
    $row = $rs->fetch_assoc();

    // Build message string.
    $msg = '';

    // Add name.
    $msg .= 'Name: <a href="tg://user?id=' . $row['user_id'] . '">' . htmlspecialchars($row['name']) . '</a>' . CR;

    // Unknown team.
    if ($row['team'] === NULL) {
        $msg .= 'Team: ' . $GLOBALS['teams']['unknown'] . CR;

    // Known team.
    } else {
        $msg .= 'Team: ' . $GLOBALS['teams'][$row['team']] . CR;
    }

    // Add level.
    if ($row['level'] != 0) {
        $msg .= 'Level: ' . $row['level'] . CR;
    }

    return $msg;
}


/**
 * Quest type keys.
 * @param $pokestop_id
 * @param $event
 * @return array
 */
function quest_type_keys($pokestop_id, $event = 0)
{
    // Hide/show events
    if($event == 0) {
        // Init empty keys array.
        $keys_event = array();

        // Get all quest events from database
        $rs_event = my_query(
                "
                SELECT    quest_event
                FROM      questlist
                WHERE     quest_event > 0
                GROUP BY  quest_event
                "
            );

        // Add key for each event
        while ($ql_entry = $rs_event->fetch_assoc()) {
            $text = getTranslation('quest_event_'. $ql_entry['quest_event']) . '...';
            // Add keys.
            $keys_event[] = array(
                'text'          => $text,
                'callback_data' => $pokestop_id . ':quest_edit_type:0-' . $ql_entry['quest_event'] . '-0'
            );
        }

        // Get the inline key array.
        if($keys_event) {
            $keys_event = inline_key_array($keys_event, 1);
        }
    }

    // Init empty keys array.
    $keys = array();

    // Get all quest types from database
    $rs = my_query(
            "
            SELECT    quest_event, quest_type
            FROM      questlist
            WHERE     quest_event = {$event} 
            GROUP BY  quest_type
            "
        );


    // Add key for each quest quantity and action
    while ($quest = $rs->fetch_assoc()) {
        $text = getTranslation('quest_type_'. $quest['quest_type']) . '...';
        // Add keys.
        $keys[] = array(
            'text'          => $text,
            'callback_data' => $pokestop_id . ':quest_edit_type:1-' . $quest['quest_event'] . '-' . $quest['quest_type']
        );
    }

    // Get the inline key array.
    $keys = inline_key_array($keys, 2);

    // Merge keys.
    if($keys_event) {
        $keys = array_merge($keys_event, $keys);
    }

    // Add quick selection keys.
    if($event == 0) {
        $quick_keys = quick_quest_keys($pokestop_id);
        $keys = array_merge($keys, $quick_keys);
    }

    // Add navigation key.
    if($event > 0) {
        $nav_keys = array();
        $nav_keys[] = universal_inner_key($keys, $pokestop_id, 'quest_create', '0', getTranslation('back'));
        $nav_keys[] = universal_inner_key($keys, '0', 'exit', '0', getTranslation('abort'));
        $nav_keys = inline_key_array($nav_keys, 2);
        $keys = array_merge($keys, $nav_keys);
    } else {
        $nav_keys = array();
        $nav_keys[] = universal_inner_key($keys, '0', 'exit', '0', getTranslation('abort'));
        $keys[] = $nav_keys;
    }

    debug_log($keys);

    return $keys;
}

/**
 * Quick quest keys.
 * @param $pokestop_id
 * @return array
 */
function quick_quest_keys($pokestop_id)
{
    // Get data from quick questlist.
    $qs = my_query(
            "
            SELECT    *
            FROM      quick_questlist
            "
        );

    // Init empty keys array.
    $keys = array();

    // Add key for each quest quantity and action
    while ($qq = $qs->fetch_assoc()) {
        $ql_entry = get_questlist_entry($qq['quest_id']);
        $qty_action = get_quest_action($ql_entry);

        // Rewardlist entry.
        $rl_entry = get_rewardlist_entry($qq['reward_id']);

        // Reward type: Singular or plural?
        $reward_type = explode(":", getTranslation('reward_type_' . $rl_entry['reward_type']));
        $reward_type_singular = $reward_type[0];
        $reward_type_plural = $reward_type[1];
        $qty_reward = $rl_entry['reward_quantity'] . SP . (($rl_entry['reward_quantity'] > 1) ? ($reward_type_plural) : ($reward_type_singular));

        // Reward pokemon forecast?
        $msg_poke = '';

        if($rl_entry['reward_type'] == 1) {
            $el_entry = get_encounterlist_entry($ql_entry['id']);
            $quest_pokemons = explode(',', $el_entry['pokedex_ids']);
            // Get local pokemon name
            foreach($quest_pokemons as $pokedex_id) {
                $msg_poke .= get_local_pokemon_name($pokedex_id);
                $msg_poke .= ' / ';
            }
            // Trim last slash
            $msg_poke = rtrim($msg_poke,' / ');
            $msg_poke = (!empty($msg_poke) ? $msg_poke : '');
        }

        // Quest and reward text.
        $text = '';
        $text .= getTranslation('quest_type_' . $ql_entry['quest_type']) . SP . $qty_action . ' — ' . (!empty($msg_poke) ? $msg_poke : $qty_reward);

        // Add keys.
        $keys[] = array(
            'text'          => $text,
            'callback_data' => $pokestop_id . ',' . $qq['quest_id'] . ':quest_save:' . $qq['reward_id']
        );
    }

    // Add quick selection keys.
    $keys = inline_key_array($keys, 1);

    debug_log($keys);

    return $keys;
    
}

/**
 * Quest quantity and action keys.
 * @param $pokestop_id
 * @param $quest_event
 * @param $quest_type
 * @return array
 */
function quest_qty_action_keys($pokestop_id, $quest_event, $quest_type)
{
    // Get all quest types from database
    $rs = my_query(
            "
            SELECT    *
            FROM      questlist
            WHERE     quest_type = '$quest_type'
            AND       quest_event = {$quest_event}
            ORDER BY  quest_quantity
            "
        );

    // Init empty keys array.
    $keys = array();

    // Event?
    if($quest_event == 0) {
        $cb_event_type = '0-' . $quest_event . '-' . $quest_type;
    } else {
        $cb_event_type = '1-' . $quest_event . '-' . $quest_type;
    }

    // Add key for each quest quantity and action
    while ($quest = $rs->fetch_assoc()) {
        $qty_action = get_quest_action($quest);

        // Add keys.
        $keys[] = array(
            'text'          => $qty_action,
            //'callback_data' => $pokestop_id . ':quest_edit_reward:' . $quest['id'] . ',' . $quest_event . '-' . $quest_type
            'callback_data' => $pokestop_id . ':quest_edit_reward:' . $quest['id'] . ',' . $cb_event_type
        );
    }

    // Add back and abort navigation keys.
    $nav_keys = array();
    $nav_keys[] = universal_inner_key($keys, $pokestop_id, 'quest_create', '0', getTranslation('back'));
    $nav_keys[] = universal_inner_key($keys, '0', 'exit', '0', getTranslation('abort'));

    // Get the inline key array.
    $keys = inline_key_array($keys, 1);
    $keys[] = $nav_keys;

    debug_log($keys);

    return $keys;
}

/**
 * Reward type keys.
 * @param $pokestop_id
 * @param $quest_id
 * @param $quest_type
 * @return array
 */
function reward_type_keys($pokestop_id, $quest_id, $quest_type)
{
    // Get all reward types from database
    $rs = my_query(
            "
            SELECT    *, COUNT(*) as reward_count
            FROM      rewardlist
            GROUP BY  reward_type
            "
        );

    // Init empty keys array.
    $keys = array();

    // Hidden rewards array.
    $hide_rewards = array();
    $hide_rewards = (QUEST_HIDE_REWARDS == true && !empty(QUEST_HIDDEN_REWARDS)) ? (explode(',', QUEST_HIDDEN_REWARDS)) : '';

    // Add key for each quest quantity and action
    while ($reward = $rs->fetch_assoc()) {
        // Continue if some rewards shall be hidden
        if(QUEST_HIDE_REWARDS == true && in_array($reward['reward_type'], $hide_rewards)) continue;

        // Add save and share button
        if($reward['reward_type'] == 1) {
            // Reward pokemon forecast?
            $msg_poke = '';
            // Get encounters
            $enc_pokemon = get_encounterlist_entry($quest_id);
            $quest_pokemons = explode(',', $enc_pokemon['pokedex_ids']);
            // Get local pokemon name
            foreach($quest_pokemons as $pokedex_id) {
                $msg_poke .= get_local_pokemon_name($pokedex_id);
                $msg_poke .= ' / ';
            }
            // Trim last slash
            $msg_poke = rtrim($msg_poke,' / ');
            $msg_poke = (!empty($msg_poke) ? $msg_poke : '');

            // Key to save and share
            $save_share_keys = array();
            if(!empty($msg_poke)) {
                $save_share_keys[] = array(
                    'text'          => EMOJI_DISK . SP . getTranslation('share') . ' — ' . $msg_poke,
                    'callback_data' => $pokestop_id . ',' . $quest_id . ':quest_save_share:' . $reward['id']
                );

                // Encounter found, continue with next reward type
                continue;
            }
        }

        // Show quantity if there is count for reward is 1
        if($reward['reward_count'] == 1) {
            // Reward qty: Singular or plural?
            $rw_type = explode(":", getTranslation('reward_type_' . $reward['reward_type']));
            $rw_type_singular = $rw_type[0];
            $rw_type_plural = $rw_type[1];
            $qty_rw = $reward['reward_quantity'] . SP . (($reward['reward_quantity'] > 1) ? ($rw_type_plural) : ($rw_type_singular));
            // Add keys.
            $keys[] = array(
                'text'          => $qty_rw,
                'callback_data' => $pokestop_id . ',' . $quest_id . ':quest_save:' . $reward['id']
            );
        } else {
            // Get translation.
            $rw_type = explode(":", getTranslation('reward_type_' . $reward['reward_type']));
            $text = $rw_type[0];
            // Add keys.
            $keys[] = array(
                'text'          => ucfirst($text),
                'callback_data' => $pokestop_id . ',' . $quest_id . ':quest_edit_qty_reward:' . $quest_type . ',' . $reward['reward_type']
            );
        }
    }

    // Add back and abort navigation keys.
    $nav_keys = array();
    $nav_keys[] = universal_inner_key($keys, $pokestop_id, 'quest_edit_type', $quest_type, getTranslation('back'));
    $nav_keys[] = universal_inner_key($keys, '0', 'exit', '0', getTranslation('abort'));

    // Get the inline key array.
    $save_share_keys = inline_key_array($save_share_keys, 1);
    $keys = inline_key_array($keys, 2);
    $keys = array_merge($save_share_keys, $keys);
    $keys[] = $nav_keys;

    debug_log($keys);

    return $keys;
}

/**
 * Reward quantity and type keys.
 * @param $pokestop_id
 * @param $quest_id
 * @param $quest_type
 * @param $reward_type
 * @return array
 */
function reward_qty_type_keys($pokestop_id, $quest_id, $quest_type, $reward_type)
{
    // Get all reward types from database
    $rs = my_query(
            "
            SELECT    *
            FROM      rewardlist
            WHERE     reward_type = '$reward_type'
            ORDER BY  reward_quantity
            "
        );

    // Init empty keys array.
    $keys = array();

    // Add key for each reward quantity and type
    while ($reward = $rs->fetch_assoc()) {
        // Reward qty: Singular or plural?
        $rw_type = explode(":", getTranslation('reward_type_' . $reward['reward_type']));
        $rw_type_singular = $rw_type[0];
        $rw_type_plural = $rw_type[1];
        $qty_rw = $reward['reward_quantity'] . SP . (($reward['reward_quantity'] > 1) ? ($rw_type_plural) : ($rw_type_singular));

        // Add keys.
        $keys[] = array(
            'text'          => $qty_rw,
            'callback_data' => $pokestop_id . ',' . $quest_id . ':quest_save:' . $reward['id']
        );
    }

    // Add back and abort navigation keys.
    $nav_keys = array();
    $nav_keys[] = universal_inner_key($keys, $pokestop_id, 'quest_edit_reward', $quest_id . ',' . $quest_type, getTranslation('back'));
    $nav_keys[] = universal_inner_key($keys, '0', 'exit', '0', getTranslation('abort'));

    // Get the inline key array.
    $keys = inline_key_array($keys, 2);
    $keys[] = $nav_keys;

    debug_log($keys);

    return $keys;
}

/**
 * Quantity keys.
 * @param $id
 * @param $action
 * @param $arg
 * @param $type
 * @return array
 */
function quantity_keys($id, $action, $arg, $type = 'reward')
{
    // Get the action and value
    $actionval = explode("-", $arg);
    $add = $actionval[0] . '-';
    $old = $actionval[1];

    // Save and reset values
    $save_arg = ($type == 'reward') ? ('save-' . $old) : ('add-' . $old);
    $save_text = ($type == 'reward') ? EMOJI_DISK : getTranslation('next');
    $reset_arg = $add . '0';
    
    // Init empty keys array.
    $keys = array();

    // Max is 9999 and no the value 999 is not a typo!
    // Keys will be shown up to 9999 and when user is adding one more number we exceed 9999, so we remove the keys then
    // This means we do not exceed a Max value of 9999 :)
    if($old <= 999) {

        // Add keys 0 to 9
        /**
         * 7 8 9
         * 4 5 6
         * 1 2 3
         * 0
        */

        // 7 8 9
        for ($i = 7; $i <= 9; $i = $i + 1) {
            // Set new
            $new = $add . ($old == 0 ? '' : $old) . $i;

            // Set keys.
            $keys[] = array(
                'text'          => $i,
                'callback_data' => $id . ':' . $action . ':' . $new
            );
        }

        // 4 5 6
        for ($i = 4; $i <= 6; $i = $i + 1) {
            // Set new
            $new = $add . ($old == 0 ? '' : $old) . $i;

            // Set keys.
            $keys[] = array(
                'text'          => $i,
                'callback_data' => $id . ':' . $action . ':' . $new
            );
        }

        // 1 2 3
        for ($i = 1; $i <= 3; $i = $i + 1) {
            // Set new
            $new = $add . ($old == 0 ? '' : $old) . $i;

            // Set keys.
            $keys[] = array(
                'text'          => $i,
                'callback_data' => $id . ':' . $action . ':' . $new
            );
        }

        // 0
        if($old != 0) {
            // Set new
            $new = $add . $old . '0';
        } else {
            $new = $reset_arg;
        }
        
        // Set keys.
        $keys[] = array(
            'text'          => '0',
            'callback_data' => $id . ':' . $action . ':' . $new
        );
    }

    // Reward? Save button!
    if($type == 'reward') {
        // Save
        $keys[] = array(
            'text'          => $save_text,
            'callback_data' => $id . ':' . $action . ':' . $save_arg
        );
    }

    // Reset
    $keys[] = array(
        'text'          => getTranslation('reset'),
        'callback_data' => $id . ':' . $action . ':' . $reset_arg
    );

    // Get the inline key array.
    $keys = inline_key_array($keys, 3);

    // Not reward? Next button!
    if($type != 'reward') {
        // Add next navigation key.
        $nav_keys = [];
        $nav_keys[] = universal_inner_key($keys, $id, $action, $save_arg . '-0', getTranslation('next'));

        // Get the inline key array.
        $keys[] = $nav_keys; 
    }

    return $keys;
}


/**
 * Insert quest cleanup info to database.
 * @param $chat_id
 * @param $message_id
 * @param $quest_id
 */
function insert_cleanup($chat_id, $message_id, $quest_id)
{
    // Log ID's of quest, chat and message
    debug_log('Quest_ID: ' . $quest_id);
    debug_log('Chat_ID: ' . $chat_id);
    debug_log('Message_ID: ' . $message_id);

    if ((is_numeric($chat_id)) && (is_numeric($message_id)) && (is_numeric($quest_id)) && ($quest_id > 0)) {
        global $db;

        // Get quest.
        $quest = get_quest($quest_id);
    
        // Init found.
        $found = false;

        // Insert cleanup info to database
        if ($quest) {
            // Check if cleanup info is already in database or not
            // Needed since raids can be shared to multiple channels / supergroups!
            $rs = my_query(
                "
                SELECT    *
                    FROM      qleanup
                    WHERE     quest_id = '{$quest_id}'
                "
            );

            // Chat_id and message_id equal to info from database
            while ($cleanup = $rs->fetch_assoc()) {
                // Leave while loop if cleanup info is already in database
                if(($cleanup['chat_id'] == $chat_id) && ($cleanup['message_id'] == $message_id)) {
                    debug_log('Cleanup preparation info is already in database!');
                    $found = true;
                    break;
                } 
            }
        }

        // Insert into database when raid found but no cleanup info found
        if ($quest && !$found) {
            // Build query for cleanup table to add cleanup info to database
            debug_log('Adding cleanup info to database:');
            $rs = my_query(
                "
                INSERT INTO   qleanup
                SET           quest_id = '{$quest_id}',
                                  chat_id = '{$chat_id}',
                                  message_id = '{$message_id}'
                "
            );
        }
    } else {
        debug_log('Invalid input for cleanup preparation!');
    }
}

/**
 * Run quests cleanup.
 * @param $telegram
 * @param $database
 */
function run_cleanup ($telegram = 2, $database = 2) {
    /* Check input
     * 0 = Do nothing
     * 1 = Cleanup
     * 2 = Read from config
    */

    // Get cleanup values from config per default.
    if ($telegram == 2) {
        $telegram = (CLEANUP_QUEST_TELEGRAM == true) ? 1 : 0;
    }

    if ($database == 2) {
        $database = (CLEANUP_QUEST_DATABASE == true) ? 1 : 0;
    }

    // Start cleanup when at least one parameter is set to trigger cleanup
    if ($telegram == 1 || $database == 1) {
        // Query for telegram cleanup without database cleanup
        if ($telegram == 1 && $database == 0) {
            // Get cleanup info.
            $rs = my_query(
                "
                SELECT    * 
                FROM      qleanup
                  WHERE   chat_id <> 0
                  ORDER BY id DESC
                  LIMIT 0, 250     
                ", true
            );
        // Query for database cleanup without telegram cleanup
        } else if ($telegram == 0 && $database == 1) {
            // Get cleanup info.
            $rs = my_query(
                "
                SELECT    * 
                FROM      qleanup
                  WHERE   chat_id = 0
                  LIMIT 0, 250
                ", true
            );
        // Query for telegram and database cleanup
        } else {
            // Get cleanup info.
            $rs = my_query(
                "
                SELECT    * 
                FROM      qleanup
                  LIMIT 0, 250
                ", true
            );
        }

        // Init empty cleanup jobs array.
        $cleanup_jobs = array();

        // Fill array with cleanup jobs.
        while ($rowJob = $rs->fetch_assoc()) {
            $cleanup_jobs[] = $rowJob;
        }

        // Write to log.
        cleanup_log($cleanup_jobs);

        // Init previous quest id.
        $prev_quest_id = "FIRST_RUN";

        foreach ($cleanup_jobs as $row) {
            // Set current quest id.
            $current_quest_id = ($row['quest_id'] == 0) ? $row['cleaned'] : $row['quest_id'];

            // Write to log.
            cleanup_log("Cleanup ID: " . $row['id']);
            cleanup_log("Chat ID: " . $row['chat_id']);
            cleanup_log("Message ID: " . $row['message_id']);
            cleanup_log("Quest ID: " . $row['quest_id']);

            // Make sure quest exists
            $rs = my_query(
                "
                SELECT  quest_date
                FROM    quests
                  WHERE id = {$current_quest_id}
                ", true
            );

            // Fetch quest date.
            $quest = $rs->fetch_assoc();

            // No quest found - set cleanup to 0 and continue with next quest
            if (!$quest) {
                cleanup_log('No quest found with ID: ' . $current_quest_id, '!');
                cleanup_log('Updating cleanup information.');
                my_query(
                "
                    UPDATE    qleanup
                    SET       chat_id = 0, 
                              message_id = 0 
                    WHERE   id = {$row['id']}
                ", true
                );

                // Continue with next quest
                continue;
            }

            // Get quest data only when quest_id changed compared to previous run
            if ($prev_quest_id != $current_quest_id) {
                // Today.
                $today = utcnow('Ymd');
                $log_today = utcnow('Y-m-d');

                // Get quest date.
                $qd = new DateTimeImmutable($quest['quest_date'], new DateTimeZone('UTC'));
                $questdate = $qd->format('Ymd');
                $log_questdate = $qd->format('Y-m-d');

                // Write times to log.
                cleanup_log($log_today, 'Current UTC date:');
                cleanup_log($log_questdate, 'Quest UTC date:');
            }

            // Time for telegram cleanup?
            if ($today > $questdate) {
                // Delete quest telegram message if not already deleted
                if ($telegram == 1 && $row['chat_id'] != 0 && $row['message_id'] != 0) {
                    // Delete telegram message.
                    cleanup_log('Deleting telegram message ' . $row['message_id'] . ' from chat ' . $row['chat_id'] . ' for quest ' . $row['quest_id']);
                    delete_message($row['chat_id'], $row['message_id']);
                    // Set database values of chat_id and message_id to 0 so we know telegram message was deleted already.
                    cleanup_log('Updating telegram cleanup information.');
                    my_query(
                    "
                        UPDATE    qleanup
                        SET       chat_id = 0, 
                                  message_id = 0 
                        WHERE   id = {$row['id']}
                    ", true
                    );
                } else {
                    if ($telegram == 1) {
                        cleanup_log('Telegram message is already deleted!');
                    } else {
                        cleanup_log('Telegram cleanup was not triggered! Skipping...');
                    }
                }
            } else {
                cleanup_log('Skipping cleanup of telegram for this quest! Cleanup time has not yet come...');
            }

            // Time for database cleanup?
            if ($today > $questdate) {
                // Delete quest from quests table.
                // Make sure to delete only once - quest may be in multiple channels/supergroups, but only 1 time in database
                if (($database == 1) && $row['quest_id'] != 0 && ($prev_quest_id != $current_quest_id)) {
                    // Delete quest from quest table.
                    cleanup_log('Deleting quest ' . $current_quest_id);
                    my_query(
                    "
                        DELETE FROM    quests
                        WHERE   id = {$row['id']}
                    ", true
                    );

                    // Set database value of quest_id to 0 so we know info was deleted already
                    // Use quest_id in where clause since the same quest_id can in cleanup more than once
                    cleanup_log('Updating database cleanup information.');
                    my_query(
                    "
                        UPDATE    qleanup
                        SET       quest_id = 0, 
                                  cleaned = {$row['quest_id']}
                        WHERE   quest_id = {$row['quest_id']}
                    ", true
                    );
                } else {
                    if ($database == 1) {
                        cleanup_log('Quest is already deleted!');
                    } else {
                        cleanup_log('Quest cleanup was not triggered! Skipping...');
                    }
                }

                // Delete quest from cleanup table once every value is set to 0 and cleaned got updated from 0 to the quest_id
                // In addition trigger deletion only when previous and current quest_id are different to avoid unnecessary sql queries
                if ($row['quest_id'] == 0 && $row['chat_id'] == 0 && $row['message_id'] == 0 && $row['cleaned'] != 0 && ($prev_quest_id != $current_quest_id)) {
                    // Get all cleanup jobs which will be deleted now.
                    cleanup_log('Removing cleanup info from database:');
                    $rs_cl = my_query(
                    "
                        SELECT *
                        FROM    qleanup
                        WHERE   cleaned = {$row['cleaned']}
                    ", true
                    );

                    // Log each cleanup ID which will be deleted.
                    while($rs_cleanups = $rs_cl->fetch_assoc()) {
                        cleanup_log('Cleanup ID: ' . $rs_cleanups['id'] . ', Former Quest ID: ' . $rs_cleanups['cleaned']);
                    }

                    // Finally delete from cleanup table.
                    my_query(
                    "
                        DELETE FROM    qleanup
                        WHERE   cleaned = {$row['cleaned']}
                    ", true
                    );
                } else {
                    if ($prev_quest_id != $current_quest_id) {
                        cleanup_log('Time for complete removal of quest from database has not yet come.');
                    } else {
                        cleanup_log('Complete removal of quest from database was already done!');
                    }
                }
            } else {
                cleanup_log('Skipping cleanup of database for this quest! Cleanup time has not yet come...');
            }

            // Store current quest id as previous id for next loop
            $prev_quest_id = $current_quest_id;
        }

        // Write to log.
        cleanup_log('Finished with cleanup process!');
    }
}


/**
 * Quest list.
 * @param $update
 */
function quest_list($update)
{
    // Init empty rows array and query type.
    $rows = [];

    // Init quest id.
    $iqq = 0;
   
    // Botname:quest_id received? 
    if (substr_count($update['inline_query']['query'], ':') == 1) {
        // Botname: received, is there a quest_id after : or not?
        if(strlen(explode(':', $update['inline_query']['query'])[1]) != 0) {
            // Quest ID.
            $iqq = intval(explode(':', $update['inline_query']['query'])[1]);
        }
    }

    // Inline list quests.
    if ($iqq != 0) {

        // Quest by ID.
        $request = my_query(
            "
            SELECT  *,
                    id AS iqq_quest_id
                    FROM      quests
                      WHERE   id = {$iqq}
                      AND     quest_date = UTC_DATE()
            "
        );

        while ($answer = $request->fetch_assoc()) {
            $rows[] = $answer;
        }
    } else {
        // Get quest data by user.
        $request = my_query(
            "
            SELECT              *,
                                quests.id AS iqq_quest_id
                    FROM        quests
                      WHERE     user_id = {$update['inline_query']['from']['id']}
                      AND       quest_date = UTC_DATE()
                      ORDER BY  id DESC LIMIT 3
            "
        );

        while ($answer_quests = $request->fetch_assoc()) {
            $rows[] = $answer_quests;
        }
    }

    // Init array.
    $contents = array();

    // For each rows.
    foreach ($rows as $key => $row) {
            // Get the quest.
            $quest = get_quest($row['iqq_quest_id']);

            // Set the text.
            $contents[$key]['text'] = get_formatted_quest($quest, true, true, false, true);

            // Set the title.
            $contents[$key]['title'] = $quest['pokestop_name'];

            // Set the inline keyboard.
            $contents[$key]['keyboard'] = [];

            // Set the description.
            $contents[$key]['desc'] = get_formatted_quest($quest, false, false, true, true);
    }

    debug_log($contents);
    answerInlineQuery($update['inline_query']['id'], $contents);
}

/**
 * Process response from telegram api.
 * @param $json
 * @param $json_response
 * @return mixed
 */
function curl_json_response($json_response, $json)
{
    // Write to log.
    debug_log($json_response, '<-');

    // Decode json response.
    $response = json_decode($json_response, true);

    // Validate response.
    if ($response['ok'] != true || isset($response['update_id'])) {
        // Write error to log.
        debug_log('ERROR: ' . $json . "\n\n" . $json_response . "\n\n");
    } else {
	// Result seems ok, get message_id and chat_id if supergroup or channel message
	if (isset($response['result']['chat']['type']) && ($response['result']['chat']['type'] == "channel" || $response['result']['chat']['type'] == "supergroup")) {
            // Init cleanup_id
            $cleanup_id = 0;

	    // Set chat and message_id
            $chat_id = $response['result']['chat']['id'];
            $message_id = $response['result']['message_id'];

            // Get raid/quest id from $json
            $json_message = json_decode($json, true);

            // Write to log that message was shared with channel or supergroup
            debug_log('Message was shared with ' . $response['result']['chat']['type'] . ' ' . $response['result']['chat']['title']);
            debug_log('Checking input for cleanup info now...');

            // Check if it's a venue and get quest id
            if (!empty($response['result']['venue']['address'])) {
                // Get raid_id or quest_id from address.
                $cleanup_id = substr(strrchr($response['result']['venue']['address'], substr(strtoupper(BOT_ID), 0, 1) . '-ID = '), 7);

            // Check if it's a text and get quest id
            } else if (!empty($response['result']['text'])) {
                $cleanup_id = substr(strrchr($response['result']['text'], substr(strtoupper(BOT_ID), 0, 1) . '-ID = '), 7);
            }

            // Trigger Cleanup when quest id was found
            if($cleanup_id != 0) {
                debug_log('Found ID for cleanup preparation from callback_data or venue!');
                debug_log('Cleanup ID: ' . $cleanup_id);
                debug_log('Chat_ID: ' . $chat_id);
                debug_log('Message_ID: ' . $message_id);

	        // Trigger cleanup preparation process when necessary id's are not empty and numeric
	        if (!empty($chat_id) && !empty($message_id) && !empty($cleanup_id)) {
		    debug_log('Calling cleanup preparation now!');
		    insert_cleanup($chat_id, $message_id, $cleanup_id);
	        } else {
		    debug_log('Missing input! Cannot call cleanup preparation!');
		}
            } else {
                debug_log('No cleanup info found! Skipping cleanup preparation!');
            }
	}
    }

    // Return response.
    return $response;
}

