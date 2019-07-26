<?php
// Write to log.
debug_log('CRYPTO()');

// For debug.
//debug_log($update);
//debug_log($data);

// Check access.
bot_access_check($update, 'invasion-create');

// Add comment to invasion.
// Trim away everything before the first space
// Max. 48 chars.
$comment = $update['message']['text'];
$comment = trim(substr($comment, strpos($comment, ' ') + 1));
$comment = substr($comment,0,48);

// Get all quests of the day from database.
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
    $text = empty($invasions['pokestop_name']) ? getTranslation('unnamed_pokestop') : $invasions['pokestop_name'];

    // Add buttons to delete invasions.
    $keys[] = array(
        'text'          => $text,
        'callback_data' => $invasions['id'] . ':crypto:' . $comment
    );

}

// Keys array received?
if ($keys) {
    // Set message.
    $msg = '<b>' . getTranslation('add_this_info_to_invasion') . '</b>';
    $msg .= CR . CR . getTranslation('info') . ': ' . '<b>' . $comment . '</b>';

    // Add abort navigation key.
    $keys = inline_key_array($keys, 2);
    $nav_keys = [];
    $nav_keys[] = universal_inner_key($keys, '0', 'exit', '0', getTranslation('abort'));

    // Get the inline key array.
    $keys[] = $nav_keys;
} else {
    // Set message.
    $msg = '<b>' . getTranslation('no_invasions_currently') . '</b>';

    // Set empty keys.
    $keys = [];
}

// Send message.
send_message($update['message']['chat']['id'], $msg, $keys, ['reply_markup' => ['selective' => true, 'one_time_keyboard' => true]]);

exit();
