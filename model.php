<?php

/**
 * @param string $host Database host
 * @param string $db Database name
 * @param string $user Database user
 * @param string $pass Database password
 * @return PDO Database object
 */
function connect_db($host, $db, $user, $pass){
    $charset = 'utf8mb4';

    $dsn = "mysql:host=$host;dbname=$db;charset=$charset";
    $options = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ];
    try {
        $pdo = new PDO($dsn, $user, $pass, $options);
    } catch (PDOException $e) {
        echo sprintf("Failed to connect. %s",$e->getMessage());
        exit();
    }
    return $pdo;
}

/**
 * Creates a new navigation array item using URL and active status
 * @param string $url The URL of the navigation item
 * @param bool $active Set the navigation item to active or inactive
 * @return array
 */
function na($url, $active){
    return [$url, $active];
}

/**
 * Creates filename to the template
 * @param string $template Filename of the template without extension
 * @return string
 */
function use_template($template){
    $template_doc = sprintf("views/%s.php", $template);
    return $template_doc;
}

/**
 * Creates breadcrumb HTML code using given array
 * @param array $breadcrumbs Array with as Key the page name and as Value the corresponding URL
 * @return string HTML code that represents the breadcrumbs
 */
function get_breadcrumbs($breadcrumbs) {
    $breadcrumbs_exp = '
    <nav aria-label="breadcrumb">
    <ol class="breadcrumb">';
    foreach ($breadcrumbs as $name => $info) {
        if ($info[1]){
            $breadcrumbs_exp .= '<li class="breadcrumb-item active" aria-current="page">'.$name.'</li>';
        }else{
            $breadcrumbs_exp .= '<li class="breadcrumb-item"><a href="'.$info[0].'">'.$name.'</a></li>';
        }
    }
    $breadcrumbs_exp .= '
    </ol>
    </nav>';
    return $breadcrumbs_exp;
}

/**
 * Creates navigation HTML code using given array
 * @param array $navigation Array with as Key the page name and as Value the corresponding URL
 * @return string HTML code that represents the navigation
 */
function get_navigation($template, $active_id){
    $logged_in = check_login();
    $navigation_exp = '
    <div class="container">
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark static-top">
        <a class="navbar-brand" href="#">
            <img src="https://placeholder.pics/svg/150x50/888888/EEE/Logo" alt="..." height="36">
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <ul class="navbar-nav mr-auto">';
    if ($logged_in) {
        $user_role = $_SESSION['user_role'];
        foreach ($template as $id => $info) {
            if ((($info['login'] == 'yes') or ($info['login'] == 'always')) and (($user_role == $info['role']) or ($info['role'] == 'everyone')) and $info['align'] == 'left') {
                if (($id == $active_id) and (($info['login'] == 'yes') or ($info['login'] == 'always'))){
                    $navigation_exp .= '<li class="nav-item active">';
                    $navigation_exp .= '<a class="nav-link" href="'.$info['url'].'">'.$info['name'].'</a>';
                }else{
                    $navigation_exp .= '<li class="nav-item">';
                    $navigation_exp .= '<a class="nav-link" href="'.$info['url'].'">'.$info['name'].'</a>';
                }
            }
            $navigation_exp .= '</li>';
        }
        $navigation_exp .= '
        </ul>
        <ul class="navbar-nav ml-auto">';
        foreach ($template as $id => $info) {
            if ((($info['login'] == 'yes') or ($info['login'] == 'always')) and (($user_role == $info['role']) or ($info['role'] == 'everyone')) and $info['align'] == 'right') {
                if ($id == $active_id){
                    $navigation_exp .= '<li class="nav-item active">';
                    $navigation_exp .= '<a class="nav-link" href="'.$info['url'].'">'.$info['name'].'</a>';
                }else{
                    $navigation_exp .= '<li class="nav-item">';
                    $navigation_exp .= '<a class="nav-link" href="'.$info['url'].'">'.$info['name'].'</a>';
                }
            }
            $navigation_exp .= '</li>';
        }
        $navigation_exp .= '
        </ul>
        </div>';
    }
    if (!$logged_in) {
        foreach ($template as $id => $info) {
            if ((($info['login'] == 'no') or ($info['login'] == 'always')) and $info['align'] == 'left') {
                if ($id == $active_id){
                    $navigation_exp .= '<li class="nav-item active">';
                    $navigation_exp .= '<a class="nav-link" href="'.$info['url'].'">'.$info['name'].'</a>';
                }else{
                    $navigation_exp .= '<li class="nav-item">';
                    $navigation_exp .= '<a class="nav-link" href="'.$info['url'].'">'.$info['name'].'</a>';
                }
            }
            $navigation_exp .= '</li>';
        }
        $navigation_exp .= '
        </ul>
        <ul class="navbar-nav ml-auto">';
        foreach ($template as $id => $info) {
            if (($info['login'] == 'no') or ($info['login'] == 'always') and $info['align'] == 'right') {
                if ($id == $active_id){
                    $navigation_exp .= '<li class="nav-item active">';
                    $navigation_exp .= '<a class="nav-link" href="'.$info['url'].'">'.$info['name'].'</a>';
                }else{
                    $navigation_exp .= '<li class="nav-item">';
                    $navigation_exp .= '<a class="nav-link" href="'.$info['url'].'">'.$info['name'].'</a>';
                }
            }
            $navigation_exp .= '</li>';
        }
        $navigation_exp .= '
        </ul>
        </div>';
    }
    $navigation_exp .= '
    </nav>
    </div>';
    return $navigation_exp;
}

