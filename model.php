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
    $navigation_exp = '
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
    <a class="navbar-brand">Find a room</a>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
    <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarSupportedContent">
    <ul class="navbar-nav mr-auto">';
    foreach ($template as $id => $info) {
        if ($id == $active_id){
            $navigation_exp .= '<li class="nav-item active">';
            $navigation_exp .= '<a class="nav-link" href="'.$info['url'].'">'.$info['name'].'</a>';
        }else{
            $navigation_exp .= '<li class="nav-item">';
            $navigation_exp .= '<a class="nav-link" href="'.$info['url'].'">'.$info['name'].'</a>';
        }

        $navigation_exp .= '</li>';
    }
    $navigation_exp .= '
    </ul>
    </div>
    </nav>';
    return $navigation_exp;
}

/**
 * Get array with all listed rooms from the database
 * @param PDO $pdo Database object
 * @return array Associative array with all rooms
 */
function get_rooms($pdo){
    $stmt = $pdo->prepare('SELECT * FROM rooms');
    $stmt->execute();
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
    $rooms = get_rooms($pdo);
    $table_exp = '
    <table class="table table-hover">
    <thead
    <tr>
        <th scope="col">Room name</th>
        <th scope="col">Type</th>
        <th scope="col">Price</th>
        <th scope="col"></th>
    </tr>
    </thead>
    <tbody>';
    foreach($rooms as $key => $value){
        $table_exp .= '
        <tr>
            <th scope="row">'.$value['room_name'].'</th>
            <td scope="row">'.$value['type'].'</td>
            <td scope="row">'.$value['price'].'</td>
            <td><a href="/final_project_ddwt21/room/?room_id='.$value['room_id'].'" role="button" class="btn btn-primary">More info</a></td>
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
                <div class="ml-auto col-md-6 border rounded bg-light my-2 py-1">
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
                <div class="mr-auto col-md-6 border rounded bg-light my-2 py-1">
                <div class="d-flex">
                    <div class="font-weight-light">'.$user2_name.'</div>
                    <div class="ml-auto font-weight-light small">'.$value['datetime_formatted'].'</div>
                </div>
                    <div class="text-left">'.$value['content'].'</div>
                </div>
            </div>';
        }
    }
    $message_divs .= '</div><div class="col-md-12 border" style="height:10vh;">Reply (add form in model.php)</div></div>';

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