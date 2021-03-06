<?php

/* Require composer autoloader */
require __DIR__ . '/vendor/autoload.php';

/* Get model.php */
include 'model.php';

/* Connect to DB */
$db = connect_db('localhost', 'final_project', 'ddwt21','ddwt21');

/* Create Router instance */
$router = new \Bramus\Router\Router();

/** Navigation template
 * name = page title
 * url = page url
 * login = 'always' means it is always shown, 'yes' and 'no' mean you should or shouldnt be logged in
 * role = user role: 1, 2, or 'everyone' (no role specified)
 * align = 'left' or 'right'
 * */ 
$navigation_template = Array(
    1 => Array(
        'name' => 'Home',
        'url' => '/final_project_ddwt21/',
        'login' => 'always',
        'role' => 'everyone',
        'align' => 'left'
    ),
    2 => Array(
        'name' => 'Overview',
        'url' => '/final_project_ddwt21/overview/',
        'login' => 'yes',
        'role' => 'everyone',
        'align' => 'left'
    ),
    3 => Array(
        'name' => 'Register',
        'url'   => '/final_project_ddwt21/register/',
        'login' => 'no',
        'role' => 'everyone',
        'align' => 'right'
    ),
    4 => Array(
        'name' => 'Messages',
        'url'   => '/final_project_ddwt21/messages/',
        'login' => 'yes',
        'role' => 'everyone',
        'align' => 'right'
    ),
    5 => Array(
        'name' => 'Login',
        'url'   => '/final_project_ddwt21/login/',
        'login' => 'no',
        'role' => 'everyone',
        'align' => 'right'
    ),
    6 => Array(
        'name' => 'My Profile',
        'url'   => '/final_project_ddwt21/user/'.get_user_id(),
        'login' => 'yes',
        'role' => 'everyone',
        'align' => 'right'
    ),
    7 => Array(
        'name' => 'Logout',
        'url'   => '/final_project_ddwt21/logout/',
        'login' => 'yes',
        'role' => 'everyone',
        'align' => 'right'
    ),
    8 => Array(
        'name' => 'Add Room',
        'url'   => '/final_project_ddwt21/addroom/',
        'login' => 'yes',
        'role' => 1,
        'align' => 'left'
    ),
    9 => Array(
        'name' => 'Opt-ins',
        'url'   => '/final_project_ddwt21/optins/',
        'login' => 'yes',
        'role' => 2,
        'align' => 'left'
    ),
);


/* Landing page */
$router->get('/', function() use($navigation_template, $db){
    /* Page info */
    $page_title = 'Roomnet';
    $breadcrumbs = get_breadcrumbs([
        'Home' => na('/final_project_ddwt21/', True)
    ]);
    $unread_messages = unread_count($db);
    $navigation = get_navigation($navigation_template, 1, $unread_messages);

    /* Page content */
    $page_subtitle = 'The place where you can kick off your student life or upgrade your room!';

    /* Get error msg from POST route */
    if ( isset($_GET['error_msg']) ) { $error_msg = get_error($_GET['error_msg']); }

    /* Include Template */
    include use_template('main');
});

/* GET overview */
$router->get('/overview/', function() use($navigation_template, $db){
    /* Page info */
    $page_title = 'Overview';
    $breadcrumbs = get_breadcrumbs([
        'Home' => na('/final_project_ddwt21/', False),
        'Overview' => na('/final_project_ddwt21/overview/', True)
    ]);
    $unread_messages = unread_count($db);
    $navigation = get_navigation($navigation_template, 2, $unread_messages);

    /* Page content */
    $page_subtitle = 'Overview of rooms';
    $page_content = 'An overview of the rooms. For more information click on More Info';
    $left_content = get_rooms_table($db);

    /* Get error msg from POST route */
    if (isset($_GET['error_msg'])) { $error_msg = get_error($_GET['error_msg']); }

    /* Choose Template */
    include use_template('overview');
});

