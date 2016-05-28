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
require_once("../../class2.php");

if (!e107::isInstalled('events') or !check_class(e_UC_MEMBER))
{
	header("Location: ".e_BASE."index.php");
	exit;
}


if(!defined("USER_WIDTH")){ define("USER_WIDTH","width:95%"); }

require_once(HEADERF);
e107::lan('events');

$mes = e107::getMessage();
$frm = e107::getForm();
$sql = e107::getDB(); 
$sql2 = e107::getDb('sql2');
$parse = e107::getParser();



// $f = new formreload;

$text = "";

if(!e_QUERY) {
	$records = 20;
	$from = 0;
	$order = "dup";
	// $action = "";
}else{
	$qs = explode(".", e_QUERY);
	$from = intval($qs[0]);
	$records = intval($qs[1]);
	$order = $qs[2];
	// $action = $qs[3];
	$oldevents = $qs[3];
}
// print_a (e_QUERY);
if($order == "ddown") {
	$mysqlorder = " ORDER BY event_datum DESC";
}
if($order == "dup") {
	$mysqlorder = " ORDER BY event_datum ASC";
}
if($order == "tdown") {
	$mysqlorder = " ORDER BY event_name DESC";
}
if($order == "tup") {
	$mysqlorder = " ORDER BY event_name ASC";
}


$text .= "
<table border=0 class='table table-striped' style='margin-top:20px;' style='".USER_WIDTH."'>
	
	<tr>";
	$text .= "	
				<td>
					" . LAN_EVENT_05 . " <a class='e-tip' href='?0.{$records}.dup" . $sort . "'>" . $parse->toGlyph('chevron-up') . "</a> <a class='e-tip' href='?0.{$records}.ddown" . $sort . "'>" . $parse->toGlyph('chevron-down') . "</a>
				</td>
				<td>
					" . LAN_EVENT_08 . "
				</td>
				<td>
					" . LAN_EVENT_07 . " <a class='e-tip' href='?0.{$records}.tup" . $sort . "'>" . $parse->toGlyph('chevron-up') . "</a> <a class='e-tip' href='?0.{$records}.tdown" . $sort . "'>" . $parse->toGlyph('chevron-down') . "</a>
				</td>
				<td style='text-align:right'>
					" . LAN_EVENT_09 . "
				</td>
				<td style='text-align:right'>
					" . LAN_EVENT_10 . "
				</td>
	</tr>";
		$query = "SELECT * FROM #events";
		if ($oldevents != "old") {
			$query .= " WHERE event_datum >= " . (time()-86400);
		}
		$query .= $mysqlorder;
		$counter = $sql->gen($query);
		$query .= " LIMIT " . $from . ", " . $records;
		// echo $query;
		$sql->gen($query);
		
		$trans = array(
			'Mon'       => 'Mo',
			'Tue'       => 'Di',
			'Wed'       => 'Mi',
			'Thu'       => 'Do',
			'Fri'       => 'Fr',
			'Sat'       => 'Sa',
			'Sun'       => 'So'
		);
		while ($row = $sql->fetch()) {
			$id            			= $row['id'];
			$datum					= date("d.m.Y", $row[event_datum]);
			$tag					= date("D,", $row[event_datum]);
			$tag					= strtr($tag, $trans);
			$titel         			= $row['event_name'];
			$verfasser				= $row[verfasser];
			$max_plaetze			= $row['event_max_plaetze'];
			$datum_anmeldeschluss 	= date("d.m.Y", $row['event_anmeldeschluss']);
			
			$places = $sql2->count('events_anmeldung', '(*)' , "WHERE event_id={$id}");
			
			// Spieler schon angemeldet?
			$angemeldet = $sql2->select("events_anmeldung", "*", "member_id=" . USERID . " AND event_id=" . $id);

			$text .= "<tr>";
			$text .= "	<td>" . $tag . " " . $datum . "</td>";
			$text .= "	<td>" . $datum_anmeldeschluss . "</td>";
			$text .= "	<td><a class='e-tip' href='event_details.php?action=details&id=$id' title='" . $titel . "'>" . $titel . "</a></td>";
			$text .= "	<td style='text-align:right';>" . ($max_plaetze - $places) . " / " . $max_plaetze . "</td>";
			$text .= "	<td style='text-align:right'>";
			$text .= "		<div class='btn-group'>
								<a class='btn btn-xs btn-default e-tip' href='event_details.php?action=details&id=$id' title='" . LAN_EVENT_17 . "'><span>" . $tp->toGlyph('fa-info-circle') . "</span> </a>
								<a class='btn btn-xs btn-default e-tip' href='event_details.php?action=liste&id=$id' title='" . LAN_EVENT_14 . "'><span>" . $tp->toGlyph('fa-group') . "</span> </a>";

								//define('e_UC_PUBLIC', 0);
								//define('e_UC_MAINADMIN', 250);
								//define('e_UC_READONLY', 251);
								//define('e_UC_GUEST', 252);
								//define('e_UC_MEMBER', 253);
								//define('e_UC_ADMIN', 254);
								//define('e_UC_NOBODY', 255); 
								if(check_class(e_UC_MAINADMIN) or $verfasser == USERID) {;
									$text .= "<a class='btn btn-xs btn-default e-tip' href='event_add.php?action=edit&id=$id' title='" . LAN_EVENT_13 . "'><span>" . $tp->toGlyph('fa-edit') . "</span> </a>
											  <a class='btn btn-xs btn-default e-tip' onclick=\"return jsconfirm('".$tp->toJS(LAN_EVENT_06)."', '".$titel."')\" href='event_view.php?action=del&id=$id' title='" . LAN_EVENT_15 . "'><span>" . $tp->toGlyph('fa-trash') . "</span> </a>";
								}
			$text .= "		</div>";
			$text .= "	</td>
					</tr>";
		}
$text .= "	<tr>
				<td colspan='6' style='text-align:right'>" . LAN_EVENT_16 . $counter . "</td>
			</tr>
</table>";
$text .= "
<table align=center>
	<tr>
		<td colspan='6' style='text-align:center'>
			<a href='event_add.php' title='" . LAN_EVENT_20 . "'><span class='btn btn-primary'>" . LAN_EVENT_20 . "</span></a>";
			if ($oldevents != "old") {
				$text .= " <a href='event_view.php?0.20.dup.old' title='" . LAN_EVENT_52 . "'><span class='btn btn-primary'>" . LAN_EVENT_52 . "</span></a>";
			}else{
				$text .= " <a href='event_view.php?0.20.dup' title='" . LAN_EVENT_53 . "'><span class='btn btn-primary'>" . LAN_EVENT_53 . "</span></a>";
			}
$text .="
		</td>
	</tr>
	<tr>
		<td colspan='6' style='text-align:center'>";
			if ($oldevents == "old") {
				$parms = $counter.",".$records.",".$from.",".e_SELF.'?[FROM].'.$records.".".$order."."."old";
			}else{
				$parms = $counter.",".$records.",".$from.",".e_SELF.'?[FROM].'.$records.".".$order;								
			}
			// echo $parms;
			$text .= $tp->parseTemplate("{NEXTPREV={$parms}}");
$text .= "	
		</td>
	</tr>
</table>";
			

// Event löschen mit allen Anmeldungen
if($_GET['action'] == "del") {
	$sql->delete('events', 'id = '.$_GET['id']);
	$sql->delete('events_anmeldung', 'event_id = '.$_GET['id']);
	header("Location: {$_SERVER["HTTP_REFERER"]}");
}
$ns->tablerender(LAN_EVENT_04, $mes->render().$text);
require_once(FOOTERF);
?>