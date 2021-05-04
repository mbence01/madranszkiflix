<?php
	session_start();
	error_reporting(E_ALL);

	function redirectUser($path) {
		echo "<script type='text/javascript'>window.location.href = '".$path."'</script>";
	}
	
	setcookie("seek", "-1");

	function mainPage() {
		if(!isset($_SESSION["logged"]) || $_SESSION["logged"] == NULL) redirectUser("index.php?page=login&redirect=index.php");
		?>
			<div class="switch-type-div">
				<button onclick="location.href='index.php?page=series'" class="switch-type-btn">Sorozatok</button>
				<button onclick="location.href='index.php?page=movies'" class="switch-type-btn">Filmek</button>
			</div>
		<?php
	}
	
	function logOut() {
			$_SESSION["logged"] = NULL;
			$_SESSION["from"] = NULL;
			redirectUser("index.php");
		//if(isset($_SESSION["logged"]) AND $_SESSION["logged"] == "true") {
		//}	
	}
	
	function logIn() {
		if(isset($_SESSION["logged"]) AND $_SESSION["logged"] == "true") redirectUser("index.php");	
		
		$r = "index.php";

		if($_SERVER["REQUEST_METHOD"] == "POST") {
			if($_POST["user"] == "madranszki" AND $_POST["pass"] == "Bence200109") {
				$_SESSION["logged"] = "true";
				redirectUser($_POST["red"]);
			} else {
				echo "<script>alert('Helytelen felhasználónév vagy jelszó!'); location.href=history.back();</script>";
			}
		} else {
			if($_GET["redirect"] == "choosepart") {
				$r = "index.php?page=choosepart&dir=" . $_GET["val"];
			} else if($_GET["redirect"] == "watchm") {
				$r = "index.php?page=watchm&dir=" . $_GET["val"];
			} else if($_GET["redirect"] == "watchs") {
				$r = "index.php?page=watchs&dir=" . $_GET["val"] . "&part=" . $_GET["part"];
			} else if($_GET["redirect"] == "series") {
				$r = "index.php?page=series";
			} else if($_GET["redirect"] == "movies") {
				$r = "index.php?page=movies";
			}
				
			if(!isset($_SESSION["page"]) || (isset($_SESSION["page"]) AND $_SESSION["page"] != "elearning")) {
				if($_SERVER["REMOTE_ADDR"] == "176.63.31.11" || $_SERVER["REMOTE_ADDR"] == "176.63.31.219" || $_SERVER["REMOTE_ADDR"] == "176.63.30.253" || $_SERVER["REMOTE_ADDR"] == "176.63.31.103") {
					$_SESSION["logged"] = "true";
					$_SESSION["from"] = "Laura";
					redirectUser($r);
				} else if($_SERVER["REMOTE_ADDR"] == "46.139.73.4" || $_SERVER["REMOTE_ADDR"] == "127.0.0.1" || $_SERVER["REMOTE_ADDR"] == "192.168.0.1") {
					$_SESSION["logged"] = "true";
					$_SESSION["from"] = "Bence";
					redirectUser($r);
				} else if($_SERVER["REMOTE_ADDR"] == "31.46.96.252") {
					$_SESSION["logged"] = "true";
					$_SESSION["from"] = "Mák";
				redirectUser($r);
				}else if($_SERVER["REMOTE_ADDR"] == "77.110.187.31") {
					$_SESSION["logged"] = "true";
					$_SESSION["from"] = "Káloz";
					redirectUser($r);
				}else if($_SERVER["REMOTE_ADDR"] == "176.63.3.23") {
					$_SESSION["logged"] = "true";
					$_SESSION["from"] = "Máté";
					redirectUser($r);
				}else if($_SERVER["REMOTE_ADDR"] == "84.224.76.62") {
					$_SESSION["logged"] = "true";
					$_SESSION["from"] = "Stella";
					redirectUser($r);
				}
			}
		}
		?>
			<div class="login-div">
				<table align="center" width="100%">
					<form action="index.php?page=login" method="post">
						<input type="hidden" name="red" value="<?php echo $r; ?>">
						<tr height="50">
							<td><img src="user.png" width="128"></td>
						</tr>
						<tr height="100">
							<td><input name="user" type="text" class="login-input" placeholder="Felhasználónév"><br></td>
						</tr>
						<tr height="100">
							<td><input name="pass" type="password" class="login-input" placeholder="Jelszó"><br></td>
						</tr>
						<tr height="100">
							<td><input name="submit" type="submit" class="login-submit" value="Bejelentkezés"></td>
						</tr>
					</form>
				</table>
			</div>
		<?php
	}
	
	function chooseSeriesPart() {
		$redirect = "index.php?page=login&redirect=choosepart&val=" . $_GET["dir"];
		if(!isset($_SESSION["logged"])) redirectUser($redirect);
		
		echo "<div class='list-records'>";
	
		$dir = array_diff(scandir($_GET["dir"]), array('..', '.'));
		
		foreach($dir as $key) {
			$f = fopen_($_GET["dir"] . "/conf.txt");
			
			fgets($f);
			$img = fgets($f);
			
			fclose($f);
			
			if(strpos($key, ".mp4") !== false || strpos($key, ".mkv") !== false || strpos($key, ".avi") !== false) {
				$s = 0;
				echo "<div class='record' style='position: relative;'>";
				echo "<a href='index.php?page=watchs&dir=".$_GET["dir"]."&part=".$key."&red=0'>";
				echo "<img src='" . $_GET["dir"] . "/".$img."' width='100%'>";
				echo "<span class='record-span'><img src='icon.png' width='20' align='absmiddle'> ".pathinfo($key, PATHINFO_FILENAME)."</span>";
				echo "</a>";
				echo "</div>"; 
			}
		}
		
		echo "</div>";
	}
	
	function seriesPage() {		
		if(!isset($_SESSION["logged"])) redirectUser("index.php?page=login&redirect=series");
		
		echo "<div class='list-records'>";
	
		$dir = array_diff(scandir('.'), array('..', '.'));
		foreach($dir as $key) {
			if(is_dir($key) AND $key != "yt2mp3") {
				$conf = fopen_($key . "/conf.txt");
				if(strpos(fgets($conf), "TYPE=SERIES") !== false) {
					echo "<div class='record' style='position: relative;'>";
					echo "<a href='index.php?page=choosepart&dir=".$key."'>";
					echo "<img src='".$key."/".fgets($conf)."' width='100%'>";
					echo "<span class='record-span'>".fgets($conf)."</span>";
					echo "</a>";
					echo "</div>"; 
				}
				fclose($conf);
			}
		}
		
		echo "</div>";
	}
	
	function moviesPage() {		
		if(!isset($_SESSION["logged"])) redirectUser("index.php?page=login&redirect=movies");
		
		echo "<div class='list-records'>";
	
		$dir = array_diff(scandir('.'), array('..', '.'));
		foreach($dir as $key) {
			if(is_dir($key) AND $key != "yt2mp3") {
				$conf = fopen_($key . "/conf.txt");
				if(strpos(fgets($conf), "TYPE=MOVIE") !== false) {
					echo "<div class='record'>";
					echo "<a href='index.php?page=watchm&dir=".$key."'>";
					echo "<img src='".$key."/".fgets($conf)."' title='".fgets($conf)."' width='100%'>";
					echo "</a>";
					echo "</div>"; 
				}
				fclose($conf);
			}
		}
		
		echo "</div>";
	}
	
	function watchMovie() {
		$redirect = "index.php?page=login&redirect=watchm&val=" . $_GET["dir"];
		if(!isset($_SESSION["logged"])) redirectUser($redirect);
		
		if(isset($_GET["dir"])) {
			$f = fopen_($_GET["dir"] . "/conf.txt");
			fgets($f); 
			setcookie("img-src", fgets($f));
			$title = fgets($f);
			$filename = fgets($f);
			$sub = fgets($f);
			fclose($f);
			echo "<div style='text-align: center;'>";
			echo "<h1 class='video-title'>".$title."</h1>";
			echo "<table width='100%' align='center'><tr><td style='width:25%;'></td><td>";
			echo "<div id='video-div'><video onloadeddata='setTime()' onloadedmetadata='getDuration()' width='100%' id='myVideo' autoplay>";
			echo "<source src='".$_GET["dir"]."/".$filename."' type='video/mp4'>";
			echo "<track label='Magyar' kind='subtitles' srclang='hu' src='".$sub."' default>";
			echo "</video><div id='video-controls'>";
			echo "<table width='100%' style='height:100%;vertical-align:middle;text-align: center;'>";
			echo "<tr><td><img id='play' src='pause.png' width='20' align='absmiddle'></td><td><span id='duration-span'>0:00 / 0:00</span></td><td><img id='volume' src='volume.png' width='20' align='absmiddle' style='transform: scaleX(-1);'> <span id='volume-span'>100%</span></td><td><img id='fullscreen' src='full.png' width='16' align='absmiddle'></td></tr>";
			echo "<tr><td colspan='4'><progress id='progress-bar' value='0' max='100'></progress></td></tr>";
			echo "</table></div>";
			echo "</td><td style='width:25%;'></td></tr></table>";
			echo "</div></div><br><br><br>";
		} else header("location: index.php");
	}
	
	function watchSeries() {
		$redirect = "index.php?page=login&redirect=watchs&val=" . $_GET["dir"] . "&part=" . $_GET["part"];
		if(!isset($_SESSION["logged"])) redirectUser($redirect);
		
		if(isset($_GET["dir"]) && isset($_GET["part"])) {
			$prev = "";
			$c = 0;
			$p_set = 0;
			$changed = 0;
			$x = 1;
			$red = array();
			$dir = array_diff(scandir($_GET["dir"]), array('..','.'));
			$part = $_GET["part"];
			while($x) {
				foreach($dir as $key) {
					if(is_dir($key) || (strpos($key, ".mp4") === false && strpos($key, ".mkv") === false)) continue;
					
					if($key == $part) {
						$c = 998;
						$p_set = 1;
					}
					
					if(!$p_set) $prev = $key;
					
					if($c == 999) {
						$changed = 1;
						array_push($red, $key);
						$part = $key;
					}
					$c++;
				}
				if($changed == 0 AND $c == 999) $x = 0;
				$changed = 0;
				$c = 0;
			}
			$f = fopen_($_GET["dir"] . "/conf.txt");
			fgets($f); fgets($f);
			$title = fgets($f);
			fclose($f);
			
			echo "<div style='text-align: center; background: rgba(0,0,0,.4); height: 100%'>";
			
			if(strpos($_SERVER["HTTP_USER_AGENT"], "SMART-TV") !== false) echo "<table align='center' width='100%'>";
			else echo "<table align='center' width='100%'>";
			
			echo "<tr><td colspan='3'><center><h1 class='video-title'>".pathinfo($_GET["part"], PATHINFO_FILENAME)."</h1></center></td></tr>";
			
			if(strpos($_SERVER["HTTP_USER_AGENT"], "SMART-TV") !== false) {
				echo "<tr><td style='width: 5%'></td><td>";
			} else {
				if($prev != "") echo "<tr><td style='text-align:center;'><a id='back' href='index.php?page=watchs&dir=".$_GET["dir"]."&part=".$prev."&red=0'><img id='back-img' src='arrow.png' style='transform: scaleX(-1); -webkit-transform: scaleX(-1);' width='128'></a></td><td>";
				else echo "<tr><td style='text-align:center;'><a id='back' href='#'><img id='back-img' src='null.png' width='128'></a></td><td>";
			}
			
			echo "<div onloaded='setBg()' id='video-div'><video onloadeddata='setTime()' onloadedmetadata='getDuration()' width='100%' id='myVideo' onended='endedVid()' autoplay>";
			echo "<source src='".$_GET["dir"]."/".$_GET["part"]."' type='video/mp4'>";
			echo "</video><div id='video-controls'>";
			echo "<table width='100%' style='height:100%;vertical-align:middle;text-align: center;'>";
			echo "<tr><td><img id='play' src='pause.png' width='20' align='absmiddle'></td><td><span id='duration-span'>0:00 / 0:00</span></td><td><img id='volume' src='volume.png' width='20' align='absmiddle' style='transform: scaleX(-1);'> <span id='volume-span'>100%</span></td><td><img id='fullscreen' src='full.png' width='16' align='absmiddle'></td></tr>";
			echo "<tr><td colspan='4'><progress id='progress-bar' value='0' max='100'></progress></td></tr>";
			echo "</table>";
			echo "</div>";
			echo "<div id='next-part-div'><img align='absmiddle' width='20' src='icon.png'> <span style='vertical-align:middle;'>Következő rész</span></div>";
			echo "</div></td>";
			
			if(strpos($_SERVER["HTTP_USER_AGENT"], "SMART-TV") !== false) {
				echo "<td style='width: 5%'></td></tr></table>";
			} else {
				if(count($red) > 0) echo "<td style='text-align:center;'><a id='forward' href='index.php?page=watchs&dir=".$_GET["dir"]."&part=".$red[0]."&red=0'><img id='forward-img' src='arrow.png' width='128'></a></td></tr></table>";
				else echo "<td style='text-align:center;'><a id='forward' href='#'><img id='forward-img' src='null.png' width='128'></td></tr></table>";				
			}
			echo "</div><br><br><br>";

			echo "<script type='text/javascript'>";
			echo "function endedVid() { 
						$('#next-part-div').hide(500);
			
						var value = parseInt(getCookie('seek')) + 1;
						var array = ".json_encode($red).";
						if(value < array.length) {
							document.cookie = 'seek=' + value;
							var video = document.getElementById('myVideo');
							video.src = '".$_GET["dir"]."/' + array[parseInt(getCookie('seek'))];
							
							document.getElementById('back').setAttribute('href', 'index.php?page=watchs&dir=' + '".$_GET["dir"]."&part=' + array[(parseInt(getCookie('seek')) - 1)]);
							if((parseInt(getCookie('seek')) + 1) < array.length) document.getElementById('forward').setAttribute('href', 'index.php?page=watchs&dir=' + '".$_GET["dir"]."&part=' + array[(parseInt(getCookie('seek')) + 1)]);
							else {
								document.getElementById('forward').setAttribute('href', '#');
								document.getElementById('forward-img').src = 'null.png';
							}
							video.load();
							
							var x = document.getElementsByClassName('video-title');
							var y = array[parseInt(getCookie('seek'))];
							x[0].innerHTML = y.slice(0, y.length - 4);
							
							setTime();
						}
					}";
			echo "</script>";
		} else header("location: index.php");
	}
	
	function fopen_($fileName) { 
		$fc = iconv('windows-1250', 'utf-8', file_get_contents($fileName)); 
		$handle=fopen("php://memory", "rw"); 
		fwrite($handle, $fc); 
		fseek($handle, 0); 
		return $handle; 
	} 
