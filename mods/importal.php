<?php
// Write to log.
debug_log('importal()');

// For debug.
//debug_log($update);
//debug_log($data);

// Check access.
bot_access_check($update, 'portal-import');

// Ingressportalbot icon
$icon = iconv('UCS-4LE', 'UTF-8', pack('V', 0x1F4DC));
$coords = explode('&pll=',$update['message']['entities']['1']['url'])[1];
$latlon = explode(',', $coords);
$lat = $latlon[0];
$lon = $latlon[1];
// Ingressportalbot
if(strpos($update['message']['text'], $icon . 'Portal:') === 0) {
    // Get portal name.
    $portal = trim(str_replace($icon . 'Portal:', '', strtok($update['message']['text'], PHP_EOL)));
    // Get portal address.
    $address = explode(PHP_EOL, $update['message']['text'])[1];
    $address = trim(explode(':', $address, 2)[1]);
// PortalMapBot
} else if(substr_compare(strtok($update['message']['text'], PHP_EOL), '(Intel)', -strlen('(Intel)')) === 0) {
    // Get portal name.
    $portal = trim(substr(strtok($update['message']['text'], PHP_EOL), 0, -strlen('(Intel)')));
    // Get portal address.
    $address = trim(explode(PHP_EOL, $update['message']['text'])[4]);
}

// Remove country from address, e.g. ", Netherlands"
$address = explode(',',$address,-1);
$address = trim(implode(',',$address));

// Empty address? Try lookup.
if(empty($address)) {
    // Get address.
    $addr = get_address($lat, $lon);
    $address = format_address($addr);
}

// Write to log.
debug_log('Detected message from @PortalMapBot');
debug_log($portal, 'Portal:');
debug_log($coords, 'Coordinates:');
debug_log($lat, 'Latitude:');
debug_log($lon, 'Longitude:');
debug_log($address, 'Address:');

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

// Send the message.
send_message($update['message']['chat']['id'], $msg, $keys, ['disable_web_page_preview' => 'true']);

?>
