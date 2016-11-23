<?php
// +-------------------------------------------------+
// � 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// | creator : Eric ROBERT                                                    |
// | modified : ...                                                           |
// +-------------------------------------------------+
// $Id: func_customfields_artevod.inc.php,v 1.2 2015-04-03 11:16:22 jpermanne Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

// DEBUT param�trage propre � la base de donn�es d'importation :
require_once($base_path."/admin/import/func_customfields.inc.php");

function z_recup_noticeunimarc_suite($notice) {
	recup_noticeunimarc_suite($notice);
} 
	
function z_import_new_notice_suite() {
	import_new_notice_suite();
} 

// Permet de m�moriser la valeur d'un import extern pour ensuite l'int�gr� dans un champ perso de la notice avec param_perso_form
function param_perso_prepare($record) {
	global $param_perso_900;
	
	$param_perso_900=$record->get_subfield("900","a","l","n");
	
}

function param_perso_form(&$p_perso) {
	global $dbh;
	global $param_perso_900;

	for($i=0;$i<count($param_perso_900);$i++){
	
		$req = " select idchamp, type, datatype from notices_custom where name='".$param_perso_900[$i]['n']."'";
		$res = pmb_mysql_query($req,$dbh);
		if(pmb_mysql_num_rows($res)){
			$perso = pmb_mysql_fetch_object($res);
			if($perso->idchamp){
				if($perso->type == 'list'){
					$requete="select notices_custom_list_value from notices_custom_lists where notices_custom_list_lib='".addslashes($param_perso_900[$i]['a'])."' and notices_custom_champ=$perso->idchamp";
					$resultat=pmb_mysql_query($requete);
					if (pmb_mysql_num_rows($resultat)) {
						$value=pmb_mysql_result($resultat,0,0);
					} else {
						$requete="select max(notices_custom_list_value*1) from notices_custom_lists where notices_custom_champ=$perso->idchamp";
						$resultat=pmb_mysql_query($requete);
						$max=@pmb_mysql_result($resultat,0,0);
						$n=$max+1;
						$requete="insert into notices_custom_lists (notices_custom_champ,notices_custom_list_value,notices_custom_list_lib) values($perso->idchamp,$n,'".addslashes($param_perso_900[$i]['a'])."')";
						pmb_mysql_query($requete);
						$value=$n;
					}
					$p_perso->values[$perso->idchamp][]=$value;
				} else {
					$p_perso->values[$perso->idchamp][]=$param_perso_900[$i]['a'];
				}
			}
		}
	}
}

// enregistrement de la notices dans les cat�gories
function traite_categories_enreg($notice_retour,$categories,$thesaurus_traite=0) {

}

function traite_categories_for_form($tableau_600="",$tableau_601="",$tableau_602="",$tableau_605="",$tableau_606="",$tableau_607="",$tableau_608="") {
	
}

function traite_categories_from_form() {

}

function traite_info_subst(&$mes_info){
	
	//Correspondance avec les codes langues PMB
	$lang = new marc_list('lang');
	if (count($mes_info->original_language_code)) {
		asort($lang->table);
		foreach ($mes_info->original_language_code as $i=>$language) {
			if (strlen($language) == 2) {
				foreach ($lang->table as $code=>$value) {
					if ($mes_info->original_language_code[$i] == substr($code,0,2)) {
						$mes_info->original_language_code[$i] = $code;
						break;
					}
				}
			}
		}
	}
	
	//Gestion des documents num�riques
	if (count($mes_info->doc_nums)) {
		foreach ($mes_info->doc_nums as $i=>$docnum) {
			$filename = substr($mes_info->doc_nums[$i]['a'], strrpos($mes_info->doc_nums[$i]['a'], "/")+1);
			$filename = substr($filename,0,strrpos($filename,"."));
			$mes_info->doc_nums[$i]['b'] = $filename; 
		}
	}
}

?>