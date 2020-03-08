<?php
// Write to log.
debug_log('invasion_create()');

// For debug.
//debug_log($update);
//debug_log($data);

// Access check.
bot_access_check($update, 'invasion-create');

// Pokestop id.
$pokestop_id = $data['id'];

// Check if invasion already exists for this pokestop.
$invasion_in_db = invasion_duplication_check($pokestop_id);

// Init keys.
$keys = [];

// Invasion already in database or new
if(!$invasion_in_db) {
    // Build message string.
    $msg = '';
    $stop = get_pokestop($pokestop_id, false);
    $msg .= getTranslation('pokestop') . ': <b>' . $stop['pokestop_name'] . '</b>' . (!empty($stop['address']) ? (CR . $stop['address']) : '');
    $msg .= CR . CR . '<b>' . getTranslation('invasion_select_time') . '</b>';

    // Invasion event to short in 5 minute steps as duration
    if($config->INVASION_DURATION_EVENT > $config->INVASION_DURATION_LONG) { 
        $slotmax = $config->INVASION_DURATION_EVENT;
        $slotsize = 5;
   
        // Create the keys.
        for ($i = $slotmax; $i >= $config->INVASION_DURATION_SHORT; $i = $i - $slotsize) {
            $keys[] = array(
                // Just show the time, no text - not everyone has a phone or tablet with a large screen...
                'text'          => floor($i / 60) . ':' . str_pad($i % 60, 2, '0', STR_PAD_LEFT),
                'callback_data' => $pokestop_id . ':invasion_save:' . $i
            );
        }

        $keys = inline_key_array($keys, 5);
        $keys = universal_key($keys, '0', 'exit', '0', getTranslation('abort'));
        //$keys = array_merge($keys,$keys_exit);

    // Duration short and long as selection
    } else {
        // Create the keys.
        $keys = [
            [
                [
                    'text'          => $config->INVASION_DURATION_SHORT . 'min',
                    'callback_data' => $pokestop_id . ':invasion_save:' . INVASION_DURATION_SHORT
                ],
                [
                    'text'          => $config->INVASION_DURATION_LONG . 'min',
                    'callback_data' => $pokestop_id . ':invasion_save:' . INVASION_DURATION_LONG
                ]
            ],
            [
                [
                    'text'          => getTranslation('abort'),
                    'callback_data' => '0:exit:0'
                ]
            ]
        ];
    }

} else {
    // Invasion already in the database for this pokestop.
    $msg = EMOJI_WARN . '<b> ' . getTranslation('invasion_already_submitted') . ' </b>' . EMOJI_WARN . CR . CR;
    $invasion = get_invasion($invasion_in_db['id']);
    $invasion_id = $invasion_in_db['id'];
    $keys_delete = universal_key($keys, $invasion_id, 'invasion_delete', '0', getTranslation('delete'));
    $keys_exit = universal_key($keys, '0', 'exit', '0', getTranslation('abort'));
    $msg .= get_formatted_invasion($invasion);

    // Empty keys.
    $keys = array_merge($keys_delete,$keys_exit);
}

// Telegram JSON array.
$tg_json = array();

// Build callback message string.
$callback_response = 'OK';

// Answer callback.
$tg_json[] = answerCallbackQuery($update['callback_query']['id'], $callback_response, true);

// Edit the message.
$tg_json[] = edit_message($update, $msg, $keys, ['disable_web_page_preview' => 'true'], true);

// Telegram multicurl request.
curl_json_multi_request($tg_json);

exit();
