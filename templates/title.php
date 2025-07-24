<?php
?>

<div class="top-menu">
    <div class="menu-options">
        <?php if(isLoggedIn()):?>
            Hello <?php echo htmlEscape(getAuthUser())?>.
            <a href="logout.php">Log out</a>
            <?php else:?>
                <a href="login.php">Log in</a>
            <?php endif;?>
        </div>
    </div>
<a href="index.php"><h1>Blog title</h1></a>
<p><?php echo htmlEscape('This paragaph summaries what the blog is about.'); ?></p>
<?php
?>

