<?php
// Write to log.
debug_log('WILLOW()');

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

// Send message.
send_message($update['message']['chat']['id'], $msg, $keys, ['reply_markup' => ['selective' => true, 'one_time_keyboard' => true]]);

exit();