/**
 * Add room to the database
 * @param PDO $pdo Database object
 * @param array $room_info Associative array with series info
 * @return array Associative array with key type and message
 */
function add_room($pdo, $room_info){
    /* Check if current user is allowed to add a room */
    if (get_user_role() != 1){
        return [
            'type' => 'danger',
            'message' => 'You are not authorized to add a room.'
        ];
    }

    /* Check if all fields are set */
    if (
        empty($room_info['room_name']) or
        empty($room_info['price']) or
        empty($room_info['type']) or
        empty($room_info['size']) or
        empty($room_info['city']) or
        empty($room_info['postcode']) or
        empty($room_info['street']) or
        empty($room_info['house_nr']) or
        empty($room_info['description'])
        ) {
        return [
            'type' => 'danger',
            'message' => 'There was an error. Not all fields were filled in.'
        ];
    }

    /* Check if room name already exists */
    $stmt = $pdo->prepare('SELECT * FROM rooms WHERE room_name = ?');
    $stmt->execute([$room_info['room_name']]);
    $rooms = $stmt->rowCount();
    if ($rooms){
        return [
            'type' => 'danger',
            'message' => 'There is a room with this name already. Please choose another one.'
        ];
    }

    /* Add Room */
    $stmt = $pdo->prepare("INSERT INTO rooms (room_name, price, type, size, city, postcode, street, house_nr, description, owner_id) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([
        $room_info['room_name'],
        $room_info['price'],
        $room_info['type'],
        $room_info['size'],
        $room_info['city'],
        $room_info['postcode'],
        $room_info['street'],
        $room_info['house_nr'],
        $room_info['description'],
        get_user_id()
    ]);
    $inserted = $stmt->rowCount();
    if ($inserted ==  1) {
        return [
            'type' => 'success',
            'message' => sprintf("Your room '%s' was successfully added!", $room_info['room_name'])
        ];
    }
    else {
        return [
            'type' => 'danger',
            'message' => 'There was an error, your room was not added. Please try again.'
        ];
    }
}

/**
 * Removes a room based on room ID
 * @param PDO $pdo Database object
 * @param int $room_id
 * @return array
 */
function delete_room($pdo, $room_id){
    /* Get series info */
    $owner_id = get_roomowner_id($pdo, $room_id);

    /* Check if current user is allowed to remove this room */
    if (get_user_id() != $owner_id){
        return [
            'type' => 'danger',
            'message' => 'You are not authorized to delete this room.'
        ];
    }

    /* Delete room */
    $stmt = $pdo->prepare("DELETE FROM rooms WHERE room_id = ?");
    $stmt->execute([$room_id]);
    $deleted = $stmt->rowCount();
    if ($deleted ==  1) {
        return [
            'type' => 'success',
            'message' => 'Your room was successfully deleted!'
        ];
    }
    else {
        return [
            'type' => 'warning',
            'message' => 'An error occurred. Your room was not deleted.'
        ];
    }
}

/**
 * Funciton to edit room
 * @param PDO $pdo Database object
 * @param int $room_id
 * @return array
 */