/* GET register */
$router->get('/register/', function() use($navigation_template, $db){
    /* Page info */
    $page_title = 'Register';
    $note = 'Note: To offer rooms you need to register as Owner, to find rooms you need to register as Tenant. You cannot change this after you create your account.';
    $breadcrumbs = get_breadcrumbs([
        'Home' => na('/final_project_ddwt21/', False),
        'Register' => na('/final_project_ddwt21/register/', True)
    ]);
    $unread_messages = unread_count($db);
    $navigation = get_navigation($navigation_template, 3, $unread_messages);

    /* Get error msg from POST route */
    if (isset($_GET['error_msg'])) { $error_msg = get_error($_GET['error_msg']); }

    /* Choose Template */
    $form_action = '/final_project_ddwt21/register/';
    include use_template('register');
});

/* POST register */
$router->post('/register/', function() use($db){
    /* Register user */
    $feedback = register_user($db, $_POST);
   
    if($feedback['type'] == 'danger') {
        /* Redirect to register form */
        redirect(sprintf('/final_project_ddwt21/register/?error_msg=%s',
                 json_encode($feedback)));
    } else {
        /* Redirect to landing page */
        redirect(sprintf('/final_project_ddwt21/?error_msg=%s',
                 json_encode($feedback)));
    }
});

/* POST login */
$router->post('/login/', function() use($db){
    /* Login user */
    $feedback = login_user($db, $_POST);
    if($feedback['type'] == 'danger') {
        /* Redirect to login screen */
        redirect(sprintf('/final_project_ddwt21/login/?error_msg=%s',
                 json_encode($feedback)));
    } else {
        /* Redirect to home page */
        redirect(sprintf('/final_project_ddwt21/?error_msg=%s',
                 json_encode($feedback)));
    }
});

/* GET messages */
$router->get('/messages/', function() use($navigation_template, $db){
    if (!check_login() ) {
        redirect('/final_project_ddwt21/login/');
    }
    /* Page info */
    $page_title = 'Messages';
    $breadcrumbs = get_breadcrumbs([
        'Home' => na('/final_project_ddwt21/', False),
        'Messages' => na('/final_project_ddwt21/messages/', True)
    ]);

    if(check_login()){
        /* Page content */
        $current_user = $_SESSION['user_id'];
        if (isset($_GET['chat_id'])){
            $chat_id = $_GET['chat_id'];
        } else {
            $chat_id = 0;
        }
        $conversation_overview = get_conversation_overview_divs($db, $current_user, $chat_id);
        if ($chat_id){
            read_message($db, $chat_id);
            $chat = get_messages_divs($db, $current_user, $chat_id);
        }
    }
    $unread_messages = unread_count($db);
    $navigation = get_navigation($navigation_template, 4, $unread_messages);

        /* Get error msg from POST route */
        if (isset($_GET['error_msg'])) { $error_msg = get_error($_GET['error_msg']); }

        /* Choose Template */
        include use_template('messages');
});

/* POST messages */
$router->post('/messages/', function() use($db){
    /* Check if logged in */
    if ( !check_login() ) {
        redirect('/final_project_ddwt21/login/');
    }
    
    /* Add message to database */
    $feedback = send_message($db, $_POST);

    /* Redirect to messages if already on a conversation page, else show feedback on room or user page */
    if(isset($_POST['room_page'])){
        redirect(sprintf('/final_project_ddwt21/room/%s/?error_msg=%s',
                $_POST['room_page'], json_encode($feedback)));
    }
    if(isset($_POST['user_page'])){
        redirect(sprintf('/final_project_ddwt21/user/%s/?error_msg=%s',
                $_POST['user_page'], json_encode($feedback)));
    }
    redirect(sprintf('/final_project_ddwt21/messages/?chat_id=%s&error_msg=%s',
                $_POST['receiver_id'], json_encode($feedback)));
});

