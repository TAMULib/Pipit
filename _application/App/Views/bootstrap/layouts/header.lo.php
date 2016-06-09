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
<!--        <link rel="stylesheet" type="text/css" href="<?php echo $config['PATH_CSS'];?>style.css" media="screen"/>-->
        <link rel="stylesheet" type="text/css" href="<?php echo $config['PATH_CSS'];?>helpers.css" media="screen"/>
        <link rel="stylesheet" type="text/css" href="<?php echo $config['PATH_THEMES'];?>bootstrap/css/style.css" media="screen"/>
<?php
if (is_file("{$config['PATH_FILE']}{$controllerName}.css")) {
    echo '<link rel="stylesheet" type="text/css" href="'.$config['PATH_CSS'].$controller.'.css" media="screen"/>';
}
?>
        <script type="text/javascript" src="<?php echo $config['PATH_JS'];?>jquery.min.js"></script>
		<!-- Bootstrap JS - Latest compiled and minified JavaScript -->
		<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js" integrity="sha384-0mSbJDEHialfmuBBQP6A4Qrprq5OVfW37PRR3j5ELqxss1yVqOtnepnHVP9aJ7xS" crossorigin="anonymous"></script>
<!--
        <link rel="stylesheet" href="<?php echo $config['PATH_JS'];?>jquery-ui-1.11.2.custom/jquery-ui.css">
        <script type="text/javascript" src="<?php echo $config['PATH_JS'];?>jquery.min.js"></script>
        <script type="text/javascript" src="<?php echo $config['PATH_JS'];?>jquery-ui-1.11.2.custom/jquery-ui.js"></script>
-->
        <script type="text/javascript">
            var app_http = '<?php echo $app_http;?>';
        </script>
        <script type="text/javascript" src="<?php echo $config['PATH_THEMES'];?>bootstrap/js/default.js"></script>

<?php
/*
if ($controllerName != 'default' && is_file("{$config['PATH_FILE']}resources/js/{$controllerName}.js")) {
    echo '<script type="text/javascript" src="'.$config['PATH_JS'].$controllerName.'.js"></script>';
}
*/
?>
        <link rel="shortcut icon" href="ico/favicon.ico">
    </head>
    <body>
        <div id="theModal" class="modal fade" tabindex="-1" role="dialog">
			<div class="modal-dialog">
				<div class="modal-content">
            		<div class="modal-header">
                		<button type="button" class="close" data-dismiss="modal" aria-label="Close">
							<span aria-hidden="true">&times;</span>
						</button>
					</div>
					<div class="modal-body"></div>
				</div>
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
        <div id="systemBar" class="clearfix">
<?php
//present any system messages
echo '      <div class="sysMsg col-sm-10">';
if (isset($systemMessages)) {
	foreach ($systemMessages as $sysMsg) {
        $typeMap = array('error'=>'danger');
        $msgType = $sysMsg->getType();
        if (array_key_exists($msgType,$typeMap)) {
            $msgType = $typeMap[$msgType];
        }
		echo "    <div class=\"alert alert-{$msgType}\">{$sysMsg->getMessage()}</div>";
	}
}
echo '      </div>';
if ($globalUser->isLoggedIn()) {
    echo '  <div class="col-sm-2">
				<span>Hi <a href="'.$config['PATH_HTTP'].'user.php?action=edit">'.$globalUser->getProfileValue('username').'</a>! (<a href="'.$config['PATH_HTTP'].'user.php?action=logout">logout</a>)</span>
			</div>';
}

?>
        </div>
        <div class="container clearfix">
<?php
if (!empty($page)) {
    if (isset($page['title'])) {
		echo "<div class=\"page-header\">
            	<h1>{$page['title']}</h1>
			</div>";
    }
    echo '  <div id="subNav" class="row">';
    if (isset($page['navigation'])) {
        echo "  <div class=\"col col-sm-8\">
					<ul class=\"nav nav-pills\">";
    	foreach ($page['navigation'] as $subnav) {
            $isCurrent = (isset($data['action']) && isset($subnav['action']) && $subnav['action'] == $data['action']) || (!isset($data['action']) && !isset($subnav['action']));
    		echo "		<li".(($isCurrent) ? ' class="active"':'').">
							<a class=\"capitalize".(isset($subnav['modal']) ? ' do-loadmodal':'')."\" href=\"{$app_http}".((isset($subnav['action'])) ? "?action={$subnav['action']}":'')."\">{$subnav['name']}</a>
						</li>";
    	}
        echo '  	</ul>
				</div>';
    }
    if (isset($page['search'])) {
        echo '  <div class="col col-sm-4">
					<form id="doSearch" class="do-get" name="search" method="POST" action="'.$app_http.'">
                    	<input type="hidden" name="action" value="search" />
						<div class="input-group">
                    		<input id="searchTerm" class="form-control" type="text" name="term" />';
        echo '		      	<span class="input-group-btn">
								<input id="searchResults" class="btn btn-default" type="submit" name="submit" value="Search" />
							</span>
						</div>
	                    <div class="inline-block" id="searchStatus">
	                        <a href="#clearSearch">clear search</a>
	                    </div>
	                </form>
				</div>';
    }

    echo '    </div>';
}
echo '		<div id="modalContent">';
if (isset($page['subtitle']) && $page['subtitle']) {
	echo "     	<div class=\"page-header\">
					<h1 class=\"capitalize\"><small>{$page['subtitle']}</small></h1>
				</div>";
}

?>