function edit_room($pdo, $room_info){
    $original_room_info = get_room_info($pdo, $room_info['room_id']);
    $owner_id = $original_room_info['owner_id'];

    /* Check if current user is allowed to edit this room */
    if (get_user_id() != $owner_id){
        return [
            'type' => 'danger',
            'message' => 'You are not authorized to edit this room.'
        ];
    }

    /* Check if all fields are set */
    if (
        empty($room_info['room_name']) or
        empty($room_info['price']) or
        empty($room_info['type']) or
        empty($room_info['size']) or
        empty($room_info['city']) or
        empty($room_info['postcode']) or
        empty($room_info['street']) or
        empty($room_info['house_nr']) or
        empty($room_info['description'])
        ) {
        return [
            'type' => 'danger',
            'message' => 'There was an error. Not all fields were filled in.'
        ];
    }

    /* Check if room name already exists */
    $stmt = $pdo->prepare('SELECT * FROM rooms WHERE room_name = ? AND room_id != ?');
    $stmt->execute([$room_info['room_name'], $room_info['room_id']]);
    $rooms = $stmt->rowCount();
    if ($rooms){
        return [
            'type' => 'danger',
            'message' => 'There is a room with this name already. Please choose another one.'
        ];
    }

    /* Add Room */
    $stmt = $pdo->prepare("UPDATE rooms SET room_name = ?, price = ?, type = ?, size = ?, city = ?, postcode = ?, street = ?, house_nr = ?, description = ?, owner_id = ? WHERE room_id = ?");
    $stmt->execute([
        $room_info['room_name'],
        $room_info['price'],
        $room_info['type'],
        $room_info['size'],
        $room_info['city'],
        $room_info['postcode'],
        $room_info['street'],
        $room_info['house_nr'],
        $room_info['description'],
        get_user_id(),
        $room_info['room_id']
    ]);
    $edited = $stmt->rowCount();
    if ($edited ==  1) {
        return [
            'type' => 'success',
            'message' => sprintf("Your room '%s' was successfully edited!", $room_info['room_name'])
        ];
    }
    else {
        return [
            'type' => 'danger',
            'message' => 'There was an error, your room was not edited. Please try again.'
        ];
    }
}


/**
 * Get the owner_id that belongs to a given room_id from the database
 * @param PDO $pdo Database object
 * @param $room_id
 * @return int owner_id
 */
function get_roomowner_id($pdo, $room_id){
    $stmt = $pdo->prepare('SELECT owner_id FROM rooms WHERE room_id = ?');
    $stmt->execute([$room_id]);
    $owner_info = $stmt->fetch();
 
    return $owner_info['owner_id'];
}

/**
 * Generates an array with room information
 * @param PDO $pdo Database object
 * @param int $room_id ID of the room
 * @return mixed
 */
function get_room_info($pdo, $room_id){
    $stmt = $pdo->prepare('SELECT * FROM rooms WHERE room_id = ?');
    $stmt->execute([$room_id]);
    $room_info = $stmt->fetch();
    $room_info_exp = Array();

    /* Create array with htmlspecialchars */
    foreach ($room_info as $key => $value){
        $room_info_exp[$key] = htmlspecialchars($value);
    }
    return $room_info_exp;
}

/**
 * Get array with all listed rooms from the database
 * Contains all information from the rooms table
 * When user role = owner, the nr of optins per room is also given
 * @param PDO $pdo Database object
 * @return array Associative array with all rooms
 */
function get_rooms($pdo){
    $user_role = get_user_role();
    $user_id = get_user_id();
    if ($user_role == 1) {
        $stmt = $pdo->prepare('SELECT rooms.room_id, room_name, price, type, size, city, postcode, street, house_nr, description, owner_id, COUNT(tenant_id) AS optin_count FROM rooms LEFT JOIN `opt-ins` ON rooms.room_id = `opt-ins`.room_id WHERE owner_id = ? GROUP BY rooms.room_id');
        $stmt->execute([$user_id]);
    } else {
        $stmt = $pdo->prepare('SELECT * FROM rooms');
        $stmt->execute();
    }
    $rooms = $stmt->fetchAll();
    $rooms_exp = Array();

    /* Create array with htmlspecialchars */
    foreach ($rooms as $key => $value){
        foreach ($value as $user_key => $user_input) {
            $rooms_exp[$key][$user_key] = htmlspecialchars($user_input);
        }
    }
    return $rooms_exp;
}

/**
 * Creates a Bootstrap table with a list of rooms
 * @param PDO $pdo Database object
 * @return string
 */
