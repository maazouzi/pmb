<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: authority.class.php,v 1.3 2015-06-09 09:41:19 pmbs Exp $

require_once($include_path."/h2o/pmb_h2o.inc.php");

class authority {
	
	/**
	 * 
	 * @var unknown
	 */
	private $type_autority;
	
	/**
	 * 
	 * @var unknown
	 */
	private $id;
	
	/**
	 * 
	 * @var unknown
	 */
	private $authority_class;
	
	private $autlink_class;
	
	public function __construct($type_autority,$id){
		$this->type_autority = $type_autority;
		$this->id = $id*1;
	}
	
	public function get_type_autority() {
		return $this->type_autority;
	}
	
	public function get_id() {
		return $this->id;
	}
	
	public function lookup($name,$context) {
		$value = null;
		if(strpos($name,":authority.")!==false){
			$property = str_replace(":authority.","",$name);
			$value = $this->generic_lookup($this, $property);
			if(!$value){
				$this->init_authority_class();
				$value = $this->specific_lookup($property);
				if(!$value){
					$value = $this->generic_lookup($this->authority_class, $property);
				}
			}
		}else if (strpos($name,":aut_link.")!==false){
			$this->init_autlink_class();
			$property = str_replace(":aut_link.","",$name);
			$value = $this->generic_lookup($this->autlink_class, $property);
		}
		if(!$value){
			$value = null;
		}
		return $value;
	}
	
	private function specific_lookup($property){
		$value = null;
		switch($this->get_type_autority()){
			case "category" :
				if($property == "child_list"){
					global $css;
					$value = $this->authority_class->child_list("./images/folder.gif",$css);
				}
				break;
		}
		return $value;
	}
	
	private function generic_lookup($obj,$property){
		$value = null;		
		if(is_object($obj)){
			$elems = array();
			$property_to_check = $property;
			if(strpos($property,".") !== false){
				$elems = explode(".",$property);
				$property_to_check = $elems[0];
			}
			if(method_exists($obj, $property_to_check)){
				$value = $obj->{$property_to_check}();
			}
			//on teste la propriété...
			
			if(!$value && property_exists($obj, $property_to_check)){
				$value = $obj->{$property_to_check};
			}
			//on teste le getter...
			if(!$value && method_exists($obj, "get_".$property_to_check)){
				$value = $obj->{"get_".$property_to_check}();
			}
		}
		if(strpos($property,".") !== false){
			for($i=1 ; $i<count($elems) ; $i++){
				$value = $value[$elems[$i]];	
			}
		}
		return $value;
	}
	
	public function render($context=array()){
		global $opac_authorities_templates_folder;
		if(!$opac_authorities_templates_folder){
			$opac_authorities_templates_folder = "./includes/templates/authorities/common";
		}
		$template_path = $opac_authorities_templates_folder."/".$this->get_type_autority().".html";
		
		if(!file_exists($template_path)){
			$template_path =  "./includes/templates/authorities/common/".$this->get_type_autority().".html";
		}
		if(file_exists($opac_authorities_templates_folder."/".$this->get_type_autority()."_subst.html")){
			$template_path =  $opac_authorities_templates_folder."/".$this->get_type_autority()."_subst.html";
		}
		if(file_exists($template_path)){
			$h2o = new H2o($template_path);
			$h2o->addLookup(array($this,"lookup"));
			echo $h2o->render($context);
		}
	}
	
