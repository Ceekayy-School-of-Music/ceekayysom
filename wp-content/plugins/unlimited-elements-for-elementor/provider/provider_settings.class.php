<?php
/**
 * @package Unlimited Elements
 * @author UniteCMS.net
 * @copyright (C) 2012 Unite CMS, All Rights Reserved. 
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * */
defined('UNLIMITED_ELEMENTS_INC') or die('Restricted access');

class UniteCreatorSettings extends UniteCreatorSettingsWork{

	
	/**
	 * add settings provider types
	 */
	protected function addSettingsProvider($type, $name,$value,$title,$extra ){
		
		$isAdded = false;
		
		return($isAdded);
	}
	
	
	/**
	 * show taxanomy
	 */
	private function showTax(){
										
		$showTax = UniteFunctionsUC::getGetVar("maxshowtax", "", UniteFunctionsUC::SANITIZE_NOTHING);
		$showTax = UniteFunctionsUC::strToBool($showTax);
		
		if($showTax == true){
			
			$args = array("taxonomy"=>"");
			$cats = get_categories($args);
			
			$arr1 = UniteFunctionsWPUC::getTaxonomiesWithCats();
			
			$arrPostTypes = UniteFunctionsWPUC::getPostTypesAssoc();
			$arrTax = UniteFunctionsWPUC::getTaxonomiesWithCats();
			$arrCustomTypes = get_post_types(array('_builtin' => false));
			
			$arr = get_taxonomies();
			
			$taxonomy_objects = get_object_taxonomies( 'post', 'objects' );
   			dmp($taxonomy_objects);
   			
			dmp($arrCustomTypes);
			dmp($arrPostTypes);
			exit();
		}
		
	}
	
	/**
	 * add template picker
	 */
	protected function addTemplatePicker($name,$value,$title,$extra){
		
		$this->addPostIDSelect($name."_templateid", "Choose Template", null, "elementor_template");
		
	}
	
	/**
	 * get categories from all post types
	 */
	protected function getCategoriesFromAllPostTypes($arrPostTypes){
		
		if(empty($arrPostTypes))
			return(array());

		$arrAllCats = array();
		$arrAllCats[__("All Categories", "unlimited-elements-for-elementor")] = "all";
		
		foreach($arrPostTypes as $name => $arrType){
		
			if($name == "page")
				continue;
			
			$postTypeTitle = UniteFunctionsUC::getVal($arrType, "title");
			
			$cats = UniteFunctionsUC::getVal($arrType, "cats");
			
			if(empty($cats))
				continue;
			
			foreach($cats as $catID => $catTitle){
				
				if($name != "post")
					$catTitle = $catTitle." ($postTypeTitle type)";
				
				$arrAllCats[$catTitle] = $catID;
			}
			
		}
		
		
		return($arrAllCats);
	}
	
	
	
	/**
	 * get taxonomies array for terms picker
	 */
	private function addPostTermsPicker_getArrTaxonomies($arrPostTypesWithTax){
		
		$arrAllTax = array();
		
		
		//make taxonomies data
		$arrTaxonomies = array();
		foreach($arrPostTypesWithTax as $typeName => $arrType){
			
			$arrItemTax = UniteFunctionsUC::getVal($arrType, "taxonomies");
			
			$arrTaxOutput = array();
			
			//some fix that avoid double names
			$arrDuplicateValues = UniteFunctionsUC::getArrayDuplicateValues($arrItemTax);
			
			foreach($arrItemTax as $slug => $taxTitle){
				
				$isDuplicate = array_key_exists($taxTitle, $arrDuplicateValues);
				
				//some modification for woo
				if($taxTitle == "Tag" && $slug != "post_tag")
					$isDuplicate = true;
				
				if(isset($arrAllTax[$taxTitle]))
					$isDuplicate = true;
					
				if($isDuplicate == true)
					$taxTitle = UniteFunctionsUC::convertHandleToTitle($slug);
				
				$taxTitle = ucwords($taxTitle);
				
				$arrTaxOutput[$slug] = $taxTitle;
				
				$arrAllTax[$taxTitle] = $slug;
			}
			
			if(!empty($arrTaxOutput))
				$arrTaxonomies[$typeName] = $arrTaxOutput;
		}
		
		$response = array();
		$response["post_type_tax"] = $arrTaxonomies;
		$response["taxonomies_simple"] = $arrAllTax;
		
		
		return($response);
	}

	
	/**
	 * add users picker
	 */
	protected function addUsersPicker($name,$value,$title,$extra){
		
		//----- custom or manual
		
		$params = array();
		$params["origtype"] = UniteCreatorDialogParam::PARAM_DROPDOWN;
		
		$arrType = array();
		$arrType["custom"] = __("Custom Query", "unlimited-elements-for-elementor");
		$arrType["manual"] = __("Manual Selection", "unlimited-elements-for-elementor");
		
		$arrType = array_flip($arrType);
		
		$this->addSelect($name."_type", $arrType, __("Select Users By", "unlimited-elements-for-elementor"), "custom", $params);
		
		$arrConditionCustom = array();
		$arrConditionCustom[$name."_type"] = "custom";
		
		$arrConditionManual = array();
		$arrConditionManual[$name."_type"] = "manual";
		
		//----- roles in -------
		
		$arrRoles = UniteFunctionsWPUC::getRolesShort();
		
		$params = array();
		$params["origtype"] = UniteCreatorDialogParam::PARAM_DROPDOWN;
		$params["description"] = __("Leave empty for all the roles", "unlimited-elements-for-elementor");
		$params["elementor_condition"] = $arrConditionCustom;
		
		if(!empty($arrRoles))
			$arrRoles = array_flip($arrRoles);
		
		$role = UniteFunctionsUC::getVal($value, $name."_role");
		if(empty($role))
			$role = UniteFunctionsUC::getArrFirstValue($arrRoles);
		
		$params["is_multiple"] = true;
		$params["placeholder"] = __("All Roles", "unlimited-elements-for-elementor");
		//$params["description"] = __("Get all the users if leave empty", "unlimited-elements-for-elementor");
		
		$this->addMultiSelect($name."_role", $arrRoles, __("Select Roles", "unlimited-elements-for-elementor"), $role, $params);
		
		
		//-------- exclude roles ---------- 
		
		$arrRoles = UniteFunctionsWPUC::getRolesShort();
		
		$params = array();
		$params["origtype"] = UniteCreatorDialogParam::PARAM_DROPDOWN;
		$params["elementor_condition"] = $arrConditionCustom;
		
		if(!empty($arrRoles))
			$arrRoles = array_flip($arrRoles);
		
		$roleExclude = UniteFunctionsUC::getVal($value, $name."_role_exclude");
		
		$params["is_multiple"] = true;
		
		$this->addMultiSelect($name."_role_exclude", $arrRoles, __("Exclude Roles", "unlimited-elements-for-elementor"), $roleExclude, $params);
		
		//---- exclude user -----
		
		$arrAuthors = UniteFunctionsWPUC::getArrAuthorsShort();
		
		$params = array();
		$params["origtype"] = UniteCreatorDialogParam::PARAM_DROPDOWN;
		$params["is_multiple"] = true;
		$params["placeholder"] = __("Select one or more users", "unlimited-elements-for-elementor");
		$params["elementor_condition"] = $arrConditionCustom;
		
		$arrAuthors = array_flip($arrAuthors);
		
		$this->addMultiSelect($name."_exclude_authors", $arrAuthors, __("Exclude By Specific Users", "unlimited-elements-for-elementor"), "", $params);
		
		//---- include users -----
		
		$params = array();
		$params["origtype"] = UniteCreatorDialogParam::PARAM_DROPDOWN;
		$params["is_multiple"] = true;
		$params["placeholder"] = __("Select one or more users", "unlimited-elements-for-elementor");
		$params["elementor_condition"] = $arrConditionManual;
		
		$this->addMultiSelect($name."_include_authors", $arrAuthors, __("Select Specific Users", "unlimited-elements-for-elementor"), "", $params);
		
		
		//---- hr before max users -----
		
		$params = array();
		$params["origtype"] = UniteCreatorDialogParam::PARAM_HR;
		$params["elementor_condition"] = $arrConditionCustom;
		
		$this->addHr("hr_before_max", $params);
		
		//---- max items -----
		 
		$params = array("unit"=>"users");
		$params["origtype"] = UniteCreatorDialogParam::PARAM_TEXTFIELD;
		$params["placeholder"] = __("all users if empty","unlimited-elements-for-elementor");
		$params["elementor_condition"] = $arrConditionCustom;
		$params["add_dynamic"] = true;
		
		$this->addTextBox($name."_maxusers", "", esc_html__("Max Number of Users", "unlimited-elements-for-elementor"), $params);
		
		//---- hr before order by -----
		
		$params = array();
		$params["origtype"] = UniteCreatorDialogParam::PARAM_HR;
		
		$this->addHr("hr_before_orderby", $params);
		
		//---- orderby -----
		
		$arrOrderBy = HelperProviderUC::getArrUsersOrderBySelect();
		$arrOrderBy = array_flip($arrOrderBy);
		
		$params = array();
		$params["origtype"] = UniteCreatorDialogParam::PARAM_DROPDOWN;
		
		$this->addSelect($name."_orderby", $arrOrderBy, __("Order By", "unlimited-elements-for-elementor"), "default", $params);
		
		//--------- order direction -------------
		
		$arrOrderDir = UniteFunctionsWPUC::getArrSortDirection();
		$arrOrderDir = array_flip($arrOrderDir);
				
		$params = array();
		$params["origtype"] = UniteCreatorDialogParam::PARAM_DROPDOWN;
		
		$this->addSelect($name."_orderdir", $arrOrderDir, __("Order Direction", "unlimited-elements-for-elementor"), "default", $params);

		//---- hr before meta -----
		
		$params = array();
		$params["origtype"] = UniteCreatorDialogParam::PARAM_HR;
		
		$this->addHr("hr_before_metakeys", $params);
			
		//---- meta keys addition -----
		
		$params = array();
		$params["origtype"] = UniteCreatorDialogParam::PARAM_TEXTFIELD;
		$params["description"] = __("Get additional meta data by given meta keys comma separated","unlimited-elements-for-elementor");
		$params["placeholder"] = "meta_key1, meta_key2...";
		$params["label_block"] = true;
		$params["add_dynamic"] = true;
		
		$this->addTextBox($name."_add_meta_keys", "", __("Additional Meta Data Keys", "unlimited-elements-for-elementor"), $params);
		
		//---- hr before debug -----
		
		$params = array();
		$params["origtype"] = UniteCreatorDialogParam::PARAM_HR;
		
		$this->addHr("hr_before_debug", $params);
				
		//---- show debug -----
		
		$params = array();
		$params["origtype"] = UniteCreatorDialogParam::PARAM_RADIOBOOLEAN;
		$params["description"] = __("Show the query for debugging purposes. Don't forget to turn it off before page release", "unlimited-elements-for-elementor");
		
		$this->addRadioBoolean($name."_show_query_debug", __("Show Query Debug", "unlimited-elements-for-elementor"), false, "Yes", "No", $params);
		
		
	}

