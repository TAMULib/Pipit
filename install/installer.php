<?php

function executeQuery($handle,$sql,$bindparams=NULL) {
	$result = $handle->prepare($sql);
	if ($result->execute($bindparams)) {
		return true;
	} else {
		return $result->errorInfo();
	}
	return false;
}

if (!empty($_POST['user'])) {
	$dbConfig = $_POST['config']['db'];
	$user = $_POST['user'];
	$dsn = 'mysql:host='.$dbConfig['host'].';dbname='.$dbConfig['database'];
	$opt = array(
		    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
		    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
	);
    try {
		$handle = new PDO($dsn, $dbConfig['user'], $dbConfig['password'],$opt);
 		$user['password'] = password_hash($user['password'], PASSWORD_DEFAULT);
		$sql = "INSERT INTO `users` (`username`,`password`) VALUES (:username,:password)";
		$bindparams = array(":username"=>$user['username'],":password"=>$user['password']);
		if (executeQuery($handle,$sql,$bindparams)) {
			echo json_encode(array("result"=>1));
		} else {
			echo json_encode(array("result"=>0));
		}
	} catch (PDOException $e) {
		echo json_encode(array("result"=>$e->getMessage()));
	}
} elseif (!empty($_POST['config'])) {
	$config = $_POST['config'];
	$config['path_http'] = $config['path_http'].$config['app']['directory'].'/';
	$config['path_file'] = $config['path_root'].$config['app']['directory'].'/';
	$config['path_app'] = "{$config['path_file']}_application/";
	$config['path_lib'] = "{$config['path_app']}lib/";
	$config['path_classes'] = "{$config['path_app']}classes/";
	$config['path_controllers'] = "{$config['path_app']}controllers/";
	$config['path_views'] = "{$config['path_app']}views/";
	$config['path_css'] = "{$config['path_http']}_application/css/";
	$config['path_js'] = "{$config['path_http']}_application/js/";
	$config['path_images'] = "{$config['path_http']}_application/images/";
	$filename = $config['path_app']."/config/config.json";
	if (file_put_contents($filename, json_encode($config))) {
		$result = 1;
	} else {
		$result = 0;
	}
	echo json_encode(array("result"=>$result));
} elseif (!empty($_POST['dbmaster']) && !empty($_POST['dbcreate'])) {
	$dbConfig = $_POST['dbmaster'];
	$dbCreate = $_POST['dbcreate'];
	$filename = "phpseedapp.sql";
	if (is_file($filename)) {
		$dsn = 'mysql:host='.$dbConfig['host'].';';
		$opt = array(
		    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
		    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
		);
	    try {
			$handle = new PDO($dsn, $dbConfig['user'], $dbConfig['password'],$opt);
			$handle->beginTransaction();
			$sql = "CREATE DATABASE IF NOT EXISTS `{$dbCreate['database']}` DEFAULT CHARACTER SET latin1 COLLATE latin1_swedish_ci;";
			if (executeQuery($handle,$sql)) {
				$sql = "USE `{$dbCreate['database']}`;";
				if (executeQuery($handle,$sql)) {
					$sql = "CREATE USER :newuser@:host IDENTIFIED BY :password";
					$bindparams = array(":newuser"=>$dbCreate['newuser'],":host"=>$dbConfig['host'],":password"=>$dbCreate['password']);
					if (executeQuery($handle,$sql,$bindparams)) {
						$sql = "FLUSH PRIVILEGES;";
						if (executeQuery($handle,$sql)) {
							$handle->commit();
							$command='mysql --protocol=TCP -h ' .$dbConfig['host'].' -u '.$dbConfig['user'].' -p'.$dbConfig['password'].' '.$dbCreate['database'].' < '.$filename;
							exec($command,$output=array(),$result);
							if ($result == 0) {
								$sql = "GRANT ALL PRIVILEGES ON {$dbCreate['database']}.* TO  :newuser@:host IDENTIFIED BY :password 
										WITH GRANT OPTION MAX_QUERIES_PER_HOUR 0 MAX_CONNECTIONS_PER_HOUR 0 MAX_UPDATES_PER_HOUR 0 MAX_USER_CONNECTIONS 0";
								if (executeQuery($handle,$sql,$bindparams)) {
									echo json_encode(array("result"=>$result));
								} else {
									echo json_encode(array("result"=>1));
								}
							}
						}
					}
				}
			}
		} catch (PDOException $e) {
			$handle->rollBack();
			echo json_encode(array("result"=>$e->getMessage()));
		}
	} else {
		echo "Couldn't find the import SQL file";
	}
} elseif (!empty($_POST['dbconfig'])) {
	$dsn = 'mysql:host='.$_POST['dbconfig']['host'].';';
	$opt = array(
	    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
	    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
	);
    try {
		$handle = new PDO($dsn, $_POST['dbconfig']['user'], $_POST['dbconfig']['password'],$opt);
		echo json_encode(array("result"=>1));
	} catch (PDOException $e) {
		echo json_encode(array("result"=>$e->getMessage()));
	}
} else {
?>
<html>
	<head>
		<style type="text/css">
			html, body {
				margin: 0px;
				padding: 0px;
				font-family: Arial;
			}

			body {
				background-color: #efefef;
			}

			h4 {
				margin-bottom: 8px;
			}

			input,label {
				display: block;
				margin: 8px 8px 0px 8px;
				padding: 12px;
			}

			label {
				padding-bottom: 4px;
			}

			input {
				margin-top: 4px;
			}

			.column {
				display: inline-block;
				vertical-align: top;
				width: 200px;
				margin: 20px;
			}

			.hidden {
				display: none;
			}

			#dbResults {
				border: 1px solid #000;
				background-color: #fff;
				margin-top: 24px;
				padding: 10px;
				height: 300px;
				overflow-y: auto;
				font-family: Consolas, Menlo, Monaco, Lucida Console, Liberation Mono, DejaVu Sans Mono, Bitstream Vera Sans Mono, monospace, serif;
			}

			.help {
				font-size: .7em;
			}
			
			#goLogin {
				font-size: 1.8em;
				text-align: center;
			}

			a {
				text-decoration: none;
				color: #555;
			}

			a:hover {
				text-decoration: underline;
			}

		</style>
		<script type="text/javascript" src="../_application/js/jquery.min.js"></script>
	<head>
	<body>
		<div class="column">
			<h4>Install Operation Results:</h4>
			<div id="dbResults"></div>
		</div>
		<div class="column">
			<h4>1) Test MySQL Connection:</h4>
			<div class="help">
				<ul>
					<li>Enter an existing MySQL user</li>
					<li>If you need a new database created, the user should be able to create databases and users.</li>
					<li>If your database is already set up, the user should have privileges on it.</li>
				</ul>
			</div>
			<form id="testConnection" name="testconnection" method="POST">
				<label for="dbconfig[host]">Host</label>
				<input id="dbConfigHost" type="text" name="dbconfig[host]" value="localhost" />
				<label for="dbconfig[user]">User</label>
				<input id="dbConfigUser" type="text" name="dbconfig[user]" />
				<label for="dbconfig[password]">Password</label>
				<input id="dbConfigPassword" type="password" name="dbconfig[password]" />
				<input type="submit" name="submitconfig" value="Test Connection" />
			</form>
		</div>
		<div class="column">
			<h4>2) Configure DB and User:</h4>
			<div class="help">
				<ul>
					<li>This will create a new database using the value of 'Database Name'</li>
					<li>As well as a new database user with the name you enter</li>
					<li>It will also grant them privileges on the newly created database.</li>
				</ul>
			</div>
			<form class="hidden" id="dbInstaller" name="installer" method="POST">
				<a id="skipDb" href="#">No thanks, my database is already set up.</a>
				<label for="dbcreate[newuser]">New User</label>
				<input id="dbCreateUser" type="text" name="dbcreate[newuser]" />
				<label for="dbcreate[password]">New User Password</label>
				<input id="dbCreatePassword" type="password" name="dbcreate[password]" />
				<label for="dbcreate[database]">Database Name</label>
				<input id="dbCreateDatabase" type="text" name="dbcreate[database]" />
				<input class="db-host" type="hidden" name="dbmaster[host]" />
				<input class="db-user" type="hidden" name="dbmaster[user]" />
				<input class="db-password" type="hidden" name="dbmaster[password]" />
				<input type="submit" name="submitInstall" value="Build Database" />
			</form>
		</div>
		<div class="column">
			<h4>3) Configure App:</h4>
			<div class="help">
				<ul>
					<li>You'll need to provide the full file path and http path to your web server's document root, as well as the
				subdirectory you installed the web app to. Best guesses are pre-filled for the file path and app subdirectory.
					</li>
					<li>Also, the PHP user will need permissions to create a file under {app directory}/_application/config/</li>
					<li>If you're using a server that doesn't support Apache's .htaccess (like nginx), you'll probably want to take steps to make sure
						the generated JSON config file is not accessible via the web. This is already taken care of for servers that honor .htaccess.
					</li>
				</ul>
			</div>
