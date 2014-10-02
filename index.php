<?php $GLOBALS['start'] = microtime(true); ?>
<?php require_once(dirname(__FILE__) . '/core/dispatcher.php'); ?>
<?php Dispatcher::dispatch(); ?>
<!DOCTYPE html>
<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html;charset=iso-8859-1"/>
		<title>Aushowmatic</title>
		<link rel="shortcut icon" type="image/png" href="./assets/favicon.png" />
		<link rel="apple-touch-icon" href="./assets/favicon-touch.png" />
		<link rel="stylesheet" type="text/css" href="./assets/main.css" />
		<link rel="stylesheet" type="text/css" href="./assets/yt-buttons.min.css" />
        <link rel="stylesheet" type="text/css" media="only screen and (max-device-width:1080px)" href="assets/handheld.css" />
	</head>
	<body>

		<header>
			<h1>Aushowmatic</h1>
		</header>

        <div id="remote">
            <ul class="yt-button-group">
                <li><a href="?a=transmission&param=stop" class="yt-button" title="Stop all torrents">&#8718;</a></li>
                <li><a href="?a=transmission&param=start" class="yt-button" title="Start all torrents">&#9658;</a></li>
            </ul>
            <ul class="yt-button-group">
                <li><a href="?a=transmission&param=altSpeedOn" class="yt-button" title="Turtle ON">Turtle</a></li>
                <li><a href="?a=transmission&param=altSpeedOff" class="yt-button" title="Turtle OFF">&infin;</a></li>
            </ul>
            <ul class="yt-button-group">
                <li><a href="?a=transmission&param=listFiles" class="yt-button" title="List torrents">&equiv;</a></li>
                <li><a href="?a=transmission&param=info" class="yt-button" title="Info">&iexcl;</a></li>
                <li><a href="<?php echo TRANSMISSION_WEB; ?>" target="_blank" class="yt-button" title="Transmission Web Interface">TWI</a></li>
            </ul>
        </div>

        <div id="main_container" class="auto">

            <?php if( !is_writable(FEED_INFO) ): ?>
                <div class="alert">The feed file is not writable. Please update permissions.</div>
            <?php endif; ?>

            <nav>
                <ul class="yt-button-group left">
                    <li><a href="?a=done" class="yt-button <?php if(isset($_GET['a']) && $_GET['a'] == 'done') echo 'active'; ?>">Links already processed</a></li>
                    <li><a href="?a=shows" class="yt-button <?php if(isset($_GET['a']) && $_GET['a'] == 'shows') echo 'active'; ?>">Shows added</a></li>
                    <li><a id="add_show" href="#add_show" class="yt-button">Add a show</a></li>
                </ul>

                <ul class="yt-button-group right">
                    <li><a href="?a=preview" class="yt-button <?php if(isset($_GET['a']) && $_GET['a'] == 'preview') echo 'active'; ?>">Preview downloads</a></li>
                    <li><a href="?a=launch" class="yt-button primary <?php if(isset($_GET['a']) && $_GET['a'] == 'launch') echo 'active'; ?>">Launch downloads</a></li>
                </ul>
                <div class="clear"></div>

                <form id="form_add_show" method="post" action="?a=add_show">
                    <input id="show_name" name="show_name" type="text" placeholder="Show name or ID"/>
                    <input id="show_label" name="show_label" type="text" placeholder="Show label (optional)"/>
                    <input id="sumbit_add_show" class="yt-button" type="submit" value="Add"/>
                </form>
            </nav>

            <pre id="response"><?php if( isset($_SESSION['result']) ) echo $_SESSION['result']; ?></pre>

            <div id="bottom_links">
                <div class="left">
                    <a href="?a=update_date" class="yt-button">Update min. date</a>
                    <a href="?a=empty_done" class="yt-button danger">Empty processed links</a>
                </div>
                <?php if( SYSTEM_CMDS_ENABLED ): ?>
	                <div class="right">
	                    <ul class="yt-button-group">
	                        <li><a href="?a=status_xbmc" class="yt-button">XBMC Status</a></li>
	                        <li><a href="?a=start_xbmc" class="yt-button primary">Start XBMC</a></li>
	                    </ul>
	                    <a id="show_hidden_actions" href="#bottom_links" class="yt-button">&#9660;</a>

	                    <div id="hidden_actions">
	                        <a href="?a=kill_xbmc" class="yt-button danger big">Kill XBMC</a>
	                        <a href="?a=reboot" class="yt-button danger big">Reboot</a>
	                        <a href="?a=shutdown" class="yt-button danger big">Shutdown</a>
	                    </div>
	                </div>
                <?php endif; ?>
                <div class="clear"></div>
            </div>

        </div>

        <footer>Min. date : <?php echo Utils::getMinDate(); ?> / Generated in <?php echo $_SESSION['generated_in']; ?>s</footer>

        <script src="./assets/main.js"></script>

	</body>
</html>
