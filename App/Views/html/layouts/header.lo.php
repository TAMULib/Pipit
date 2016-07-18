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
        <link rel="stylesheet" type="text/css" href="<?php echo $config['PATH_CSS'];?>helpers.css" media="screen"/>
        <link rel="stylesheet" type="text/css" href="<?php echo $config['PATH_CSS'];?>style.css" media="screen"/>
<?php
if (is_file("{$config['PATH_APP']}{$controllerName}.css")) {
    echo '<link rel="stylesheet" type="text/css" href="'.$config['PATH_CSS'].$controller.'.css" media="screen"/>';
}
?>
        <link rel="stylesheet" href="<?php echo $config['PATH_JS'];?>jquery-ui-1.11.2.custom/jquery-ui.css">
        <script type="text/javascript" src="<?php echo $config['PATH_JS'];?>jquery.min.js"></script>
        <script type="text/javascript" src="<?php echo $config['PATH_JS'];?>jquery-ui-1.11.2.custom/jquery-ui.js"></script>
        <script type="text/javascript">
            var app_http = '<?php echo $app_http;?>';
        </script>
        <script type="text/javascript" src="<?php echo $config['PATH_JS'];?>default.js"></script>
<?php
if (is_file("{$config['PATH_APP']}resources/js/{$controllerName}.js")) {
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
        <header>
            <h1><?php echo $config["APP_NAME"];?></h1>
        </header>
        <div class="navigation">
<?php
if ($globalUser->isLoggedIn()) {
    foreach ($pages as $nav) {
        if ($nav->getAccessLevel() == SECURITY_ADMIN && $globalUser->isAdmin()) {
            echo "<a class=\"capitalize".(($controllerName == $nav->getPath()) ? ' current':'')."\" href=\"{$config['PATH_HTTP']}admin/{$nav->getPath()}/\">{$nav->getName()}</a>";
        } else {
            echo "<a class=\"capitalize".(($controllerName == $nav->getPath()) ? ' current':'')."\" href=\"{$config['PATH_HTTP']}{$nav->getPath()}/\">{$nav->getName()}</a>";
        }
    }
}
?>
        </div>
        <div id="systemBar">
<?php
if ($globalUser->isLoggedIn()) {
    echo '  <div style="float:right;min-width: 5%;padding:12px 20px;">Hi <a href="'.$config['PATH_HTTP'].'user.php?action=edit">'.$globalUser->getProfileValue('username').'</a>! (<a href="'.$config['PATH_HTTP'].'user.php?action=logout">logout</a>)</div>';
}
//present any system messages
echo '      <div class="sysMsg">';
if (isset($systemMessages)) {
    foreach ($systemMessages as $sysMsg) {
        echo "    <h4 class=\"alert\">{$sysMsg->getMessage()}</h4>";
    }
}
echo '      </div>';
?>
        </div>
        <div class="container">
<?php
if (!empty($page)) {
    if ($page->getTitle()) {
    	echo "
            <h1>{$page->getTitle()}</h1>";
    }
    echo '  <div>';
    if ($page->getOptions()) {
        $size = sizeof($page->getOptions());
        $navWidth = 15*$size;
        $btnWidth = $navWidth/($size*.8);
        echo "  <div style=\"width:{$navWidth}%\" class=\"inline-block navigation subNav\">";
    	foreach ($page->getOptions() as $subnav) {
            $isCurrent = (isset($data['action']) && isset($subnav['action']) && $subnav['action'] == $data['action']) || (!isset($data['action']) && !isset($subnav['action']));
    		echo "<a style=\"width:{$btnWidth}%\" class=\"capitalize".($isCurrent ? ' current':'').(isset($subnav['modal']) ? ' do-loadmodal':'')."\" href=\"{$app_http}".((isset($subnav['action'])) ? "?action={$subnav['action']}":'')."\">{$subnav['name']}</a>";
    	}
        echo '  </div>';
    }

    if ($page->isSearchable()) {
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
if (!empty($page) && $page->getSubtitle()) {
	echo "     <h4 class=\"capitalize\">{$page->getSubtitle()}</h4>";
}

?>
