<?php
// Write to log.
debug_log('DELETE()');

// For debug.
//debug_log($update);
//debug_log($data);

// Check access.
bot_access_check($update, 'access-bot');

// Get all quests of the day from database.
$rs = my_query(
        "
        SELECT     quests.*,
                   questlist.quest_event, questlist.quest_type, questlist.quest_quantity, questlist.quest_action, questlist.quest_pokedex_ids, questlist.quest_poketypes,
                   rewardlist.reward_type, rewardlist.reward_quantity, 
                   pokestops.pokestop_name, pokestops.lat, pokestops.lon, pokestops.address
        FROM       quests
        LEFT JOIN  pokestops
        ON         quests.pokestop_id = pokestops.id
        LEFT JOIN  questlist
        ON         quests.quest_id = questlist.id
        LEFT JOIN  rewardlist
        ON         quests.reward_id = rewardlist.id
        WHERE      quest_date > UTC_DATE()
        AND        quest_date < UTC_DATE() + INTERVAL 1 DAY
        ORDER BY   quests.id
        "
    );

// Init empty keys array.
$keys = array();

// Add key for quest
while ($quests = $rs->fetch_assoc()) {
    // Pokestop name.
    $pokestop_name = (!empty($quests['pokestop_name']) ? ($quests['pokestop_name']) : (getTranslation('unnamed_pokestop')));

    // Add buttons to delete quests.
    $keys[] = array(
        'text'          => $pokestop_name,
        'callback_data' => $quests['id'] . ':quest_delete:0'
    );

}

if(!$keys) {
    // Set the message.
    $msg = '<b>' . getTranslation('no_quests_today') . '</b>' . CR;

    // Set empty keys.
    $keys = [];
} else {
    // Add header.
    $msg = '<b>' . getTranslation('quests_today') . '</b>' . CR;
    $msg .= get_todays_formatted_quests();

    // Set keys.
    $keys = inline_key_array($keys, 2);

    // Add Done key.
    $keys[] = [
        [
            'text'          => getTranslation('done'),
            'callback_data' => '0:exit:1'
        ]
    ];
}

// Send the message.
send_message($update['message']['chat']['id'], $msg, $keys, ['disable_web_page_preview' => 'true']);

exit();
