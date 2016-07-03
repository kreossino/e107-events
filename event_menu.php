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

if (!defined('e107_INIT')) { exit; }
$e107 = e107::getInstance();

if (!$e107->isInstalled('events')) return '';

$ns = e107::getRender();
e107::lan('events');
$sql = e107::getDB(); 

//Anzahl Events anzeigen
$anzahl_events = 5;
$anzahl_zeichen = 45;
$count = $sql->count('events');

$event_text = '';

if (!check_class(e_UC_MEMBER)) {
	$event_text = LAN_EVENT_21;	
}else{
	$query = "SELECT * FROM #events WHERE event_date >= " . (time()-86400) . " ORDER BY event_date ASC LIMIT " . $anzahl_events;
	$sql->gen($query);
	while ($row = $sql->fetch()) {
		if (strlen($row['event_name']) > $anzahl_zeichen){
			$event_text .= "	<a class='e-tip' href='" . e_HTTP . e_PLUGIN . "events/event_details.php?action=details&id={$row['id']}' title='" . LAN_EVENT_07 . " " . chr(34) . $row['event_name'] . chr(34) . " " . LAN_EVENT_02 . date("d.m.Y", $row['event_date']) . LAN_EVENT_02a . "'>" . substr($row['event_name'],0,$anzahl_zeichen) . "...</a><br />";
		}else{
			$event_text .= "	<a class='e-tip' href='" . e_HTTP . e_PLUGIN . "events/event_details.php?action=details&id={$row['id']}' title='" . LAN_EVENT_07 . " " . chr(34) . $row['event_name'] . chr(34) . " " . LAN_EVENT_02 . date("d.m.Y", $row['event_date']) . LAN_EVENT_02a . "'>" . $row['event_name'] . "</a><br />";
		}
	}
	if ($count > $anzahl_events){
		$event_text .= "<br /><a class='e-tip; btn btn-primary' href='" . e_HTTP . e_PLUGIN . "events/event_view.php' title='" . LAN_EVENT_61 . "'>" . LAN_EVENT_61 . "</a>";
	}
}

$ns->tablerender(LAN_EVENT_01, $event_text, 'next_event_menu');
?>
