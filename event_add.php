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
$pref = e107::getPref();
$tp = e107::getParser();


require_once(e_PLUGIN.'events/includes/event_function.php');

$text = "";

$f = new formreload;

if (isset($_POST['add_event'])) {
    /**
     * Formular wurde submitted?
     */
		$titel 					= $_POST['Event_Titel'];
		// $datum					= datum_mktime($_POST['Event_Date']);
		// $datum_anmeldeschluss	= datum_mktime($_POST['Event_Anmeldeschluss']);
		$datum					= $_POST['Event_Date'];
		$datum_anmeldeschluss	= $_POST['Event_Anmeldeschluss'];
		$details 				= $_POST['Event_Details'];
		$max_plaetze			= $_POST['Event_max_Plaetze'];
		$ort					= $_POST['Event_ort'];
		$kosten					= $_POST['Event_kosten'];
		
		$fehler = false;
		$text .= "<table class='fborder' style='width:43%;'>";
		if (empty($titel)) {
			$mes->addWarning(LAN_EVENT_32);	
			$fehler = true;
		}
		if (!Admin) {
			if (empty($datum)) {
				$mes->addWarning(LAN_EVENT_33);	
				$fehler = true;
			}
			if (time() > $datum) {		
				$mes->addWarning(LAN_EVENT_34);	
				$fehler = true;
			}
			if ($datum_anmeldeschluss > $datum) {		
				$mes->addWarning(LAN_EVENT_30);	
				$fehler = true;
			}
		}

    if ($f->easycheck()) {
		if ($fehler == false) {
			if ($_POST['edit'] == 'edit') {
				$arr_update['event_name']			= $tp->toDB($titel);
				$arr_update['event_datum']			= $tp->toDB($datum);
				$arr_update['event_anmeldeschluss']	= $tp->toDB($datum_anmeldeschluss);
				$arr_update['event_details']		= $tp->toDB($details);
				$arr_update['event_max_plaetze']	= $tp->toDB($max_plaetze);
				$arr_update['event_ort']			= $tp->toDB($ort);
				$arr_update['event_kosten']			= $tp->toDB($kosten);
				
				$arr_update['WHERE'] = 'id = '. $_GET['id'];
				$result = $sql->db_update('events', $arr_update);
				// print_r ($arr_update);
				// exit;
			}else{
				$arr_insert['id'] 					= 0;
				$arr_insert['event_name']			= $tp->toDB($titel);
				$arr_insert['event_datum']			= $tp->toDB($datum);
				$arr_insert['event_anmeldeschluss']	= $tp->toDB($datum_anmeldeschluss);
				$arr_insert['event_details']		= $tp->toDB($details);
				$arr_insert['verfasser']			= USERID;
				$arr_insert['datum']				= time();
				$arr_insert['event_max_plaetze']	= $tp->toDB($max_plaetze);
				$arr_insert['event_ort']			= $tp->toDB($ort);
				$arr_insert['event_kosten']			= $tp->toDB($kosten);
				
				$result = $sql->db_insert('events', $arr_insert);
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
		
    } else {
        //print "Du hat einen Reload gemacht";
    }
	
	if ($fehler == true) {
		$mes->addWarning(LAN_EVENT_36);	
	}
}
 

//prüfen ob editieren angesagt ist
if ($_GET['action']=='edit'){
	$sql->db_select("events", "*", "id=" . $_GET['id'] . " ORDER BY id ASC");
	$row = $sql->db_Fetch();
	$titel					= $row['event_name'];
	$datum 					= date("d.m.Y", $row[event_datum]);
	$datum_anmeldeschluss 	= date("d.m.Y", $row[event_anmeldeschluss]);
	$details				= $row['event_details'];
	$max_plaetze			= $row['event_max_plaetze'];
	$ort					= $row['event_ort'];
	$kosten					= $row['event_kosten'];
}

$text .= "
<table style='".USER_WIDTH."'>
	<form action='".$PHP_SELF."' method='post'>".$f->get_formtoken()."
		<tr>
			<td class='forumheader' style='width:10%'>" . LAN_EVENT_25 . "</td>
			<td class='forumheader' style='width:90%'>". $frm->text('Event_Titel',$titel,80,'required=1') . "</td>
		</tr>
		<tr><td><br></td></tr>
		<tr>
			<td class='forumheader' style='width:10%'>" . LAN_EVENT_63 . "</td>
			<td class='forumheader' style='width:90%'>". $frm->text('Event_ort',$ort,100,'required=0') . "</td>
		</tr>
		<tr><td><br></td></tr>
		<tr>
			<td class='forumheader' style='width:10%'>" . LAN_EVENT_64 . "</td>
			<td class='forumheader' style='width:90%'>". $frm->text('Event_kosten',$kosten,100,'required=0') . "</td>
		</tr>
		<tr><td><br></td></tr>
		<tr>
			<td>" . LAN_EVENT_27 . "</td>
			<td>". $frm->number('Event_max_Plaetze', vartrue($max_plaetze) ? $max_plaetze : '10', 5, array('size'=>5,'class'=>'tbox','min'=>1,'max'=>999,'required'=>1)) . "</td>
		</tr>
		<tr><td><br></td></tr>
		<tr>
			<td>" . LAN_EVENT_26 . "</td>
			<td>" . $frm->datepicker("Event_Date", vartrue($datum) ? $datum : time()+86400, array('size'=>10,'class'=>'tbox e-time','type'=>'date','format'=>'dd.mm.yyyy','required'=>1)) . "</td> 
		</tr>
		<tr><td><br></td></tr>
		<tr>
			<td>" . LAN_EVENT_29 . "</td>
			<td>" . $frm->datepicker("Event_Anmeldeschluss", vartrue($datum_anmeldeschluss) ? $datum_anmeldeschluss : time()+86400, array('size'=>10,'class'=>'tbox e-time','type'=>'date','format'=>'dd.mm.yyyy','required'=>1)) . "</td> 
		</tr>
		<tr><td><br></td></tr>
		<tr>
			<td>" . LAN_EVENT_31 . "</td>
			
			<td>". $frm->bbarea('Event_Details', $details) . "</td>
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