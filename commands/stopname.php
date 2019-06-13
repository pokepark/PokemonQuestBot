<?php
// Write to log.
debug_log('STOPNAME()');

// For debug.
//debug_log($update);
//debug_log($data);

// Check access.
bot_access_check($update, 'pokestop-name');

// Get pokestop by name.
// Trim away everything before "/stopname "
$id_info = $update['message']['text'];
$id_info = substr($id_info, 9);
$id_info = trim($id_info);

// Display info to get pokestop ids.
if(empty($id_info)) {
    debug_log('Missing pokestop name!');
    // Set message.
    $msg = '<b>' . getTranslation('pokestop_id_name_missing') . '</b>';
    $msg .= CR . CR . getTranslation('pokestop_name_instructions');
    $msg .= CR . getTranslation('pokestop_name_example');
    $msg .= CR . CR . getTranslation('pokestop_get_id_details');

    // Set keys.
    $keys = [];
} else {
    // Set keys.
    $keys = [];

    // Init vars.
    $pokestop = false;
    $info = '';
    $id = 0;
    $tg_id = '#' . $update['message']['from']['id'];

    // Get pokestop id.
    if(substr_count($id_info, ',') >= 1) {
        $split_id_info = explode(',', $id_info,2);
        $id = $split_id_info[0];
        $info = $split_id_info[1];
        $info = trim($info);

        // Make sure we have a valid pokestop id.
        if(is_numeric($id)) {
            $pokestop = get_pokestop($id);
        }
    }

    // Maybe get pokestop by telegram id?
    if(!$pokestop) {
        $pokestop = get_pokestop_by_telegram_id($tg_id);
        // Get new id.
        if($pokestop) {
            $id = $pokestop['id'];
            $info = $id_info;
        }
    }

    // Update pokestop info.
    if($pokestop && !empty($info) && $id > 0) {
        debug_log('Changing name for pokestop with ID: ' . $id);
        debug_log('Pokestop name: ' . $info);
        my_query(
            "
            UPDATE    pokestops
            SET       pokestop_name = '{$db->real_escape_string($info)}'
              WHERE   id = {$id}
            "
        );

        // Set message.
        $pokestop = get_pokestop($id);
        $msg = get_pokestop_details($pokestop);        
        $msg .= CR . '<b>' . getTranslation('pokestop_name_updated') . '</b>';
    } else if($pokestop && empty($info)) {
        debug_log('Missing pokestop name!');
        // Set message.
        $msg = CR . '<b>' . getTranslation('pokestop_id_name_missing') . '</b>';
        $msg .= CR . CR . getTranslation('pokestop_name_instructions');
        $msg .= CR . getTranslation('pokestop_name_example');
        $msg .= CR . CR . getTranslation('pokestop_get_id_details');
    } else {
        // Set message.
        $msg = getTranslation('invalid_input');
    }
}

// Send message.
send_message($update['message']['chat']['id'], $msg, $keys, ['reply_markup' => ['selective' => true, 'one_time_keyboard' => true], 'disable_web_page_preview' => 'true']);

?>
