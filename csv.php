<?php

// Set error reporting in debug mode.
if ('DEBUG' === true) {
    error_reporting(E_ALL ^ E_NOTICE);
}

// Get current unix timestamp as float.
$start = microtime(true);

// Include files.
require_once('config.php');
require_once('core/class/debug.php');
require_once('core/class/functions.php');
require_once('constants.php');
require_once('logic.php');
require_once('core/class/geo_api.php');

$db = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
$db->set_charset('utf8mb4');


$query=       "
        SELECT     quests.*,
                   questlist.quest_type, questlist.quest_quantity, questlist.quest_action,
                   rewardlist.reward_type, rewardlist.reward_quantity, 
                   pokestops.pokestop_name, pokestops.lat, pokestops.lon, pokestops.address,
                   encounterlist.pokedex_ids
        FROM       quests
        LEFT JOIN  pokestops
        ON         quests.pokestop_id = pokestops.id
        LEFT JOIN  questlist
        ON         quests.quest_id = questlist.id
        LEFT JOIN  rewardlist
        ON         quests.reward_id = rewardlist.id
        LEFT JOIN  encounterlist
        ON         quests.quest_id = encounterlist.quest_id
        WHERE      quest_date = CURDATE()
        ";
if ($id) $query .= ' AND quest_id='.intval($id);
        
$query .= " ORDER BY   pokestops.pokestop_name ";

$rs = my_query($query);

$fp = fopen('php://temp', 'w+');


while ($row = $rs->fetch_assoc()) {
    
    $getTypeTranslation = 'getQuestTranslation';
    
    $quest = $row;

    // Pokestop name and address.
    $pokestop_name = SP . '<b>' . (!empty($quest['pokestop_name']) ? ($quest['pokestop_name']) : ($getTypeTranslation('unnamed_pokestop'))) . '</b>' . CR;

    // Get pokestop info.
    $stop = get_pokestop($quest['pokestop_id'], false);

    // Add google maps link.
    if(!empty($quest['address'])) {
        $pokestop_address = '<a href="https://maps.google.com/?daddr=' . $quest['lat'] . ',' . $quest['lon'] . '">' . $quest['address'] . '</a>';
    } else if(!empty($stop['address'])) {
        $pokestop_address = '<a href="https://maps.google.com/?daddr=' . $stop['lat'] . ',' . $stop['lon'] . '">' . $stop['address'] . '</a>';
    } else {
        $pokestop_address = '<a href="https://maps.google.com/maps?q=' . $quest['lat'] . ',' . $quest['lon'] . '">https://maps.google.com/maps?q=' . $quest['lat'] . ',' . $quest['lon'] . '</a>';
    }

    // Quest action: Singular or plural?
    $quest_action = explode(":", $getTypeTranslation('quest_action_' . $quest['quest_action']));
    $quest_action_singular = $quest_action[0];
    $quest_action_plural = $quest_action[1];
    $qty_action = $quest['quest_quantity'] . SP . (($quest['quest_quantity'] > 1) ? ($quest_action_plural) : ($quest_action_singular));

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
            $msg_poke .= ($override_language == true) ? (get_local_pokemon_name($pokedex_id, true, 'quest')) : (get_local_pokemon_name($pokedex_id));
            $msg_poke .= ' / ';
        }
        // Trim last slash
        $msg_poke = rtrim($msg_poke,' / ');
        $msg_poke = (!empty($msg_poke) ? (SP . '(' . $msg_poke . ')') : '');
    }

    // Build quest message
    $msg = '';
    if($compact_format == false) {
        $msg .= $getTypeTranslation('pokestop') . ':' . $pokestop_name . $pokestop_address . CR;
        $msg .= $getTypeTranslation('quest') . ': <b>' . $getTypeTranslation('quest_type_' . $quest['quest_type']) . SP . $qty_action . '</b>' . CR;
        $msg .= $getTypeTranslation('reward') . ': <b>' . $qty_reward . '</b>' . $msg_poke . CR;
    } else {
        $msg .= $getTypeTranslation('quest_type_' . $quest['quest_type']) . SP . $qty_action . ' — ' . $qty_reward . $msg_poke;
    }
    
    $data = [
        $row['quest_date'],
        $row['pokestop_name'],
        $row['lat'].','.$row['lon'],
        $row['address'],
        $getTypeTranslation('quest_type_' . $quest['quest_type']),
        $qty_action,
        $qty_reward,
        $msg_poke ? $msg_poke : $qty_reward,
    ];
    
    fputcsv($fp, $data);
}

rewind($fp);
$csv = stream_get_contents($fp);
fclose($fp);

echo $csv;



