<?php

/* Require composer autoloader */
require __DIR__ . '/vendor/autoload.php';

/* Get model.php */
include 'model.php';

/* Connect to DB */
$db = connect_db('localhost', 'final_project', 'ddwt21','ddwt21');

/* Create Router instance */
$router = new \Bramus\Router\Router();

/* Navigation template */
$navigation_template = Array(
    1 => Array(
        'name' => 'Home',
        'url' => '/final_project_ddwt21/'
    ),
    2 => Array(
        'name' => 'Overview',
        'url' => '/final_project_ddwt21/overview/'
    ),
    3 => Array(
        'name' => 'Register',
        'url'   => '/final_project_ddwt21/register/'
    ),
    4 => Array(
        'name' => 'Messages',
        'url'   => '/final_project_ddwt21/messages/'
    ),
    5 => Array(
        'name' => 'Login',
        'url'   => '/final_project_ddwt21/login/'
    ),
    6 => Array(
        'name' => 'Test_login',
        'url'  => '/final_project_ddwt21/test_login/'
    ));


/* Landing page */
$router->get('/', function() use($navigation_template){
    /* Page info */
    $page_title = 'Home';
    $breadcrumbs = get_breadcrumbs([
        'Home' => na('/final_project_ddwt21/', True)
    ]);
    $navigation = get_navigation($navigation_template, 1);

    /* Page content */
    $page_subtitle = 'Subtitle';
    $page_content = 'Content.';

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
    $navigation = get_navigation($navigation_template, 2);

    /* Page content */
    $page_subtitle = 'Overview of rooms';
    $page_content = 'An overview of the rooms. For more information click on More Info';
    $left_content = get_rooms_table($db);

    /* Get error msg from POST route */
    if (isset($_GET['error_msg'])) { $error_msg = get_error($_GET['error_msg']); }

    /* Choose Template */
    include use_template('main');
});

/* GET register */
$router->get('/register/', function() use($navigation_template, $db){
    /* Page info */
    $page_title = 'Register';
    $breadcrumbs = get_breadcrumbs([
        'Home' => na('/final_project_ddwt21/', False),
        'Register' => na('/final_project_ddwt21/register/', True)
    ]);
    $navigation = get_navigation($navigation_template, 2);

    /* Page content */
    $page_subtitle = 'Overview of rooms';
    $page_content = 'An overview of the rooms. For more information click on More Info';
    $left_content = get_rooms_table($db);

    /* Get error msg from POST route */
    if (isset($_GET['error_msg'])) { $error_msg = get_error($_GET['error_msg']); }



    /* Choose Template */
    include use_template('register');
});

/* POST register */
$router->post('/register/', function() use($db){
    /* register user */
    register_user($db,$_POST);
    /* Redirect to homepage*/
    redirect('/final_project_ddwt21/');

});

/* POST login */
$router->post('/login/', function() use($db){
    /* login user */
    login_user($db,$_POST);
    /* Redirect to homepage*/
    redirect('/final_project_ddwt21/register/');

});

/* GET messages */
$router->get('/messages/', function() use($navigation_template, $db){
    /* Page info */
    $current_user = 1; /* Will be replaced by line below */
    // $current_user = $_SESSION['user_id'];
    $chat_id = $_GET['chat_id'];
    $page_title = 'Messages';
    $breadcrumbs = get_breadcrumbs([
        'Home' => na('/final_project_ddwt21/', False),
        'Messages' => na('/final_project_ddwt21/messages/', True)
    ]);
    $navigation = get_navigation($navigation_template, 4);

    /* Page content */
    $conversation_overview = get_conversation_overview_divs($db, $current_user);
    if (isset($chat_id)){
        $chat = get_messages_divs($db, $current_user, $chat_id);
    }

    /* Get error msg from POST route */
    if (isset($_GET['error_msg'])) { $error_msg = get_error($_GET['error_msg']); }

    /* Choose Template */
    include use_template('messages');
});

/* GET overview */
$router->get('/login/', function() use($navigation_template, $db){
    /* Page info */
    $page_title = 'Login';
    $breadcrumbs = get_breadcrumbs([
        'Home' => na('/final_project_ddwt21/', False),
        'Overview' => na('/final_project_ddwt21/login/', True)
    ]);
    $navigation = get_navigation($navigation_template, 5);

    /* Page content */
    $page_subtitle = 'Login here';
    $page_content = 'Log into your account here';
    $left_content = get_rooms_table($db);

    /* Get error msg from POST route */
    if (isset($_GET['error_msg'])) { $error_msg = get_error($_GET['error_msg']); }

    /* Choose Template */
    include use_template('login');
});

/* GET overview */
$router->get('/test_login/', function() use($navigation_template, $db){

    /* Check if logged in */
    if ( !check_login() ) {
        redirect('/final_project_ddwt21/login/');
    }
    /* Page info */
    $page_title = 'Test login';
    $breadcrumbs = get_breadcrumbs([
        'Home' => na('/final_project_ddwt21/', False),
        'Overview' => na('/final_project_ddwt21/test_login/', True)
    ]);
    $navigation = get_navigation($navigation_template, 6);

    /* Page content */
    $page_subtitle = 'You are logged in';
    $page_content = 'You are logged in';
    $left_content = get_rooms_table($db);

    /* Get error msg from POST route */
    if (isset($_GET['error_msg'])) { $error_msg = get_error($_GET['error_msg']); }

    /* Choose Template */
    include use_template('test_login');
});



/* Run the router */
$router->run();