?>

<html>
	<head>
		<title>MadranszkiFlix</title>
		<meta charset="UTF-8">
		
		<link rel="stylesheet" href="bootstrap.css">
		<link rel="stylesheet" href="style.css">
		<link rel="icon" href="icon.png">
		
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
		
		<link rel="preconnect" href="https://fonts.gstatic.com"> 
		<link href="https://fonts.googleapis.com/css2?family=Noto+Sans+JP&display=swap" rel="stylesheet">
		
		<script type="text/javascript">					
			$(document).ready(function(){
				var fullscreenControls = null;
				
				$("#video-controls").hide();
				$("#next-part-div").hide();
				
				
				
				$("#next-part-div").click(function(){
					window.location.href = $("#forward").attr("href");
				});
				
				$("#video-div").mouseenter(function(){
					$("#video-controls").fadeIn();
				}).mouseleave(function(){
					$("#video-controls").fadeOut();
				});
				
				$("#play").click(function(){
					var vid = $("#myVideo").get(0);
					if(!vid.paused) {
						vid.pause();
						$("#play").attr("src", "icon.png");
					} else {
						vid.play();
						$("#play").attr("src", "pause.png");
					}
				});
				
				$("#fullscreen").click(function(){
					var elem = $("#video-div").get(0);
					if(elem.classList.contains("fullscreen-vid")) {
						if(document.exitFullscreen) {
							document.exitFullscreen();
						} else if(document.mozCancelFullScreen) {
							document.mozCancelFullScreen();
						} else if(document.webkitExitFullscreen) {
							document.webkitExitFullscreen();
						}
						elem.classList.remove("fullscreen-vid");
						document.getElementById("myVideo").classList.remove("fullscreen-vid");
					} else {
						if (elem.requestFullscreen) {
						  elem.requestFullscreen();
						} else if (elem.mozRequestFullScreen) {
						  elem.mozRequestFullScreen();
						} else if (elem.webkitRequestFullscreen) {
						  elem.webkitRequestFullscreen();
						} else if (elem.msRequestFullscreen) { 
						  elem.msRequestFullscreen();
						}
						elem.classList.add("fullscreen-vid");
						document.getElementById("myVideo").classList.add("fullscreen-vid");
						
						$("#video-controls").hide();
					}
				});
				
				$("#video-div").mousemove(function(){
					var vdiv = $("#video-div").get(0);
					if(vdiv.classList.contains("fullscreen-vid")) {
						$("#video-controls").fadeIn();
						
						clearTimeout(fullscreenControls);
						fullscreenControls = setTimeout(function(){ $("#video-controls").fadeOut(); }, 3000);
					}
				});
				
				$("#progress-bar").click(function(e){
					var elementX = Math.round($("#progress-bar").offset().left);
					var clickedX = e.pageX;
					
					var val = Math.floor(($("#progress-bar").attr("max") / $("#progress-bar").width()) * (clickedX - elementX));
					
					document.getElementById("myVideo").currentTime = val;
					$("#progress-bar").attr("value", val.toString());
					
				});
			}); 
			
			document.onkeydown = function(e) {
				var vid = document.getElementById("myVideo");
				switch(e.which) {
					case 37: // left
						if(vid.currentTime < 5) vid.currentTime = 0;
						else vid.currentTime -= 5;
						e.preventDefault();
						break;
					case 39: // right
						if((vid.duration - vid.currentTime) < 5) vid.currentTime = vid.duration;
						else vid.currentTime += 5;
						e.preventDefault();
						break;
					case 32: // space
						$("#play").click();
						e.preventDefault();
						break;
					case 38:
						if(vid.volume > 0.95) vid.volume = 1;
						else vid.volume += 0.05;
						document.getElementById("volume-span").innerHTML = Math.floor(document.getElementById("myVideo").volume * 100) + "%";
						e.preventDefault();
						break;
					case 40:
						if(vid.volume < 0.05) vid.volume = 0;
						else vid.volume -= 0.05;
						document.getElementById("volume-span").innerHTML = Math.floor(document.getElementById("myVideo").volume * 100) + "%";
						e.preventDefault();
						break;
				}
			};
			
			document.addEventListener('fullscreenchange', exitHandler);
			document.addEventListener('webkitfullscreenchange', exitHandler);
			document.addEventListener('mozfullscreenchange', exitHandler);
			document.addEventListener('MSFullscreenChange', exitHandler);

			function exitHandler() {
				if (!document.fullscreenElement && !document.webkitIsFullScreen && !document.mozFullScreen && !document.msFullscreenElement) {
					document.getElementById("myVideo").classList.remove("fullscreen-vid");
					document.getElementById("video-div").classList.remove("fullscreen");
				}
			}  
			
			var videoduration = "";
			
			function setTime() {
				videoduration = "";
				var dur2 = document.getElementById("myVideo").duration;
				var res3 = Math.floor(dur2/60); //perc
				var res4 = Math.floor(dur2%60); //masodperc
				var res5 = Math.floor(res3/60); //ora
				if(res5 > 0) res3 -= res5*60;
				videoduration += "0" + res5.toString() + ":";
				if(res3 > 0) {
					if(res3 < 10) {
						videoduration += "0" + res3.toString() + ":";
					} else videoduration += res3.toString() + ":";
				} else {
					videoduration += "00:";
				}
				
				if(res4 < 10) {
						videoduration += "0" + res4.toString();
				} else videoduration += res4.toString();
				
				document.getElementById("progress-bar").setAttribute("max", Math.floor(dur2).toString());
			}
			
			function getDuration() {
				var dur = document.getElementById("myVideo").currentTime;
				var str = "";
				var res = Math.floor(dur/60);
				var res2 = Math.floor(dur%60);
				var res3 = Math.floor(res/60); // ora
				if(res3 > 0) res -= res3*60;
				//currenttime
				str += "0" + res3.toString() + ":";
				if(res > 0) {
					if(res < 10) {
						str += "0" + res.toString() + ":";
					} else str += res.toString() + ":";
				} else {
					str += "00:";
				}
				
				if(res2 < 10) {
						str += "0" + res2.toString();
				} else str += res2.toString();
				
				str += " / " + videoduration;
				
				document.getElementById("progress-bar").setAttribute("value", Math.floor(dur).toString());
				
				document.getElementById("duration-span").innerHTML = str;
				
				if(Math.floor(document.getElementById("myVideo").duration) - Math.floor(dur) == 30) $("#next-part-div").fadeIn(1000);
			
				setTimeout(getDuration, 500);
			}
		
			function getCookies() {
				var c = document.cookie, v = 0, cookies = {};
				if (document.cookie.match(/^\s*\$Version=(?:"1"|1);\s*(.*)/)) {
					c = RegExp.$1;
					v = 1;
				}
				if (v === 0) {
					c.split(/[,;]/).map(function(cookie) {
						var parts = cookie.split(/=/, 2),
							name = decodeURIComponent(parts[0].trimLeft()),
							value = parts.length > 1 ? decodeURIComponent(parts[1].trimRight()) : null;
						cookies[name] = value;
					});
				} else {
					c.match(/(?:^|\s+)([!#$%&'*+\-.0-9A-Z^`a-z|~]+)=([!#$%&'*+\-.0-9A-Z^`a-z|~]*|"(?:[\x20-\x7E\x80\xFF]|\\[\x00-\x7F])*")(?=\s*[,;]|$)/g).map(function($0, $1) {
						var name = $0,
							value = $1.charAt(0) === '"'
									  ? $1.substr(1, -1).replace(/\\(.)/g, "$1")
									  : $1;
						cookies[name] = value;
					});
				}
				return cookies;
			}
			function getCookie(name) {
				return getCookies()[name];
			}
		</script>
	</head>
	<body>
		<div class="bg-img" style="background-image: url('<?php if($_GET["page"] == "watchm" || $_GET["page"] == "watchs") echo $_GET["dir"] . "/img.jpg"; ?>');"></div>
		<header class="row">
			<div class="container-fluid">
				<div class="col-xs-3">
					<a href="index.php">
						<h1 class="header-title">
							<img src="icon.png" align="absmiddle" width="64"> MadranszkiFlix
						</h1><br>
					</a>
				</div>
				<div class="col-xs-8 header-nav">
					<a href="index.php?page=series">Sorozatok</a>
					<a href="index.php?page=movies">Filmek</a>
					<a href="/elearning">E-learning</a>
					<?php 
						if(isset($_SESSION["logged"]) AND $_SESSION["logged"] == "true") {
							if(isset($_SESSION["from"]) AND $_SESSION["from"] == "Laura") echo "<span style='font-size: 13px; color: rgba(255,255,255,.5);'>Bejelentkezve innen: Laura</span>";
							else if(isset($_SESSION["from"]) AND $_SESSION["from"] == "Bence") echo "<span style='font-size: 13px; color: rgba(255,255,255,.5);'>Bejelentkezve innen: Bence</span>";
							else if(isset($_SESSION["from"]) AND $_SESSION["from"] == "Mák") echo "<span style='font-size: 13px; color: rgba(255,255,255,.5);'>Bejelentkezve innen: MákBence</span>";
							else if(isset($_SESSION["from"]) AND $_SESSION["from"] == "Káloz") echo "<span style='font-size: 13px; color: rgba(255,255,255,.5);'>Bejelentkezve innen: Káloz</span>";
							else if(isset($_SESSION["from"]) AND $_SESSION["from"] == "Máté") echo "<span style='font-size: 13px; color: rgba(255,255,255,.5);'>Bejelentkezve innen: Máté</span>";
							else if(isset($_SESSION["from"]) AND $_SESSION["from"] == "Stella") echo "<span style='font-size: 13px; color: rgba(255,255,255,.5);'>Bejelentkezve innen: Stella</span>";
							else echo "<a href='index.php?page=logout'>Kijelentkezés</a>"; 
						}
						
						//if(isset($_SESSION["logged"])) echo " <span id='imhigh' style='color: darkgreen;text-shadow: 1px 1px 3px black;'>I'm high</span>";
					?>
				</div>
				<div class="col-xs-1">
					<?php if(isset($_SESSION["logged"]) AND $_SESSION["logged"] == "true") 
						echo "<a href='index.php?page=profile'>
						<img src='user.png' width='100' align='absmiddle'>
						</a>"; ?>
				</div>
			</div>
		</header>
	
		<?php
			if(isset($_GET["page"])) {
				switch($_GET["page"]) {
					case "main":
						mainPage();
						break;
					case "series":
						seriesPage();
						break;
					case "movies":
						moviesPage();
						break;
					case "choosepart":
						chooseSeriesPart();
						break;
					case "watchm":
						watchMovie();
						break;
					case "watchs":
						watchSeries();
						break;
					case "login":
						logIn();
						break;
					case "logout":
						logOut();
						break;
					default:
						mainPage();
						break;
				}
			} else mainPage();
		?>
	</body>
</html>