	/**
	 * add menu picker
	 */
	protected function addMenuPicker($name, $value, $title, $extra){
		
		$arrMenus = UniteFunctionsWPUC::getMenusListShort();
		
		$menuID = UniteFunctionsUC::getVal($value, $name."_id");
		
		if(empty($menuID))
			$menuID = UniteFunctionsUC::getFirstNotEmptyKey($arrMenus);
					
		$arrMenus = array_flip($arrMenus);
		
		$params = array();
		$params["origtype"] = UniteCreatorDialogParam::PARAM_DROPDOWN;
		
		$this->addSelect($name."_id", $arrMenus, __("Select Menu", "unlimited-elements-for-elementor"), $menuID, $params);
		
		//add depth
		$arrDepth = array();
		$arrDepth["0"] = __("All Depths", "unlimited-elements-for-elementor");
		$arrDepth["1"] = __("1", "unlimited-elements-for-elementor");
		$arrDepth["2"] = __("2", "unlimited-elements-for-elementor");
		$arrDepth["3"] = __("3", "unlimited-elements-for-elementor");
		
		$arrDepth = array_flip($arrDepth);
		$depth = UniteFunctionsUC::getVal($value, $name."_depth", "0");
				
		$this->addSelect($name."_depth", $arrDepth, __("Max Depth", "unlimited-elements-for-elementor"), $depth, $params);
		
	}
	
	private function __________TERMS_______(){}
	