function get_rooms_table($pdo){
    $user_id = get_user_id();
    $user_role = get_user_role();
    $rooms = get_rooms($pdo);
    $table_exp = '
    <table class="table table-hover">
    <thead class="thead-dark"
    <tr>
        <th scope="col">Room name</th>
        <th scope="col">City</th>
        <th scope="col">Street</th>
        <th scope="col">Type</th>
        <th scope="col">Size</th>
        <th scope="col">Price</th>';
    if ($user_role == 1) {
        $table_exp .= '
        <th scope="col">Nr of opt-ins</th>
        <th scope="col"></th>
        <th scope="col"></th>';
    }
    $table_exp .= '
    <th scope="col"></th>
    </tr>
    </thead>
    <tbody>';
    foreach($rooms as $key => $value){
        $table_exp .= '
        <tr>
            <th scope="row">'.$value['room_name'].'</th>
            <td scope="row">'.$value['city'].'</td>
            <td scope="row">'.$value['street'].'</td>
            <td scope="row">'.$value['type'].'</td>
            <td scope="row">'.$value['size'].' m<sup>2</sup></td>
            <td scope="row">&euro;'.$value['price'].'</td>';
        if ($user_role == 1) {
        $table_exp .= '
        <td scope="row">'.$value['optin_count'].'</td>
        <td><a href="/final_project_ddwt21/rooms/edit/?room_id='.$value['room_id'].'" role="button" class="btn btn-secondary btn-sm">Edit</a></td>
        <td><form action="/final_project_ddwt21/rooms/delete/" method="POST">
        <input type="hidden" value="'.$value['room_id'].'" name="room_id">
        <button type="submit" class="btn btn-danger btn-sm">Delete</button></form></td>';
        }
        $table_exp .= '
        <td><a href="/final_project_ddwt21/room/'.$value['room_id'].'" role="button" class="btn btn-secondary btn-sm">More information</a></td>
        </tr>
        ';
    }
    $table_exp .= '
    </tbody>
    </table>
    ';
    return $table_exp;
}

/**
 * Add room to the database
 * @param PDO $pdo Database object
 * @param int $room_id
 * @return array Associative array with key type and message
 */
function add_optin($pdo, $room_id){
    $tenant_id = get_user_id();
    /* Check if current user is allowed to add an optin */
    if (get_user_role() != 2){
        return [
            'type' => 'danger',
            'message' => json_encode(get_user_role())
        ];
    }

    /* Check if optin already exists */
    $stmt = $pdo->prepare('SELECT * FROM `opt-ins` WHERE room_id = ? AND tenant_id = ?');
    $stmt->execute([$room_id, $tenant_id]);
    $rooms = $stmt->rowCount();
    if ($rooms){
        return [
            'type' => 'danger',
            'message' => 'You already have an opt-in for this room.'
        ];
    }

    /* Add Room */
    $stmt = $pdo->prepare("INSERT INTO `opt-ins` (tenant_id, room_id) VALUES (?, ?)");
    $stmt->execute([
        $tenant_id,
        $room_id
    ]);
    $inserted = $stmt->rowCount();
    if ($inserted ==  1) {
        return [
            'type' => 'success',
            'message' => sprintf("Your opt-in was successfully registered!")
        ];
    }
    else {
        return [
            'type' => 'danger',
            'message' => 'There was an error, your opt-in was not registered. Please try again.'
        ];
    }
}

/**
 * Get array with all listed optins for the current user from the database
 * @param PDO $pdo Database object
 * @return array Associative array with all optins for the current user
 */
function get_optins_user($pdo, $current_user){
    $stmt = $pdo->prepare('SELECT `opt-in_id`, tenant_id, `opt-ins`.room_id, room_name FROM `opt-ins`, rooms WHERE rooms.room_id = `opt-ins`.room_id AND tenant_id = ?');
    $stmt->execute([$current_user]);
    $optins = $stmt->fetchAll();

    return $optins;
}

/**
 * Get array with all listed optins for a specific room from the database
 * Also included is the name of the tenant
 * @param PDO $pdo Database object
 * @return array Associative array with all optins for that room
 */
