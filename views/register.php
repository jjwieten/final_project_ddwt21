<?php
    include_once 'header.php';
?>


    <!-- Breadcrumbs -->
    <div class="pd-15">&nbsp</div>
    <?= $breadcrumbs ?>

    <div class="row">

        <!-- Left column -->
        <div class="col-md-12">
            <!-- Error message -->
            <?php if (isset($error_msg)){echo $error_msg;} ?>

            <h1><?= $page_title ?></h1>
            <h5><?= $page_subtitle ?></h5>

            <div class="pd-15">&nbsp;</div>

            <form action="/final_project_ddwt21/register/" method="POST">
                <div class="form-group">
                    <label for="inputUsername">Username</label>
                    <input type="text" class="form-control" id="inputUsername" placeholder="Sandy" name="username" value="<?php if (isset($user_info)){echo $user_info['username'];} ?>" required>
                </div>
                <?php if (!isset($user_info)){ echo
                '<div class="form-group">
                    <label for="inputPassword">Password</label>
                    <input type="password" class="form-control" id="inputPassword" placeholder="******" name="password"  required>
                </div>';} ?>
                <div class="form-group">
                    <label for="inputFirstname">First name</label>
                    <input type="text" class="form-control" id="inputFirstname" placeholder="Kayla" name="firstname" value="<?php if (isset($user_info)){echo $user_info['firstname'];} ?>" required>
                </div>
                <div class="form-group">
                    <label for="inputLastname">Last name</label>
                    <input type="text" class="form-control" id="inputLastname" placeholder="Graversma" name="lastname" value="<?php if (isset($user_info)){echo $user_info['lastname'];} ?>" required>
                </div>
                <div class="form-group">
                    <label for="inputDate">Birthday</label>
                    <input type="date" class="form-control" id="inputDate" placeholder="" name="birth_date"  value="<?php if (isset($user_info)){echo $user_info['birth_date'];} ?>" required>
                </div>
                <div class="form-group">
                    <label for="inputPhone">Phone number</label>
                    <input type="number" class="form-control" id="inputPhone" placeholder="00000000" name="phone_nr" value="<?php if (isset($user_info)){echo $user_info['phone_nr'];} ?>" required>
                </div>
                <div class="form-group">
                    <label for="inputEmail">Email</label>
                    <input type="email" class="form-control" id="inputEmail" placeholder="sandy@gmail.com" name="email" value="<?php if (isset($user_info)){echo $user_info['email'];} ?>" required>
                </div>
                <div class="form-group">
                    <label for="inputLanguage">Language</label>
                    <input type="text" class="form-control" id="inputLanguage" placeholder="Nederlands" name="language" value="<?php if (isset($user_info)){echo $user_info['language'];} ?>" required>
                </div>
                <div class="form-group">
                    <label for="inputST">Studies/Profession</label>
                    <input type="text" class="form-control" id="inputST" placeholder="Student" name="profession" value="<?php if (isset($user_info)){echo $user_info['profession'];} ?>" required>
                </div>
                <div class="form-group">
                    <label for="inputBio">Biography</label>
                    <input type="text" class="form-control" id="inputBio" placeholder="Kayla's bio" name="biography" value="<?php if (isset($user_info)){echo $user_info['biography'];} ?>" required>
                </div>
                <?php if (!isset($user_info)){ echo
                '                <div class="form-group">
                    <label for="inputAccount">Account type: </label>
                    <select name="account_type_id" id="account">
                        <option value="1">Eigenaar</option>
                        <option value="2">Gebruiker</option>
                    </select>  </div>';} else{
                    echo '<input type="hidden" name="user_id" value="'. $user_id.'">';
                } ?>

                <?php if (!isset($user_info)){ echo
                '<button type="submit" class="btn btn-primary">Registreer nu</button>';} else{
                    echo
                    '<button type="submit" class="btn btn-warning">Edit</button>';
                } ?>
            </form>

        </div>

    </div>
</div>




<!-- Optional JavaScript -->
<!-- jQuery first, then Popper.js, then Bootstrap JS -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.5.1/jquery.slim.min.js" integrity="sha512-/DXTXr6nQodMUiq+IUJYCt2PPOUjrHJ9wFrqpJ3XkgPNOZVfMok7cRw6CSxyCQxXn6ozlESsSh1/sMCTF1rL/g==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.1/umd/popper.min.js" integrity="sha512-ubuT8Z88WxezgSqf3RLuNi5lmjstiJcyezx34yIU2gAHonIi27Na7atqzUZCOoY4CExaoFumzOsFQ2Ch+I/HCw==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/4.6.1/js/bootstrap.min.js" integrity="sha512-UR25UO94eTnCVwjbXozyeVd6ZqpaAE9naiEUBK/A+QDbfSTQFhPGj5lOR6d8tsgbBk84Ggb5A3EkjsOgPRPcKA==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
</body>
</html>
