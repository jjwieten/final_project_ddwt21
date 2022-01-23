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
        <div class="col-md-8">
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
                                <th scope="row">Address</th>
                                <td><?= $room_info['street'] . ' ' . $room_info['house_nr'] . '</br>' . $room_info['postcode'] . ' ' . $room_info['city']?></td>
                            </tr>
                            <tr>
                                <th scope="row">Type</th>
                                <td><?= $room_info['type'] ?></td>
                            </tr>
                            <tr>
                                <th scope="row">Price</th>
                                <td>&euro;	<?= $room_info['price'] ?></td>
                            </tr>
                            <tr>
                                <th scope="row">Description</th>
                                <td><?= $room_info['description'] ?></td>
                            </tr>
                            <?php if ($user_role == 2) { ?>
                            <tr>
                                <th scope="row">Owner</th>
                                <td><?= $owner_name ?></td>
                            </tr>
                            <?php } ?>
                            </tbody>
                        </table>
                        <?php if ($user_role == 1) { ?>
                            <div class="row">
                                <div class="col-sm-2">
                                    <a href="/final_project_ddwt21/rooms/edit/?room_id=<?= $room_info['room_id'] ?>" role="button" class="btn btn-secondary">Edit</a>
                                    <form action="/final_project_ddwt21/rooms/delete" method="POST">
                                        <input type="hidden" value="<?= $room_info['room_id'] ?>" name="room_id">
                                        <button type="submit" class="btn btn-danger">Delete</button>
                                    </form>
                                </div>
                            </div>
                        <?php } ?>
                        <?php if ($user_role == 2) { ?>
                            <div class="row">
                            <div class="col-sm-2">
                                    <form action="/final_project_ddwt21/optins/add" method="POST">
                                        <input type="hidden" value="<?= $room_info['room_id'] ?>" name="room_id">
                                        <button type="submit" class="btn btn-info">Opt-in</button>
                                    </form>
                                </div>
                                <td><a href="/final_project_ddwt21/user/<?= $room_info['owner_id'] ?>" role="button" class="btn btn-secondary">View owner profile</a></td>
                                <a href="/final_project_ddwt21/room/<?= $room_info['room_id'] ?>/?message=<?= $room_info['owner_id'] ?>" role="button" class="btn btn-secondary">Message owner</a>
                            </div>
                        <?php } ?>
                        <div class="pd-15">&nbsp;</div>
                        
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
                                        <input type="hidden" id="room_page" name="room_page" value='.$room_info['room_id'].'>
                                        <input type="submit" value="Send message" class="btn btn-secondary">
                                        <a href="/final_project_ddwt21/room/'.$room_info["room_id"].'" role="button" class="btn btn-danger">Cancel</a>
                                    </form>
                                    
                                </div>';
                        } else
                        if (isset($optins)) {echo $optins;} ?>
                        
                        
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
