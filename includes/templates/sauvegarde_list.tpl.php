<?php
// +-------------------------------------------------+
// � 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: sauvegarde_list.tpl.php,v 1.10 2014-10-31 19:38:55 Alexandre Exp $

if (stristr($_SERVER['REQUEST_URI'], ".tpl.php")) die("no access");

$form='<h1>'.$msg["sauv_list_titre"].'</h1>
<form name="sauvegarde_list" method="post" action="admin.php?categ=sauvegarde&sub=list">
<input type="hidden" name="act" value="" />

<!--
<center>'.$msg["sauv_list_dates_list"].'<br />!!date_saving!!<br /><input type="submit" value="'.$msg["sauv_list_filtrer"].'" class="bouton" onClick="this.form.act.value=\'\';" /></center>
-->
<table class="nobrd"><tr>
<td class="nobrd"  width="30%">'.$msg["sauv_list_dates_list"].'&nbsp;&nbsp;
</td><td class="nobrd" width="20%">
!!date_saving!! &nbsp;&nbsp;
</td><td class="nobrd">
<input type="submit" value="'.$msg["sauv_list_filtrer"].'" class="bouton" onClick="this.form.act.value=\'\';" />
</td></tr></table>
!!sauvegarde_list!!
<div clas="row">
<input type="submit" value="'.$msg["sauv_list_del_sets"].'" class="bouton" onClick="if (confirm(\''.$msg["sauv_list_confirm_delete"].'\')) {this.form.act.value=\'delete\';} else return false;" />
</div>
</form>';

?>