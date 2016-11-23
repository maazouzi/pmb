<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: view_serial.inc.php,v 1.1 2015-04-24 14:20:57 dbellamy Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

// page d'affichage des éléments bulletinés d'un périodique en recherche prévisions

require_once("$class_path/serials.class.php");

$serial = new serial($serial_id);
print pmb_bidi("<h3>".$msg[1150]." : ".$serial->tit1."</h3>");

$requete = "select bulletin_id from bulletins WHERE bulletin_notice=$serial_id ORDER BY bulletin_id DESC";
$myQuery = pmb_mysql_query($requete, $dbh);

if(pmb_mysql_num_rows($myQuery)) {

	while($bulletin = pmb_mysql_fetch_object($myQuery)) {

		$entry = new bulletinage($bulletin->bulletin_id);

		if(sizeof($entry->expl)) {

			$link_bulletin_temp = str_replace('!!id!!', $bulletin->bulletin_id, $link_bulletin );
			if ($link_bulletin_temp) {
				print pmb_bidi("<br /><b><a href='$link_bulletin_temp'>".$entry->header."</a>");
			} else {
				print pmb_bidi("<br /><b>".$entry->header);
			}

			print pmb_bidi("</b>&nbsp;".sizeof($entry->expl)."&nbsp;exemplaire(s)");

		} else {

			print pmb_bidi('<br />'.$entry->header);

		}
	}
}
