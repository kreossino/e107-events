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
e107::css('events','events.css');
e107::lan('events');

$text = renderOnlineUsers();

function renderOnlineUsers($data=false)
	{
		$ol = e107::getOnline();
		$tp = e107::getParser();

		$panelOnline = "<font size='2'>
				
				<table class='table table-condensed table-striped' style='width:96%;margin-left:auto;margin-right:auto'>
				<colgroup>
					<!--<col style='width: 10%' />-->
		            <col style='width: 35%' />
					<!--<col style='width: 10%' />-->
					<col style='width: 40%' />
					<col style='width: auto' />
				</colgroup>
				<thead>
					<tr class='first'>
						<!--!th>Timestamp</th>-->
						<th>Username</th>
						<!--<th>IP</th>-->
						<th>Page</th>
						<th class='center'>Agent</th>
					</tr>
				</thead>
				<tbody>";	

		$online = $ol->userList() + $ol->guestList();
		// print_a ($online);
		if($data == 'count')
		{
			return count($online);	
		}
				
		//	echo "Users: ".print_a($online);
		
// print_a ($online);
		foreach ($online as $val)
		{

			$panelOnline .= "
			<tr>
				<!--<td class='nowrap'>".e107::getDateConvert()->convert_date($val['user_currentvisit'],'%H:%M:%S')."</td>-->
				<td><a class='e-tip' href='#' title='Time: ".e107::getDateConvert()->convert_date($val['user_currentvisit'],'%H:%M:%S')."'>".renderOnlineName($val['online_user_id'])."</td>
				<!--<td>".e107::getIPHandler()->ipDecode($val['user_ip'])."</td>-->
				<td><a class='e-tip' href='".$val['user_location']."' title='".$val['user_location']."'>".$tp->html_truncate(basename($val['user_location']),50,"...")."</a></td>
				<td class='center'><a class='e-tip' href='#' title='".$val['user_agent']. "\n\n IP: " . e107::getIPHandler()->ipDecode($val['user_ip']) . "'>".browserIcon($val)."</a></td>
			</tr>
			";
		}

		$panelOnline .= "</tbody></table></font>";
		
		return $panelOnline;
	}	
	
	function browserIcon($row)
	{
	
		$types = array(
			"ie" 		=> "MSIE",
			'chrome'	=> 'Chrome',
			'firefox'	=> 'Firefox',
			'seamonkey'	=> 'Seamonkey',
		//	'Chromium/xyz
			'safari'	=> "Safari",
			'opera'		=> "Opera"
		);
				
		if($row['user_bot'] === true)
		{
			return "<i class='browser e-bot-16'></i>";	
		}
		
		foreach($types as $icon=>$b)
		// print_a ($types);
		{
			if(strpos($row['user_agent'], $b)!==false)
			{
				return "<i class='browsers e-".$icon."-16' ></i>";	
			}
		}
		return "<i class='browsers e-firefox-16'></i>"; // FIXME find a default icon. 
	}

	
	function renderOnlineName($val)
	{
		if($val==0)
		{
			return LAN_GUEST;
		}
		$sql = e107::getDb(); 
		$user_id = explode(".",$val);
		$sql->db_Select("user", "user_login", "user_id=" . $user_id[0]);
		$row = $sql->db_Fetch(); 
		return $row['user_login'];
	}

	// print_a ($text);
	
$ns->tablerender("Online User", $text);

?>
