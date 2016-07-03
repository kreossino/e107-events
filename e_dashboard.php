<?php

if (!defined('e107_INIT')) { exit; }
e107::lan('events');

class events_dashboard // include plugin-folder in the name.
{
	function chart()
	{
		return false;
	}
	
	
	
	function status()
	{
		$sql = e107::getDb();
		$event_posts 	= $sql->count('events');
		$event_logs		= $sql->count('admin_log', '(*)', 'WHERE dblog_eventcode=\'EventMgr\'');
		
		$var[0]['icon'] 	= "<img src='".e_PLUGIN_ABS."events/images/event_16.png' style='width: 16px; height: 16px; vertical-align: bottom' alt='' /> ";
		$var[0]['title'] 	= LAN_EVENT_42;
		$var[0]['url']		= e_PLUGIN."events/event_view.php?0.20.dup.old";
		$var[0]['total'] 	= $event_posts;
		$var[0]['invert'] 	= "";

		// $class = admin_shortcodes::getBadge($event_logs, 'invert'); 
		$var[1]['icon'] 	= "<img src='".e_PLUGIN_ABS."events/images/event_16.png' style='width: 16px; height: 16px; vertical-align: bottom' alt='' /> ";
		$var[1]['title'] 	= LAN_EVENT_42a;
		$var[1]['url']		= e_ADMIN."admin_log.php?searchquery=&filter_options=dblog_eventcode__EventMgr";
		$var[1]['total'] 	= $event_logs;
		$var[1]['invert']	= "not empty";
		
		return $var;
	}	
}



?>