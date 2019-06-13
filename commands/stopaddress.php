<?php
// Write to log.
debug_log('STOPADDRESS()');

// For debug.
//debug_log($update);
//debug_log($data);

// Check access.
bot_access_check($update, 'pokestop-address');

// Get pokestop by name.
// Trim away everything before "/stopaddress "
$id_info = $update['message']['text'];
$id_info = substr($id_info, 13);
$id_info = trim($id_info);

// Show info.
if(empty($id_info)) {
    debug_log('Missing pokestop address!');
    // Set message.
    $msg = CR . '<b>' . getTranslation('pokestop_id_address_missing') . '</b>';
    $msg .= CR . CR . getTranslation('pokestop_address_instructions');
    $msg .= CR . getTranslation('pokestop_address_example');
    $msg .= CR . CR . getTranslation('pokestop_address_reset');
    $msg .= CR . getTranslation('pokestop_address_reset_example');
    $msg .= CR . CR . getTranslation('pokestop_get_id_details');

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

    // Make sure we have a valid pokestop id.
    $pokestop = false;
    if(is_numeric($id)) {
        $pokestop = get_pokestop($id);
    }

    // Update pokestop info.
    if($pokestop && !empty($info) && strtolower($info) == 'reset') {
        debug_log('Deleting address for pokestop with ID: ' . $id);
        my_query(
            "
            UPDATE    pokestops
            SET       address = NULL
              WHERE   id = {$id}
            "
        );

        // Set message.
        $msg = get_pokestop_details($pokestop);        
        $msg .= CR . '<b>' . getTranslation('pokestop_address_deleted') . '</b>';
    } else if($pokestop && !empty($info)) {
        debug_log('Adding address for pokestop with ID: ' . $id);
        debug_log('Pokestop address: ' . $info);
        my_query(
            "
            UPDATE    pokestops
            SET       address = '{$db->real_escape_string($info)}'
              WHERE   id = {$id}
            "
        );

        // Set message.
        $msg = get_pokestop_details($pokestop);        
        $msg .= EMOJI_NEW . SP . $info;
        $msg .= CR . CR . '<b>' . getTranslation('pokestop_address_added') . '</b>';
    } else if($pokestop && empty($info)) {
        debug_log('Missing pokestop address!');
        // Set message.
        $msg .= CR . '<b>' . getTranslation('pokestop_id_address_missing') . '</b>';
        $msg .= CR . CR . getTranslation('pokestop_address_instructions');
        $msg .= CR . getTranslation('pokestop_address_example');
        $msg .= CR . CR . getTranslation('pokestop_address_reset');
        $msg .= CR . getTranslation('pokestop_address_reset_example');
        $msg .= CR . CR . getTranslation('pokestop_get_id_details');
    } else {
        // Set message.
        $msg .= getTranslation('invalid_input');
    }
}

// Send message.
send_message($update['message']['chat']['id'], $msg, $keys, ['reply_markup' => ['selective' => true, 'one_time_keyboard' => true], 'disable_web_page_preview' => 'true']);

?>
