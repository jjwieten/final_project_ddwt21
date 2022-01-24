<?php
    include_once 'header.php';
?>
<!-- Content -->
<div class="container">
    <!-- Breadcrumbs -->
    <div class="pd-15">&nbsp</div>
    <?= $breadcrumbs ?>
    
    <div class="row">

        <!-- Main content -->
        <div class="col-md-12">
            <!-- Error message -->
            <?php if (isset($error_msg)){echo $error_msg;} ?>

            <div class="pd-15">&nbsp;</div>
                <table class="table">
                            <thead class="thead-dark">
                                <tr>
                                    <th scope="col" colspan="2"><?= $page_title ?></th>
                                </tr>
                            </thead>
                            <tbody>
                            <tr>
                                <th scope="row" style="width: 30%;">Username</th>
                                <td style="width: 70%;"><?= $user_info['username']?></td>
                            </tr>
                            <tr>
                                <th scope="row" style="width: 30%;">Full name</th>
                                <td style="width: 70%;"><?= $user_info['firstname'] .' '.$user_info['lastname'] ?></td>
                            </tr>
                            <tr>
                                <th scope="row" style="width: 30%;">Birthdate</th>
                                <td style="width: 70%;"><?= $user_info['birth_date'] ?></td>
                            </tr>
                            <tr>
                                <th scope="row" style="width: 30%;">Studies / profession</th>
                                <td style="width: 70%;"><?= $user_info['profession'] ?></td>
                            </tr>
                            <tr>
                                <th scope="row" style="width: 30%;">Biography</th>
                                <td style="width: 70%;"><?= $user_info['biography'] ?></td>
                            </tr>
                            <tr>
                                <th scope="row" style="width: 30%;">Language</th>
                                <td style="width: 70%;"><?= $user_info['language'] ?></td>
                            </tr>
                            <tr>
                                <th scope="row" style="width: 30%;">E-mail</th>
                                <td style="width: 70%;"><?= $user_info['email'] ?></td>
                            </tr>
                            <tr>
                                <th scope="row" style="width: 30%;">Phone number</th>
                                <td style="width: 70%;"><?= $user_info['phone_nr'] ?></td>
                            </tr>
                            </tbody>
                        </table>
                        <?php if ($own_profile) { ?>
                            <div class="row ml-1">
                                <a href="/final_project_ddwt21/user/edit/?user_id=<?= $user_info['user_id'] ?>" role="button" class="btn btn-secondary">Edit account information</a>
                                <form action="/final_project_ddwt21/user/delete" method="POST">
                                    <input type="hidden" value="<?= $user_info['user_id'] ?>" name="user_id">
                                    <button type="submit" class="btn btn-danger ml-1">Delete account</button>
                                </form>
                            </div>
                        <?php } else {
                            echo '<a href="/final_project_ddwt21/user/'.$user_info["user_id"].'/?message='.$user_info["user_id"].'" role="button" class="btn btn-secondary">Send message</a>';
                        }?>
                        
                        <?php if (isset($chat_id)) {
                            echo '
                                <div class="col-md-12">
                                <div class="pd-15">&nbsp;</div>
                                <h5>Send a message to '.$receiver_name.'</h5>
                                    <form action="/final_project_ddwt21/messages/" method="POST">
                                        <div class="form-group">
                                            <textarea class="form-control" rows="5" id="content" name="content" placeholder="Type your message here..."></textarea>
                                        </div>
                                        <input type="hidden" id="receiver_id" name="receiver_id" value='.$chat_id.'>
                                        <input type="hidden" id="user_page" name="user_page" value='.$user_info['user_id'].'>
                                        <input type="submit" value="Send message" class="btn btn-secondary">
                                        <a href="/final_project_ddwt21/user/'.$user_info["user_id"].'" role="button" class="btn btn-danger">Cancel</a>
                                    </form>
                                    
                                </div>';
                        }?>
        </div>
    </div>
</div>

<?php
include_once 'footer.php';
?>


<!-- Optional JavaScript -->
<!-- jQuery first, then Popper.js, then Bootstrap JS -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.5.1/jquery.slim.min.js" integrity="sha512-/DXTXr6nQodMUiq+IUJYCt2PPOUjrHJ9wFrqpJ3XkgPNOZVfMok7cRw6CSxyCQxXn6ozlESsSh1/sMCTF1rL/g==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.1/umd/popper.min.js" integrity="sha512-ubuT8Z88WxezgSqf3RLuNi5lmjstiJcyezx34yIU2gAHonIi27Na7atqzUZCOoY4CExaoFumzOsFQ2Ch+I/HCw==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/4.6.1/js/bootstrap.min.js" integrity="sha512-UR25UO94eTnCVwjbXozyeVd6ZqpaAE9naiEUBK/A+QDbfSTQFhPGj5lOR6d8tsgbBk84Ggb5A3EkjsOgPRPcKA==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
</body>
</html>