function get_optins_room($pdo, $room_id){
    $stmt = $pdo->prepare('SELECT `opt-in_id`, tenant_id, `opt-ins`.room_id, CONCAT(firstname, " ", lastname) AS username FROM `opt-ins`, users WHERE users.user_id = `opt-ins`.tenant_id AND room_id = ?');
    $stmt->execute([$room_id]);
    $optins = $stmt->fetchAll();
    $optins_exp = Array();

    /* Create array with htmlspecialchars */
    foreach ($optins as $key => $value){
        foreach ($value as $inner_key => $inner_value) {
            $optins_exp[$key][$inner_key] = htmlspecialchars($inner_value);
        }
    }
    return $optins_exp;
}

/**
 * Creates a Bootstrap table with a list of optins for the current user
 * @param PDO $pdo Database object
 * @return string
 */
function get_optins_per_room_table($pdo, $room_id){
    $optins = get_optins_room($pdo, $room_id);
    $table_exp = '
        <table class="table table-hover">
            <thead class="thead-light">
                <tr>
                    <th scope="col" colspan="2">Opt-ins</th>
                </tr>
            </thead>
            <tbody>';
    foreach($optins as $key => $value){
        $table_exp .= '
        <tr>
            <th>'.$value['username'].'</th>
            <td><a href="/final_project_ddwt21/room/'.$room_id.'/?message='.$value['tenant_id'].'" role="button" class="btn btn-secondary">Send message</a></td>
        ';
    }
    $table_exp .= '
    </tbody>
    </table>
    ';
    return $table_exp;
}

/**
 * Get the tenant_id that belongs to a given optin_id from the database
 * @param PDO $pdo Database object
 * @param $optin_id
 * @return int tenant_id
 */
function get_optin_tenant_id($pdo, $optin_id){
    $stmt = $pdo->prepare('SELECT tenant_id FROM `opt-ins` WHERE `opt-in_id` = ?');
    $stmt->execute([$optin_id]);
    $tenant_info = $stmt->fetch();
 
    return $tenant_info['tenant_id'];
}


/**
 * Creates a Bootstrap table with a list of optins for the current user
 * @param PDO $pdo Database object
 * @return string
 */
function get_optins_table($pdo){
    $current_user = get_user_id();
    $optins = get_optins_user($pdo, $current_user);
    $table_exp = '
    <table class="table table-hover">
    <thead
    <tr>
        <th scope="col">Room name</th>
        <th scope="col"></th>
        <th scope="col"></th>
    </tr>
    </thead>
    <tbody>';
    foreach($optins as $key => $value){
        $table_exp .= '
        <tr>
            <th scope="row">'.$value['room_name'].'</th>
            <td><a href="/final_project_ddwt21/room/'.$value['room_id'].'" role="button" class="btn btn-primary">View room</a></td>
            <td><form action="/final_project_ddwt21/optins/delete/" method="POST">
            <input type="hidden" value="'.$value['opt-in_id'].'" name="optin_id">
            <button type="submit" class="btn btn-danger">Cancel</button></form></td>
        </tr>
        ';
    }
    $table_exp .= '
    </tbody>
    </table>
    ';
    return $table_exp;
}

/**
 * Removes an optin based on optin ID
 * @param PDO $pdo Database object
 * @param int $optin_in
 * @return array
 */
function cancel_optin($pdo, $optin_id){
    /* Get optin info */
    $optin_tenant_id = get_optin_tenant_id($pdo, $optin_id);

    /* Check if current user is allowed to cancel this opt-in */
    if (get_user_id() != $optin_tenant_id){
        return [
            'type' => 'danger',
            'message' => 'You are not authorized to cancel this opt-in.'
        ];
    }

    /* Delete optin */
    $stmt = $pdo->prepare("DELETE FROM `opt-ins` WHERE `opt-in_id` = ?");
    $stmt->execute([$optin_id]);
    $deleted = $stmt->rowCount();
    if ($deleted ==  1) {
        return [
            'type' => 'success',
            'message' => 'Your opt-in was successfully cancelled!'
        ];
    }
    else {
        return [
            'type' => 'warning',
            'message' => 'An error occurred. Your opt-in was not cancelled.'
        ];
    }
}

/**
 * Get full name of user
 * @param user_id id from users table from database
 * @param PDO $pdo Database object
 * @return string Full name of user based on user id
 */
function get_user_fullname($pdo, $user_id){
    $stmt = $pdo->prepare('SELECT CONCAT(firstname, " ", lastname) AS fullname FROM users WHERE user_id = ?');
    $stmt->execute([$user_id]);
    $user_info = $stmt->fetch();
    return htmlspecialchars($user_info['fullname']);
}