	/**
	 * add post terms settings
	 */
	protected function addPostTermsPicker($name, $value, $title, $extra){
		
		$arrPostTypesWithTax = UniteFunctionsWPUC::getPostTypesWithTaxomonies(GlobalsProviderUC::$arrFilterPostTypes, false);
		
		$taxData = $this->addPostTermsPicker_getArrTaxonomies($arrPostTypesWithTax);
		
		$arrPostTypesTaxonomies = $taxData["post_type_tax"];
		
		$arrTaxonomiesSimple = $taxData["taxonomies_simple"];
				
		//----- add post types ---------
		
		//prepare post types array
		
		$arrPostTypes = array();
		foreach($arrPostTypesWithTax as $typeName => $arrType){
			
			$title = UniteFunctionsUC::getVal($arrType, "title");
			if(empty($title))
				$title = ucfirst($typeName);
			
			$arrPostTypes[$title] = $typeName;
		}
		
		$postType = UniteFunctionsUC::getVal($value, $name."_posttype");
		if(empty($postType))
			$postType = UniteFunctionsUC::getArrFirstValue($arrPostTypes);
		
		$params = array();
		
		$params[UniteSettingsUC::PARAM_CLASSADD] = "unite-setting-post-type";
		$dataTax = UniteFunctionsUC::encodeContent($arrPostTypesTaxonomies);
		
		$params[UniteSettingsUC::PARAM_ADDPARAMS] = "data-arrposttypes='$dataTax' data-settingtype='select_post_taxonomy' data-settingprefix='{$name}'";
		$params["datasource"] = "post_type";
		$params["origtype"] = "uc_select_special";
		
		$this->addSelect($name."_posttype", $arrPostTypes, __("Select Post Type", "unlimited-elements-for-elementor"), $postType, $params);
		
		//---------- add taxonomy ---------
				
		$params = array();
		$params["datasource"] = "post_taxonomy";
		$params[UniteSettingsUC::PARAM_CLASSADD] = "unite-setting-post-taxonomy";
		$params["origtype"] = UniteCreatorDialogParam::PARAM_DROPDOWN;
		
		$arrTax = UniteFunctionsUC::getVal($arrPostTypesTaxonomies, $postType, array());
		
		if(!empty($arrTax))
			$arrTax = array_flip($arrTax);
				
		$taxonomy = UniteFunctionsUC::getVal($value, $name."_taxonomy");
		if(empty($taxonomy))
			$taxonomy = UniteFunctionsUC::getArrFirstValue($arrTax);
				
		$this->addSelect($name."_taxonomy", $arrTaxonomiesSimple, __("Select Taxonomy", "unlimited-elements-for-elementor"), $taxonomy, $params);
		
		// --------- add include by -------------
		
		$arrIncludeBy = array();
		$arrIncludeBy["spacific_terms"] = __("Specific Terms","unlimited-elements-for-elementor");
		$arrIncludeBy["parents"] = __("Parent Of","unlimited-elements-for-elementor");
		$arrIncludeBy["search"] = __("By Search Text","unlimited-elements-for-elementor");
		$arrIncludeBy["childless"] = __("Only Childless","unlimited-elements-for-elementor");
		$arrIncludeBy["no_parent"] = __("Not a Child of Other Term","unlimited-elements-for-elementor");
		$arrIncludeBy["meta"] = __("Term Meta","unlimited-elements-for-elementor");
		
		$arrIncludeBy = array_flip($arrIncludeBy);
		
		$params = array();
		$params["is_multiple"] = true;
		$params["origtype"] = UniteCreatorDialogParam::PARAM_DROPDOWN;
				
		$this->addMultiSelect($name."_includeby", $arrIncludeBy, esc_html__("Include By", "unlimited-elements-for-elementor"), "", $params);

		// --------- include by meta key -------------
		
		$params = array();
		$params["origtype"] = UniteCreatorDialogParam::PARAM_TEXTFIELD;
		$params["placeholder"] = __("Meta Key","unlimited-elements-for-elementor");
		$params["elementor_condition"] = array($name."_includeby"=>"meta");
		
		$this->addTextBox($name."_include_metakey", "", esc_html__("Include by Meta Key", "unlimited-elements-for-elementor"), $params);

		// --------- include by meta compare -------------
		
		$params = array();
		$params["origtype"] = UniteCreatorDialogParam::PARAM_DROPDOWN;
		$params["elementor_condition"] = array($name."_includeby"=>"meta");
		$params["description"] = __("Get only those terms that has the meta key/value. For IN, NOT IN, BETWEEN, NOT BETWEEN compares, use coma saparated values");
		
		$arrItems = array();
		$arrItems["="] = "Equals";
		$arrItems["!="] = "Not Equals";
		$arrItems[">"] = "More Then";
		$arrItems["<"] = "Less Then";
		$arrItems[">="] = "More Or Equal";
		$arrItems["<="] = "Less Or Equal";
		$arrItems["LIKE"] = "LIKE";
		$arrItems["NOT LIKE"] = "NOT LIKE";
		
		$arrItems["IN"] = "IN";
		$arrItems["NOT IN"] = "NOT IN";
		$arrItems["BETWEEN"] = "BETWEEN";
		$arrItems["NOT BETWEEN"] = "NOT BETWEEN";
		
		$arrItems["EXISTS"] = "EXISTS";
		$arrItems["NOT EXISTS"] = "NOT EXISTS";
		
		$arrItems = array_flip($arrItems);
		
		$this->addSelect($name."_include_metacompare", $arrItems, esc_html__("Include by Meta Compare", "unlimited-elements-for-elementor"), "=", $params);
		
		
		// --------- include by meta value -------------
		
		$params = array();
		$params["origtype"] = UniteCreatorDialogParam::PARAM_TEXTFIELD;
		$params["placeholder"] = __("Meta Value","unlimited-elements-for-elementor");
		$params["elementor_condition"] = array($name."_includeby"=>"meta");
		$params["add_dynamic"] = true;
		
		$this->addTextBox($name."_include_metavalue", "", esc_html__("Include by Meta Value", "unlimited-elements-for-elementor"), $params);

		
		// --------- add include by specific term -------------
		
		$params = array();
		$params["description"] = __("Only those selected terms will be loaded");
		
		$elementorCondition = array($name."_includeby"=>"spacific_terms");
		
		$exclude = UniteFunctionsUC::getVal($value, $name."_exclude");
		
		$addAttrib = "data-taxonomyname='{$name}_taxonomy'";
		
		$this->addPostIDSelect($name."_include_specific", __("Select Specific Terms", "unlimited-elements-for-elementor"), $elementorCondition, "terms", $addAttrib, $params);

		// --------- add terms parents -------------
		
		$params = array();
		$params["placeholder"] = "all--parents";
		
		$elementorCondition = array($name."_includeby"=>"parents");
		
		$exclude = UniteFunctionsUC::getVal($value, $name."_exclude");
				
		$addAttrib = "data-taxonomyname='{$name}_taxonomy' data-issingle='true'";
		
		$this->addPostIDSelect($name."_include_parent", __("Select Parent Term", "unlimited-elements-for-elementor"), $elementorCondition, "terms", $addAttrib, $params);
		
		// --------- add terms parents - direct switcher -------------
		
		$params = array();
		$params["origtype"] = UniteCreatorDialogParam::PARAM_RADIOBOOLEAN;
		$params["description"] = __("If turned off, all the terms tree will be selected", "unlimited-elements-for-elementor");
		$params["elementor_condition"] = array($name."_includeby"=>"parents");
		
		$this->addRadioBoolean($name."_include_parent_isdirect", __("Is Direct Parent", "unlimited-elements-for-elementor"), true, "Yes", "No", $params);

		// --------- by search phrase -------------
		
		$params = array("unit"=>"terms");
		$params["origtype"] = UniteCreatorDialogParam::PARAM_TEXTFIELD;
		$params["placeholder"] = __("Search Text","unlimited-elements-for-elementor");
		$params["elementor_condition"] = array($name."_includeby"=>"search");
		$params["add_dynamic"] = true;
		
		$this->addTextBox($name."_include_search", "", esc_html__("Include by Search", "unlimited-elements-for-elementor"), $params);
		
		
		//---------- add hr ---------
		
		$params = array();
		$params["origtype"] = UniteCreatorDialogParam::PARAM_HR;
		$this->addHr("after_include_by",$params);

		// --------- add exclude by -------------
		
		$arrIncludeBy = array();
		$arrIncludeBy["spacific_terms"] = __("Specific Terms","unlimited-elements-for-elementor");
		$arrIncludeBy["current_term"] = __("Current Term (for archive only)","unlimited-elements-for-elementor");
		$arrIncludeBy["hide_empty"] = __("Hide Empty Terms","unlimited-elements-for-elementor");
		
		$arrIncludeBy = array_flip($arrIncludeBy);
		
		$params = array();
		$params["is_multiple"] = true;
		$params["origtype"] = UniteCreatorDialogParam::PARAM_DROPDOWN;
				
		$this->addMultiSelect($name."_excludeby", $arrIncludeBy, esc_html__("Exclude By", "unlimited-elements-for-elementor"), "", $params);
		
		
		//---------- add exclude ---------
		
		$elementorCondition = array($name."_excludeby"=>"spacific_terms");
		
		$exclude = UniteFunctionsUC::getVal($value, $name."_exclude");
		
		$addAttrib = "data-taxonomyname='{$name}_taxonomy'";
		
		$this->addPostIDSelect($name."_exclude", __("Exclude Terms", "unlimited-elements-for-elementor"), $elementorCondition, "terms", $addAttrib);
		
		//----- exclude all the parents tree --------------
		
		$params = array();
		$params["origtype"] = UniteCreatorDialogParam::PARAM_RADIOBOOLEAN;
		$params["elementor_condition"] = $elementorCondition;
		
		$this->addRadioBoolean($name."_exclude_tree", __("Exclude With All Children Tree", "unlimited-elements-for-elementor"), true, "Yes", "No", $params);
		
		
		//----- add hr --------------
		
		$params = array();
		$params["origtype"] = UniteCreatorDialogParam::PARAM_HR;
		
		$this->addHr($name."_post_terms_before_additions", $params);
		
		//--------- add max terms -------------
		
		$params = array("unit"=>"terms");
		$params["origtype"] = UniteCreatorDialogParam::PARAM_TEXTFIELD;
		$params["placeholder"] = __("100 terms if empty","unlimited-elements-for-elementor");
		$params["add_dynamic"] = true;
		
		$this->addTextBox($name."_maxterms", "", esc_html__("Max Number of Terms", "unlimited-elements-for-elementor"), $params);
						
		//------- add hr before order by -------------
		
		$params = array();
		$params["origtype"] = UniteCreatorDialogParam::PARAM_HR;
		
		$this->addHr($name."_post_terms_before_orderby", $params);
		
		
		// --------- add order by -------------
		
		$arrOrderBy = UniteFunctionsWPUC::getArrTermSortBy();
		$arrOrderBy["include"] = __("Include - (specific terms order)", "unlimited-elements-for-elementor");
		$arrOrderBy["meta_value"] = __("Meta Value", "unlimited-elements-for-elementor");
		$arrOrderBy["meta_value_num"] = __("Meta Value - Numeric", "unlimited-elements-for-elementor");
		
		
		$arrOrderBy = array_flip($arrOrderBy);
		
		$orderBy = UniteFunctionsUC::getVal($value, $name."_orderby", "name");
		
		$params = array();
		$params["origtype"] = UniteCreatorDialogParam::PARAM_DROPDOWN;
		
		$this->addSelect($name."_orderby", $arrOrderBy, __("Order By", "unlimited-elements-for-elementor"), $orderBy, $params);
		
		//--- meta value param -------
		
		$params = array();
		$params["origtype"] = UniteCreatorDialogParam::PARAM_TEXTFIELD;
		
		$arrCondition = array();
		$arrCondition[$name."_orderby"] = array("meta_value","meta_value_num");
		
		$params["elementor_condition"] = $arrCondition;
		$params["add_dynamic"] = true;
		
		$this->addTextBox($name."_orderby_meta_key", "" , __("&nbsp;&nbsp;Custom Field Name","unlimited-elements-for-elementor"), $params);
		
		
		//--------- add order direction -------------
		
		$arrOrderDir = UniteFunctionsWPUC::getArrSortDirection();
		
		$orderDir = UniteFunctionsUC::getVal($value, $name."_orderdir", UniteFunctionsWPUC::ORDER_DIRECTION_ASC);
		
		$arrOrderDir = array_flip($arrOrderDir);
		
		$params = array();
		$params["origtype"] = UniteCreatorDialogParam::PARAM_DROPDOWN;
		
		$this->addSelect($name."_orderdir", $arrOrderDir, __("Order Direction", "unlimited-elements-for-elementor"), $orderDir, $params);
		
		
		//add hr
		$params = array();
		$params["origtype"] = UniteCreatorDialogParam::PARAM_HR;
		
		$this->addHr($name."_post_terms_before_queryid", $params);
		
		//---- show debug -----
		
		$params = array();
		$params["origtype"] = UniteCreatorDialogParam::PARAM_RADIOBOOLEAN;
		$params["description"] = __("Show the query for debugging purposes. Don't forget to turn it off before page release", "unlimited-elements-for-elementor");
		
		$this->addRadioBoolean($name."_show_query_debug", __("Show Query Debug", "unlimited-elements-for-elementor"), false, "Yes", "No", $params);
		
		
		//add hr
		$params = array();
		$params["origtype"] = UniteCreatorDialogParam::PARAM_HR;
		
		$this->addHr($name."post_terms_sap", $params);
		
		
	}
	
	
	/**
	 * add woo commerce categories picker
	 */
	protected function addWooCatsPicker($name, $value, $title, $extra){

		$conditionQuery = array(
			$name."_type" => "query",
		);
		
		$conditionManual = array(
			$name."_type" => "manual",
		);
		
		
		//---------- type choosing ---------
		
		$arrType = array();
		$arrType["query"] = __("Categories Query","unlimited-elements-for-elementor");
		$arrType["manual"] = __("Manual Selection","unlimited-elements-for-elementor");
		
		$arrType = array_flip($arrType);
		
		$params = array();
		$params["origtype"] = UniteCreatorDialogParam::PARAM_DROPDOWN;
		
		$type = UniteFunctionsUC::getVal($value, $name."_type", "query");
		
		$this->addSelect($name."_type", $arrType, __("Selection Type", "unlimited-elements-for-elementor"), $type, $params);
		
		//---------- add hr ---------
		
		$params = array();
		$params["origtype"] = UniteCreatorDialogParam::PARAM_HR;
		
		$this->addHr("woocommere_terms_sap_type", $params);
		
		
		//---------- add parent ---------
		
		$params = array();
		$params["origtype"] = UniteCreatorDialogParam::PARAM_TEXTFIELD;
		$params["placeholder"] = __("Example: cat1", "unlimited-elements-for-elementor");
		$params["description"] = __("Write parent category slug, if no parent leave empty", "unlimited-elements-for-elementor");
		$params["elementor_condition"] = $conditionQuery;
		
		$parent = UniteFunctionsUC::getVal($value, $name."_parent", "");
		
		$this->addTextBox($name."_parent", $parent, __("Parent Category", "unlimited-elements-for-elementor"), $params);
		
		
		//---------- include children ---------
		
		$includeChildren = UniteFunctionsUC::getVal($value, $name."_children", "not_include");
		
		$arrChildren = array();
		$arrChildren["not_include"] = __("Don't Include", "unlimited-elements-for-elementor");
		$arrChildren["include"] = __("Include", "unlimited-elements-for-elementor");
		$arrChildren = array_flip($arrChildren);
		
		
		//---------- add children ---------
		
		$params = array();
		$params["origtype"] = UniteCreatorDialogParam::PARAM_DROPDOWN;
		$params["elementor_condition"] = $conditionQuery;
		
		$this->addSelect($name."_children", $arrChildren, __("Include Children", "unlimited-elements-for-elementor"), $includeChildren, $params);
		
		
		//---------- add exclude ---------
		
		$params = array();
		$params["origtype"] = UniteCreatorDialogParam::PARAM_TEXTFIELD;
		$params["placeholder"] = "Example: cat1,cat2";
		$params["description"] = "To exclude, enter comma saparated term slugs";
		$params["label_block"] = true;
		$params["elementor_condition"] = $conditionQuery;
		
		$exclude = UniteFunctionsUC::getVal($value, $name."_exclude");
		
		$this->addTextBox($name."_exclude", $exclude, __("Exclude Categories", "unlimited-elements-for-elementor"), $params);
		
		// --------- add exclude categorized -------------
		
		$excludeUncat = UniteFunctionsUC::getVal($value, $name."_excludeuncat", "exclude");
		
		
		$arrExclude = array();
		$arrExclude["exclude"] = __("Exclude","unlimited-elements-for-elementor");
		$arrExclude["no_exclude"] = __("Don't Exclude","unlimited-elements-for-elementor");
		$arrExclude = array_flip($arrExclude);
		
		$params = array();
		$params["origtype"] = UniteCreatorDialogParam::PARAM_DROPDOWN;
		$params["elementor_condition"] = $conditionQuery;
		
		$this->addSelect($name."_excludeuncat", $arrExclude, __("Exclude Uncategorized Category", "unlimited-elements-for-elementor"), $excludeUncat, $params);
		
		// --------- hr -------------
		
		$params = array();
		$params["origtype"] = UniteCreatorDialogParam::PARAM_HR;
		$params["elementor_condition"] = $conditionQuery;
		
		$this->addHr("woocommere_terms_sap1", $params);
		
		// --------- add order by -------------
		
		$arrOrderBy = UniteFunctionsWPUC::getArrTermSortBy();
		$arrOrderBy["meta_value"] = __("Meta Value", "unlimited-elements-for-elementor");
		$arrOrderBy["meta_value_num"] = __("Meta Value - Numeric", "unlimited-elements-for-elementor");
		
		
		$arrOrderBy = array_flip($arrOrderBy);
		
		$orderBy = UniteFunctionsUC::getVal($value, $name."_orderby", "name");
		
		$params = array();
		$params["origtype"] = UniteCreatorDialogParam::PARAM_DROPDOWN;
		$params["elementor_condition"] = $conditionQuery;
		
		$this->addSelect($name."_orderby", $arrOrderBy, __("Order By", "unlimited-elements-for-elementor"), $orderBy, $params);

		//--- meta key param -------
		
		$params = array();
		$params["origtype"] = UniteCreatorDialogParam::PARAM_TEXTFIELD;
		
		$arrCondition = $conditionQuery;
		$arrCondition[$name."_orderby"] = array("meta_value","meta_value_num");
		
		$params["elementor_condition"] = $arrCondition;
		$params["add_dynamic"] = true;
		
		$this->addTextBox($name."_orderby_meta_key", "" , __("&nbsp;&nbsp;Meta Field Name","unlimited-elements-for-elementor"), $params);
		
		
		//--------- add order direction -------------
		
		$arrOrderDir = UniteFunctionsWPUC::getArrSortDirection();
		
		$orderDir = UniteFunctionsUC::getVal($value, $name."_orderdir", UniteFunctionsWPUC::ORDER_DIRECTION_ASC);
		
		$arrOrderDir = array_flip($arrOrderDir);
		
		$params = array();
		$params["origtype"] = UniteCreatorDialogParam::PARAM_DROPDOWN;
		$params["elementor_condition"] = $conditionQuery;
		
		$this->addSelect($name."_orderdir", $arrOrderDir, __("Order Direction", "unlimited-elements-for-elementor"), $orderDir, $params);
		
		
		//--------- add hide empty -------------
		
		$hideEmpty = UniteFunctionsUC::getVal($value, $name."_hideempty", "no_hide");
		
		$arrHide = array();
		$arrHide["no_hide"] = "Don't Hide";
		$arrHide["hide"] = "Hide";
		$arrHide = array_flip($arrHide);
		
		$params = array();
		$params["origtype"] = UniteCreatorDialogParam::PARAM_DROPDOWN;
		$params["elementor_condition"] = $conditionQuery;
		 
		$this->addSelect($name."_hideempty", $arrHide, __("Hide Empty", "unlimited-elements-for-elementor"), $hideEmpty, $params);
		
		//add hr
		$params = array();
		$params["origtype"] = UniteCreatorDialogParam::PARAM_HR;
		$params["elementor_condition"] = $conditionQuery;
		
		$this->addHr("woocommere_terms_sap", $params);

		
		//---------- include categories - manual selection ---------
		
		$params = array();
		$params["origtype"] = UniteCreatorDialogParam::PARAM_TEXTFIELD;
		$params["placeholder"] = __("Example: cat1, cat2", "unlimited-elements-for-elementor");
		$params["description"] = __("Include specific categories by slug", "unlimited-elements-for-elementor");
		$params["label_block"] = true;
		$params["elementor_condition"] = $conditionManual;
		
		$cats = UniteFunctionsUC::getVal($value, $name."_include", "");
		
		$this->addTextBox($name."_include", $cats, __("Include Specific Categories", "unlimited-elements-for-elementor"), $params);
		
		
		//add hr
		$params = array();
		$params["origtype"] = UniteCreatorDialogParam::PARAM_HR;
		
		$this->addHr($name."_post_terms_before_queryid", $params);
		
		//---- show debug -----
		
		$params = array();
		$params["origtype"] = UniteCreatorDialogParam::PARAM_RADIOBOOLEAN;
		$params["description"] = __("Show the query for debugging purposes. Don't forget to turn it off before page release", "unlimited-elements-for-elementor");
		
		$this->addRadioBoolean($name."_show_query_debug", __("Show Query Debug", "unlimited-elements-for-elementor"), false, "Yes", "No", $params);
		
		
		//add hr
		$params = array();
		$params["origtype"] = UniteCreatorDialogParam::PARAM_HR;
		
		$this->addHr($name."post_terms_sap", $params);
		
		
	}
	
	
	/**
	 * add background settings
	 */
	protected function addBackgroundSettings($name, $value, $title, $param){
		
		$arrTypes = array();
		$arrTypes["none"] = __("No Background", "unlimited-elements-for-elementor");
		$arrTypes["solid"] = __("Solid", "unlimited-elements-for-elementor");
		$arrTypes["gradient"] = __("Gradient", "unlimited-elements-for-elementor");
		
		$arrTypes = array_flip($arrTypes);
		
		$type = UniteFunctionsUC::getVal($param, "background_type", "none");
		
		$this->addRadio($name."_type", $arrTypes, "Background Type", $type);
		
		$solid = UniteFunctionsUC::getVal($param, "solid_color");
		$gradient1 = UniteFunctionsUC::getVal($param, "gradient_color1");
		$gradient2 = UniteFunctionsUC::getVal($param, "gradient_color2");
		
		$this->addHr();
		
		//solid color
		$this->startBulkControl($name."_type", "show", "solid");
		
			$this->addColorPicker($name."_color_solid", $solid, __("Solid Color", "unlimited-elements-for-elementor"));
		
		$this->endBulkControl();
		
		//gradient color
		$this->startBulkControl($name."_type", "show", "gradient");
		
			$this->addColorPicker($name."_color_gradient1", $gradient1, __("Gradient Color1", "unlimited-elements-for-elementor"));
			$this->addColorPicker($name."_color_gradient2", $gradient2, __("Gradient Color2", "unlimited-elements-for-elementor"));
		
		$this->endBulkControl();
		
	}
	
