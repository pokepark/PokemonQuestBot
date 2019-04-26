<?php
// Write to log.
debug_log('quest_edit_type()');

// For debug.
//debug_log($update);
//debug_log($data);

// Check access.
bot_access_check($update, 'create');

// Pokestop id.
$pokestop_id = $data['id'];

// Quest event and type.
$arg = explode('-', $data['arg']);
$quest_flag = $arg[0];
$quest_event = $arg[1];
$quest_type = $arg[2];

// Get keys for events and types
if($quest_flag == 0 && $quest_event == 0) {
    // Build message string.
    $msg = '';
    $stop = get_pokestop($pokestop_id, false);
    $msg .= getTranslation('pokestop') . ': <b>' . $stop['pokestop_name'] . '</b>' . (!empty($stop['address']) ? (CR . $stop['address']) : '');
    $msg .= CR . getTranslation('quest') . ': <b>' . getTranslation('quest_type_' . $quest_type) . '...</b>';
    $msg .= CR . CR . '<b>' . getTranslation('quest_select_type') . '</b>';

    // Create the keys.
    $keys = quest_type_keys($pokestop_id);

// Get type keys for event
} else if($quest_flag == 0 && $quest_event > 0) {
    // Build message string.
    $msg = '';
    $stop = get_pokestop($pokestop_id, false);
    $msg .= getTranslation('pokestop') . ': <b>' . $stop['pokestop_name'] . '</b>' . (!empty($stop['address']) ? (CR . $stop['address']) : '');
    $msg .= CR . '<b>' . getTranslation('quest_event_' . $quest_event) . ':' . SP . getTranslation('quest_type_' . $quest_type) . '...</b>';
    $msg .= CR . CR . '<b>' . getTranslation('quest_select_type') . '</b>';

    // Create the keys.
    $keys = quest_type_keys($pokestop_id, $quest_event);

// Get qty action keys
} else if($quest_flag == 1) {
    // Build message string.
    $msg = '';
    $stop = get_pokestop($pokestop_id, false);
    $msg .= getTranslation('pokestop') . ': <b>' . $stop['pokestop_name'] . '</b>' . (!empty($stop['address']) ? (CR . $stop['address']) : '');
    // Event?    
    if($quest_event > 0) {
        $msg .= CR . '<b>' . getTranslation('quest_event_' . $quest_event) . ':' . SP . getTranslation('quest_type_' . $quest_type) . '...</b>';
    } else {
        $msg .= CR . getTranslation('quest') . ': <b>' . getTranslation('quest_type_' . $quest_type) . '...</b>';
    }
    $msg .= CR . CR . '<b>' . getTranslation('quest_select_qty_action') . '</b>';

    // Create the keys.
    $keys = quest_qty_action_keys($pokestop_id, $quest_event, $quest_type);
}

// Telegram JSON array.
$tg_json = array();

// Edit message.
$tg_json[] = edit_message($update, $msg, $keys, false, true);

// Build callback message string.
$callback_response = 'OK';

// Answer callback.
$tg_json[] = answerCallbackQuery($update['callback_query']['id'], $callback_response, true);

// Telegram multicurl request.
curl_json_multi_request($tg_json);

exit();