/**
 * Get array with messages between two specified users from the database
 * @param PDO $pdo Database object
 * @param user1 id from users table from database
 * @param user2 id from users table from database
 * @return array Associative array with the messages
 */
function get_messages($pdo, $user1, $user2){
    $stmt = $pdo->prepare('SELECT receiver_id, sender_id, datetime, DATE_FORMAT(datetime, "%d-%m-%Y %H:%i") AS datetime_formatted, content FROM messages WHERE (sender_id = ? AND receiver_id = ?) OR (sender_id = ? AND receiver_id = ?) ORDER BY datetime desc');
    $stmt->execute([$user1, $user2, $user2, $user1]);
    $messages = $stmt->fetchAll();
    $messages_exp = Array();

    /* Create array with htmlspecialchars */
    foreach ($messages as $key => $value){
        foreach ($value as $inner_key => $inner_value) {
            $messages_exp[$key][$inner_key] = htmlspecialchars($inner_value);
        }
    }
    return $messages_exp;
}

/**
 * Creates DIVs with messages
 * @param PDO $pdo Database object
 * @param user1 id from users table from database
 * @param user2 id from users table from database
 * @return string
 */
function get_messages_divs($pdo, $user1, $user2){
    $messages = get_messages($pdo, $user1, $user2);
    $user2_name = get_user_fullname($pdo, $user2);
    $message_divs = '<div class="col-md-9"><div class="border col-md-12 overflow-auto d-flex flex-column-reverse" style="height:50vh;">';
    foreach($messages as $key => $value){
        if ($value['sender_id'] == $user1) {
            $message_divs .= '
            <div class="row">
                <div class="ml-auto col-md-8 border rounded bg-info my-2 py-1">
                <div class="d-flex">
                    <div class="font-weight-light">You</div>
                    <div class="ml-auto font-weight-light small">'.$value['datetime_formatted'].'</div>
                </div>
                    <div class="text-left">'.$value['content'].'</div>
                </div>
            </div>';
        } else {
            $message_divs .= '
            <div class="row">
                <div class="mr-auto col-md-8 border rounded bg-light my-2 py-1">
                <div class="d-flex">
                    <div class="font-weight-light">'.$user2_name.'</div>
                    <div class="ml-auto font-weight-light small">'.$value['datetime_formatted'].'</div>
                </div>
                    <div class="text-left">'.$value['content'].'</div>
                </div>
            </div>';
        }
    }
    $message_divs .= '</div>
    <form action="/final_project_ddwt21/messages/" method="POST">
    <div class="form-group">
        <textarea class="form-control" rows="3" id="content" name="content" placeholder="Type your reply here..."></textarea>
    </div>
    <input type="hidden" id="receiver_id" name="receiver_id" value='.$user2.'>
    <input type="submit" value="Send reply" class="btn btn-primary">
    </form></div>';

    return $message_divs;
}


/**
 * Get text of last message between two specified users from the database
 * @param PDO $pdo Database object
 * @param user1 id from users table from database
 * @param user2 id from users table from database
 * @return string with last message
 */
function get_last_message_text($pdo, $user1, $user2){
    $stmt = $pdo->prepare('SELECT content FROM messages WHERE (sender_id = ? AND receiver_id = ?) OR (sender_id = ? AND receiver_id = ?) ORDER BY datetime desc LIMIT 1');
    $stmt->execute([$user1, $user2, $user2, $user1]);
    $last_message = $stmt->fetch();
    return htmlspecialchars($last_message['content']);
}

/**
 * Get ids of users with whom the logged in person has conversations
 * @param user1 id from users table from database
 * @param PDO $pdo Database object
 * @return associative arrays with user id, full name and last message of conversation
 */
function get_conversation_overview($pdo, $user1){
    $stmt = $pdo->prepare('SELECT sender_id FROM messages WHERE receiver_id = ? UNION SELECT receiver_id FROM messages WHERE sender_id = ?;');
    $stmt->execute([$user1, $user1]);
    $conversation_partners = $stmt->fetchAll();

    $conversation_overview = Array();

    /* Create array with htmlspecialchars */
    foreach ($conversation_partners as $key => $value){
        foreach ($value as $innerkey => $user2) {
            $new_conversation = array (
                'partner_user_id' => $user2,
                'full_name' => htmlspecialchars(get_user_fullname($pdo, $user2)),
                'last_message' => htmlspecialchars(get_last_message_text($pdo, $user1, $user2))
            );
            $conversation_overview[] = $new_conversation;
        }
    }
    return $conversation_overview;
}

