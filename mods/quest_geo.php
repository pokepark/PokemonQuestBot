<?php
// Write to log.
debug_log('quest_geo()');

// For debug.
//debug_log($update);
//debug_log($data);

// Access check.
bot_access_check($update, 'create');

// Latitude and longitude
$lat = '';
$lon = '';

// Get latitude / longitude values from Telegram Mobile Client
if (isset($update['message']['location'])) {
    $lat = $update['message']['location']['latitude'];
    $lon = $update['message']['location']['longitude'];
}

// Get latitude / longitude from message text if empty
if (empty($lat) && empty($lon)) {
    // Create data array (max. 2)
    $data = explode(',', $data['arg'], 2);

    // Set latitude / longitude
    $lat = $data[0];
    $lon = $data[1];

    // Debug
    debug_log('Lat=' . $lat);
    debug_log('Lon=' . $lon);
}

// Set keys.
$keys = get_pokestops_in_radius_keys($lat, $lon, $config->QUEST_STOPS_RADIUS);

// Keys array received?
if ($keys) {
    // Set message.
    $msg = '<b>' . getTranslation('quest_by_pokestop') . '</b>';

    // Add back navigation key.
    $nav_keys = [];
    $nav_keys[] = universal_inner_key($keys, '0', 'exit', '0', getTranslation('abort'));

    // Get the inline key array.
    $keys[] = $nav_keys;
} else {
    // Set message.
    $msg = '<b>' . getTranslation('pokestops_not_found') . '</b>';

    // Set empty keys.
    $keys = [];
}

// Answer callback or send message based on input prior raid creation
if(empty($update['message']['location']['latitude']) && empty($update['message']['location']['longitude'])) {
    // Telegram JSON array.
    $tg_json = array();

    // Edit the message.
    $tg_json[] = edit_message($update, $msg, $keys, false, true);

    // Build callback message string.
    $callback_response = 'OK';

    // Answer callback.
    $tg_json[] = answerCallbackQuery($update['callback_query']['id'], $callback_response, true);

    // Telegram multicurl request.
    curl_json_multi_request($tg_json);

} else {
    // Send message.
    send_message($update['message']['chat']['id'], $msg, $keys);
}

exit();
