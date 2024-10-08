<script src="/public/js/header.js"></script>
<header class="header">
    <md-elevation></md-elevation>
    <?php if (!isset($datanguoidung)) return header('Location: /');
    ?>
    <h1 id='title'>Chào <?php echo checkTime() ?><span>@<?php echo $datanguoidung[0]['username'] ?></span> </h1>
    <div class='btn-list-header'>
        <?php if ($datanguoidung[0]['role'] === 0 && (str_starts_with($_SERVER['REQUEST_URI'], '/profile') || str_starts_with($_SERVER['REQUEST_URI'], '/admin/review')))
            echo '
        <md-filled-tonal-icon-button id="adminbtn"  href="/admin/usermanager">
            <md-icon>Admin_Panel_Settings</md-icon>
        </md-filled-tonal-icon-button>';
        if ($datanguoidung[0]['role'] === 0 && str_starts_with($_SERVER['REQUEST_URI'], '/admin')&& !str_starts_with($_SERVER['REQUEST_URI'], '/admin/review'))
            echo '
        <md-filled-tonal-icon-button id="userbtn"  href="/profile">
            <md-icon>person</md-icon>
        </md-filled-tonal-icon-button>';
        ?>
        <?php if ($datanguoidung[0]['role'] === 1 && str_starts_with($_SERVER['REQUEST_URI'], '/fastcheck'))
            echo '
        <md-filled-tonal-icon-button id="userbtn"  href="/profile">
            <md-icon>person</md-icon>
        </md-filled-tonal-icon-button>';
        else if ($datanguoidung[0]['role'] === 1) echo
        '<md-filled-tonal-icon-button id="tablecheck"  href="/fastcheck">
        <md-icon>Fact_Check</md-icon>
    </md-filled-tonal-icon-button>'
        ?>

        <md-filled-tonal-icon-button id="logoutbtn" href="/api/logout">
            <md-icon>logout</md-icon>

        </md-filled-tonal-icon-button>
    </div>
</header>