/**
 * Creates DIVs with conversations
 * @param PDO $pdo Database object
 * @param user1 id from users table from database
 * @return string
 */
function get_conversation_overview_divs($pdo, $user1){
    $conversation_overview = get_conversation_overview($pdo, $user1);
    $conversation_divs = '<div class="col-md-3">';
    if (empty($conversation_overview)) {
        $conversation_divs .= '
        <div class="col-md-12 border">You do not have any conversations yet.</div>';
    } else {
        foreach ($conversation_overview as $key => $value){
            $conversation_divs .= '
            <div class="col-md-12 border">
                <a href="/final_project_ddwt21/messages/?chat_id='.$value['partner_user_id'].'" class="stretched-link">
                    <div class="font-weight-bold">'.$value['full_name'].'</div>
                </a>
                <div class="font-weight-light no-overflow">'.$value['last_message'].'</div>
            </div>';
        }
    }
    $conversation_divs .= '</div>';
    return $conversation_divs;
}

/**
 * Add message to the database
 * @param PDO $pdo Database object
 * @param array $message_info Associative array with message info
 * @return array Associative array with key type and message
 */
function send_message($pdo, $message_info){
    /* Check if content field is set */
    if (
        empty($message_info['content'])
    ) {
        return [
            'type' => 'danger',
            'message' => 'Please type in a message.'
        ];
    }

    /* Add message to database */
    $stmt = $pdo->prepare("INSERT INTO messages (content, sender_id, receiver_id) VALUES (?, ?, ?)");
    $stmt->execute([
        $message_info['content'],
        get_user_id(),
        $message_info['receiver_id']
    ]);
    $inserted = $stmt->rowCount();
    if ($inserted ==  1) {
        return [
            'type' => 'success',
            'message' => 'Your message was sent successfully.'
        ];
    }
    else {
        return [
            'type' => 'danger',
            'message' => 'There was an error. Your message was not sent. Try it again.'
        ];
    }
}

/**
 * This function takes form data input and puts it into the database
 * @param PDO $pdo Database object
 * @param $form_data User info for registration
 * return string response
 */
