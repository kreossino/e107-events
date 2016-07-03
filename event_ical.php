<?php
require_once("../../class2.php");

require_once "includes/iCalcreator.class.php";
$sql = e107::getDB();

if ($_GET['action']=="create") {
	$sql->select("events", "*", "id=" . $_GET['id']);
	$row = $sql->fetch();
	$title		= $row['event_name'];
	$event_details	= $row['event_details'];
	$datumStart	= date("Ymd", $row['event_date']);
	$datumEnd 	= date("Ymd", $row['event_date']+86400);
	
	$config    = array( "unique_id" => "whatyouwant.ch", "TZID" => "Europe/Zurich" );
	$vcalendar = new vcalendar( $config );
	$vcalendar->setProperty('method', 'PUBLISH' );
	// $vcalendar->setProperty( "x-wr-calname",  $title );
	// $vcalendar->setProperty( "X-WR-CALDESC",  "Kalender Import" );
	$vcalendar->setProperty( "X-WR-TIMEZONE", "Europe/Zurich" );
	
	$vevent = new vevent();
	$vevent->setProperty('DTSTART', $datumStart, array( "VALUE" => "DATE" ));
	$vevent->setProperty('DTEND', $datumEnd, array( "VALUE" => "DATE" ));
	$vevent->setProperty('SUMMARY', $title);
	$vevent->setProperty('CLASS', "private");
	// $vevent->setProperty('url', "http://www.whatyouwant.ch/e107_plugins/events/event_details.php?action=details&id=20");
	$vevent->setProperty('DESCRIPTION', "http://www.whatyouwant.ch/e107_plugins/events/event_details.php?action=details&id={$_GET['id']}");

	$vcalendar->setComponent($vevent);
	$vcalendar->returnCalendar();
}
?>

