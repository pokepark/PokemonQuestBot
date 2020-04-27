<?php
// Write to log.
debug_log('importal()');

// For debug.
//debug_log($update);
//debug_log($data);

// Check access.
bot_access_check($update, 'portal-import');

// Import allowed?
if($config->PORTAL_IMPORT) {

    // Process message for portal information.
    require_once(CORE_BOT_PATH . '/importal.php');

    // Insert pokestop.
    try {

        global $db;

        // Pokestop name.
        $pokestop_name = $portal;
        if(empty($portal)) {
            $pokestop_name = '#' . $update['message']['from']['id'];
        }

        // Build query to check if pokestop is already in database or not
        $rs = my_query("
        SELECT    id, COUNT(*)
        FROM      pokestops
          WHERE   pokestop_name = '{$pokestop_name}'
         ");

        $row = $rs->fetch_row();

        // Pokestop already in database or new
        if (empty($row['0'])) {
            // insert pokestop in table.
            debug_log('Pokestop not found in database pokestop list! Inserting pokestop "' . $pokestop_name . '" now.');
            $query = '
            INSERT INTO pokestops (pokestop_name, lat, lon, address)
            VALUES (:pokestop_name, :lat, :lon, :address)
            ';
            $msg = getTranslation('pokestop_added');

        } else {
            // Update pokestops table to reflect pokestop changes.
            debug_log('Pokestop found in database pokestop list! Updating pokestop "' . $pokestop_name . '" now.');
            $query = '
                UPDATE        pokestops
                SET           lat = :lat,
                              lon = :lon,
                              address = :address
                WHERE      pokestop_name = :pokestop_name
            ';
            $msg = getTranslation('pokestop_updated');
            $pokestop_id = get_pokestop_by_telegram_id($pokestop_name);
            $pokestop_id = $pokestop_id['id'];
        }

        // Insert / Update.
        $statement = $dbh->prepare($query);
        $statement->bindValue(':pokestop_name', $pokestop_name, PDO::PARAM_STR);
        $statement->bindValue(':lat', $lat, PDO::PARAM_STR);
        $statement->bindValue(':lon', $lon, PDO::PARAM_STR);
        $statement->bindValue(':address', $address, PDO::PARAM_STR);
        $statement->execute();
    } catch (PDOException $exception) {
        error_log($exception->getMessage());
        $dbh = null;
        exit();
    }

    // Get last insert id.
    if (empty($row['0'])) {
        $pokestop_id = $dbh->lastInsertId();
    }

    // Pokestop details.
    if($pokestop_id > 0) {
        $pokestop = get_pokestop($pokestop_id);
        $msg .= CR . CR . get_pokestop_details($pokestop);
    }

    // Set keys.
    $keys = [
        [
            [
                'text'          => getTranslation('delete'),
                'callback_data' => $pokestop_id . ':stop_delete:0'
            ],
            [
                'text'          => getTranslation('done'),
                'callback_data' => '0:exit:1'
            ]
        ]
    ];
} else {
    $msg = getTranslation('bot_access_denied');
    $keys = [];
}

// Send the message.
send_message($update['message']['chat']['id'], $msg, $keys, ['disable_web_page_preview' => 'true']);

?>
