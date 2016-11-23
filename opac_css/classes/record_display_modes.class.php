<?php
// +-------------------------------------------------+
// � 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: record_display_modes.class.php,v 1.3 2015-05-12 08:35:38 abacarisse Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class record_display_modes {
	private $filename;
	private $modes;
	
	public function __construct(){
		global $opac_notices_display_modes;
		
		$this->filename = $opac_notices_display_modes;
		$this->filename=str_replace(array(".xml",".XML"), "", $this->filename);
		$this->analyse();
	}
	
	/**
	 * On parse le fichier xml des modes d'affichage.
	 */
	private function analyse(){
		global $include_path;
		
		if( !is_array($this->modes) || !count($this->modes)){
			if(file_exists($include_path."/records/".$this->filename."_subst.xml")){
				$filepath = $include_path."/records/".$this->filename."_subst.xml";
			}else if (file_exists($include_path."/records/".$this->filename.".xml")){
				$filepath = $include_path."/records/".$this->filename.".xml";
			}else{
				$filepath = $include_path."/records/display_modes.xml";
			}
			
			$fp = fopen($filepath,"r");
			$xml=fread($fp,filesize($filepath));
			fclose($fp);
			$this->modes =_parser_text_no_function_($xml, "MODES");
		}
	}
	
	/**
	 * Retourne un mode en fonction de son ID
	 * 
	 * @param int $mode_id l'identifiant d'un mode
	 * @return array le tableau correspondant au mode recherch�
	 */
	public function get_mode($mode_id){
		if(sizeof($this->modes['MODE'])){
			foreach($this->modes['MODE'] as $mode_offset=>$mode){
				if($mode['ID']==$mode_id){
					return $this->modes['MODE'][$mode_offset];
				}
			}
		}
		return false;
	}
	
	/**
	 * Compare les types de doc autoris�s dans le mode avec les types de doc dans le r�sultat.
	 * 
	 * @param int $mode_id l'identifiant du mode � comparer
	 * @return boolean comparaison vrai ou fausse
	 */
	private function compare_typdoc($mode_id){
		global $l_typdoc;
		
		$tab_typdoc_result=explode(",",$l_typdoc);
		
		$mode=$this->get_mode($mode_id);
		
		$return=false;
		if($mode['DOCTYPES'][0]['value'] && $tab_typdoc_mode=explode(",",$mode['DOCTYPES'][0]['value'])){
			$return=true;
			foreach($tab_typdoc_result as $typdoc_result){
				if(!in_array($typdoc_result, $tab_typdoc_mode)){
					$return=false;
				}
			}
		}elseif(!sizeof($mode['DOCTYPES'])){
			$return=true;
		}
		
		return $return;
	}
	
	/**
	 * Retourne le mode courrant � utiliser pour un resultat de recherche
	 * en fonction de la sesson, et du param�trage dans le fichier xml
	 * 
	 * @return int $mode_id l'identifiant du mode � utiliser
	 */
	public function get_current_mode(){
		$mode_id=0;
		
		$mode_id_selected=0;
		$mode_id_auto=0;
		$mode_id_default=0;
		
		if(($_SESSION['user_current_mode'] && $this->compare_typdoc($_SESSION['user_current_mode'])) ||  $_SESSION['user_current_mode']==="0"){
			$mode_id_selected=$_SESSION['user_current_mode'];
		}
		
		if(sizeof($this->modes['MODE'])){
			foreach($this->modes['MODE'] as $mode){
				
				if($mode['DOCTYPES'][0]['AUTO']=='yes' && $this->compare_typdoc($mode['ID'])){
					//Mode auto
					$mode_id_auto= $mode['ID'];
				}
				
				if($mode['DEFAULT']=='yes'){
					//mode par d�faut
					$mode_id_default= $mode['ID'];
				}
			}
		}
		
		if($mode_id_selected || $mode_id_selected==="0"){
			$mode_id=$mode_id_selected;
		}elseif($mode_id_auto){
			$mode_id=$mode_id_auto;
		}elseif($mode_id_default){
			$mode_id=$mode_id_default;
		}
		
		return $mode_id;
	}
	
	/**
	 * Retourne la bonne fonction en fonction du mode $mode_id
	 * 
	 * @param int $mode_id le mode courrant
	 * @return unknown|boolean
	 */
	public function get_aff_function($mode_id){
		global $include_path;
		
		$mode=$this->get_mode($mode_id);
		if($aff_notice_fonction=$mode['FUNCTION'][0]['SRC']){
			if(file_exists($include_path."/".$aff_notice_fonction.".inc.php")){
				require_once($include_path."/".$aff_notice_fonction.".inc.php");
				return $aff_notice_fonction;
			}else{
				return false;
			}
		}else{
			return false;
		}
	}
	
	/**
	 * Retourne l'identifiant du template pour l'objet mode pass� en param
	 * 
	 * @param unknown $mode
	 * @return unknown|number
	 */
	public function get_template_id($mode_id){
		$mode=$this->get_mode($mode_id);
		$template_id=$mode['TEMPLATE'][0]['TEMPLATE_ID'];
		if($template_id){
			return $template_id;
		}else {
			return 0;
		}
	}
	
	/**
	 * Retourne le code du template si il est saisi dans le fichier xml
	 * 
	 * @param unknown $mode
	 * @return unknown|number
	 */
	public function get_template_code($mode_id){
		$mode=$this->get_mode($mode_id);
		$code=$mode['TEMPLATE'][0];
		if(sizeof($code)){
			return $code;
		}else {
			return 0;
		}
	}
	
	/**
	 * Retourne les informations du layout
	 * 
	 * @param unknown $mode_id
	 * @return Ambigous <multitype:, boolean>|number
	 */
	public function get_layout($mode_id){
		$mode=$this->get_mode($mode_id);
		$layout=$mode['LAYOUT'][0];
		if($layout){
			return $layout;
		}else {
			return 0;
		}
	}
	
	/**
	 * Enregistre le mode choisi par l'utilisateur en session
	 * 
	 * @param int $user_current_mode le mode choisi par l'utilisateur
	 */
	public function set_user_current_mode($user_current_mode){
		$_SESSION['user_current_mode']=$user_current_mode;
	}
	
	/**
	 * On affiche le menu d'affichage
	 * 
	 * @return string
	 */
	public function show_mode_selector(){
		
		$current_mode=$this->get_current_mode();
		
		$html = "<ul class='mode_selector_list'>";
		//le mode par d�faut 
		if($this->modes['NOMODE']){
			$selected='';
			if($current_mode==0){
				$selected='_selected';
			}
			$html.= "<li class='mode_selector$selected' onclick='switch_mode(0)'><img src='".$this->get_icon_url($this->modes['NOMODE'][0]['ICON'])."' alt='".$this->modes['NOMODE'][0]['NAME']."'/></li>";
		}
		
		foreach($this->modes['MODE'] as $mode){
			if($this->compare_typdoc($mode['ID']) || !$mode['DOCTYPES']){
				
				$selected='';
				if($current_mode==$mode['ID']){
					$selected='_selected';
				}
				$html.= "<li class='mode_selector$selected' onclick='switch_mode(".$mode['ID'].")' $selected><img src='".$this->get_icon_url($mode['ICON'])."' alt='".$mode['NAME']."'/></li>";
			}
		}
		$html.= "</ul>";
		
		$html.="
		<script type='text/javascript'>
			function switch_mode(id_mode){
				
				var formName='';
				
				for(var iForm in document.forms){
					
					if(document.forms[iForm].nodeName=='FORM'){
						var replace = false;
						for(var iInput in document.forms[iForm].children){
							if(document.forms[iForm].children[iInput].name=='user_current_mode'){
								document.forms[iForm].children[iInput].value=id_mode;
								replace=true;
							}
						}
						
						if(!replace){
							var user_current_mode='';
						
							user_current_mode=document.createElement('input');
							user_current_mode.setAttribute('name','user_current_mode');
							user_current_mode.setAttribute('value',id_mode);
							user_current_mode.setAttribute('type','hidden');
											
							try{
							 	document.forms[iForm].appendChild(user_current_mode);
							}catch(e){
								
							}
						}
						
						if(document.forms[iForm].name=='form_values'){
							formName='form_values';
						}
						
						if(!formName && document.forms[iForm].name=='form'){
							formName='form';
						}
					}
				}
				
				document.getElementsByName(formName)[0].submit();
				
			}
		</script>
		";
		
		return $html;
	}
	
	/**
	 * retourne l'url de l'icone � afficher dans la liste de choix
	 *
	 * @param string $name le nom de l'icone
	 * @return string le path de l'icone
	 */
	private function get_icon_url($name){
		global $css;
		global $base_path;
	
		$src='';
		if(file_exists($base_path.'/styles/'.$css.'/images/'.$name)){
			$src=$base_path.'/styles/'.$css.'/images/'.$name;
		}elseif(file_exists($base_path.'/styles/common/images/'.$name)){
			$src=$base_path.'/styles/common/images/'.$name;
		}elseif(file_exists($base_path.'/images/'.$name)){
			$src=$base_path.'/images/'.$name;
		}
		return $src;
	}
}