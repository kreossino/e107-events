<?php

class events_notify extends notify // plugin-folder + '_notify' 
{		
	function config()
	{
		$config = array();
		$config[] = array(
			'name'			=> 'Events',
			'function'		=> "eventspost",
			'category'		=> ''
		);	
		return $config;
	}
	
	function eventspost($data) 
	{
		$message = LAN_EVENT_65.' '.$data['event_author'].'<br />';
		$message .= LAN_EVENT_25.' '.$data['event_name'].'<br />';
		$message .= LAN_EVENT_26.' '.$data['event_date'].'<br />';
		$message .= LAN_EVENT_29.': '.$data['event_deadline'].'<br />';
		$message .="<a href='http://www.whatyouwant.ch/e107_plugins/events/event_details.php?action=details&id=".$data['id']."' title='" . LAN_EVENT_67 . "'> <span class='btn btn-primary'>" . LAN_EVENT_67 . "</span></a>";
		$message .="<br /><br />" . LAN_EVENT_71;
		$message .="<br />" . LAN_EVENT_72;
		// echo $message;
		// exit;
		$this->send('eventspost', LAN_EVENT_66, $message);
	}
}
?>