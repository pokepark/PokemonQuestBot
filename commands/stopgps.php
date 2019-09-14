<?php
// Write to log.
debug_log('STOPGPS()');

// For debug.
//debug_log($update);
//debug_log($data);

// Check access.
bot_access_check($update, 'pokestop-gps');

// Get pokestop by name.
// Trim away everything before "/stopgps "
$id_info = $update['message']['text'];
$id_info = substr($id_info, 9);
$id_info = trim($id_info);

// Display keys to get stop ids.
if(empty($id_info)) {
    debug_log('Missing pokestop coordinates!');
    // Set message.
    $msg = CR . '<b>' . getTranslation('pokestop_id_gps_missing') . '</b>';
    $msg .= CR . CR . getTranslation('pokestop_gps_instructions');
    $msg .= CR . getTranslation('pokestop_gps_example');

    // Set keys.
    $keys = [];
} else {
    // Set keys.
    $keys = [];

    // Get pokestop id.
    $split_id_info = explode(',', $id_info,2);
    $id = $split_id_info[0];
    $info = $split_id_info[1];
    $info = trim($info);

    // Count commas given in info.
    $count = substr_count($info, ",");

    // 1 comma as it should be?
    // E.g. 52.5145434,13.3501189
    if($count == 1) {
        $lat_lon = explode(',', $info);
        $lat = $lat_lon[0];
        $lon = $lat_lon[1];

    // Lat and lon with comma instead of dot?
    // E.g. 52,5145434,13,3501189
    } else if($count == 3) {
        $lat_lon = explode(',', $info);
        $lat = $lat_lon[0] . '.' . $lat_lon[1];
        $lon = $lat_lon[2] . '.' . $lat_lon[3];
    } else {
        // Invalid input - send the message and exit.
        $msg = '<b>' . getTranslation('invalid_input') . '</b>' . CR . CR;
        $msg .= getTranslation('pokestop_gps_coordinates_format_error') . CR;
        $msg .= getTranslation('pokestop_gps_example');
        sendMessage($update['message']['chat']['id'], $msg);
        exit();
    }

    // Make sure we have a valid pokestop id.
    $pokestop = false;
    if(is_numeric($id)) {
        $pokestop = get_pokestop($id);
    }

    if($pokestop && !empty($info)) {
        debug_log('Updating gps coordinates for pokestop with ID: ' . $id);
        debug_log('Pokestop latitude: ' . $lat);
        debug_log('Pokestop longitude: ' . $lon);
        my_query(
            "
            UPDATE    pokestops
            SET       lat = {$lat},
                      lon = {$lon}
              WHERE   id = {$id}
            "
        );

        // Set message.
        $msg = get_pokestop_details($pokestop);        
        $msg .= EMOJI_NEW . SP . $info;
        $msg .= CR . CR . '<b>' . getTranslation('pokestop_gps_added') . '</b>';
    } else if($pokestop && empty($info)) {
        debug_log('Missing pokestop coordinates!');
        // Set message.
        $msg .= CR . '<b>' . getTranslation('pokestop_id_gps_missing') . '</b>';
        $msg .= CR . CR . getTranslation('pokestop_gps_instructions');
        $msg .= CR . getTranslation('pokestop_gps_example');
    } else {
        // Set message.
        $msg .= getTranslation('invalid_input');
    }
}

// Send message.
send_message($update['message']['chat']['id'], $msg, $keys, ['reply_markup' => ['selective' => true, 'one_time_keyboard' => true], 'disable_web_page_preview' => 'true']);

?>
