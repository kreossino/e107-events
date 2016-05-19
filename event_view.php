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
<table border=0 class='table table-striped' style='".USER_WIDTH."'>
	
	<tr>";
	$text .= "	
				<td colspan=2 width='130'>
					" . LAN_EVENT_05 . " <a class='e-tip' href='?0.{$records}.dup" . $sort . "'>" . $parse->toGlyph('chevron-up') . "</a> <a class='e-tip' href='?0.{$records}.ddown" . $sort . "'>" . $parse->toGlyph('chevron-down') . "</a>
				</td>
				<td width='130'>
					" . LAN_EVENT_08 . "
				</td>
				<td width='500'>
					" . LAN_EVENT_07 . " <a class='e-tip' href='?0.{$records}.tup" . $sort . "'>" . $parse->toGlyph('chevron-up') . "</a> <a class='e-tip' href='?0.{$records}.tdown" . $sort . "'>" . $parse->toGlyph('chevron-down') . "</a>
				</td>
				<td width='85' style='text-align:right'>
					" . LAN_EVENT_09 . "
				</td>
				<td width='5'></td>
				<td width='60' style='text-align:center'>
					" . LAN_EVENT_10 . "
				</td>
	</tr>";
		$query = "SELECT * FROM #events";
		if ($oldevents != "old") {
			$query .= " WHERE event_datum >= " . (time()-86400);
		}
		$query .= $mysqlorder;
		$counter = $sql->db_Select_Gen($query);
		$query .= " LIMIT " . $from . ", " . $records;
		// echo $query;
