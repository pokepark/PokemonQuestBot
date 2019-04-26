<?php
// Write to log.
debug_log('WILLOW_START()');

// For debug.
//debug_log($update);
//debug_log($data);

// Check access.
bot_access_check($update, 'willow');

// Init empty keys array.
$keys = [];

// Create keys array.
$keys = [
    [
        [
            'text'          => getTranslation('quest'),
            'callback_data' => '0:willow:quest'
        ]
    ],
    [
        [
            'text'          => getTranslation('reward'),
            'callback_data' => '0:willow:reward'
        ]
    ],
    [
        [
            'text'          => getTranslation('encounter'),
            'callback_data' => '0:willow:encounter'
        ]
    ],
    [
        [
            'text'          => getTranslation('quicklist'),
            'callback_data' => '0:willow:quicklist'
        ]
    ]
];

// Add abort navigation key.
$nav_keys = [];
$nav_keys[] = universal_inner_key($keys, '0', 'exit', '0', getTranslation('abort'));

// Get the inline key array.
$keys[] = $nav_keys;

// Set message.
$msg = '<b>' . getTranslation('edit_quests_encounters_rewards_quicklist') . '</b>';

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