function register_user($pdo, $form_data){
    if (
        empty($form_data['username']) or
        empty($form_data['password']) or
        empty($form_data['firstname']) or
        empty($form_data['lastname']) or
        empty($form_data['birth_date']) or
        empty($form_data['biography']) or
        empty($form_data['profession']) or
        empty($form_data['language']) or
        empty($form_data['email']) or
        empty($form_data['phone_nr'])

    ){return [
        'type' => 'danger',
        'message' => 'Please fill in all forms'
    ]; }
    /* Check if user already exists */
    try {
        $stmt = $pdo->prepare('SELECT * FROM users WHERE username = ?');
        $stmt->execute([$form_data['username']]);
        $user_exists = $stmt->rowCount();
    } catch (\PDOException $error) {
        return [
            'type' => 'danger',
            'message' => sprintf('There was an error: %s', $error->getMessage())
        ];
    }
    /* Return error message for existing username */
    if ( !empty($user_exists) ) {
        return [
            'type' => 'danger',
            'message' => 'The username you entered already exists!'
        ];
    }

    /* Hash password */
    $password = password_hash($form_data['password'], PASSWORD_DEFAULT);
    var_dump($form_data);

    /* Save user to the database */
    try {
        $stmt = $pdo->prepare('INSERT into users (username, password, role, firstname, lastname, birth_date, biography, profession, language, email, phone_nr) VALUES (?,?,?,?,?,?,?,?,?,?,?)');
        $stmt ->execute([$form_data['username'],$password,
            $form_data['account_type_id'],
            $form_data['firstname'],$form_data['lastname'],
            $form_data['birth_date'],
            $form_data['biography'],$form_data['profession'],$form_data['language'],
            $form_data['email'],$form_data['phone_nr']]);
        $user_id = $pdo->lastInsertId();
    } catch (PDOException $error_msg) {
        return [
            'type' => 'danger',
            'message' => sprintf('There was an error: %s', $error_msg->getMessage())
        ];
    }

    /* Login user */
    session_start();
    $_SESSION['user_id'] = $user_id;
    $_SESSION['user_role'] = $form_data['account_type_id'];
    return [
        'type' => 'success',
        'message' => sprintf('%s, your account was successfully
    created!', get_user_fullname($pdo, $_SESSION['user_id']))
    ];
}

/* Function to login the user */
function login_user($pdo, $form_data)
{
    /* Check if all fields are set */
    if (
        empty($form_data['username']) or
        empty($form_data['password'])
    ) {
        return [
            'type' => 'danger',
            'message' => 'You should enter a username and password.'
        ];
    }

    /* Check if user exists */
    try {
        $stmt = $pdo->prepare('SELECT * FROM users WHERE username = ?');
        $stmt->execute([$form_data['username']]);
        $user_info = $stmt->fetch();
    } catch (\PDOException $e) {
        return [
            'type' => 'danger',
            'message' => sprintf('There was an error: %s', $e->getMessage())
        ];
    }
    /* Return error message for wrong username */
    if (empty($user_info)) {
        return [
            'type' => 'danger',
            'message' => 'The username you entered does not exist!'
        ];
    }

    /* Return error message for wrong password */
    if (!password_verify($form_data['password'], $user_info['password'])) {
        return [
            'type' => 'danger',
            'message' => 'The password you entered is incorrect!'
        ];
    } else {
        session_start();
        $_SESSION['user_id'] = $user_info['user_id'];
        $_SESSION['user_role'] = $user_info['role'];
        return [
            'type' => 'success',
            'message' => sprintf('You were logged in successfully!')
        ];
    }
}

/* Checks if logged in */
function check_login(){
    session_start();
    if (isset($_SESSION['user_id'])){
        return True;
    } else {
        return False;
    }
}

/* This function logs you out */
function logout(){
    session_start();
    session_destroy();
    return [
        'type' => 'success',
        'message' => 'You were logged out successfully!'
    ];
}

/**
 * Changes the HTTP Header to a given location
 * @param string $location Location to redirect to
 */
function redirect($location){
    header(sprintf('Location: %s', $location));
    die();
}


/* Get user name */
function get_username($pdo, $series_id){
    $stmt = $pdo->prepare('SELECT username FROM users WHERE user_id = ?');
    $stmt->execute([$series_id]);
    $username = $stmt->fetch();
    $user_name = implode( " ", $username );

    return $user_name;
}


/**
 * Creates HTML alert code with information about the success or failure
 * @param array $feedback Array with keys 'type' and 'message'.
 * @return string
 */
function get_error($feedback){
    $feedback = json_decode($feedback, True);
    $error_exp = '
       <div class="alert alert-'.$feedback['type'].'" role="alert">
           '.$feedback['message'].'
       </div>';

    return $error_exp;
}


/**
 * Check if logged in user is owner
 * @param PDO $pdo  PDO Object
 * @return bool
 */
function is_owner($pdo)
{
    $stmt = $pdo->prepare('SELECT role FROM users  WHERE user_id = ?');
    $stmt->execute([get_user_id()]);
    /* Create array with htmlspecialchars */
    $role = $stmt->fetch();
    if ($role == 1) {
        return true;
    } else {
        return false;
    }
}

/**
 * Check if logged in user is owner
 * @param PDO $pdo  PDO Object
 * @return bool
 */
function is_tenant($pdo)
{
    $stmt = $pdo->prepare('SELECT role FROM users  WHERE user_id = ?');
    $stmt->execute([get_user_id()]);
    /* Create array with htmlspecialchars */
    $role = $stmt->fetch();
    if ($role == 2) {
        return true;
    } else {
        return false;
    }
}

function check_role($pdo){
    $stmt = $pdo->prepare('SELECT role FROM users  WHERE user_id = ?');
    $stmt->execute([get_user_id()]);
    /* Create array with htmlspecialchars */
    $role = $stmt->fetch();
    return $role;
}

/**
 * Get current user id
 * @return bool current user id or False if not logged in
 */
function get_user_id(){
    if(!isset($_SESSION)) { 
        session_start(); 
    }
    if (isset($_SESSION['user_id'])){
        return $_SESSION['user_id'];
    } else {
        return False;
    }
}

/**
 * Get current user id
 * @return bool current user id or False if not logged in
 */
function get_user_role(){
    if(!isset($_SESSION)) { 
        session_start(); 
    }
    if (isset($_SESSION['user_role'])){
        return $_SESSION['user_role'];
    } else {
        return False;
    }
}