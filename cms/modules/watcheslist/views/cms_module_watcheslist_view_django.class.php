<?php
// +-------------------------------------------------+
// © 2002-2012 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: cms_module_watcheslist_view_django.class.php,v 1.1 2015-02-25 17:28:31 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class cms_module_watcheslist_view_django extends cms_module_common_view_django{
	
	public function __construct($id=0){
		parent::__construct($id);
		$this->default_template = "<div>
{% for watch in watches %}
<h3>{{watch.title}}</h3>
<img src='{{watch.logo_url}}'/>
<blockquote>{{watch.desc}}</blockquote>
{% endfor %}
</div>";
	}
		
	public function get_format_data_structure(){		
		$datasource = new cms_module_watcheslist_datasource_watches();
		$datas = $datasource->get_format_data_structure();
		
		$format_datas = array_merge($datas,parent::get_format_data_structure());
		return $format_datas;
	}
}