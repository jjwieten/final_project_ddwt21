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
        <h5><?= $get_username ?></h5>

        <div class="pd-15">&nbsp;</div>

        <table class="table">
        <tbody>
        <tr>
            <th>Full name</th>
            <td>Rub bro</td>
        </tr>
        <tr>
            <th scope="row"> Age</th>
            <td>14</td>
        </tr>
        <th> Biem</th>
        <td>Badabiem</td>
        </tbody>
        </table>

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

