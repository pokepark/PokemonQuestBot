<?php
// Check access.
$access = bot_access_check($update, 'help', false, true);

// Display help for each permission
if($access && (is_file(ROOT_PATH . '/access/' . $access) || $access == 'BOT_ADMINS')) {
    // Get permissions from file.
    if($access == 'BOT_ADMINS') {
        $permissions = array();
        $permissions[] = 'access-bot';
        $permissions[] = 'create';
        $permissions[] = 'list';
        $permissions[] = 'delete-all';
        $permissions[] = 'pokestop-details';
        $permissions[] = 'pokestop-name';
        $permissions[] = 'pokestop-address';
        $permissions[] = 'pokestop-add';
        $permissions[] = 'pokestop-delete';
        $permissions[] = 'dex';
        $permissions[] = 'willow';
        $permissions[] = 'config-get';
        $permissions[] = 'config-set';
        $permissions[] = 'help';
    } else {
        // Get permissions from file.
        $permissions = file(ROOT_PATH . '/access/' . $access);
    }

    // Write to log.
    // debug_log($permissions,'ACCESS: ');

    // Show help header.
    debug_log('Showing help to user now');
    $msg = '<b>' . getTranslation('personal_help') . '</b>' . CR . CR;

    // Quest via location?
    if(QUEST_VIA_LOCATION == true) {
        $msg .= EMOJI_CLIPPY . SP . getTranslation('help_create_via_location') . CR . CR;
    }

    // Show help.
    foreach($permissions as $id => $p) {
        if($p == 'access-bot' || strpos($p, 'share-') === 0) continue;
        $msg .= getTranslation('help_' . $p) . CR . CR;
    }
// No help for the user.
} else {
    $msg = getTranslation('bot_access_denied');
}

// Send message.
sendMessage($update['message']['from']['id'], $msg);

?>

