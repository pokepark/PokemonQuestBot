<?php
// Write to log.
debug_log('QUEST()');

// For debug.
//debug_log($update);
//debug_log($data);

// Get pokestops by name.
// Trim away everything before the first space
$searchterm = $update['message']['text'];
$searchterm = trim(substr($searchterm, strpos($searchterm, ' ') + 1));

// Get all matching pokestops.
$keys = get_pokestop_list_keys($searchterm);

// Keys array received?
if (is_array($keys)) {
    // Set message.
    $msg = '<b>' . getTranslation('quest_by_pokestop') . '</b>';

    // Add back navigation key.
    $nav_keys = [];
    $nav_keys[] = universal_inner_key($keys, '0', 'exit', '0', getTranslation('abort'));

    // Get the inline key array.
    $keys[] = $nav_keys;
} else if ($keys == false) {
    // Set message.
    $msg = '<b>' . getTranslation('pokestops_not_found') . '</b>' . CR . CR . getTranslation('pokestops_not_found_command_text') . SP . getTranslation('pokestops_not_found_command_example');

    // Set empty keys.
    $keys = [];
} else {
    // Set message.
    $msg = '<b>' . getTranslation('pokestops_not_found') . '</b>';

    // Set empty keys.
    $keys = [];
}

// Send message.
send_message($update['message']['chat']['id'], $msg, $keys, ['reply_markup' => ['selective' => true, 'one_time_keyboard' => true]]);

exit();
