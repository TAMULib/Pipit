<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
        <title><?php echo $config['app']['name'];?></title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0" />

        <meta name="apple-mobile-web-app-capable" content="yes" />
        <meta name="apple-mobile-web-app-status-bar-style" content="default" />
        <link rel="apple-touch-icon" href="iphone-icon.png" />
        <link rel="stylesheet" type="text/css" href="<?php echo $config['path_css'];?>style.css" media="screen"/>
<?php
if (is_file("{$config['path_css']}{$controller}.css")) {
    echo '<link rel="stylesheet" type="text/css" href="'.$config['path_css'].$controller.'.css" media="screen"/>';
}
?>
        <link rel="stylesheet" href="<?php echo $config['path_js'];?>jquery-ui-1.11.2.custom/jquery-ui.css">
        <script type="text/javascript" src="<?php echo $config['path_js'];?>jquery.min.js"></script>
        <script type="text/javascript" src="<?php echo $config['path_js'];?>jquery-ui-1.11.2.custom/jquery-ui.js"></script>
        <script type="text/javascript">
            var app_http = '<?php echo $app_http;?>';
        </script>
        <script type="text/javascript" src="<?php echo $config['path_js'];?>default.js"></script>
<?php
if (is_file("{$config['path_app']}js/{$controller}.js")) {
    echo '<script type="text/javascript" src="'.$config['path_js'].$controller.'.js"></script>';
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
            <div class="content hidden">
            </div>
        </div>
        <header>
            <h1><?php echo $config['app']['name'];?></h1>
        </header>
        <div class="navigation">
<?php
foreach ($pages as $nav) {
    if (!empty($nav['admin']) && ($nav['admin'] == true && $globaluser->isAdmin())) {
        if ($controller == $nav['path']) {
           echo "<a class=\"capitalize current\" href=\"{$config['path_http']}admin/{$nav['path']}/\">{$nav['name']}</a>";
        } else {
            echo "<a class=\"capitalize\" href=\"{$config['path_http']}admin/{$nav['path']}/\">{$nav['name']}</a>";
        }
    } elseif (empty($nav['admin'])) {
        if ($controller == $nav['path']) {
           echo "<a class=\"capitalize current\" href=\"{$config['path_http']}{$nav['path']}/\">{$nav['name']}</a>";
        } else {
            echo "<a class=\"capitalize\" href=\"{$config['path_http']}{$nav['path']}/\">{$nav['name']}</a>";
        }
    }
}
?>
        </div>
        <div id="systemBar">
<?php
if ($globaluser->isLoggedIn()) {
    echo '<div style="float:right;min-width: 5%;padding:12px 20px;">Hi '.$globaluser->getProfileValue('username').'! (<a href="'.$config['path_http'].'logout.php">logout</a>)</div>';
}
//present any system messages
echo '  <div class="sysMsg">';
if (isset($system)) {
	foreach ($system as $msg) {
		echo "<h4>{$msg}</h4>";
	}
}
echo '  </div>';
?>
        </div>
        <div class="container">
<?php
if (isset($page['title'])) {
	echo " <h1>{$page['title']}</h1>";
}
echo '     <div>';
if (isset($page['navigation'])) {
    $size = sizeof($page['navigation']);
    $navWidth = 15*$size;
    $btnWidth = $navWidth/($size*.8);
    echo "      <div style=\"width:{$navWidth}%\" class=\"inline-block navigation subNav\">";
	foreach ($page['navigation'] as $subnav) {
        $isCurrent = (isset($data['action']) && isset($subnav['action']) && $subnav['action'] == $data['action']) || (!isset($data['action']) && !isset($subnav['action']));
		echo "    <a style=\"width:{$btnWidth}%\" class=\"capitalize".($isCurrent ? ' current':'').(isset($subnav['modal']) ? ' do-loadmodal':'')."\" href=\"{$app_http}".((isset($subnav['action'])) ? "?action={$subnav['action']}":'')."\">{$subnav['name']}</a>";
	}
    echo '      </div>';
}

if (isset($page['search'])) {
    echo '  <form id="doSearch" class="do-submit inline" name="search" method="POST" action="'.$app_http.'">
                <input type="hidden" name="action" value="search" />
                <input id="searchTerm" class="inline" type="text" name="term" />';
    echo '      <input id="searchResults" class="inline" type="submit" name="submit" value="Search" />
                <div class="inline" id="searchStatus">
                    <a class="hidden" href="#clearSearch">clear search</a>
                </div>
            </form>';
}
echo '    </div>
          <div id="modalContent">';
if (isset($page['subtitle']) && $page['subtitle']) {
	echo "     <h4 class=\"capitalize\">{$page['subtitle']}</h4>";
}

?>