	private function init_authority_class(){
		if(!$this->authority_class){
			switch($this->get_type_autority()){
				case "author" :
					$this->load_class("author");
					$this->authority_class = new auteur($this->get_id());
					break;
				case "publisher" :
					$this->load_class("publisher");
					$this->authority_class = new publisher($this->get_id());
					break;
				case "collection" :
					$this->load_class("collection");
					$this->authority_class = new collection($this->get_id());
					break;
				case "subcollection" :
					$this->load_class("subcollection");
					$this->authority_class = new subcollection($this->get_id());
					break;
				case "serie" :
					$this->load_class("serie");
					$this->authority_class = new serie($this->get_id());
					break;
				case "indexint" :
					$this->load_class("indexint");
					$this->authority_class = new indexint($this->get_id());
					break;
				case "titre_uniforme" :
					$this->load_class("titre_uniforme");
					$this->authority_class = new titre_uniforme($this->get_id());
					break;
				case "category" :
					global $lang;
					$this->load_class("categorie");
					$this->authority_class = new categorie($this->get_id(),$lang);
					break;
				case "authperso" :
					$this->load_class("authperso_authority");
					$this->authority_class = new authperso_authority($this->get_id());
					break;
					
			}
		}
	}
	private function init_autlink_class(){
		if(!$this->autlink_class){
			$this->load_class("aut_link");
			switch($this->get_type_autority()){
				case "author" :
					$this->autlink_class= new aut_link(AUT_TABLE_AUTHORS,$this->get_id());
					break;
				case "publisher" :
					$this->autlink_class= new aut_link(AUT_TABLE_PUBLISHERS,$this->get_id());
					break;
				case "collection" :
					$this->autlink_class= new aut_link(AUT_TABLE_COLLECTIONS,$this->get_id());
					break;
				case "subcollection" :
					$this->autlink_class= new aut_link(AUT_TABLE_SUB_COLLECTIONS,$this->get_id());
					break;
				case "serie" :
					$this->autlink_class= new aut_link(AUT_TABLE_SERIES,$this->get_id());
					break;
				case "indexint" :
					$this->autlink_class= new aut_link(AUT_TABLE_INDEXINT,$this->get_id());
					break;
				case "titre_uniforme" :
					$this->autlink_class= new aut_link(AUT_TABLE_TITRES_UNIFORMES,$this->get_id());
					break;
				case "category" :
					$this->autlink_class= new aut_link(AUT_TABLE_CATEG,$this->get_id());
					break;
				case "concept" :
					$this->autlink_class= new aut_link(AUT_TABLE_CONCEPT,$this->get_id());
					break;
			}
		}
	}
	
	public function get_indexing_concepts(){
		$this->load_class("/skos/skos_concepts_list");
		$this->load_class("/skos/skos_view_concepts");
		$concepts_list = new skos_concepts_list();
		switch($this->get_type_autority()){
			case "author" :
				if ($concepts_list->set_concepts_from_object(TYPE_AUTHOR, $this->get_id())) {
					return skos_view_concepts::get_list_in_authority($concepts_list);
				}
				break;
			case "publisher" :
				if ($concepts_list->set_concepts_from_object(TYPE_PUBLISHER, $this->get_id())) {
					return skos_view_concepts::get_list_in_authority($concepts_list);
				}
				break;
			case "collection" :
				if ($concepts_list->set_concepts_from_object(TYPE_COLLECTION, $this->get_id())) {
					return skos_view_concepts::get_list_in_authority($concepts_list);
				}
				break;
			case "subcollection" :
				if ($concepts_list->set_concepts_from_object(TYPE_SUBCOLLECTION, $this->get_id())) {
					return skos_view_concepts::get_list_in_authority($concepts_list);
				}
				break;
			case "serie" :
				if ($concepts_list->set_concepts_from_object(TYPE_SERIE, $this->get_id())) {
					return skos_view_concepts::get_list_in_authority($concepts_list);
				}
				break;
			case "indexint" :
				if ($concepts_list->set_concepts_from_object(TYPE_INDEXINT, $this->get_id())) {
					return skos_view_concepts::get_list_in_authority($concepts_list);
				}
				break;
			case "titre_uniforme" :
				if ($concepts_list->set_concepts_from_object(TYPE_TITRE_UNIFORME, $this->get_id())) {
					return skos_view_concepts::get_list_in_authority($concepts_list);
				}
				break;
			case "category" :
				if ($concepts_list->set_concepts_from_object(TYPE_CATEGORY, $this->get_id())) {
					return skos_view_concepts::get_list_in_authority($concepts_list);
				}
				break;
			case "authperso" :
				if ($concepts_list->set_concepts_from_object(TYPE_AUTHPERSO, $this->get_id())) {
					return skos_view_concepts::get_list_in_authority($concepts_list);
				}
				break;
		}
		return null;
	}
	
	public function get_concepts_composed(){
		$this->load_class("/skos/skos_concepts_list");
		$this->load_class("/skos/skos_view_concepts");
		$composed_concepts = new skos_concepts_list();
		switch($this->get_type_autority()){
			case "author" :
			case "publisher" :
			case "collection" :
			case "subcollection" :
			case "serie" :
			case "indexint" :
			case "titre_uniforme" :		
			case "category" :			
			case "concept" :				
			case "authperso" :		
				if ($composed_concepts->set_composed_concepts_built_with_element($this->get_id(), $this->get_type_autority())) {
					print skos_view_concepts::get_composed_concepts_list($composed_concepts);
				}
				break;
		}
		return null;
	}
	
	private function load_class($classname) {
		global $base_path,$include_path,$class_path,$javascript_path,$style_path;
		require_once($class_path."/".$classname.".class.php");
	}
}