	private function __________POSTS_______(){}
	
	
	/**
	 * add post ID select
	 */
	protected function addPostIDSelect($settingName, $text = null, $elementorCondition = null, $isForWoo = false, $addAttribOpt = "", $params = array()){
		
		if(empty($text))
			$text = __("Search and Select Posts", "unlimited-elements-for-elementor");
		
		$params[UniteSettingsUC::PARAM_CLASSADD] = "unite-setting-special-select";
		
		$placeholder = __("All Posts", "unlimited-elements-for-elementor");
		
		if($isForWoo === true)
			$placeholder = __("All Products", "unlimited-elements-for-elementor");
		
		$placeholder = str_replace(" ", "--", $placeholder);
		
		$loaderText = __("Loading Data...", "unlimited-elements-for-elementor");
		$loaderText = UniteFunctionsUC::encodeContent($loaderText);
		
		$addAttrib = "";
		if($isForWoo === true)
			$addAttrib = " data-woo='yes'";
		
		if($isForWoo === "elementor_template"){
			$addAttrib = " data-datatype='elementor_template' data-issingle='true'";
			$placeholder = "All";
		}
		
		if($isForWoo === "terms"){
			$addAttrib = " data-datatype='terms'";
			$placeholder = "All--Terms";
		}
		
		if(isset($params["placeholder"])){
			$placeholder = $params["placeholder"];
		}
		
		if(!empty($addAttribOpt))
			$addAttrib .= " ".$addAttribOpt;
		
		$params[UniteSettingsUC::PARAM_ADDPARAMS] = "data-settingtype='post_ids' data-placeholdertext='{$placeholder}' data-loadertext='$loaderText' $addAttrib";
		
		$params["datasource"] = "post_type";
		$params["origtype"] = "uc_select_special";
		$params["label_block"] = true;
				
		if(!empty($elementorCondition))
			$params["elementor_condition"] = $elementorCondition;
		
		$this->addSelect($settingName, array(), $text , "", $params);
		
	}
	
