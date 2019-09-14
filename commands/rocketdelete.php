<?php
// Write to log.
debug_log('ROCKETDELETE()');

// For debug.
//debug_log($update);
//debug_log($data);

// Check access.
bot_access_check($update, 'access-bot');

// Get all active invasions from database.
$rs = my_query(
        "
        SELECT     invasions.*,
                   pokestops.pokestop_name, pokestops.lat, pokestops.lon, pokestops.address
        FROM       invasions
        LEFT JOIN  pokestops
        ON         invasions.pokestop_id = pokestops.id
        WHERE      invasions.end_time > UTC_TIMESTAMP()
        ORDER BY   invasions.id
        "
    );

// Init empty keys array.
$keys = array();

// Add key for quest
while ($invasions = $rs->fetch_assoc()) {
    // Pokestop name.
    $pokestop_name = (!empty($invasions['pokestop_name']) ? ($invasions['pokestop_name']) : (getTranslation('unnamed_pokestop')));

    // Add buttons to delete quests.
    $keys[] = array(
        'text'          => $pokestop_name,
        'callback_data' => $invasions['id'] . ':invasion_delete:0'
    );

}

if(!$keys) {
    // Set the message.
    $msg = '<b>' . getTranslation('no_invasions_currently') . '</b>' . CR;

    // Set empty keys.
    $keys = [];
} else {
    // Add header.
    $msg = '<b>' . getTranslation('invasions_currently') . '</b>' . CR;
    $msg .= get_current_formatted_invasions();

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
