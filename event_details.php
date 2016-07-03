<?php
/*
+---------------------------------------------------------------+
|     Event-System for e107 v2
|        
|     a plugin for the e107 website system
|     http://www.e107.org/
|
|     © schlrech
|     rene.schlegel@sendto.ch
+---------------------------------------------------------------+
*/
if (!defined('e107_INIT'))
{
	require_once("../../class2.php");
}

if (!e107::isInstalled('events') or !check_class(e_UC_MEMBER))
{
	e107::redirect();
	exit;
}

require_once(HEADERF);
e107::lan('events');
e107::css('events','events.css');

$mes = e107::getMessage();
$frm = e107::getForm();
$sql = e107::getDB();
$log = e107::getLog();

$event_id 	= $_GET['id'];
$meld		= $_GET['meld'];

if ($_GET['action']=="details") {
	$akt_plaetze = $sql->count("events_registration", "(*)", " WHERE event_id = " . $_GET['id']);

	// Event
	$query = "SELECT e.*, u.user_login FROM #events as e 
				LEFT JOIN #user as u ON e.event_author = u.user_id
				WHERE e.id=" . $_GET['id'] . " GROUP BY u.user_id ORDER BY e.id ASC";
	$sql->gen($query);
	$row=$sql->Fetch(); 
	$event_max_places	= $row['event_max_places'];
	$free_plaetze		= $event_max_places - $akt_plaetze;
	$event_date			= date("d.m.Y", $row['event_date']);
	$event_deadline 	= date("d.m.Y", $row['event_deadline']);
	$titel 				= "&#034;" . $row['event_name'] . "&#034;";
	$event_details			= $tp->toHTML($row['event_details'], TRUE);
	$event_author		= $row['user_login'];
	$event_location		= $row['event_location'];
	$event_costs		= $row['event_costs'];
	$event_author_id	= $row['event_author'];
	
	$text = "<table border=0 class='table table-striped' style='".USER_WIDTH."'>";
	$text .= "
		<tr>
			<td>" . LAN_EVENT_42 . "</td>
			<td>" . $event_author . "</td>
		</tr>
		<tr >
			<td style='vertical-align:middle' width='150'>" . LAN_EVENT_26 . "</td>
			<td style='vertical-align:middle'>" . $event_date . "&nbsp;<a class='e-tip' href='event_ical.php?action=create&id=$event_id' title='" . LAN_EVENT_62 . "'><img src='images/event_32.png' alt='event_32.png' height='30' width='30'></a></td>
		</tr>
		<tr>
			<td>" . LAN_EVENT_63 . "</td>
			<td>" . $event_location . "</td>
		</tr>
		<tr>
			<td>" . LAN_EVENT_64 . "</td>
			<td>" . $event_costs . "</td>
		</tr>
		<tr>
			<td>" . LAN_EVENT_29 . "</td>
			<td>" . $event_deadline . "</td>
		</tr>
		<tr>
			<td>" . LAN_EVENT_41 . "</td>
			<td>" . $free_plaetze . "</font> / " . $event_max_places . "</td>
		</tr>
		<tr>
			<td>" . LAN_EVENT_14 . "</td>
			<td><a class='btn btn-xs btn-default e-tip' href='event_details.php?action=liste&id=$event_id' title='" . LAN_EVENT_14 . "'><span>" . $tp->toGlyph('fa-group') . "</span></a></td>
		</tr>
		<tr>
			<td>" . LAN_EVENT_31 . "</td>
			<td>" . $event_details . "</td>
		</tr>
		</table>
		<table align=center>";
		$sql->Select("events_registration", "*", " member_id=" . USERID . " AND event_id=" . $event_id);
		$row = $sql->Fetch();
		if ($row['member_id'] != USERID) {
			$angemeldet = false;
		}else{
			$angemeldet = true;
		}
		$text .= "<tr>
			<td colspan=10><center>";
		if ($angemeldet == false) {
			$text .= "
			<form action='event_details.php?action=checkin&id=$event_id' method='post'>
				<input class='btn btn-primary' type='submit' name='anmelden' value='".LAN_EVENT_45."' />";
		} else { 
			$text .= "	
			<form action='event_details.php?action=checkout&id=$event_id' method='post'>
				<input class='btn btn-primary' type='submit' name='abmelden' value='".LAN_EVENT_47."' />";
			// $text .= "	";
		}
	$text .=	"
			<a class='btn btn-primary' href='event_view.php' title='" . LAN_EVENT_51 . "'>" . LAN_EVENT_51 . "</a>";
			if (USERID == $event_author_id OR check_class(e_UC_ADMIN)){
				$text .= " <a class='btn btn-danger' href='event_details.php?action=sendmail&id=$event_id' title='" . LAN_EVENT_68 . "'>" . LAN_EVENT_68 . "</a>";
			}
$text .= "
		</form>
		</center></td>
		</tr>
	</table>";
}