/* GET optins for tenants */
$router->get('/optins/', function() use($navigation_template, $db){
    /* Check if logged in */
    if (!check_login() ) {
        redirect('/final_project_ddwt21/login/');
    }
    /* Check if user is a tenant */
    if (get_user_role() != 2) {
        $feedback = [
            'type' => 'danger',
            'message' => sprintf('You have to be logged in as a tenant to view that page.')
        ];
        redirect(sprintf('/final_project_ddwt21/?error_msg=%s',
                json_encode($feedback)));
    }

    /* Page info */
    $page_title = 'Opt-ins';
    $breadcrumbs = get_breadcrumbs([
        'Home' => na('/final_project_ddwt21/', False),
        'Messages' => na('/final_project_ddwt21/optins/', True)
    ]);
    $unread_messages = unread_count($db);
    $navigation = get_navigation($navigation_template, 9, $unread_messages);

    /* Page content */
    $page_subtitle = 'Overview of opt-ins you initiated';
    $page_content = 'Click on Cancel to cancel your opt-in. Or view the room information by clicking on View room.';
    $main_content = get_optins_table($db);

    /* Get error msg from POST route */
    if (isset($_GET['error_msg'])) { $error_msg = get_error($_GET['error_msg']); }

    /* Choose Template */
    include use_template('optins');
});

/* POST add optin */
$router->post('/optins/add', function() use($db){
    /* Login user */
    $feedback = add_optin($db, $_POST['room_id']);
    
    redirect(sprintf('/final_project_ddwt21/optins/?error_msg=%s',
                 json_encode($feedback)));

});

/* POST route to delete optins */
$router->post('/optins/delete/', function() use($db){
    /* Check if logged in */
    if ( !check_login() ) {
        redirect('/final_project_ddwt21/login/');
    }
    
    /* Add message to database */
    $optin_id = $_POST['optin_id'];
    $feedback = cancel_optin($db, $optin_id);

    /* Redirect to optin get route */
    redirect(sprintf('/final_project_ddwt21/optins/?error_msg=%s',
                json_encode($feedback)));
});


/* GET login */
$router->get('/login/', function() use($navigation_template, $db){
    /* Page info */
    $page_title = 'Login';
    $breadcrumbs = get_breadcrumbs([
        'Home' => na('/final_project_ddwt21/', False),
        'Overview' => na('/final_project_ddwt21/login/', True)
    ]);
    $unread_messages = unread_count($db);
    $navigation = get_navigation($navigation_template, 5, $unread_messages);

    /* Page content */
    $page_subtitle = 'Login here';
    $page_content = 'Log into your account here';

    /* Get error msg from POST route */
    if (isset($_GET['error_msg'])) { $error_msg = get_error($_GET['error_msg']); }

    /* Choose Template */
    include use_template('login');
});

/* GET logout*/
$router->get('/logout/', function() use($navigation_template, $db){
    /* Log out user */
    $feedback = logout();

    /* Redirect to landing page */
    redirect(sprintf('/final_project_ddwt21/?error_msg=%s',
                json_encode($feedback)));
});

/* GET user profile */
$router->get('/user/(\d+)', function($user_id) use($navigation_template, $db){
    if (!check_login()) {
        redirect('/final_project_ddwt21/login/');
    }
    $user_info = get_user_info($db, $user_id);
    $own_profile = (get_user_id() == $user_id);
    $current_user_id = get_user_id();
    $user_role = get_user_role();
    /* Check if user is allowed to view profile. Tenants can only see owners profiles and vice versa */
    if (($current_user_id != $user_id) and ($user_role == $user_info['role'])){
        $feedback = [
            'type' => 'danger',
            'message' => 'You are not allowed to view this profile'
        ];
        redirect(sprintf('/final_project_ddwt21/?error_msg=%s',
                json_encode($feedback)));
    }
    /* Page info */
    $page_title = 'Profile of '.$user_info['firstname'].' '.$user_info['lastname'];
    $breadcrumbs = get_breadcrumbs([
        'Home' => na('/final_project_ddwt21/', False),
        'Profile of '.$user_info['firstname'].' '.$user_info['lastname'] => na('/final_project_ddwt21/user/'.$user_id, True)
    ]);
    $unread_messages = unread_count($db);
    if ($own_profile) {
        $navigation = get_navigation($navigation_template, 6, $unread_messages);
    } else
        $navigation = get_navigation($navigation_template, 0, $unread_messages);

    /* Page content */
    if (isset($_GET['message'])) {
        $chat_id = $_GET['message'];
        $receiver_name = get_user_fullname($db, $chat_id);
    }

    /* Get error msg from POST route */
    if (isset($_GET['error_msg'])) { $error_msg = get_error($_GET['error_msg']); }

    /* Choose Template */
    include use_template('profile');
});