	/**
	 * add advanced query controls for post selection
	 */
	public function postSelection_addTaxQuery($objControls, $param){
		
		$name = UniteFunctionsUC::getVal($param, "name");
		$title = UniteFunctionsUC::getVal($param, "title");
				
		$repeater = new \Elementor\Repeater();
		
		$repeater->add_control(
			'list_title', [
				'label' => __( 'Title', 'unlimited-elements-for-elementor' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'default' => __( 'List Title' , 'unlimited-elements-for-elementor' ),
				'label_block' => true,
			]
		);
		
		$objControls->add_control(
			$name,
			[
				'label' => $title,
				'type' => \Elementor\Controls_Manager::REPEATER,
				'fields' => $repeater->get_controls(),
				'default' => [],
				'title_field' => '{{{ list_title }}}',
			]
		);
		
		
		
	}
	
	/**
	 * add post list picker
	 */
	protected function addPostsListPicker($name,$value,$title,$extra){
		
 		$simpleMode = UniteFunctionsUC::getVal($extra, "simple_mode");
		$simpleMode = UniteFunctionsUC::strToBool($simpleMode);
		
		$allCatsMode = UniteFunctionsUC::getVal($extra, "all_cats_mode");
		$allCatsMode = UniteFunctionsUC::strToBool($allCatsMode);
		
		$isForWooProducts = UniteFunctionsUC::getVal($extra, "for_woocommerce_products");
		$isForWooProducts = UniteFunctionsUC::strToBool($isForWooProducts);
		
		$addCurrentPosts = UniteFunctionsUC::getVal($extra, "add_current_posts");
		$addCurrentPosts = UniteFunctionsUC::strToBool($addCurrentPosts);
		
		$defaultMaxPosts = UniteFunctionsUC::getVal($extra, "default_max_posts");
		$defaultMaxPosts = (int)($defaultMaxPosts);
				
		$arrPostTypes = UniteFunctionsWPUC::getPostTypesWithCats(GlobalsProviderUC::$arrFilterPostTypes);
		
		$isWpmlExists = UniteCreatorWpmlIntegrate::isWpmlExists();
		
		$textPosts = __("Posts","unlimited-elements-for-elementor");
		$textPost = __("Post","unlimited-elements-for-elementor");
		
		if($isForWooProducts == true){
			$textPosts = __("Products","unlimited-elements-for-elementor");
			$textPost = __("Product","unlimited-elements-for-elementor");			
		}
		
		/*
		if($isWpmlExists == true){
			
			$objWpmlIntegrate = new UniteCreatorWpmlIntegrate();
			
			$arrLanguages = $objWpmlIntegrate->getLanguagesShort(true);
			$activeLanguege = $objWpmlIntegrate->getActiveLanguage();
		}
		*/
		
		//fill simple types
		$arrTypesSimple = array();
		
		if($simpleMode)
			$arrTypesSimple = array("Post"=>"post","Page"=>"page");
		else{
			
			foreach($arrPostTypes as $arrType){
				
				$postTypeName = UniteFunctionsUC::getVal($arrType, "name");
				$postTypeTitle = UniteFunctionsUC::getVal($arrType, "title");
				
				if(isset($arrTypesSimple[$postTypeTitle]))
					$arrTypesSimple[$postTypeName] = $postTypeName;
				else
					$arrTypesSimple[$postTypeTitle] = $postTypeName;
			}
			
		}
		
		//----- posts source ----
		//UniteFunctionsUC::showTrace();
		
		$arrNotCurrentElementorCondition = array();
		$arrCustomOnlyCondition = array();
		$arrRelatedOnlyCondition = array();
		$arrCurrentElementorCondition = array();
		$arrCustomAndCurrentElementorCondition = array();
		$arrNotManualElementorCondition = array();
		$arrCustomAndRelatedElementorCondition = array();
		$arrManualElementorCondition = array();
		
		
		if($addCurrentPosts == true){
						
			$arrCurrentElementorCondition = array(
				$name."_source" => "current",
			);
			
			$arrNotCurrentElementorCondition = array(
				$name."_source!" => "current",
			);
			
			$arrCustomAndCurrentElementorCondition = array(
				$name."_source" => array("current","custom"),
			);
			
			$arrCustomAndRelatedElementorCondition = array(
				$name."_source" => array("related","custom"), 
			);

			
			$arrCustomOnlyCondition = array(
				$name."_source" => "custom",
			);
			
			$arrRelatedOnlyCondition = array(
				$name."_source" => "related",
			);
			
			$arrNotInRelatedCondition = array(
				$name."_source!" => "related",
			);
			
			$arrNotManualElementorCondition = array(
				$name."_source!" => "manual",
			);
			
			$arrManualElementorCondition = array(
				$name."_source" => "manual",
			);
			
			
			$params = array();
			$params["origtype"] = UniteCreatorDialogParam::PARAM_DROPDOWN;
			//$params["description"] = esc_html__("Choose the source of the posts list", "unlimited-elements-for-elementor");
			
			$source = UniteFunctionsUC::getVal($value, $name."_source", "custom");
			$arrSourceOptions = array();
			$arrSourceOptions[sprintf(__("Current Query %s", "unlimited-elements-for-elementor"), $textPost)] = "current";
			$arrSourceOptions[sprintf(__("Custom %s", "unlimited-elements-for-elementor"),$textPosts)] = "custom";
			$arrSourceOptions[sprintf(__("Related %s", "unlimited-elements-for-elementor"), $textPosts)] = "related";
			$arrSourceOptions[__("Manual Selection", "unlimited-elements-for-elementor")] = "manual";
			
			$this->addSelect($name."_source", $arrSourceOptions, sprintf(esc_html__("%s Source", "unlimited-elements-for-elementor"), $textPosts), $source, $params);
			
			//-------- add static text - current --------
			
			$params = array();
			$params["origtype"] = UniteCreatorDialogParam::PARAM_STATIC_TEXT;
			$params["description"] = esc_html__("Choose the source of the posts list", "unlimited-elements-for-elementor");
			$params["elementor_condition"] = $arrCurrentElementorCondition;
			
			$maxPostsPerPage = get_option("posts_per_page");
			
			$this->addStaticText("The current posts are being used in archive pages. Posts per page: {$maxPostsPerPage}. Set this option in Settings -> Reading ", $name."_currenttext", $params);
			
			//-------- add static text - related --------
			
			$params = array();
			$params["origtype"] = UniteCreatorDialogParam::PARAM_STATIC_TEXT;
			$params["elementor_condition"] = $arrRelatedOnlyCondition;
			
			$this->addStaticText("The related posts are being used in single post. Posts from same post type and terms", $name."_relatedtext", $params);
		}
		
		//----- post type -----
		
		$defaultPostType = "post";
		if($isForWooProducts == true)
			$defaultPostType = "product";
		
		$postType = UniteFunctionsUC::getVal($value, $name."_posttype", $defaultPostType);
		
		$params = array();
		
		if($simpleMode == false){
			$params["datasource"] = "post_type";
			$params[UniteSettingsUC::PARAM_CLASSADD] = "unite-setting-post-type";
			
			$dataCats = UniteFunctionsUC::encodeContent($arrPostTypes);
			
			$params[UniteSettingsUC::PARAM_ADDPARAMS] = "data-arrposttypes='$dataCats' data-settingtype='select_post_type' data-settingprefix='{$name}'";
		}
		
		$params["origtype"] = "uc_select_special";
		//$params["description"] = esc_html__("Select which Post Type or Custom Post Type you wish to display", "unlimited-elements-for-elementor");
		
		$params["elementor_condition"] = $arrCustomOnlyCondition;
		
		$params["is_multiple"] = true;
		
		if($isForWooProducts == false)
			$this->addMultiSelect($name."_posttype", $arrTypesSimple, esc_html__("Post Types", "unlimited-elements-for-elementor"), $postType, $params);
		
		//----- hr -------
		

		//------- Show Advanced Query --------
		/*
		$params = array();
		$params["origtype"] = UniteCreatorDialogParam::PARAM_RADIOBOOLEAN;
		
		$this->addRadioBoolean($name."_show_advanced", __("Show Advanced / Dynamic Settings", "unlimited-elements-for-elementor"), false, "Yes", "No", $params);
		
		$params = array();
		$params["origtype"] = "custom_controls";
		$params["function"] = array($this, "postSelection_addTaxQuery");
		
		$params = array();
		$params["origtype"] = UniteCreatorDialogParam::PARAM_HR;
		
		$this->addHr("hr_after_advanced",$params);
		
		
		*/
		
		//----- hr -------
		
		$params = array();
		$params["origtype"] = UniteCreatorDialogParam::PARAM_HR;
		$params["elementor_condition"] = $arrCustomOnlyCondition;
		
		$this->addHr("post_before_include",$params);
		
		// --------- Include BY some options -------------
		
		$arrIncludeBy = array();
		
		if($isForWooProducts == false){
			$arrIncludeBy["sticky_posts"] = __("Include Sticky Posts", "unlimited-elements-for-elementor");
			$arrIncludeBy["sticky_posts_only"] = __("Get Only Sticky Posts", "unlimited-elements-for-elementor");			
		}
		
		$arrIncludeBy["author"] = __("Author", "unlimited-elements-for-elementor");
		$arrIncludeBy["date"] = __("Date", "unlimited-elements-for-elementor");
		
		if($isForWooProducts == false){
			$arrIncludeBy["parent"] = __("Post Parent", "unlimited-elements-for-elementor");
		}
		
		if($isForWooProducts == true){
			$arrIncludeBy["products_on_sale"] = __("Products On Sale Only (woo)","unlimited-elements-for-elementor");
			$arrIncludeBy["up_sells"] = __("Up Sells Products (woo)","unlimited-elements-for-elementor");
			$arrIncludeBy["cross_sells"] = __("Cross Sells Products (woo)","unlimited-elements-for-elementor");
			$arrIncludeBy["out_of_stock"] = __("Out Of Stock Products Only (woo)", "unlimited-elements-for-elementor");
			$arrIncludeBy["recent"] = __("Recently Viewed Produts (woo)", "unlimited-elements-for-elementor");
		}
		
		$addPostsText = sprintf(__("Add Specific %s", "unlimited-elements-for-elementor"), $textPosts);
				
		$includeBy = UniteFunctionsUC::getVal($value, $name."_includeby");
		
		$arrIncludeBy = array_flip($arrIncludeBy);
		
		$params = array();
		$params["is_multiple"] = true;
		$params["origtype"] = UniteCreatorDialogParam::PARAM_DROPDOWN;
		
		$arrConditionIncludeBy = $arrCustomOnlyCondition;
		$params["elementor_condition"] = $arrConditionIncludeBy;
		
		$this->addMultiSelect($name."_includeby", $arrIncludeBy, esc_html__("Include By", "unlimited-elements-for-elementor"), $includeBy, $params);
			
			//--- add hr after include by----
			
			$params = array();
			$params["origtype"] = UniteCreatorDialogParam::PARAM_HR;
			
			$params["elementor_condition"] = $arrConditionIncludeBy;
			
			$this->addHr("after_include_by",$params);
		
		//---- Include By Author -----

		$arrAuthors = UniteFunctionsWPUC::getArrAuthorsShort();
		
		$params = array();
		$params["origtype"] = UniteCreatorDialogParam::PARAM_DROPDOWN;
		$params["is_multiple"] = true;
		$params["placeholder"] = __("Select one or more authors", "unlimited-elements-for-elementor");
		
		$arrConditionIncludeAuthor = $arrConditionIncludeBy;
		$arrConditionIncludeAuthor[$name."_includeby"] = "author";
		
		$params["elementor_condition"] = $arrConditionIncludeAuthor;
		
		$arrAuthors = array_flip($arrAuthors);
		
		$this->addMultiSelect($name."_includeby_authors", $arrAuthors, __("Include By Author", "unlimited-elements-for-elementor"), "", $params);
		
		//---- Include By Date -----
		
		$arrDates = HelperProviderUC::getArrPostsDateSelect();
		
		$params = array();
		$params["origtype"] = UniteCreatorDialogParam::PARAM_DROPDOWN;
		
		$arrConditionIncludeByDate = $arrConditionIncludeBy;
		$arrConditionIncludeByDate[$name."_includeby"] = "date";

		$params["elementor_condition"] = $arrConditionIncludeByDate;
		
		$arrDates = array_flip($arrDates);
		
		$this->addSelect($name."_includeby_date", $arrDates, __("Include By Date", "unlimited-elements-for-elementor"), "all", $params);
		
		//----- add date before and after -------
		
		$params = array();
		$params["origtype"] = UniteCreatorDialogParam::PARAM_DATETIME;
		$params["description"] = __("Show all the posts published until the chosen date, inclusive.","unlimited-elements-for-elementor");
		$params["placeholder"] = __("Choose Date", "unlimited-elements-for-elementor");
		
		$arrConditionDateCustom = $arrConditionIncludeByDate;
		$arrConditionDateCustom[$name."_includeby_date"] = "custom";
		
		$params["elementor_condition"] = $arrConditionDateCustom;
		$this->addTextBox($name."_include_date_before","",__("Published Before Date","unlimited-elements-for-elementor"),$params);
		
		$params["description"] = __("Show all the posts published since the chosen date, inclusive.","unlimited-elements-for-elementor");
		
		$this->addTextBox($name."_include_date_after","", __("Published After Date","unlimited-elements-for-elementor"),$params);
		
		//----- add hr after date -------
		
		$params = array();
		$params["origtype"] = UniteCreatorDialogParam::PARAM_HR;
		$params["elementor_condition"] = $arrConditionIncludeByDate;
		
		$this->addHr("hr_after_date",$params);

		//---- Include By Post Parent -----
		
		$arrConditionIncludeParents = $arrConditionIncludeBy;
		$arrConditionIncludeParents[$name."_includeby"] = "parent";
		
		$this->addPostIDSelect($name."_includeby_parent", sprintf(__("Select %s Parents"), $textPosts), $arrConditionIncludeParents, $isForWooProducts);
		
		//-------- include by recently viewed --------
		
		$params = array();
		$params["origtype"] = UniteCreatorDialogParam::PARAM_STATIC_TEXT;
		$arrConditionIncludeRecent = $arrConditionIncludeBy;
		$arrConditionIncludeRecent[$name."_includeby"] = "recent";
		
		$params["elementor_condition"] = $arrConditionIncludeRecent;
		
		$this->addStaticText("Recently viewed by the current site visitor, taken from cookie: woocommerce_recently_viewed. Works only if active wordpress widget: \"Recently Viewed Products\" ", $name."_includeby_recenttext", $params);
		
		
		//----- add categories -------
		
		$arrCats = array();
		
		if($simpleMode == true){
			
			$arrCats = $arrPostTypes["post"]["cats"];
			$arrCats = array_flip($arrCats);
			$firstItemValue = reset($arrCats);
			
		}else if($allCatsMode == true){
			
			$arrCats = $this->getCategoriesFromAllPostTypes($arrPostTypes);
			$firstItemValue = reset($arrCats);
			
		}else{
			$firstItemValue = "";
		}
		
		$category = UniteFunctionsUC::getVal($value, $name."_category", $firstItemValue);
		
		$params = array();
		
		if($simpleMode == false){
			$params["datasource"] = "post_category";
			$params[UniteSettingsUC::PARAM_CLASSADD] = "unite-setting-post-category";
		}
		
		$params["origtype"] = UniteCreatorDialogParam::PARAM_DROPDOWN;
		$params["is_multiple"] = true;
				
		$params["elementor_condition"] = $arrCustomOnlyCondition;
		
		$paramsTermSelect = $params;
		
		$this->addMultiSelect($name."_category", $arrCats, esc_html__("Include By Terms", "unlimited-elements-for-elementor"), $category, $params);
		
		
		// --------- Include by term relation -------------
		
		$params = array();
		$params["origtype"] = UniteCreatorDialogParam::PARAM_DROPDOWN;
		
		$params["elementor_condition"] = $arrCustomOnlyCondition;
		
		$relation = UniteFunctionsUC::getVal($value, $name."_category_relation", "AND");
		
		$arrRelationItems = array();
		$arrRelationItems["And"] = "AND";
		$arrRelationItems["Or"] = "OR";
				
		$this->addSelect($name."_category_relation", $arrRelationItems, __("Include By Terms Relation", "unlimited-elements-for-elementor"), $relation, $params);
		
		//--------- show children -------------
				
		$params = array();
		$params["origtype"] = UniteCreatorDialogParam::PARAM_RADIOBOOLEAN;
		
		$params["elementor_condition"] = $arrCustomOnlyCondition;
		
		$isIncludeChildren = UniteFunctionsUC::getVal($value, $name."_terms_include_children", false);
		$isIncludeChildren = UniteFunctionsUC::strToBool($isIncludeChildren);
		
		$this->addRadioBoolean($name."_terms_include_children", __("Include Terms Children", "unlimited-elements-for-elementor"), $isIncludeChildren, "Yes", "No", $params);
		
		//---- manual selection search and replace -----
		
		$textManualSelect = sprintf(__("Seach And Select %s"), $textPosts);
		
		$this->addPostIDSelect($name."_manual_select_post_ids", $textManualSelect, $arrManualElementorCondition, $isForWooProducts);
		
		// --------- add hr before exclude -------------
		
		$params = array();
		$params["origtype"] = UniteCreatorDialogParam::PARAM_HR;
		$params["elementor_condition"] = $arrCustomOnlyCondition;
		
		$this->addHr("before_exclude_by",$params);
		
		
		// --------- add exclude by -------------
		
		$arrExclude = array();
		
		if($isForWooProducts == true){
			$arrExclude["out_of_stock"] = __("Out Of Stock Products (woo)", "unlimited-elements-for-elementor");
			$arrExclude["products_on_sale"] = __("Products On Sale (woo)","unlimited-elements-for-elementor");
		}
		
		$arrExclude["terms"] = __("Terms", "unlimited-elements-for-elementor");		
		$arrExclude["current_post"] = sprintf(__("Current %s", "unlimited-elements-for-elementor"), $textPosts);
		$arrExclude["specific_posts"] = sprintf(__("Specific %s", "unlimited-elements-for-elementor"), $textPosts);
		$arrExclude["author"] = __("Author", "unlimited-elements-for-elementor");
		$arrExclude["no_image"] = sprintf(__("%s Without Featured Image", "unlimited-elements-for-elementor"),$textPost);
		$arrExclude["current_category"] = sprintf(__("%s with Current Category", "unlimited-elements-for-elementor"),$textPosts);
		$arrExclude["current_tags"] = sprintf(__("%s With Current Tags", "unlimited-elements-for-elementor"),$textPosts);
		$arrExclude["offset"] = sprintf(__("Offset", "unlimited-elements-for-elementor"),$textPosts);
		
		$params = array();
		$params["origtype"] = UniteCreatorDialogParam::PARAM_DROPDOWN;
		$params["is_multiple"] = true;
		
		$conditionExcludeBy = $arrCustomAndRelatedElementorCondition;
		
		$params["elementor_condition"] = $conditionExcludeBy;
		
		$arrExclude = array_flip($arrExclude);
		
		$arrExcludeValues = "";
		
		$this->addMultiSelect($name."_excludeby", $arrExclude, __("Exclude By", "unlimited-elements-for-elementor"), $arrExcludeValues, $params);
				
		//------- Exclude By --- TERM --------
		
		$params = $paramsTermSelect;
		$conditionExcludeByTerms = $conditionExcludeBy;
		$conditionExcludeByTerms[$name."_excludeby"] = "terms";
		
		$params["elementor_condition"] = $conditionExcludeByTerms;
		
		$this->addMultiSelect($name."_exclude_terms", $arrCats, esc_html__("Exclude By Terms", "unlimited-elements-for-elementor"), "", $params);
		
		//------- Exclude By --- AUTHOR --------
				
		$params = array();
		$params["origtype"] = UniteCreatorDialogParam::PARAM_DROPDOWN;
		$params["is_multiple"] = true;
		$params["placeholder"] = __("Select one or more authors", "unlimited-elements-for-elementor");
		
		$arrConditionIncludeAuthor = $conditionExcludeBy;
		$arrConditionIncludeAuthor[$name."_excludeby"] = "author";
		
		$params["elementor_condition"] = $arrConditionIncludeAuthor;
		
		$this->addMultiSelect($name."_excludeby_authors", $arrAuthors, __("Exclude By Author", "unlimited-elements-for-elementor"), "", $params);

		//------- Exclude By --- OFFSET --------
		
		$params = array();
		$params["origtype"] = UniteCreatorDialogParam::PARAM_NUMBER;
		
		$params["description"] = __("Use this setting to skip over posts, not showing first posts to the offset given","unlimited-elements-for-elementor");
		
		$conditionExcludeByOffset = $conditionExcludeBy;
		$conditionExcludeByOffset[$name."_excludeby"] = "offset";
		
		$params["elementor_condition"] = $conditionExcludeByOffset;
		$params["add_dynamic"] = true;
		
		$this->addTextBox($name."_offset", "0", esc_html__("Offset", "unlimited-elements-for-elementor"), $params);
		
		
		//--------- show children -------------
				
		$params = array();
		$params["origtype"] = UniteCreatorDialogParam::PARAM_RADIOBOOLEAN;
		$params["elementor_condition"] = $conditionExcludeByTerms;
		
		$this->addRadioBoolean($name."_terms_exclude_children", __("Exclude Terms With Children", "unlimited-elements-for-elementor"), true, "Yes", "No", $params);

		//------- Exclude By --- SPECIFIC POSTS --------
		
		$conditionExcludeBySpecific = $conditionExcludeBy;
		$conditionExcludeBySpecific[$name."_excludeby"] = "specific_posts";
		
		$params = array();
		$params["elementor_condition"] = $conditionExcludeBySpecific;
		
		$this->addPostIDSelect($name."_exclude_specific_posts", sprintf(__("Specific %s To Exclude", "unlimited-elements-for-elementor"),$textPosts), $conditionExcludeBySpecific, $isForWooProducts);
		
		//----- hr -------
		
		$params = array();
		$params["origtype"] = UniteCreatorDialogParam::PARAM_HR;
		$params["elementor_condition"] = $arrNotManualElementorCondition;
		
		$this->addHr("post_after_exclude",$params);
		
		//------- Post Status --------
		
		$arrStatuses = HelperProviderUC::getArrPostStatusSelect();
				
		$params = array();
		$params["origtype"] = UniteCreatorDialogParam::PARAM_DROPDOWN;
		$params["is_multiple"] = true;
		$params["placeholder"] = __("Select one or more statuses", "unlimited-elements-for-elementor");
		
		$params["elementor_condition"] = $arrCustomOnlyCondition;
		
		$arrStatuses = array_flip($arrStatuses);
		
		$this->addMultiSelect($name."_status", $arrStatuses, __("Post Status", "unlimited-elements-for-elementor"), array("publish"), $params);
		
		//------- max items --------
		
		$params = array("unit"=>"posts");
		
		if(empty($defaultMaxPosts))
			$defaultMaxPosts = 10;
		
		$maxItems = UniteFunctionsUC::getVal($value, $name."_maxitems", $defaultMaxPosts);
		
		$params["origtype"] = UniteCreatorDialogParam::PARAM_TEXTFIELD;
		$params["placeholder"] = __("100 posts if empty","unlimited-elements-for-elementor");
		
		//$params["description"] = "Enter how many Posts you wish to display, -1 for unlimited";
		
		$params["elementor_condition"] = $arrCustomAndRelatedElementorCondition;
		$params["add_dynamic"] = true;
		
		$this->addTextBox($name."_maxitems", $maxItems, sprintf(esc_html__("Max %s", "unlimited-elements-for-elementor"), $textPosts), $params);
		
		//----- hr before orderby --------
		
		$params = array();
		$params["origtype"] = UniteCreatorDialogParam::PARAM_HR;
		
		$this->addHr("hr_before_orderby",$params);
		
		
		//----- orderby --------
		
		$arrOrder = UniteFunctionsWPUC::getArrSortBy($isForWooProducts);
		$arrOrder = array_flip($arrOrder);
		
		$arrDir = UniteFunctionsWPUC::getArrSortDirection();
		$arrDir = array_flip($arrDir);
		
		//---- orderby for custom and current -----
		
		$params = array();
		
		//$params[UniteSettingsUC::PARAM_ADDFIELD] = $name."_orderdir1";
		
		$orderBY = UniteFunctionsUC::getVal($value, $name."_orderby", "default");
		$params["origtype"] = UniteCreatorDialogParam::PARAM_DROPDOWN;
		//$params["description"] = esc_html__("Select how you wish to order posts", "unlimited-elements-for-elementor");
		
		$this->addSelect($name."_orderby", $arrOrder, __("Order By", "unlimited-elements-for-elementor"), $orderBY, $params);
		
		//--- meta value param -------
		
		$params = array();
		$params["origtype"] = UniteCreatorDialogParam::PARAM_TEXTFIELD;
		$params["class"] = "alias";
		
		$arrCondition = array();
		$arrCondition[$name."_orderby"] = array(UniteFunctionsWPUC::SORTBY_META_VALUE, UniteFunctionsWPUC::SORTBY_META_VALUE_NUM);
		
		$params["elementor_condition"] = $arrCondition;
		$params["add_dynamic"] = true;
		
		$this->addTextBox($name."_orderby_meta_key1", "" , __("&nbsp;&nbsp;Custom Field Name","unlimited-elements-for-elementor"), $params);
		
		$this->addControl($name."_orderby", $name."_orderby_meta_key1", "show", UniteFunctionsWPUC::SORTBY_META_VALUE.",".UniteFunctionsWPUC::SORTBY_META_VALUE_NUM);
		
		//---- order dir -----
		
		$params = array();
		$params["origtype"] = UniteCreatorDialogParam::PARAM_DROPDOWN;
		//$params["description"] = esc_html__("Select order direction. Descending A-Z or Accending Z-A", "unlimited-elements-for-elementor");
		
		$orderDir1 = UniteFunctionsUC::getVal($value, $name."_orderdir1", "default" );
		$this->addSelect($name."_orderdir1", $arrDir, __("&nbsp;&nbsp;Order By Direction", "unlimited-elements-for-elementor"), $orderDir1, $params);
		
		
		//---- hr before query id -----
		
		$params = array();
		$params["origtype"] = UniteCreatorDialogParam::PARAM_HR;
		
		
		$this->addHr("hr_after_order_dir", $params);
				
		
		//---- query id -----
		
		$isPro = GlobalsUC::$isProVersion;
		
		$params = array();
		$params["origtype"] = UniteCreatorDialogParam::PARAM_TEXTFIELD;
		if($isPro == true){
			
			$title = __("Query ID", "unlimited-elements-for-elementor");
			$params["description"] = __("Give your Query unique ID to been able to filter it in server side using add_filter() function","unlimited-elements-for-elementor");
			
		}else{		//free version
			
			$params["description"] = __("Give your Query unique ID to been able to filter it in server side using add_filter() function. This feature exists in a PRO Version only","unlimited-elements-for-elementor");
			$title = __("Query ID (pro)", "unlimited-elements-for-elementor");
			$params["disabled"] = true;
		}
		
		$queryID = UniteFunctionsUC::getVal($value, $name."_queryid");
		
		$this->addTextBox($name."_queryid", $queryID, $title, $params);
		
		//---- show debug -----
		
		$params = array();
		$params["origtype"] = UniteCreatorDialogParam::PARAM_RADIOBOOLEAN;
		$params["description"] = __("Show the query for debugging purposes. Don't forget to turn it off before page release", "unlimited-elements-for-elementor");
		
		$this->addRadioBoolean($name."_show_query_debug", __("Show Query Debug", "unlimited-elements-for-elementor"), false, "Yes", "No", $params);
		
	}
	
	
	
}