if ($_GET['action']=="liste") {
	$sql->Select("events", "event_max_places, event_name", "id=" . $event_id);
	$row = $sql->fetch();
	$event_max_places	= $row['event_max_places'];
	$titel			= $row['event_name'];
	
	$query = "SELECT u.user_id, u.user_image, u.user_loginname, u.user_login FROM #user as u 
				LEFT JOIN #events_registration as r ON u.user_id = r.member_id 
				WHERE r.event_id=" . $event_id . " GROUP BY u.user_id ORDER BY r.id ASC";
	$counter = $sql->gen($query);

	$text = "<table border=0 class='table table-striped' style='".USER_WIDTH."'>";
	$text .="<tr>";
		
	// $text .= "<td colspan=10>" . $counter . " " . LAN_EVENT_38 . " " . $event_max_places . " " . LAN_EVENT_39 . " " . chr(34) . "<b>" . $titel . chr(34) . "</b> " . LAN_EVENT_40 . "</td>";
	$text .= "<td colspan=10>" . $counter . " " . LAN_EVENT_39 . " " . chr(34) . "<b>" . $titel . chr(34) . "</b> " . LAN_EVENT_40 . "</td>";
	$text .= "</tr><tr>";
		$text .= "</tr></table>";
	$tp->parseTemplate("{SETIMAGE: w=90&h=90&crop=1}",true); // set thumbnail size. 
	while ($row = $sql->fetch()) {
		$userData['user_image']	= $row['user_image'];
		$userData['user_name']	= $row['user_loginname']; 
		
		$text .= "
			<div id='bild'><a href='".e_HTTP."user.php?id.{$row['user_id']}'>" . $tp->toAvatar($userData) . "<br>" . $row['user_login'] . "</a>";
			if(check_class(e_UC_MAINADMIN)) {
				$text .= " <a class='btn btn-xs btn-default e-tip' href='?action=checkout&id=$event_id&user_id=" .  $row['user_id'] . "' title='" . LAN_EVENT_54. "'><span>" . $tp->toGlyph('fa-user-times') . "</span></a>";
			}
		$text .="</div>";
	}

	$sql->select("events_registration", "*", " member_id=" . USERID . " AND event_id=" . $event_id);
	$row = $sql->fetch();
	if ($row['member_id'] != USERID) {
		$angemeldet = false;
	}else{
		$angemeldet = true;
	}
	
	$text .= "<table border=0 class='table table-striped' style='".USER_WIDTH."'>
		<tr>
			<td colspan=10><center>";
		if ($angemeldet == false) {
			$text .= "
			<form action='event_details.php?action=checkin&id=$event_id' method='post'>
				<input class='btn btn-primary' type='submit' name='anmelden' value='".LAN_EVENT_45."' />";
		} else { 
			// $text .= "<img src='images/accept.png'> " . LAN_EVENT_46;
			$text .= "	<form action='event_details.php?action=checkout&id=$event_id' method='post'>
							<input class='btn btn-primary' type='submit' name='abmelden' value='".LAN_EVENT_47."' />";
			$text .= "	";
		}
	$text .=	"
			<a class='btn btn-primary' href='event_view.php' title='" . LAN_EVENT_51 . "'>" . LAN_EVENT_51 . "</a>";
	$text .= "
		</form>
		</center></td>
		</tr>
	</table>";	
		
		
		if(check_class(e_UC_MAINADMIN)) {
			$sql->select("user", "user_id, user_login", "ORDER BY user_login", "nowhere");
			while ($row = $sql->fetch()) {
				$_r[$row['user_id']] = $row['user_login']; 
			}
			// print_a ($_r);
			
			$text.="<table>
						<form action='event_details.php?action=checkin&id=$event_id' method='post'>
							<tr>
								<td>";
				$text .= 			$frm->select('checkin_teilnehmer',$_r,false,'size=xxlarge',LAN_EVENT_55); 
				$text .= "		</td>
							</tr>
							<tr><td></td></td>
							<tr>
								<td>
									<input class='btn btn-primary' type='submit' name='anmelden' value='".LAN_EVENT_56."' />
								</td>
							</tr>
						</form>
					</table>";
		}

	// echo $meld;
	
}

