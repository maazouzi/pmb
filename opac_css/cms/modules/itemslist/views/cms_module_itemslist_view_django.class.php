<?php
// +-------------------------------------------------+
// � 2002-2012 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: cms_module_itemslist_view_django.class.php,v 1.1 2015-02-26 16:02:03 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class cms_module_itemslist_view_django extends cms_module_common_view_django{
	
	public function __construct($id=0){
		parent::__construct($id);
		$this->default_template = "<div>
{% for item in items %}
<h3>{{item.title}}</h3>
<img src='{{item.logo_url}}'/>
<blockquote>{{item.summary}}</blockquote>
<blockquote>{{item.content}}</blockquote>
{% endfor %}
</div>";
	}
	
	public function get_format_data_structure(){
		$datasource = new cms_module_itemslist_datasource_items();
		$datas = $datasource->get_format_data_structure();
		
		$format_datas = array_merge($datas,parent::get_format_data_structure());
		return $format_datas;
	}
}