/* GET edit user profile */
$router->get('/user/edit/', function() use($navigation_template, $db){
    if (!check_login() ) {
        redirect('/final_project_ddwt21/login/');
    }
    /* Page info */
    $page_title = 'Edit profile information';
    $page_subtitle = 'Change your profile information here';
    $breadcrumbs = get_breadcrumbs([
        'Home' => na('/final_project_ddwt21/', False),
        'Edit profile information' => na('/final_project_ddwt21/user/edit/', True)
    ]);
    $unread_messages = unread_count($db);
    $navigation = get_navigation($navigation_template, 6, $unread_messages);

    /* Page content*/
    $form_action = '/final_project_ddwt21/user/edit/';
    $user_id = $_GET['user_id'];
    $user_info = get_user_info($db, $user_id);

    /* Get error msg from POST route */
    if (isset($_GET['error_msg'])) { $error_msg = get_error($_GET['error_msg']); }

    /* Choose Template */
    include use_template('register');
});

/* POST route to edit user information */
$router->post('/user/edit/', function() use($db){
    /* Check if logged in */
    if ( !check_login() ) {
        redirect('/final_project_ddwt21/login/');
    }
    
    /* Add new info to database */
    $user_info = $_POST;
    $feedback = edit_user($db, $user_info);

    /* Redirect to overview get route */
    redirect(sprintf('/final_project_ddwt21/user/%s/?error_msg=%s',
                $user_info['user_id'], json_encode($feedback)));
});

/* POST route to delete account */
$router->post('/user/delete/', function() use($db){
    /* Check if logged in */
    if ( !check_login() ) {
        redirect('/final_project_ddwt21/login/');
    }
    
    /* Add message to database */
    $user_id = $_POST['user_id'];
    $feedback = delete_user($db, $user_id);

    /* Redirect to landing page get route */
    redirect(sprintf('/final_project_ddwt21/?error_msg=%s',
                json_encode($feedback)));
});

/* GET single room */
$router->get('/room/(\d+)', function($room_id) use($navigation_template, $db){
    if (!check_login() ) {
        redirect('/final_project_ddwt21/login/');
    }
    $room_info = get_room_info($db, $room_id);
    $user_id = get_user_id();
    $user_role = get_user_role();
    /* Check if user is allowed to view room. Tenants can see all rooms, owners can only see their own rooms */
    if (($user_role == 1) and ($room_info['owner_id'] != $user_id)){
        $feedback = [
            'type' => 'danger',
            'message' => 'You are not allowed to view this room'
        ];
        redirect(sprintf('/final_project_ddwt21/overview/?error_msg=%s',
                json_encode($feedback)));
    }

    /* Page info */
    $page_title = $room_info['room_name'];
    $breadcrumbs = get_breadcrumbs([
        'Home' => na('/final_project_ddwt21/', False),
        $room_info['room_name'] => na('/final_project_ddwt21/rooms/'.$room_id, True)
    ]);
    $unread_messages = unread_count($db);
    $navigation = get_navigation($navigation_template, 0, $unread_messages);

    /* Page content */
    if (isset($_GET['message'])) {
        $chat_id = $_GET['message'];
        $receiver_name = get_user_fullname($db, $chat_id);
    }
    $user_role = get_user_role();
    $owner_name = get_user_fullname($db, $room_info['owner_id']);
    $page_content = 'Log into your account here';
    if ($user_role == 1) {
        $optins = get_optins_per_room_table($db, $room_id);
    }

    /* Get error msg from POST route */
    if (isset($_GET['error_msg'])) { $error_msg = get_error($_GET['error_msg']); }

    /* Choose Template */
    include use_template('room');
});

/* GET add room */
$router->get('/addroom/', function() use($navigation_template, $db){
    if (!check_login() ) {
        redirect('/final_project_ddwt21/login/');
    }
    /* Page info */
    $page_title = 'Add Room';
    $breadcrumbs = get_breadcrumbs([
        'Home' => na('/final_project_ddwt21/', False),
        'Add Room' => na('/final_project_ddwt21/addroom/', True)
    ]);
    $unread_messages = unread_count($db);
    $navigation = get_navigation($navigation_template, 8, $unread_messages);

    /* Get error msg from POST route */
    if (isset($_GET['error_msg'])) { $error_msg = get_error($_GET['error_msg']); }

    /* Choose Template */
    $form_action = '/final_project_ddwt21/addroom/';
    include use_template('newroom');
});