/*
1 = Keine Plätze mehr frei
2 = Anmeldeschluss verpasst
3 = Anmeldung OK
4 = Anmeldung verbindlich / Kein abmelden mehr
5 = Abmeldung OK
6 = Mails versenden
*/
switch ($meld) {
	case 1:
		$mes->setTitle(LAN_EVENT_11, E_MESSAGE_ERROR);
		$mes->addError(LAN_EVENT_48);
		break;
	case 2:
		$sql->select("events", "event_max_places, event_name", "id=" . $event_id);
		$row = $sql->fetch();
		$titel = $row['event_name'];
		$log->add(LAN_EVENT_11, sprintf(LAN_EVENT_58, USERNAME, $titel), E_LOG_FATAL, LAN_EVENT_59);
		$mes->setTitle(LAN_EVENT_11, E_MESSAGE_ERROR);
		$mes->addError(LAN_EVENT_43);
		break;
	case 3:
		$mes->setTitle(LAN_EVENT_11, E_MESSAGE_SUCCESS);
		$mes->addSuccess(LAN_EVENT_49);
		break;
	case 4:
		$sql->select("events", "event_max_places, event_name", "id=" . $event_id);
		$row = $sql->fetch();
		$titel = $row['event_name'];
		$log->add(LAN_EVENT_12, sprintf(LAN_EVENT_57, USERNAME, $titel), E_LOG_FATAL, LAN_EVENT_59);
		$mes->setTitle(LAN_EVENT_12, E_MESSAGE_ERROR);
		$mes->addError(LAN_EVENT_44);
		break;
	case 5:
		$mes->setTitle(LAN_EVENT_12, E_MESSAGE_INFO);
		$mes->addInfo(LAN_EVENT_50);
		break;
	case 6:
		$mes->setTitle(LAN_EVENT_69, E_MESSAGE_INFO);
		$mes->addInfo(LAN_EVENT_70);
		break;
}

if($_GET['action'] == "sendmail") {
	$sql->select("events", "*", "id=" . $_GET['id'] . " ORDER BY id ASC");
	$row = $sql->fetch();
	$titel				= $row['event_name'];
	$event_date 		= date("d.m.Y", $row['event_date']);
	$event_deadline 	= date("d.m.Y", $row['event_deadline']);
	$event_author_id	= $row['event_author'];
	$event_author		= $sql->retrieve('user', 'user_login', 'user_id=' . $event_author_id);
	
	$info = array(
				'id'				=> $_GET['id'],
				'event_name' 		=> $titel,
				'event_date' 		=> $event_date,
				'event_deadline'	=> $event_deadline,
				'event_author'		=> $event_author
				); 
	
	e107::getEvent()->trigger("eventspost", $info);
	$meld=6;
	$pos = strpos($_SERVER["HTTP_REFERER"], "?");
	if ($pos > 0) {
		$pos = strpos($_SERVER["HTTP_REFERER"], "&meld");
		if ($pos > 0) {
			$aufruf = substr("{$_SERVER["HTTP_REFERER"]}",0,$pos);
			header("Location: ". $aufruf . "&meld=" . $meld);
		}else{
			header("Location: {$_SERVER["HTTP_REFERER"]}". "&meld=" . $meld);
		}
	}else{
		header("Location: {$_SERVER["HTTP_REFERER"]}");
	}
}