<?php
$filepath = split('/',dirname(__FILE__));
$index = count($filepath)-1;
$paths['web'] = $filepath[$index-1];
$paths['docroot'] = '/';
for($x=($index-2);$x>0;$x--) {
	$paths['docroot'] .= $filepath[$x].'/';
}
?>
			<form class="hidden" id="configApp" name="configapp" method="POST">
				<label for="config[path_root]">Document Root</label>
				<input type="text" name="config[path_root]" value="<?php echo $paths['docroot'];?>" />
				<label for="config[path_http]">Base Domain</label>
				<input id="pathHttp" type="text" name="config[path_http]" value="http://" />
				<label for="config[app][directory]">App Directory</label>
				<input id="pathAppDir" type="text" name="config[app][directory]" value="<?php echo $paths['web'];?>"/>
				<label for="config[app][title]">App Title</label>
				<input type="text" name="config[app][title]" value="PHP Seed App" />
				<label for="config[db][database]">Database</label>
				<input class="db-database" id="configDatabase" type="text" name="config[db][database]" />
				<input class="db-host" id="configHost" type="hidden" name="config[db][host]" />
				<input class="db-new-user" id="configUser" type="hidden" name="config[db][user]" />
				<input class="db-new-password" id="configPassword" type="hidden" name="config[db][password]" />
				<input type="submit" name="submitconfig" value="Generate Config File" />
			</form>
		</div>
		<div class="column">
			<h4>4) Create App User:</h4>
			<div class="help">
				<ul>
					<li>This is the username and password you'll log into the app with.</li>
				</ul>
			</div>
			<form class="hidden" id="createUser" name="createuser" method="POST">
				<a id="skipToLogin" href="">No, thanks. Just take me to the app.</a>
				<label for="user[username]">User Name</label>
				<input type="text" name="user[username]" />
				<label for="user[password]">Password</label>
				<input id="password" type="text" name="user[password]" />
				<label for="password_confirm">Confirm Password</label>
				<input id="confirmPassword" type="text" name="confirm_password" />
				<input class="db-host" id="configHost" type="hidden" name="config[db][host]" />
				<input class="db-database" id="configDatabase" type="hidden" name="config[db][database]" />
				<input class="db-new-user" id="configUser" type="hidden" name="config[db][user]" />
				<input class="db-new-password" id="configPassword" type="hidden" name="config[db][password]" />
				<input type="submit" name="submituser" value="Create User" />
			</form>
			<div class="hidden" id="goLogin">
				<a href="">Login</a>
			</div>
		</div>
		<script type="text/javascript">
			$(document).ready(function() {
				$("#skipDb").click(function(e) {
					e.preventDefault();
					$(".db-host").val($("#dbConfigHost").val());
					$(".db-new-user").val($("#dbConfigUser").val());
					$(".db-new-password").val($("#dbConfigPassword").val());
					$("#dbInstaller").fadeOut("fast",function() {
						$("#configApp").fadeIn("fast");
					});
				});

				$("#createUser").submit(function() {
					if ($("#password").val() == $("#confirmPassword").val()) {
						$.ajax({type:"POST",url:"<?php echo $_SERVER['PHP_SELF'];?>",data:$(this).serialize()}).done(function(data) {
							if (data) {
								var user = JSON.parse(data);
								if (parseInt(user.result) == 1) {
									message = "User added!";
									$("#createUser").fadeOut("fast",function() {
										$("#goLogin a").attr("href",$("#pathHttp").val()+$("#pathAppDir").val());
										$("#goLogin").fadeIn("fast");
									});
								} else {
									message = "There was an error adding the user: "+user.result;
								}
								$("#dbResults").html(message);
							} else {
								$("#dbResults").html("HTTP Error. Try again?");
							}
						});
					} else {
						alert("Password confirmation doesn't match. You won't be able to log into the app if you don't remember the password you're entering");
					}
					return false;
				});

				$("#configApp").submit(function() {
					$.ajax({type:"POST",url:"<?php echo $_SERVER['PHP_SELF'];?>",data:$(this).serialize()}).done(function(data) {
						if (data) {
							var imported = JSON.parse(data);
							if (parseInt(imported.result) == 1) {
								message = "Configuration file generated!";
								$("#configApp").fadeOut("fast");
								$("#createUser").fadeIn("fast");
								$("#skipToLogin").attr("href",$("#pathHttp").val()+$("#pathAppDir").val());
							} else {
								message = "There was an error building the configuration file...";
							}
							$("#dbResults").html(message);
						} else {
							$("#dbResults").html("HTTP Error. Try again?");
						}
					});
					return false;
				});

				$("#dbInstaller").submit(function() {
					$.ajax({type:"POST",url:"<?php echo $_SERVER['PHP_SELF'];?>",data:$(this).serialize()}).done(function(data) {
						if (data) {
							var imported = JSON.parse(data);
							if (parseInt(imported.result) == 0) {
								message = "Database successfully built!";
								$("#dbInstaller").fadeOut("fast");
								$(".db-database").val($("#dbCreateDatabase").val());
								$(".db-new-user").val($("#dbCreateUser").val());
								$(".db-new-password").val($("#dbCreatePassword").val());
								$("#configApp").fadeIn("fast");
							} else {
								message = "There was an error building the database: "+imported.result;
							}
							$("#dbResults").html(message);
						} else {
							$("#dbResults").html("HTTP Error. Try again?");
						}
					});
					return false;
				});

				$("#testConnection").submit(function() {
					flag = false;
					$(this).children("input").each(function() {
						if (!$(this).val() && $(this).attr("type") != 'password') {
							flag = true;
							return false;
						}
					});

					if (!flag) {
						$.ajax({type:"POST",url:"<?php echo $_SERVER['PHP_SELF'];?>",data:$(this).serialize()}).done(function(data) {
							if (data) {
								test = JSON.parse(data);
								if (test.result == 1) {
									$(".db-host").val($("#dbConfigHost").val());
									$(".db-user").val($("#dbConfigUser").val());
									$(".db-password").val($("#dbConfigPassword").val());
									message = "DB connection successful!";
									$("#testConnection").fadeOut("fast");
									$("#dbInstaller").fadeIn("fast");
								} else {
									message = "Error connecting to the DB: "+test.result;
									$("#dbImport").fadeOut("fast");
								}
								$("#dbResults").html(message);
							} else {
								$("#dbResults").html("HTTP Error. Try again?");
							}
						});
					} else {
						alert("The Installer needs to know your MySQL host,Database name, user, and password");
					}
					return false;
				});
			});
		</script>
	</body>
</html>
<?php
}
?>