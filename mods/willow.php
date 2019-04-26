<?php
// Write to log.
debug_log('WILLOW()');

// For debug.
//debug_log($update);
//debug_log($data);

// Check access.
bot_access_check($update, 'willow');

// Get data arg.
$arg = $data['arg'];

// Init empty keys array.
$keys = [];

// Create keys array.
$keys = [
    [
        [
            'text'          => getTranslation('add'),
            'callback_data' => '0:willow_' . $arg . '_add:0'
        ]
    ],
    [
        [
            'text'          => getTranslation('delete'),
            'callback_data' => '0:willow_' . $arg . '_delete:0'
        ]
    ]
];

// Encounter?
if($arg == 'encounter') {
    $edit_keys = [];
    $edit_keys[] = universal_inner_key($keys, '0', 'willow_' . $arg . '_add', '1', getTranslation('edit'));
    $keys[] = $edit_keys;
}

// Add navigation keys.
$nav_keys = [];
$nav_keys[] = universal_inner_key($keys, '0', 'willow_start', '0', getTranslation('back'));
$nav_keys[] = universal_inner_key($keys, '0', 'exit', '0', getTranslation('abort'));

// Get the inline key array.
$keys[] = $nav_keys;

// Set message.
$msg = '<b>' . getTranslation($arg) . '</b> — ' . getTranslation('select_action');

// Telegram JSON array.
$tg_json = array();

// Edit message.
$tg_json[] = edit_message($update, $msg, $keys, ['disable_web_page_preview' => 'true'], true);

// Build callback message string.
$callback_response = 'OK';

// Answer callback.
$tg_json[] = answerCallbackQuery($update['callback_query']['id'], $callback_response, true);

// Telegram multicurl request.
curl_json_multi_request($tg_json);

exit();