// Anmeldung des jeweiligen Mitglieds am Event
if($_GET['action'] == "checkin") {
	$user_id = $_POST['checkin_teilnehmer'];
	if ($user_id == "") {
		$user_id=USERID;
	}
	
	$sql->select("events", "event_max_places, event_deadline", "id=" . $_GET['id']);
	$row = $sql->fetch();
	$counter_akt = $sql->count("events_registration", '(*)', "WHERE event_id=" . $_GET['id']);
	$meld=3;
	
	if ($row['event_deadline'] < time() and !check_class(e_UC_MAINADMIN)) {
		$meld=2;
	}

	if ($counter_akt >= $row['event_max_places'])	{
		$meld=1;
	}
	if ($meld==3) {
		$result = $sql->select("events_registration", "member_id, event_id", " member_id=" . $user_id . " AND event_id=" . $_GET['id'] . " ORDER BY id");
		// print_r ($result);
		if ($result==0){ //nur hinzufügen wenn noch nicht vorhanden
			$arr_insert['member_id']	= $user_id;
			$arr_insert['event_id']		= $_GET['id'];
			$result = $sql->insert("events_registration",$arr_insert);
		}
	}
	$pos = strpos($_SERVER["HTTP_REFERER"], "?");
	// echo $pos;
	
	if ($pos > 0) {
		$pos = strpos($_SERVER["HTTP_REFERER"], "&meld");
		if ($pos > 0) {
			// $aufruf = "{$_SERVER["HTTP_REFERER"]}";
			$aufruf = substr("{$_SERVER["HTTP_REFERER"]}",0,$pos);
			header("Location: ". $aufruf . "&meld=" . $meld);
		}else{
			header("Location: {$_SERVER["HTTP_REFERER"]}". "&meld=" . $meld);
		}
	}else{
		header("Location: {$_SERVER["HTTP_REFERER"]}");
	}
}

// Abmeldung des jeweiligen Mitglieds am Event
if($_GET['action'] == "checkout") {
	$user_id = $_GET['user_id'];
	if ($user_id == "") {
		$user_id=USERID;
	}
	$meld=5;
	$sql->select("events", "event_deadline", "id=" . $_GET['id']);
	$row = $sql->fetch();
		
	if ($row['event_deadline'] < time() and !check_class(e_UC_MAINADMIN)) {
		$meld=4;
	}
	
	if ($meld==5) {
		$sql->delete('events_registration', 'member_id = ' . $user_id . ' AND event_id = ' . $_GET['id']);
	}
	
	$pos = strpos($_SERVER["HTTP_REFERER"], "?");
	if ($pos > 0) {
		$pos = strpos($_SERVER["HTTP_REFERER"], "&meld");
		if ($pos > 0) {
			// $aufruf = "{$_SERVER["HTTP_REFERER"]}";
			$aufruf = substr("{$_SERVER["HTTP_REFERER"]}",0,$pos);
			header("Location: ". $aufruf . "&meld=" . $meld);
		}else{
			header("Location: {$_SERVER["HTTP_REFERER"]}". "&meld=" . $meld);
		}
	}else{
		header("Location: {$_SERVER["HTTP_REFERER"]}");
	}
}

$ns->tablerender(LAN_EVENT_37 . $titel, $mes->render().$text);
require_once(FOOTERF);
?>