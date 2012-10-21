<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
    <head>
        <?php include_http_metas() ?>
        <?php include_metas() ?>
        <title><?php include_slot('title', 'MineVis - Compositional Datamining and Visualizations') ?></title>
        <link rel="icon" type="image/png" href="/MineVis/favicon.png" />
        <?php use_stylesheet('ui-lightness/jquery-ui.css', 'first') ?>
        <?php use_stylesheet('colorbox.css', 'first') ?>
        <?php include_stylesheets() ?>
        <?php use_javascript('jquery-1.7.1.min.js', 'first') ?>
        <?php use_javascript('jquery-ui-1.8.16.custom.min.js', 'first') ?>
        <?php use_javascript('jquery.colorbox-min.js') ?>
        <?php use_javascript('minevis.js') ?>
        <?php include_javascripts() ?>
        <?php if (has_slot('auto_reload') && is_numeric(get_slot('auto_reload'))) : ?>
            <script type="text/javascript">
                $(document).ready(function() {
                    //$("#responsecontainer").load("response.php");
                    var refreshId = setInterval(function() {
                        location.reload(true);
                    }, <?php echo get_slot('auto_reload') ?>);
                    //$.ajaxSetup({ cache: false });
                });
            </script>
        <?php endif; ?>
    </head>
    <body>
        <div id="header">
            <div class="section">
                <h1>MineVis</h1>
            </div>
        </div>
        <div id="nav">
            <div class="section">
                <?php include_component('navigation', 'menu', array('current' => get_slot('current_menu'))) ?>
            </div>
        </div>
        <div id="content">
            <div class="section">
                <?php echo $sf_content ?>
            </div>
        </div>

        <div id="footer">
            <div class="section">
                <?php if ($sf_user->isAuthenticated()): ?>
                    <div id="menu">
                        <ul>
                            <li><?php echo link_to('Features', 'feature') ?></li>
                            <li><?php echo link_to('Users', 'sf_guard_user') ?></li>
                            <li><?php echo link_to('Logout', 'sf_guard_signout') ?></li>
                        </ul>
                    </div>
                <?php else : ?>
                <div id="menu">
                        <ul>
                            <li><?php echo link_to('Login', 'sf_guard_signin') ?></li>
                        </ul>
                    </div>
                <?php endif ?>

            </div>
            <div class="section">
                &copy; MineVis.
            </div>
        </div>
    </body>
</html>
