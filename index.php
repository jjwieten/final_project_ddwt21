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


/* Run the router */
$router->run();