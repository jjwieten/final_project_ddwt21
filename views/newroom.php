<?php
    include_once 'header.php';
?>
<!-- Content -->
<div class="container">
    <!-- Breadcrumbs -->
    <div class="pd-15">&nbsp</div>
    <?= $breadcrumbs ?>
    
    <div class="row">

        <!-- Left column -->
        <div class="col-md-12">
            <!-- Error message -->
            <?php if (isset($error_msg)){echo $error_msg;} ?>

            <h1><?= $page_title ?></h1>

            <div class="pd-15">&nbsp;</div>

            <form action="<? $form_action?>" method="POST">
                <div class="form-group">
                    <label for="inputRoomname">Room name</label>
                    <input type="text" class="form-control" id="inputRoomname" placeholder="" name="room_name" value="<?php if (isset($room_info)){echo $room_info['room_name'];} ?>" required>
                </div>
                <div class="form-group">
                    <label for="inputPrice">Price</label>
                    <input type="text" class="form-control" id="inputPrice" placeholder="" name="price" value="<?php if (isset($room_info)){echo $room_info['price'];} ?>" required>
                </div>
                <div class="form-group">
                    <label for="inputType">Type</label>
                    <input type="text" class="form-control" id="inputType" placeholder="e.g. studio, apartment" name="type" value="<?php if (isset($room_info)){echo $room_info['type'];} ?>" required>
                </div>
                <div class="form-group">
                    <label for="inputSize">Size (in square meters)</label>
                    <input type="number" class="form-control" id="inputSize" placeholder="" name="size"  value="<?php if (isset($room_info)){echo $room_info['size'];} ?>" required>
                </div>
                <div class="form-group">
                    <label for="inputCity">City</label>
                    <input type="text" class="form-control" id="inputCity" placeholder="" name="city" value="<?php if (isset($room_info)){echo $room_info['city'];} ?>" required>
                </div>
                <div class="form-group">
                    <label for="inputPostcode">Postcode</label>
                    <input type="text" class="form-control" id="inputPostcode" placeholder="" name="postcode" value="<?php if (isset($room_info)){echo $room_info['postcode'];} ?>" required>
                </div>
                <div class="form-group">
                    <label for="inputStreet">Street</label>
                    <input type="text" class="form-control" id="inputStreet" placeholder="" name="street" value="<?php if (isset($room_info)){echo $room_info['street'];} ?>" required>
                </div>
                <div class="form-group">
                    <label for="inputHousenr">House number</label>
                    <input type="text" class="form-control" id="inputHousenr" placeholder="e.g. 1, 1A" name="house_nr" value="<?php if (isset($room_info)){echo $room_info['house_nr'];} ?>" required>
                </div>
                <div class="form-group">
                    <label for="inputDescription">Description</label>
                    <input type="text" class="form-control" id="inputDescription" placeholder="" name="description" value="<?php if (isset($room_info)){echo $room_info['description'];} ?>" required>
                </div>
                <div>
                    <input type="hidden" name="room_id" value="<?php if (isset($room_id)){echo $room_id;} ?>">
                </div>

                <?php if (!isset($room_info)){ echo
                '<button type="submit" class="btn btn-primary">Add Room</button>';} else{
                    echo
                    '<button type="submit" class="btn btn-warning">Edit Room</button>';
                } ?>
            </form>

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
