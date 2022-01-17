<!doctype html>
<html lang="en">
<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/4.6.1/css/bootstrap.min.css" integrity="sha512-T584yQ/tdRR5QwOpfvDfVQUidzfgc2339Lc8uBDtcp/wYu80d7jwBgAxbyMh0a9YM9F8N3tdErpFI8iaGx6x5g==" crossorigin="anonymous" referrerpolicy="no-referrer" />

    <!-- Own CSS -->
    <link rel="stylesheet" href="/final_project_ddwt21/css/main.css">
    <title><?= $page_title ?></title>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark static-top">
        <div class="container">
            <a class="navbar-brand" href="#">
                <img src="https://placeholder.pics/svg/150x50/888888/EEE/Logo" alt="..." height="36">
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link active" aria-current="page" href="/final_project_ddwt21/">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/final_project_ddwt21/overview/">Overview</a>
                    </li>
                    <!--
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            My account
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                            <li><a class="dropdown-item" href="/final_project_ddwt21/myaccount/">My account</a></li>
                            <li><a class="dropdown-item" href="/final_project_ddwt21/logout/">Another action</a></li>
                            <li>
                                <hr class="dropdown-divider">
                            </li>
                            <li><a class="dropdown-item" href="#">Something else here</a></li>
                        </ul>
                    </li>
                    -->
                    <?php
                        /* Connect to DB */
                        $db = connect_db('localhost', 'final_project', 'ddwt21','ddwt21');
                        if (!check_login()){
                            echo "<li class='nav-item'><a class='nav-link' href='/final_project_ddwt21/register/'>Register</a></li>";
                            echo "<li class='nav-item'><a class='nav-link' href='/final_project_ddwt21/login/'>Login</a></li>";
                        }
                        else {
                            $role_array = check_role($db);
                            echo "<li class='nav-item'><a class='nav-link' href='/final_project_ddwt21/messages/'>Messages</a></li>";
                            if ($role_array['role'] == '1') {
                                echo "<li class='nav-item'><a class='nav-link' href='/final_project_ddwt21/owner/'>My account</a></li>";
                            }
                            elseif ($role_array['role'] == '2'){
                                echo "<li class='nav-item'><a class='nav-link' href='/final_project_ddwt21/tenant/'>My account</a></li>";
                            }
                            echo "<li class='nav-item'><a class='nav-link' href='/final_project_ddwt21/logout/'>Logout</a></li>";
                    };
                    ?>
                </ul>
            </div>
        </div>
    </nav>



<!-- Content -->
<div class="container">