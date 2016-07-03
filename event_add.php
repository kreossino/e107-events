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

$mes = e107::getMessage();
$frm = e107::getForm();
$sql = e107::getDB(); 	
$pref = e107::getPref();
$tp = e107::getParser();

$text = "";

if (isset($_POST['add_event'])) {
	$titel 				= $_POST['event_titel'];
	$event_date			= $_POST['event_date'];
	$event_deadline		= $_POST['event_deadline'];
	$event_details 		= $_POST['event_details'];
	$event_max_places	= $_POST['event_max_places'];
	$event_location		= $_POST['event_location'];
	$event_costs		= $_POST['event_costs'];
	$fehler = false;
	$text .= "<table class='fborder' style='width:43%;'>";
	if (empty($titel)) {
		$mes->addWarning(LAN_EVENT_32);	
		$fehler = true;
	}
	if (!Admin) {
		if (empty($event_date)) {
			$mes->addWarning(LAN_EVENT_33);	
			$fehler = true;
		}
		if (time() > $event_date) {		
			$mes->addWarning(LAN_EVENT_34);	
			$fehler = true;
		}
		if ($event_deadline > $event_date) {		
			$mes->addWarning(LAN_EVENT_30);	
			$fehler = true;
		}
	}

	if ($fehler == false) {
		if ($_POST['edit'] == 'edit') {
			$arr_update['event_name']		= $tp->toDB($titel);
			$arr_update['event_date']		= $tp->toDB($event_date);
			$arr_update['event_deadline']	= $tp->toDB($event_deadline);
			$arr_update['event_details']	= $tp->toDB($event_details);
			$arr_update['event_max_places']	= $tp->toDB($event_max_places);
			$arr_update['event_location']	= $tp->toDB($event_location);
			$arr_update['event_costs']		= $tp->toDB($event_costs);
			
			$arr_update['WHERE'] = 'id = '. $_GET['id'];
			$result = $sql->update('events', $arr_update);
			// print_a ($arr_update);
			// exit;
		}else{
			unset($arr_insert);
			$arr_insert['id'] 					= 0;
			$arr_insert['event_name']			= $tp->toDB($titel);
			$arr_insert['event_date']			= $tp->toDB($event_date);
			$arr_insert['event_deadline']		= $tp->toDB($event_deadline);
			$arr_insert['event_details']		= $tp->toDB($event_details);
			$arr_insert['event_author']			= USERID;
			$arr_insert['event_date_create']	= time();
			$arr_insert['event_max_places']		= $tp->toDB($event_max_places);
			$arr_insert['event_location']		= $tp->toDB($event_location);
			$arr_insert['event_costs']			= $tp->toDB($event_costs);
			print_a ($arr_insert);
			$result = $sql->insert("events", $arr_insert);
		}
		
		if ($result > 0){	
			if ($_GET['action']=='edit'){
				header("Location: ".e_PLUGIN . "events/event_details.php?action=details&id=" . $_GET['id']);
			}else{
				header("Location: ".e_PLUGIN . "events/event_details.php?action=details&id=" . $result);
			}
		}else{
			$mes->addError(LAN_EVENT_36);
		}
		$text .= "</table>";
	}
		
	if ($fehler == true) {
		$mes->addWarning(LAN_EVENT_36);	
	}
}
 
//prüfen ob editieren angesagt ist
if ($_GET['action']=='edit'){
	$sql->select("events", "*", "id=" . $_GET['id'] . " ORDER BY id ASC");
	$row = $sql->Fetch();
	$titel					= $row['event_name'];
	$event_date 			= date("d.m.Y", $row['event_date']);
	$event_deadline 		= date("d.m.Y", $row['event_deadline']);
	$event_details			= $row['event_details'];
	$event_max_places		= $row['event_max_places'];
	$event_location			= $row['event_location'];
	$event_costs			= $row['event_costs'];
}

$text .= "
<table>
	<form action='".$PHP_SELF."' method='post'>
		<tr>
			<td class='forumheader' style='width:10%'>" . LAN_EVENT_25 . "</td>
			<td class='forumheader' style='width:90%'>". $frm->text('event_titel',$titel,80,'required=1') . "</td>
		</tr>
		<tr><td><br></td></tr>
		<tr>
			<td class='forumheader' style='width:10%'>" . LAN_EVENT_63 . "</td>
			<td class='forumheader' style='width:90%'>". $frm->text('event_location',$event_location,100,'required=0') . "</td>
		</tr>
		<tr><td><br></td></tr>
		<tr>
			<td class='forumheader' style='width:10%'>" . LAN_EVENT_64 . "</td>
			<td class='forumheader' style='width:90%'>". $frm->text('event_costs',$event_costs,100,'required=0') . "</td>
		</tr>
		<tr><td><br></td></tr>
		<tr>
			<td>" . LAN_EVENT_27 . "</td>
			<td>". $frm->number('event_max_places', vartrue($event_max_places) ? $event_max_places : '10', 5, array('size'=>5,'class'=>'tbox','min'=>1,'max'=>1001,'required'=>1)) . "</td>
		</tr>
		<tr><td><br></td></tr>
		<tr>
			<td>" . LAN_EVENT_26 . "</td>
			<td>" . $frm->datepicker("event_date", vartrue($event_date) ? $event_date : time()+86400, array('size'=>10,'class'=>'tbox e-time','type'=>'date','format'=>'dd.mm.yyyy','required'=>1)) . "</td> 
		</tr>
		<tr><td><br></td></tr>
		<tr>
			<td>" . LAN_EVENT_29 . "</td>
			<td>" . $frm->datepicker("event_deadline", vartrue($event_deadline) ? $event_deadline : time()+86400, array('size'=>10,'class'=>'tbox e-time','type'=>'date','format'=>'dd.mm.yyyy','required'=>1)) . "</td> 
		</tr>
		<tr><td><br></td></tr>
		<tr>
			<td>" . LAN_EVENT_31 . "</td>
			
			<td>". $frm->bbarea('event_details', $event_details) . "</td>
		</tr>
		<tr><td><br></td></tr>";
		
		//Submit
		if ($_GET['action']=='edit'){
			$btn_text .= LAN_EVENT_24;
		}else{
			$btn_text .= LAN_EVENT_23;
		}
$text .= "	<tr>
				<td><input type=hidden name='edit' value='".$tp->toForm($_GET['action'])."' /></td>
				<td>
					<input class='btn btn-primary' type='submit' name='add_event' tabindex='17' value='".$btn_text."' />
					<a href='event_view.php' title='" . LAN_EVENT_28 . "'> <span class='btn btn-primary'>" . LAN_EVENT_28 . "</span></a>
				</td>
			</tr>";
		
$text .= "
	</form>
</table>";

$text .= '<script type="text/javascript">
<!--
document.getElementById("event-titel").focus();
//-->
</script>';

$ns->tablerender(LAN_EVENT_22, $mes->render().$text);
require_once(FOOTERF);
?>