// echo $query;
		$sql->db_Select_Gen($query);
		// $sql2 = new db();
		
		$trans = array(
			'Mon'       => 'Mo',
			'Tue'       => 'Di',
			'Wed'       => 'Mi',
			'Thu'       => 'Do',
			'Fri'       => 'Fr',
			'Sat'       => 'Sa',
			'Sun'       => 'So'
		);
		while ($row = $sql->db_fetch()) {
			$id            			= $row['id'];
			$datum					= date("d.m.Y", $row[event_datum]);
			$tag					= date("D,", $row[event_datum]);
			$tag					= strtr($tag, $trans);
			$titel         			= $row['event_name'];
			$verfasser				= $row[verfasser];
			$max_plaetze			= $row['event_max_plaetze'];
			$datum_anmeldeschluss 	= date("d.m.Y", $row['event_anmeldeschluss']);
			
			$places = $sql2->db_Count('events_anmeldung', '(*)' , "WHERE event_id={$id}");
			
			// Spieler schon angemeldet?
			$angemeldet = $sql2->db_Select("events_anmeldung", "*", "member_id=" . USERID . " AND event_id=" . $id);

			$text .= "
						<tr>";
				$text .= "
							<td width='1' class='fborder'>" . $tag . "</td>
							<td width='120'>" . $datum . "</td>";
				$text .= "
							<td width=120>" . $datum_anmeldeschluss . "</td>";
				$text .= "	<td><a class='e-tip' href='event_details.php?action=details&id=$id' title='" . $titel . "'>" . $titel . "</a></td>";
				$text .= "	<td width=100 style='text-align:right';>" . ($max_plaetze - $places) . " / " . $max_plaetze . "</td>
							<td>
							<td width=100 style='text-align:center'>";
							
							$text.="
									<div class='btn-group'>
									  <a class='btn btn-xs btn-default e-tip' href='event_details.php?action=details&id=$id' title='" . LAN_EVENT_17 . "'><span>" . $tp->toGlyph('fa-info-circle') . "</span> </a>
									  <a class='btn btn-xs btn-default e-tip' href='event_details.php?action=liste&id=$id' title='" . LAN_EVENT_14 . "'><span>" . $tp->toGlyph('fa-group') . "</span> </a>
									 ";
									// <a class='btn btn-xs btn-default e-tip' href='event_details.php?action=liste&id=$id' title='" . LAN_EVENT_14 . "'><span>" . $tp->toGlyph('fa-edit') . "</span> </a>
									 // echo $tp->toGlyph('fa-edit');
			
							// $text .= "	<a class='e-tip' href='event_details.php?action=details&id=$id' title='" . LAN_EVENT_17 . "'><img src='images/info.png' border='0'></a>";
							// $text .= "	<a class='btn btn-xs btn-default e-tip' href='event_details.php?action=liste&id=$id' title='" . LAN_EVENT_14 . "'><img src='images/glyphicons_042_group.png' width='14'></a>";
							
							//define('e_UC_PUBLIC', 0);
							//define('e_UC_MAINADMIN', 250);
							//define('e_UC_READONLY', 251);
							//define('e_UC_GUEST', 252);
							//define('e_UC_MEMBER', 253);
							//define('e_UC_ADMIN', 254);
							//define('e_UC_NOBODY', 255); 
							if(check_class(e_UC_MAINADMIN) or $verfasser == USERID) {;
								$text .="
									<a class='btn btn-xs btn-default e-tip' href='event_add.php?action=edit&id=$id' title='" . LAN_EVENT_13 . "'><span>" . $tp->toGlyph('fa-edit') . "</span> </a>
									<a class='btn btn-xs btn-default e-tip' onclick=\"return jsconfirm('".$tp->toJS(LAN_EVENT_06)."', '".$titel."')\" href='event_view.php?action=del&id=$id' title='" . LAN_EVENT_15 . "'><span>" . $tp->toGlyph('fa-trash') . "</span> </a>
								</div>";
								// $text .= "	<a class='e-tip' href='event_add.php?action=edit&id=$id' title='" . LAN_EVENT_13 . "'><img src='images/edit.png' border='0'></a>";
								// $text .= "	<a class='e-tip' onclick=\"return jsconfirm('".$tp->toJS(LAN_EVENT_06)."', '".$titel."')\" href='event_view.php?action=del&id=$id' title='" . LAN_EVENT_15 . "'><img src='images/del.png' border='0'></a>";
							}
				$text .= "
							</td>
						</tr>";
		}
		$text .= "
						
						<tr><td colspan='8' style='text-align:right'>" . LAN_EVENT_16 . $counter . "</td></tr>
					</table>";
				$text .= "<table align=center>
						<tr>
							<td colspan='8' style='text-align:center'>
								<a href='event_add.php' title='" . LAN_EVENT_20 . "'><span class='btn btn-primary'>" . LAN_EVENT_20 . "</span></a>";
								if ($oldevents != "old") {
									$text .= " <a href='event_view.php?0.20.dup.old' title='" . LAN_EVENT_52 . "'><span class='btn btn-primary'>" . LAN_EVENT_52 . "</span></a>";
								}else{
									$text .= " <a href='event_view.php?0.20.dup' title='" . LAN_EVENT_53 . "'><span class='btn btn-primary'>" . LAN_EVENT_53 . "</span></a>";
								}
				$text .="</td>
						</tr>
						<tr>
							<td colspan='8' style='text-align:center'>";
								if ($oldevents == "old") {
									$parms = $counter.",".$records.",".$from.",".e_SELF.'?[FROM].'.$records.".".$order."."."old";
								}{
									$parms = $counter.",".$records.",".$from.",".e_SELF.'?[FROM].'.$records.".".$order;								
								}
								// echo $parms;
								$text .= $tp->parseTemplate("{NEXTPREV={$parms}}");
				$text .= "	</td>
						</tr>
					</table>";
			

// Event löschen mit allen Anmeldungen
if($_GET['action'] == "del") {
	$sql->db_Delete('events', 'id = '.$_GET['id']);
	$sql->db_Delete('events_anmeldung', 'event_id = '.$_GET['id']);
	header("Location: {$_SERVER["HTTP_REFERER"]}");
	// $text .= "<img src='images/loading2.gif'> loading... <meta http-equiv='refresh' content='0;URL=?{$from}.{$records}.{$order}'>";
}
// $mes->addSuccess(print_a($success,true)); 
// $titel=LAN_EVENT_04;
// $ns->tablerender("<DIV ALIGN=CENTER><h3><b>". $titel . "</b></h3></DIV>", $text);
$ns->tablerender(LAN_EVENT_04, $mes->render().$text);
require_once(FOOTERF);
?>