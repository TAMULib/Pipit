<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
        <title><?php echo APP_NAME;?></title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0" />

        <meta name="apple-mobile-web-app-capable" content="yes" />
        <meta name="apple-mobile-web-app-status-bar-style" content="default" />
        <link rel="apple-touch-icon" href="iphone-icon.png" />
		<!-- Bootstrap CSS - Latest compiled and minified CSS -->
		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" integrity="sha384-1q8mTJOASx8j1Au+a5WDVnPi2lkFfwwEAa8hDDdjZlpLegxhjVME1fgjWPGmkzs7" crossorigin="anonymous">
        <link rel="stylesheet" type="text/css" href="<?php echo $config['PATH_CSS'];?>style.css" media="screen"/>
        <link rel="stylesheet" type="text/css" href="<?php echo $config['PATH_THEMES'];?>bootstrap/css/style.css" media="screen"/>
<?php
if (is_file("{$config['PATH_FILE']}{$controllerName}.css")) {
    echo '<link rel="stylesheet" type="text/css" href="'.$config['PATH_CSS'].$controller.'.css" media="screen"/>';
}
?>
		<!-- Bootstrap JS - Latest compiled and minified JavaScript -->
		<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js" integrity="sha384-0mSbJDEHialfmuBBQP6A4Qrprq5OVfW37PRR3j5ELqxss1yVqOtnepnHVP9aJ7xS" crossorigin="anonymous"></script>
        <link rel="stylesheet" href="<?php echo $config['PATH_JS'];?>jquery-ui-1.11.2.custom/jquery-ui.css">
        <script type="text/javascript" src="<?php echo $config['PATH_JS'];?>jquery.min.js"></script>
        <script type="text/javascript" src="<?php echo $config['PATH_JS'];?>jquery-ui-1.11.2.custom/jquery-ui.js"></script>
        <script type="text/javascript">
            var app_http = '<?php echo $app_http;?>';
        </script>
        <script type="text/javascript" src="<?php echo $config['PATH_JS'];?>default.js"></script>
<?php
if ($controllerName != 'default' && is_file("{$config['PATH_FILE']}resources/js/{$controllerName}.js")) {
    echo '<script type="text/javascript" src="'.$config['PATH_JS'].$controllerName.'.js"></script>';
}
?>
        <link rel="shortcut icon" href="ico/favicon.ico">
    </head>
    <body>
        <div id="theOverlay"></div>
        <div id="theModal">
            <div class="header">
                <div class="loader">
                    <div id="squaresWaveG">
                        <div id="squaresWaveG_1" class="squaresWaveG"></div>
                        <div id="squaresWaveG_2" class="squaresWaveG"></div>
                        <div id="squaresWaveG_3" class="squaresWaveG"></div>
                        <div id="squaresWaveG_4" class="squaresWaveG"></div>
                        <div id="squaresWaveG_5" class="squaresWaveG"></div>
                        <div id="squaresWaveG_6" class="squaresWaveG"></div>
                        <div id="squaresWaveG_7" class="squaresWaveG"></div>
                        <div id="squaresWaveG_8" class="squaresWaveG"></div>
                    </div>
                </div>
                <a class="do-close" href="#">Close</a>
            </div>
            <div class="content">
            </div>
        </div>
		<nav class="navbar navbar-default">
			<div class="container-fluid">
				<div class="navbar-header">
					<span class="navbar-brand"><?php echo $config["APP_NAME"];?></span>
				</div>
				<div>
					<ul class="nav navbar-nav">
<?php
if ($globalUser->isLoggedIn()) {
    foreach ($pages as $nav) {
		echo '			<li'.(($controllerName == $nav['path']) ? ' class="active"':'').'>';
        if (!empty($nav['admin']) && ($nav['admin'] == true && $globalUser->isAdmin())) {
			echo "<a class=\"capitalize\" href=\"{$config['PATH_HTTP']}admin/{$nav['path']}/\">{$nav['name']}</a>";
        } elseif (empty($nav['admin'])) {
			echo "<a class=\"capitalize\" href=\"{$config['PATH_HTTP']}{$nav['path']}/\">{$nav['name']}</a>";
        }
		echo '			</li>';
    }
}
?>
					</ul>
        		</div>
			</div>
		</nav>
        <div id="systemBar">
<?php
if ($globalUser->isLoggedIn()) {
    echo '  <div style="float:right;min-width: 5%;padding:12px 20px;">Hi <a href="'.$config['PATH_HTTP'].'user.php?action=edit">'.$globalUser->getProfileValue('username').'</a>! (<a href="'.$config['PATH_HTTP'].'user.php?action=logout">logout</a>)</div>';
}
//present any system messages
echo '      <div class="sysMsg">';
if (isset($system)) {
	foreach ($system as $msg) {
		echo "    <div class=\"alert alert-info\">{$msg}</div>";
	}
}
echo '      </div>';
?>
        </div>
        <div class="container">
<?php
if (!empty($page)) {
    if (isset($page['title'])) {
		echo "<div class=\"page-header\">
            	<h1>{$page['title']}</h1>
			</div>";
    }
    echo '  <div>';
    if (isset($page['navigation'])) {
        $size = sizeof($page['navigation']);
        $navWidth = 15*$size;
        $btnWidth = $navWidth/($size*.8);
        echo "  <div style=\"width:{$navWidth}%\" class=\"inline-block navigation subNav\">";
    	foreach ($page['navigation'] as $subnav) {
            $isCurrent = (isset($data['action']) && isset($subnav['action']) && $subnav['action'] == $data['action']) || (!isset($data['action']) && !isset($subnav['action']));
    		echo "<a style=\"width:{$btnWidth}%\" class=\"capitalize".($isCurrent ? ' current':'').(isset($subnav['modal']) ? ' do-loadmodal':'')."\" href=\"{$app_http}".((isset($subnav['action'])) ? "?action={$subnav['action']}":'')."\">{$subnav['name']}</a>";
    	}
        echo '  </div>';
    }

    if (isset($page['search'])) {
        echo '  <form id="doSearch" class="do-get inline-block" name="search" method="POST" action="'.$app_http.'">
                    <input type="hidden" name="action" value="search" />
                    <input id="searchTerm" class="inline" type="text" name="term" />';
        echo '      <input id="searchResults" class="inline" type="submit" name="submit" value="Search" />
                    <div class="inline-block" id="searchStatus">
                        <a class="hidden" href="#clearSearch">clear search</a>
                    </div>
                </form>';
    }
    echo '    </div>';
}
echo '        <div id="modalContent">';
if (isset($page['subtitle']) && $page['subtitle']) {
	echo "     	<div class=\"page-header\">
					<h1 class=\"capitalize\"><small>{$page['subtitle']}</small></h1>
				</div>";
}

?>