/* POST route add room */
$router->post('/addroom/', function() use($db){
    /* Check if logged in */
    if ( !check_login() ) {
        redirect('/final_project_ddwt21/login/');
    }
    
    /* Add room to database */
    $owner_id = get_user_id();
    $feedback = add_room($db, $_POST);

    /* Redirect to room overview get route */
    redirect(sprintf('/final_project_ddwt21/overview/?error_msg=%s',
                json_encode($feedback)));
});

/* GET edit room */
$router->get('/rooms/edit/', function() use($navigation_template, $db){
    if (!check_login() ) {
        redirect('/final_project_ddwt21/login/');
    }
    /* Page info */
    $page_title = 'Edit Room';
    $breadcrumbs = get_breadcrumbs([
        'Home' => na('/final_project_ddwt21/', False),
        'Edit Room' => na('/final_project_ddwt21/rooms/edit/', True)
    ]);
    $unread_messages = unread_count($db);
    $navigation = get_navigation($navigation_template, 8, $unread_messages);

    /* Page content*/
    $form_action = '/final_project_ddwt21/rooms/edit/';
    $room_id = $_GET['room_id'];
    $room_info = get_room_info($db, $room_id);

    /* Get error msg from POST route */
    if (isset($_GET['error_msg'])) { $error_msg = get_error($_GET['error_msg']); }

    /* Choose Template */
    include use_template('newroom');
});

/* POST route to edit rooms */
$router->post('/rooms/edit/', function() use($db){
    /* Check if logged in */
    if ( !check_login() ) {
        redirect('/final_project_ddwt21/login/');
    }
    
    /* Add new info to database */
    $feedback = edit_room($db, $_POST);

    /* Redirect to overview get route */
    redirect(sprintf('/final_project_ddwt21/overview/?error_msg=%s',
                json_encode($feedback)));
});

/* POST route to delete rooms */
$router->post('/rooms/delete/', function() use($db){
    /* Check if logged in */
    if ( !check_login() ) {
        redirect('/final_project_ddwt21/login/');
    }
    
    /* Delete room from database */
    $room_id = $_POST['room_id'];
    $feedback = delete_room($db, $room_id);

    /* Redirect to overview get route */
    redirect(sprintf('/final_project_ddwt21/overview/?error_msg=%s',
                json_encode($feedback)));
});

$router->get('/termsofuse/', function() use($db, $navigation_template){
    /* Page info */
    $page_title = 'Terms of use';

    $breadcrumbs = get_breadcrumbs([
        'Home' => na('/final_project_ddwt21/', False),
        'Edit Room' => na('/final_project_ddwt21/termsofuse/', True)
    ]);
    $navigation = get_navigation($navigation_template, 0, $unread_messages);

    /* Page content */
    $page_subtitle = 'Our terms and conditions';

    /* Include Template */
    include use_template('termsofuse');
});

$router->get('/privacy/', function() use($db, $navigation_template){
    /* Page info */
    $page_title = 'Privacy';

    $breadcrumbs = get_breadcrumbs([
        'Home' => na('/final_project_ddwt21/', False),
        'Edit Room' => na('/final_project_ddwt21/privacy/', True)
    ]);
    $unread_messages = unread_count($db);
    $navigation = get_navigation($navigation_template, 0, $unread_messages);

    /* Page content */
    $page_subtitle = 'Our privacy policy';

    /* Include Template */
    include use_template('privacy');
});

$router->get('/mission/', function() use($db, $navigation_template){
    /* Page info */
    $page_title = 'Our mission';

    $breadcrumbs = get_breadcrumbs([
        'Home' => na('/final_project_ddwt21/', False),
        'Edit Room' => na('/final_project_ddwt21/mission/', True)
    ]);
    $unread_messages = unread_count($db);
    $navigation = get_navigation($navigation_template, 0, $unread_messages);

    /* Page content */
    $page_subtitle = 'Our mission';

    /* Include Template */
    include use_template('mission');
});



/* Run the router */
$router->run();

