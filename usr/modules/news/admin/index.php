<?php

/**
* @name        JMY CMS
* @link        http://jmy.su/
* @copyright   Copyright (C) 2012-2016 JMY LTD
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Komarov Ivan
*/

if (!defined('ADMIN_SWITCH')) {
    location(' /');
    exit;
}

function news_main() 
{
	global $adminTpl, $core, $db, $admin_conf, $url;
	$adminTpl->admin_head(_MODULES . ' | ' . _NAME);
	$page = init_page();
	$cut = ($page-1)*$admin_conf['num'];
	$query_search = isset($_POST['query']) ? filter($_POST['query'], 'text') : '';
	$where = '';
	$cat = 0;
	
	if(isset($url[3]) && $url[3] == 'cat')
	{
		$cat = $url[4];
		$where = "WHERE cat LIKE '%," . $db->safesql($url[4]) . ",%' ";
	}	
	
	$whereC = $where;	
	if($where == '')
	{
		$where .= ' WHERE l.lang = \'' . $core->InitLang() . '\'';
	}
	else
	{
		$where .= ' AND l.lang = \'' . $core->InitLang() . '\'';
	}
	$count = $db->numRows($db->query('SELECT id FROM '.DB_PREFIX.'_news WHERE active=2'));
	if($count > 0)
	{
		echo '<div style="clear:both"></div>';
		$adminTpl->info(_MODER);
	}
	$cats_arr = $core->aCatList('news');
	echo '<div class="row">
			<div class="col-lg-12">
				<section class="panel">
					<div class="panel-heading">
						<b>' . _LIST_NEWS . '</b>						
						<p class="text-left mg-b"><b>' . _SORT . '</b><span class="pd-l-sm">
						<a href="' . ADMIN . '/module/news/cat/0"><span class="label ' . (($cat == 0 && isset($url[3]) && $url[3] == 'cat') ? 'label-dark' : 'label-default') . '">Без категории</span></a><span class="pd-l-sm">';	
						foreach ($cats_arr as $cid => $name) 
						{
						echo '<a href="' . ADMIN . '/module/news/cat/' . $cid . '"><span class="label ' . (($cid == $cat) ? 'label-dark' : 'label-default') . '">'.$name.'</span></a><span class="pd-l-sm">';	
						}
	echo'</p></div>';	
	$all = $db->numRows($db->query("SELECT * FROM " . DB_PREFIX . "_news $whereC"));
	$adminTpl->a_pages($page, $admin_conf['num'], $all, ADMIN.'/module/news/{page}');
	$query = $db->query("SELECT n.*, l.*, c.id as cid, c.name, c.altname as alturl FROM ".DB_PREFIX."_news AS n LEFT JOIN ".DB_PREFIX."_categories AS c ON (n.cat=c.id) LEFT JOIN ".DB_PREFIX."_langs as l on(l.postId=n.id and l.module='news') $where AND active!='2' ORDER BY n.date DESC LIMIT " . $cut . ", " . $admin_conf['num'] . "");
	if($db->numRows($query) > 0) 
	{
	echo '<div class="panel-body no-padding">
					<form id="tablesForm" style="margin:0; padding:0" method="POST" action="{ADMIN}/module/news/action">
						<table class="table no-margin table-responsive">
							<thead>
								<tr>
									<th><span class="pd-l-sm"></span>ID</th>
									<th class="col-md-3">' . _TITLE . '</th>
									<th class="col-md-2">' . _DATE . '</th>
									<th class="col-md-3">' . _CATS .'</th>
									<th class="col-md-2">' . _AUTHOR . '</th>
									<th class="col-md-2">' . _ACTIONS . '</th>								
									<th class="col-md-1">
										<div class="checkbox-custom mb5">
											<input id="all" type="checkbox" name="all" onclick="setCheckboxes(\'tablesForm\', true); return true;">
											<label for="all"></label>
										</div>								
									</th>
								</tr>
							</thead>
							<tbody>';
		while($news = $db->getRow($query)) 
		{
			$status_icon = ($news['active'] == 0) ? '<a href="{MOD_LINK}/activate/' . $news['id'] . '" onClick="return getConfirm(\'' .  _ACTIVATE_NEWS .' - ' . $news['title'] . '?\')"><button  type="button" class="btn btn btn-primary btn-sm" data-toggle="tooltip" data-placement="top" title="" data-original-title="' . _NEWS_ACTIVE .'">A</button></a>' : '<a href="{MOD_LINK}/deactivate/' . $news['id'] . '" onClick="return getConfirm(\'' . _DEACTIVATE_NEWS .' - ' . $news['title'] . '?\')" ><button  type="button" class="btn btn btn-primary btn-sm" data-toggle="tooltip" data-placement="top" title="" data-original-title="' . _NEWS_DEACTIVE .'">A</button></a>';
			
			echo '
			<tr '.(($news['active'] == 0) ? 'class="danger"' : '' ).'>
				<td><span class="pd-l-sm"></span>' . $news['id'] . '</td>
				<td>' . $news['title'] . '</td>
				<td>' . formatDate($news['date'], true) . '</td>				
				<td>' . ($news['cat'] !== ',0,' ? $core->getCat('news', $news['cat'], 'short', 3) : 'Нет') . '</td>
				<td>' . $news['author'] . '</td>
				<td>
				<div class="btn-group btn-group-xs mt15 d-sm-flex">
				<button type="button" class="btn btn-info">А</button>
				<button type="button" class="btn btn-system light">E</button>
				<button type="button" class="btn btn-Danger">X</button>
  <button type="button" data-toggle="dropdown" class="btn btn-alert dropdown-toggle" aria-expanded="true"><span class="caret"></span><span class="sr-only">Toggle Dropdown</span></button>
  <ul role="menu" class="dropdown-menu">
    <li><a href="#">'._CAT_VIEW.'</a></li>
    <li><a href="#">Another action</a></li>
    <li><a href="#">Something else here</a></li>
    <li class="divider"></li>
    <li><a href="#">Separated link</a></li>
  </ul>
				
				</div>
				
				</td>
				<td> 
				
				
				</td>
			</tr>';
		}
			echo '<tr><td></td><td></td><td></td><td></td><td></td><td></td><td></td></tr></tbody></table>';		
			echo '
				<div class="_tableBottom">
					<div align="right">
						<table>
							<tr>
								<td valign="top">
									<select class="form-control" name="act">
										<option value="blockComment">' . _FORBID_COMMENTS . '</option>
										<option value="blockIndex">' . _REMOVE_MAIN .'</option>
										<option value="nowDate">' . _SET_NOWDATE . '</option>
										<option value="activate">' . _ACTIVATE . '</option>
										<option value="deActivate">' . _DEACTIVATE . '</option>
										<option value="reActivate">' . _REACTIVATE . '</option>									
										<option value="cat">' . _CHANGE_CAT . '</option>
										<option value="delete">' . _DELETE . '</option>
									</select>
								</td>
								<td>&nbsp&nbsp</td>	
								<td valign="top">
								<input name="submit" type="submit" class="btn btn-success" id="sub" value="' .  _DOIT . '" /><span class="pd-l-sm"></span>
								</td>
							</tr>
						</table>	
					</div>
				</div>
			</form>
		</div>';
	} else {
		echo '<div class="panel-heading">'  . _NEWS_NO_NEWS . '</div>';		
	}
	echo'</section></div></div>';	
	
	$all = $db->numRows($db->query("SELECT * FROM " . DB_PREFIX . "_news $whereC"));
	$adminTpl->pages($page, $admin_conf['num'], $all, ADMIN.'/module/news/{page}');
	
	$adminTpl->admin_foot();
} 


function news_add($nid = null) 
{
global $adminTpl, $core, $db, $core, $config;
	if(isset($nid)) 
	{	
		$bb = new bb;
		$query = $db->query("SELECT * FROM ".DB_PREFIX."_news WHERE id = '" . $nid . "'");
		$news = $db->getRow($query);
		$id = $news['id']; 
		$author = $news['author']; 
		$date = gmdate('d.m.Y H:i', $news['date']); 
		$tags = $news['tags']; 
		$groups = $news['groups']; 
		$altname = $news['altname']; 
		$keywords = $news['keywords']; 
		$description = $news['description']; 		
		$allow_comments = $news['allow_comments']; 
		$allow_rating = $news['allow_rating']; 
		$allow_index = $news['allow_index']; 
		$score = $news['score']; 
		$votes = $news['votes']; 
		$views = $news['views']; 
		$comments = $news['comments']; 
		$fields = unserialize($news['fields']); 
		$fix = $news['fixed']; 
		$active = ($news['active'] == 2 ? 0 : $news['active']);
		$cat = $news['cat']; 
		$cat_array = explode(',', $cat);
		$catttt = explode(',', $cat);
		$edit = true;
		$grroups = explode(',', $groups);
		$firstCat = $catttt[1];
		$deleteKey = array_search($firstCat, $catttt);
		unset($catttt[$deleteKey]);
		$langMassiv = $core->getLangList(true);
		$query = $db->query("SELECT * FROM ".DB_PREFIX."_langs WHERE postId = '" . $id . "' AND module='news'");
		while($langs = $db->getRow($query))
		{
			$title[$langs['lang']] = prepareTitle($langs['title']);
			$short[$langs['lang']] = $bb->htmltobb($langs['short']);
			$full[$langs['lang']] = $bb->htmltobb($langs['full']);
		}
		
		$lln = _NEWS_EDIT_NEWS;
		$dosave = _UPDATE;
	} 
	else 
	{
		$id = false; 
		$title = false; 
		$short = false; 
		$full = false; 
		$author = $core->auth->user_info['nick']; 		
		$date = gmdate('d.m.Y H:i'); 
		$tags = false; 
		$cat = false; 
		$altname = false; 
		$keywords = false; 
		$description = false; 		
		$allow_comments = 1; 
		$allow_rating = 1; 
		$allow_index = 1; 
		$score = false; 
		$votes = false; 
		$views = false; 
		$comments = false; 
		$fields = false; 
		$fix = ''; 
		$active = 1;
		$lang = '';
		$edit = false;
		$catttt = array();
		$grroups = array();
		$firstCat = '';
		$lln = _NEWS_ADDPAGE;
		$dosave = _ADD;
	}
	$adminTpl->admin_head(_MODULES . ' | ' . $lln);
	$adminTpl->js_code = '
	// select dropdowns - placeholder like creation
        var selectList = $(\'.admin-form select\');
        selectList.each(function (i, e) {
          $(e).on(\'change\', function () {
            if ($(e).val() == "0") $(e).addClass("empty");
            else $(e).removeClass("empty")
          });
        });
        selectList.each(function (i, e) {
          $(e).change();
        });
        // Init tagsinput plugin
        $("input#tagsinput").tagsinput({
          tagClass: function (item) {
            return \'label label-default\';
          }
        });
';
	ajaxInit();
	$cats_arr = $core->aCatList('news');

echo '<section id="content" class="table-layout animated fadeIn">
          <!-- begin: .tray-center-->
          <div class="tray tray-center">
            <!-- create new order panel-->
            <div class="panel mb25 mt5">
              <div class="panel-heading br-b-ddd"><span class="panel-title hidden-xs"> Add New Customer</span>
                <ul class="nav panel-tabs-border panel-tabs">
                  <li class="active"><a href="#tab1_1" data-toggle="tab">General</a></li>
                  <li><a href="#tab1_2" data-toggle="tab">Settings</a></li>
                  <li><a href="#tab1_3" data-toggle="tab">Billing</a></li>
                </ul>
              </div>
              <div class="panel-body p20 pb10">
                <div class="tab-content pn br-n admin-form">
                  <div id="tab1_1" class="tab-pane active">
                    <div class="section row mbn">
                      <div class="col-md-9 pl15">
                        <div class="section row mb15">
                          <div class="col-xs-6">
                            <label for="name1" class="field prepend-icon">
                              <input id="name1" type="text" name="name1" placeholder="First Name" class="event-name gui-input br-light light">
                              <label for="name1" class="field-icon"><i class="fa fa-user"></i></label>
                            </label>
                          </div>
                          <div class="col-xs-6">
                            <label for="name2" class="field prepend-icon">
                              <input id="name2" type="text" name="name2" placeholder="Last Name" class="event-name gui-input br-light light">
                              <label for="name2" class="field-icon"><i class="fa fa-user"></i></label>
                            </label>
                          </div>
                        </div>
                        <div class="section row mb15">
                          <div class="col-xs-6">
                            <label for="password" class="field prepend-icon">
                              <input id="password" type="password" name="password" placeholder="Password" class="event-name gui-input br-light light">
                              <label for="name2" class="field-icon"><i class="fa fa-lock"></i></label>
                            </label>
                          </div>
                          <div class="col-xs-6">
                            <label for="password2" class="field prepend-icon">
                              <input id="password2" type="password2" name="password2" placeholder="Confirm Password" class="event-name gui-input br-light light">
                              <label for="password2" class="field-icon"><i class="fa fa-unlock"></i></label>
                            </label>
                          </div>
                        </div>
                        <div class="section mb15">
                          <label for="email" class="field prepend-icon">
                            <input id="email" type="text" name="email" placeholder="Customer Email Address" class="event-name gui-input br-light bg-light">
                            <label for="email" class="field-icon"><i class="fa fa-envelope-o"></i></label>
                          </label>
                        </div>
                        <div class="section mb10">
                          <input id="tagsinput" type="text" value="IBM, Software, Friend" class="bg-light">
                        </div>
                      </div>
                      <div class="col-md-3">
                        <div data-provides="fileupload" class="fileupload fileupload-new admin-form">
                          <div class="fileupload-preview thumbnail mb15"><img data-src="holder.js/100%x147" alt="holder"></div><span class="button btn-system btn-file btn-block ph5"><span class="fileupload-new">Change</span><span class="fileupload-exists">Change</span>
                            <input type="file"></span>
                        </div>
                      </div>
                    </div>
                  </div>
                  <div id="tab1_2" class="tab-pane">
                    <div class="section row mbn">
                      <div class="col-xs-6 pr15">
                        <div class="section mb10">
                          <label for="cust-phone" class="field prepend-icon">
                            <input id="cust-phone" type="text" name="cust-phone" placeholder="Customer Phone Number..." class="event-name gui-input bg-light br-light">
                            <label for="cust-phone" class="field-icon"><i class="fa fa-phone"></i></label>
                          </label>
                        </div>
                        <div class="section mb10">
                          <label for="customer-group" class="field select">
                            <select id="customer-group" name="customer-group">
                              <option value="0" selected="selected">Customer Group...</option>
                              <option value="1">Vendor</option>
                              <option value="2">Supplier</option>
                              <option value="3">Distributor</option>
                            </select><i class="arrow double"></i>
                          </label>
                        </div>
                        <div class="section">
                          <label for="customer-language" class="field select">
                            <select id="customer-language" name="customer-language">
                              <option value="0" selected="selected">Customer Language...</option>
                              <option value="1">English</option>
                              <option value="2">Spanish</option>
                              <option value="3">German</option>
                            </select><i class="arrow double"></i>
                          </label>
                        </div>
                      </div>
                      <div class="col-xs-6">
                        <label class="field option">
                          <input type="checkbox" name="info"><span class="checkbox mr10"></span> Customer is Tax Exempt
                        </label><br>
                        <label class="field option mt15">
                          <input type="checkbox" name="info"><span class="checkbox mr10"></span> Customer Accepts Marketing
                        </label><br>
                        <label class="field option mt15">
                          <input type="checkbox" name="info"><span class="checkbox mr10"></span> Activate/Enable Account?
                        </label>
                        <hr class="alt short mv15">
                        <p class="text-muted"><span class="fa fa-exclamation-circle text-warning fs15 pr5"></span> Grants the customer limited store access.</p>
                      </div>
                    </div>
                    <hr class="short alt mtn">
                    <div class="section mb15">
                      <label class="field prepend-icon">
                        <textarea id="cust-note" name="cust-note" placeholder="Customer Notes" class="gui-textarea br-light bg-light"></textarea>
                        <label for="cust-note" class="field-icon"><i class="fa fa-edit"></i></label>
                      </label>
                    </div>
                  </div>
                  <div id="tab1_3" class="tab-pane">
                    <div class="section">
                      <label for="lastaddr" class="field prepend-icon">
                        <input id="lastaddr" type="text" name="lastaddr" placeholder="Street address" class="gui-input">
                        <label for="lastaddr" class="field-icon"><i class="fa fa-map-marker"></i></label>
                      </label>
                    </div>
                    <div class="section">
                      <label class="field select">
                        <select id="location" name="location">
                          <option value="">Select country...</option>
                          <option value="AL">Albania</option>
                          <option value="DZ">Algeria</option>
                          <option value="AD">Andorra</option>
                          <option value="AO">Angola</option>
                          <option value="AI">Anguilla</option>
                          <option value="AG">Antigua and Barbuda</option>
                          <option value="AR">Argentina</option>
                          <option value="AM">Armenia</option>
                          <option value="AW">Aruba</option>
                          <option value="AU">Australia</option>
                          <option value="AT">Austria</option>
                          <option value="AZ">Azerbaijan Republic</option>
                          <option value="BS">Bahamas</option>
                          <option value="BH">Bahrain</option>
                          <option value="BB">Barbados</option>
                          <option value="BE">Belgium</option>
                          <option value="BZ">Belize</option>
                          <option value="BJ">Benin</option>
                          <option value="BM">Bermuda</option>
                          <option value="BT">Bhutan</option>
                          <option value="BO">Bolivia</option>
                          <option value="BA">Bosnia and Herzegovina</option>
                          <option value="BW">Botswana</option>
                          <option value="BR">Brazil</option>
                          <option value="BN">Brunei</option>
                          <option value="BG">Bulgaria</option>
                          <option value="BF">Burkina Faso</option>
                          <option value="BI">Burundi</option>
                          <option value="KH">Cambodia</option>
                          <option value="CA">Canada</option>
                          <option value="CV">Cape Verde</option>
                          <option value="KY">Cayman Islands</option>
                          <option value="TD">Chad</option>
                          <option value="CL">Chile</option>
                          <option value="C2">China Worldwide</option>
                          <option value="CO">Colombia</option>
                          <option value="KM">Comoros</option>
                          <option value="CK">Cook Islands</option>
                          <option value="CR">Costa Rica</option>
                          <option value="HR">Croatia</option>
                          <option value="CY">Cyprus</option>
                          <option value="CZ">Czech Republic</option>
                          <option value="CD">Democratic Republic of the Congo</option>
                          <option value="DK">Denmark</option>
                          <option value="DJ">Djibouti</option>
                          <option value="DM">Dominica</option>
                          <option value="DO">Dominican Republic</option>
                          <option value="EC">Ecuador</option>
                          <option value="EG">Egypt</option>
                          <option value="SV">El Salvador</option>
                          <option value="ER">Eritrea</option>
                          <option value="EE">Estonia</option>
                          <option value="ET">Ethiopia</option>
                          <option value="FK">Falkland Islands</option>
                          <option value="FO">Faroe Islands</option>
                          <option value="FJ">Fiji</option>
                          <option value="FI">Finland</option>
                          <option value="FR">France</option>
                          <option value="GF">French Guiana</option>
                          <option value="PF">French Polynesia</option>
                          <option value="GA">Gabon Republic</option>
                          <option value="GM">Gambia</option>
                          <option value="GE">Georgia</option>
                          <option value="DE">Germany</option>
                          <option value="GI">Gibraltar</option>
                          <option value="GR">Greece</option>
                          <option value="GL">Greenland</option>
                          <option value="GD">Grenada</option>
                          <option value="GP">Guadeloupe</option>
                          <option value="GT">Guatemala</option>
                          <option value="GN">Guinea</option>
                          <option value="GW">Guinea Bissau</option>
                          <option value="GY">Guyana</option>
                          <option value="HN">Honduras</option>
                          <option value="HK">Hong Kong</option>
                          <option value="HU">Hungary</option>
                          <option value="IS">Iceland</option>
                          <option value="IN">India</option>
                          <option value="ID">Indonesia</option>
                          <option value="IE">Ireland</option>
                          <option value="IL">Israel</option>
                          <option value="IT">Italy</option>
                          <option value="JM">Jamaica</option>
                          <option value="JP">Japan</option>
                          <option value="JO">Jordan</option>
                          <option value="KZ">Kazakhstan</option>
                          <option value="KE">Kenya</option>
                          <option value="KI">Kiribati</option>
                          <option value="KW">Kuwait</option>
                          <option value="KG">Kyrgyzstan</option>
                          <option value="LA">Laos</option>
                          <option value="LV">Latvia</option>
                          <option value="LS">Lesotho</option>
                          <option value="LI">Liechtenstein</option>
                          <option value="LT">Lithuania</option>
                          <option value="LU">Luxembourg</option>
                          <option value="MG">Madagascar</option>
                          <option value="MW">Malawi</option>
                          <option value="MY">Malaysia</option>
                          <option value="MV">Maldives</option>
                          <option value="ML">Mali</option>
                          <option value="MT">Malta</option>
                          <option value="MH">Marshall Islands</option>
                          <option value="MQ">Martinique</option>
                          <option value="MR">Mauritania</option>
                          <option value="MU">Mauritius</option>
                          <option value="YT">Mayotte</option>
                          <option value="MX">Mexico</option>
                          <option value="FM">Micronesia</option>
                          <option value="MN">Mongolia</option>
                          <option value="MS">Montserrat</option>
                          <option value="MA">Morocco</option>
                          <option value="MZ">Mozambique</option>
                          <option value="NA">Namibia</option>
                          <option value="NR">Nauru</option>
                          <option value="NP">Nepal</option>
                          <option value="NL">Netherlands</option>
                          <option value="AN">Netherlands Antilles</option>
                          <option value="NC">New Caledonia</option>
                          <option value="NZ">New Zealand</option>
                          <option value="NI">Nicaragua</option>
                          <option value="NE">Niger</option>
                          <option value="NU">Niue</option>
                          <option value="NF">Norfolk Island</option>
                          <option value="NO">Norway</option>
                          <option value="OM">Oman</option>
                          <option value="PW">Palau</option>
                          <option value="PA">Panama</option>
                          <option value="PG">Papua New Guinea</option>
                          <option value="PE">Peru</option>
                          <option value="PH">Philippines</option>
                          <option value="PN">Pitcairn Islands</option>
                          <option value="PL">Poland</option>
                          <option value="PT">Portugal</option>
                          <option value="QA">Qatar</option>
                          <option value="CG">Republic of the Congo</option>
                          <option value="RE">Reunion</option>
                          <option value="RO">Romania</option>
                          <option value="RU">Russia</option>
                          <option value="RW">Rwanda</option>
                          <option value="KN">Saint Kitts and Nevis Anguilla</option>
                          <option value="PM">Saint Pierre and Miquelon</option>
                          <option value="VC">Saint Vincent and Grenadines</option>
                          <option value="WS">Samoa</option>
                          <option value="SM">San Marino</option>
                          <option value="ST">SГЈo TomГ© and PrГ­ncipe</option>
                          <option value="SA">Saudi Arabia</option>
                          <option value="SN">Senegal</option>
                          <option value="RS">Serbia</option>
                          <option value="SC">Seychelles</option>
                          <option value="SL">Sierra Leone</option>
                          <option value="SG">Singapore</option>
                          <option value="SK">Slovakia</option>
                          <option value="SI">Slovenia</option>
                          <option value="SB">Solomon Islands</option>
                          <option value="SO">Somalia</option>
                          <option value="ZA">South Africa</option>
                          <option value="KR">South Korea</option>
                          <option value="ES">Spain</option>
                          <option value="LK">Sri Lanka</option>
                          <option value="SH">St. Helena</option>
                          <option value="LC">St. Lucia</option>
                          <option value="SR">Suriname</option>
                          <option value="SJ">Svalbard and Jan Mayen Islands</option>
                          <option value="SZ">Swaziland</option>
                          <option value="SE">Sweden</option>
                          <option value="CH">Switzerland</option>
                          <option value="TW">Taiwan</option>
                          <option value="TJ">Tajikistan</option>
                          <option value="TZ">Tanzania</option>
                          <option value="TH">Thailand</option>
                          <option value="TG">Togo</option>
                          <option value="TO">Tonga</option>
                          <option value="TT">Trinidad and Tobago</option>
                          <option value="TN">Tunisia</option>
                          <option value="TR">Turkey</option>
                          <option value="TM">Turkmenistan</option>
                          <option value="TC">Turks and Caicos Islands</option>
                          <option value="TV">Tuvalu</option>
                          <option value="UG">Uganda</option>
                          <option value="UA">Ukraine</option>
                          <option value="AE">United Arab Emirates</option>
                          <option value="GB">United Kingdom</option>
                          <option value="US">United States</option>
                          <option value="UY">Uruguay</option>
                          <option value="VU">Vanuatu</option>
                          <option value="VA">Vatican City State</option>
                          <option value="VE">Venezuela</option>
                          <option value="VN">Vietnam</option>
                          <option value="VG">Virgin Islands (British)</option>
                          <option value="WF">Wallis and Futuna Islands</option>
                          <option value="YE">Yemen</option>
                          <option value="ZM">Zambia</option>
                        </select><i class="arrow double"></i>
                      </label>
                    </div>
                    <div class="section row">
                      <div class="col-md-3">
                        <label for="zip" class="field prepend-icon">
                          <input id="zip" type="text" name="zip" placeholder="Zip" class="gui-input">
                          <label for="zip" class="field-icon"><i class="fa fa-certificate"></i></label>
                        </label>
                      </div>
                      <div class="col-md-4">
                        <label for="city" class="field prepend-icon">
                          <input id="city" type="text" name="city" placeholder="City" class="gui-input">
                          <label for="city" class="field-icon"><i class="fa fa-building-o"></i></label>
                        </label>
                      </div>
                      <div class="col-md-5">
                        <label for="states" class="field select">
                          <select id="states" name="states">
                            <option value="">Choose state</option>
                            <option value="AL">Alabama</option>
                            <option value="AK">Alaska</option>
                            <option value="AZ">Arizona</option>
                            <option value="AR">Arkansas</option>
                            <option value="CA">California</option>
                            <option value="CO">Colorado</option>
                            <option value="CT">Connecticut</option>
                            <option value="DE">Delaware</option>
                            <option value="DC">District Of Columbia</option>
                            <option value="FL">Florida</option>
                            <option value="GA">Georgia</option>
                            <option value="HI">Hawaii</option>
                            <option value="ID">Idaho</option>
                            <option value="IL">Illinois</option>
                            <option value="IN">Indiana</option>
                            <option value="IA">Iowa</option>
                            <option value="KS">Kansas</option>
                            <option value="KY">Kentucky</option>
                            <option value="LA">Louisiana</option>
                            <option value="ME">Maine</option>
                            <option value="MD">Maryland</option>
                            <option value="MA">Massachusetts</option>
                            <option value="MI">Michigan</option>
                            <option value="MN">Minnesota</option>
                            <option value="MS">Mississippi</option>
                            <option value="MO">Missouri</option>
                            <option value="MT">Montana</option>
                            <option value="NE">Nebraska</option>
                            <option value="NV">Nevada</option>
                            <option value="NH">New Hampshire</option>
                            <option value="NJ">New Jersey</option>
                            <option value="NM">New Mexico</option>
                            <option value="NY">New York</option>
                            <option value="NC">North Carolina</option>
                            <option value="ND">North Dakota</option>
                            <option value="OH">Ohio</option>
                            <option value="OK">Oklahoma</option>
                            <option value="OR">Oregon</option>
                            <option value="PA">Pennsylvania</option>
                            <option value="RI">Rhode Island</option>
                            <option value="SC">South Carolina</option>
                            <option value="SD">South Dakota</option>
                            <option value="TN">Tennessee</option>
                            <option value="TX">Texas</option>
                            <option value="UT">Utah</option>
                            <option value="VT">Vermont</option>
                            <option value="VA">Virginia</option>
                            <option value="WA">Washington</option>
                            <option value="WV">West Virginia</option>
                            <option value="WI">Wisconsin</option>
                            <option value="WY">Wyoming</option>
                          </select><i class="arrow double"></i>
                        </label>
                      </div>
                    </div>
                    <div class="section row mbn">
                      <div class="col-sm-8">
                        <label class="field option mt10">
                          <input type="checkbox" name="info" checked=""><span class="checkbox"></span>Save Customer<em class="small-text text-muted">- A Random Unique ID will be generated</em>
                        </label>
                      </div>
                      <div class="col-sm-4">
                        <p class="text-right">
                          <button type="button" class="btn btn-primary">Save Order</button>
                        </p>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <!-- recent orders table-->
            <div class="panel">
              <div class="panel-menu admin-form theme-primary">
                <div class="row">
                  <div class="col-md-4">
                    <label class="field select">
                      <select id="filter-purchases" name="filter-purchases">
                        <option value="0">Filter by Purchases</option>
                        <option value="1">1-49</option>
                        <option value="2">50-499</option>
                        <option value="1">500-999</option>
                        <option value="2">1000+</option>
                      </select><i class="arrow double"></i>
                    </label>
                  </div>
                  <div class="col-md-4">
                    <label class="field select">
                      <select id="filter-group" name="filter-group">
                        <option value="0">Filter by Group</option>
                        <option value="1">Customers</option>
                        <option value="2">Vendors</option>
                        <option value="3">Distributors</option>
                        <option value="4">Employees</option>
                      </select><i class="arrow double"></i>
                    </label>
                  </div>
                  <div class="col-md-4">
                    <label class="field select">
                      <select id="filter-status" name="filter-status">
                        <option value="0">Filter by Status</option>
                        <option value="1">Active</option>
                        <option value="2">Inactive</option>
                        <option value="3">Suspended</option>
                        <option value="4">Online</option>
                        <option value="5">Offline</option>
                      </select><i class="arrow double"></i>
                    </label>
                  </div>
                </div>
              </div>
              <div class="panel-body pn">
                <div class="table-responsive of-a">
                  <table class="table admin-form theme-warning tc-checkbox-1 fs13">
                    <thead>
                      <tr class="bg-light">
                        <th class="text-center">Select</th>
                        <th>Avatar</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Registered</th>
                        <th>Purchases</th>
                        <th>Total Spent</th>
                        <th class="text-right">Status</th>
                      </tr>
                    </thead>
                    <tbody>
                      <tr>
                        <td class="text-center">
                          <label class="option block mn">
                            <input type="checkbox" name="mobileos" value="FR"><span class="checkbox mn"></span>
                          </label>
                        </td>
                        <td class="w50"><img title="user" src="assets/img/avatars/1.jpg" class="img-responsive mw30 ib mr10"></td>
                        <td>Dave Robert</td>
                        <td>dave@company.com</td>
                        <td>12/03/2014</td>
                        <td>222</td>
                        <td>$3,600</td>
                        <td class="text-right">
                          <div class="btn-group text-right">
                            <button type="button" data-toggle="dropdown" aria-expanded="false" class="btn btn-success br2 btn-xs fs12 dropdown-toggle">Active<span class="caret ml5"></span></button>
                            <ul role="menu" class="dropdown-menu">
                              <li><a href="#">Edit</a></li>
                              <li><a href="#">Contact</a></li>
                              <li class="divider"></li>
                              <li class="active"><a href="#">Active</a></li>
                              <li><a href="#">Suspend</a></li>
                              <li><a href="#">Remove</a></li>
                            </ul>
                          </div>
                        </td>
                      </tr>
                      <tr>
                        <td class="text-center">
                          <label class="option block mn">
                            <input type="checkbox" name="mobileos" value="FR"><span class="checkbox mn"></span>
                          </label>
                        </td>
                        <td class="w50"><img title="user" src="assets/img/avatars/2.jpg" class="img-responsive mw30 ib mr10"></td>
                        <td>Sara Marshall</td>
                        <td>sara@company.com</td>
                        <td>12/03/2014</td>
                        <td>16</td>
                        <td>$4,200</td>
                        <td class="text-right">
                          <div class="btn-group text-right">
                            <button type="button" data-toggle="dropdown" aria-expanded="false" class="btn btn-success br2 btn-xs fs12 dropdown-toggle">Active<span class="caret ml5"></span></button>
                            <ul role="menu" class="dropdown-menu">
                              <li><a href="#">Edit</a></li>
                              <li><a href="#">Contact</a></li>
                              <li class="divider"></li>
                              <li class="active"><a href="#">Active</a></li>
                              <li><a href="#">Suspend</a></li>
                              <li><a href="#">Remove</a></li>
                            </ul>
                          </div>
                        </td>
                      </tr>
                      <tr>
                        <td class="text-center">
                          <label class="option block mn">
                            <input type="checkbox" name="mobileos" value="FR"><span class="checkbox mn"></span>
                          </label>
                        </td>
                        <td class="w50"><img title="user" src="assets/img/avatars/3.jpg" class="img-responsive mw30 ib mr10"></td>
                        <td>Larry Kingster</td>
                        <td>larry@company.com</td>
                        <td>12/03/2014</td>
                        <td>46</td>
                        <td>$16,200</td>
                        <td class="text-right">
                          <div class="btn-group text-right">
                            <button type="button" data-toggle="dropdown" aria-expanded="false" class="btn btn-success br2 btn-xs fs12 dropdown-toggle">Active<span class="caret ml5"></span></button>
                            <ul role="menu" class="dropdown-menu">
                              <li><a href="#">Edit</a></li>
                              <li><a href="#">Contact</a></li>
                              <li class="divider"></li>
                              <li class="active"><a href="#">Active</a></li>
                              <li><a href="#">Suspend</a></li>
                              <li><a href="#">Remove</a></li>
                            </ul>
                          </div>
                        </td>
                      </tr>
                      <tr>
                        <td class="text-center">
                          <label class="option block mn">
                            <input type="checkbox" name="mobileos" value="FR"><span class="checkbox mn"></span>
                          </label>
                        </td>
                        <td class="w50"><img title="user" src="assets/img/avatars/4.jpg" class="img-responsive mw30 ib mr10"></td>
                        <td>Emily Roundwheel</td>
                        <td>emily@company.com</td>
                        <td>12/03/2014</td>
                        <td>06</td>
                        <td>$1,400</td>
                        <td class="text-right">
                          <div class="btn-group text-right">
                            <button type="button" data-toggle="dropdown" aria-expanded="false" class="btn btn-success br2 btn-xs fs12 dropdown-toggle">Active<span class="caret ml5"></span></button>
                            <ul role="menu" class="dropdown-menu">
                              <li><a href="#">Edit</a></li>
                              <li><a href="#">Contact</a></li>
                              <li class="divider"></li>
                              <li class="active"><a href="#">Active</a></li>
                              <li><a href="#">Suspend</a></li>
                              <li><a href="#">Remove</a></li>
                            </ul>
                          </div>
                        </td>
                      </tr>
                      <tr>
                        <td class="text-center">
                          <label class="option block mn">
                            <input type="checkbox" name="mobileos" value="FR"><span class="checkbox mn"></span>
                          </label>
                        </td>
                        <td class="w50"><img title="user" src="assets/img/avatars/5.jpg" class="img-responsive mw30 ib mr10"></td>
                        <td>Nick Cannoneer</td>
                        <td>sara@company.com</td>
                        <td>12/03/2014</td>
                        <td>43</td>
                        <td>$13,600</td>
                        <td class="text-right">
                          <div class="btn-group text-right">
                            <button type="button" data-toggle="dropdown" aria-expanded="false" class="btn btn-success br2 btn-xs fs12 dropdown-toggle">Active<span class="caret ml5"></span></button>
                            <ul role="menu" class="dropdown-menu">
                              <li><a href="#">Edit</a></li>
                              <li><a href="#">Contact</a></li>
                              <li class="divider"></li>
                              <li class="active"><a href="#">Active</a></li>
                              <li><a href="#">Suspend</a></li>
                              <li><a href="#">Remove</a></li>
                            </ul>
                          </div>
                        </td>
                      </tr>
                      <tr>
                        <td class="text-center">
                          <label class="option block mn">
                            <input type="checkbox" name="mobileos" value="FR"><span class="checkbox mn"></span>
                          </label>
                        </td>
                        <td class="w50"><img title="user" src="assets/img/avatars/6.jpg" class="img-responsive mw30 ib mr10"></td>
                        <td>Morgan Lunar</td>
                        <td>morgan@company.com</td>
                        <td>12/03/2014</td>
                        <td>11</td>
                        <td>$3,200</td>
                        <td class="text-right">
                          <div class="btn-group text-right">
                            <button type="button" data-toggle="dropdown" aria-expanded="false" class="btn btn-success br2 btn-xs fs12 dropdown-toggle">Active<span class="caret ml5"></span></button>
                            <ul role="menu" class="dropdown-menu">
                              <li><a href="#">Edit</a></li>
                              <li><a href="#">Contact</a></li>
                              <li class="divider"></li>
                              <li class="active"><a href="#">Active</a></li>
                              <li><a href="#">Suspend</a></li>
                              <li><a href="#">Remove</a></li>
                            </ul>
                          </div>
                        </td>
                      </tr>
                      <tr>
                        <td class="text-center">
                          <label class="option block mn">
                            <input type="checkbox" name="mobileos" value="FR"><span class="checkbox mn"></span>
                          </label>
                        </td>
                        <td class="w50"><img title="user" src="assets/img/avatars/4.jpg" class="img-responsive mw30 ib mr10"></td>
                        <td>Emily Roundwheel</td>
                        <td>emily@company.com</td>
                        <td>12/03/2014</td>
                        <td>06</td>
                        <td>$1,400</td>
                        <td class="text-right">
                          <div class="btn-group text-right">
                            <button type="button" data-toggle="dropdown" aria-expanded="false" class="btn btn-warning br2 btn-xs fs12 dropdown-toggle">In-Active<span class="caret ml5"></span></button>
                            <ul role="menu" class="dropdown-menu">
                              <li><a href="#">Edit</a></li>
                              <li><a href="#">Contact</a></li>
                              <li class="divider"></li>
                              <li class="active"><a href="#">Active</a></li>
                              <li><a href="#">Suspend</a></li>
                              <li><a href="#">Remove</a></li>
                            </ul>
                          </div>
                        </td>
                      </tr>
                      <tr>
                        <td class="text-center">
                          <label class="option block mn">
                            <input type="checkbox" name="mobileos" value="FR"><span class="checkbox mn"></span>
                          </label>
                        </td>
                        <td class="w50"><img title="user" src="assets/img/avatars/2.jpg" class="img-responsive mw30 ib mr10"></td>
                        <td>Sara Marshall</td>
                        <td>sara@company.com</td>
                        <td>12/03/2014</td>
                        <td>16</td>
                        <td>$4,200</td>
                        <td class="text-right">
                          <div class="btn-group text-right">
                            <button type="button" data-toggle="dropdown" aria-expanded="false" class="btn btn-warning br2 btn-xs fs12 dropdown-toggle">In-Active<span class="caret ml5"></span></button>
                            <ul role="menu" class="dropdown-menu">
                              <li><a href="#">Edit</a></li>
                              <li><a href="#">Contact</a></li>
                              <li class="divider"></li>
                              <li class="active"><a href="#">Active</a></li>
                              <li><a href="#">Suspend</a></li>
                              <li><a href="#">Remove</a></li>
                            </ul>
                          </div>
                        </td>
                      </tr>
                      <tr>
                        <td class="text-center">
                          <label class="option block mn">
                            <input type="checkbox" name="mobileos" value="FR"><span class="checkbox mn"></span>
                          </label>
                        </td>
                        <td class="w50"><img title="user" src="assets/img/avatars/1.jpg" class="img-responsive mw30 ib mr10"></td>
                        <td>Roger Rover</td>
                        <td>roger@company.com</td>
                        <td>12/03/2014</td>
                        <td>33</td>
                        <td>$17,100</td>
                        <td class="text-right">
                          <div class="btn-group text-right">
                            <button type="button" data-toggle="dropdown" aria-expanded="false" class="btn btn-warning br2 btn-xs fs12 dropdown-toggle">In-Active<span class="caret ml5"></span></button>
                            <ul role="menu" class="dropdown-menu">
                              <li><a href="#">Edit</a></li>
                              <li><a href="#">Contact</a></li>
                              <li class="divider"></li>
                              <li class="active"><a href="#">Active</a></li>
                              <li><a href="#">Suspend</a></li>
                              <li><a href="#">Remove</a></li>
                            </ul>
                          </div>
                        </td>
                      </tr>
                      <tr>
                        <td class="text-center">
                          <label class="option block mn">
                            <input type="checkbox" name="mobileos" value="FR"><span class="checkbox mn"></span>
                          </label>
                        </td>
                        <td class="w50"><img title="user" src="assets/img/avatars/2.jpg" class="img-responsive mw30 ib mr10"></td>
                        <td>Laura Smileton</td>
                        <td>laura@company.com</td>
                        <td>12/03/2014</td>
                        <td>12</td>
                        <td>$3,100</td>
                        <td class="text-right">
                          <div class="btn-group text-right">
                            <button type="button" data-toggle="dropdown" aria-expanded="false" class="btn btn-danger br2 btn-xs fs12 dropdown-toggle">Suspended<span class="caret ml5"></span></button>
                            <ul role="menu" class="dropdown-menu">
                              <li><a href="#">Edit</a></li>
                              <li><a href="#">Contact</a></li>
                              <li class="divider"></li>
                              <li class="active"><a href="#">Active</a></li>
                              <li><a href="#">Suspend</a></li>
                              <li><a href="#">Remove</a></li>
                            </ul>
                          </div>
                        </td>
                      </tr>
                      <tr>
                        <td class="text-center">
                          <label class="option block mn">
                            <input type="checkbox" name="mobileos" value="FR"><span class="checkbox mn"></span>
                          </label>
                        </td>
                        <td class="w50"><img title="user" src="assets/img/avatars/1.jpg" class="img-responsive mw30 ib mr10"></td>
                        <td>Dave Robert</td>
                        <td>dave@company.com</td>
                        <td>12/03/2014</td>
                        <td>222</td>
                        <td>$3,600</td>
                        <td class="text-right">
                          <div class="btn-group text-right">
                            <button type="button" data-toggle="dropdown" aria-expanded="false" class="btn btn-danger br2 btn-xs fs12 dropdown-toggle">Suspended<span class="caret ml5"></span></button>
                            <ul role="menu" class="dropdown-menu">
                              <li><a href="#">Edit</a></li>
                              <li><a href="#">Contact</a></li>
                              <li class="divider"></li>
                              <li class="active"><a href="#">Active</a></li>
                              <li><a href="#">Suspend</a></li>
                              <li><a href="#">Remove</a></li>
                            </ul>
                          </div>
                        </td>
                      </tr>
                    </tbody>
                  </table>
                </div>
              </div>
            </div>
          </div>
          <!-- begin: .tray-right-->
          <aside data-tray-height="match" class="tray tray-right tray290">
            <!-- menu quick links-->
            <div class="admin-form">
              <h4> Find Customer</h4>
              <hr class="short">
              <div class="section mb10">
                <label for="customer-id" class="field prepend-icon">
                  <input id="customer-id" type="text" name="customer-id" placeholder="Customer ID #" class="gui-input">
                  <label for="customer-id" class="field-icon"><i class="fa fa-user"></i></label>
                </label>
              </div>
              <div class="section mb10">
                <label for="customer-name" class="field prepend-icon">
                  <input id="customer-name" type="text" name="customer-name" placeholder="Customer Name" class="gui-input">
                  <label for="customer-name" class="field-icon"><i class="fa fa-user"></i></label>
                </label>
              </div>
              <div class="section mb25">
                <label for="customer-email" class="field prepend-icon">
                  <input id="customer-email" type="text" name="customer-email" placeholder="Customer Email" class="gui-input">
                  <label for="customer-email" class="field-icon"><i class="fa fa-envelope-o"></i></label>
                </label>
              </div>
              <h5><small>Search Country</small></h5>
              <div class="section mb15">
                <label class="field select">
                  <select id="customer-location" name="customer-location">
                    <option value="0" selected="selected">Filter by Location</option>
                    <option value="1">United States</option>
                    <option value="2">Europe</option>
                    <option value="3">Asia</option>
                    <option value="4">India</option>
                  </select><i class="arrow double"></i>
                </label>
              </div>
              <h5><small>Search Company</small></h5>
              <div class="section mb15">
                <label class="field select">
                  <select id="customer-company" name="customer-company">
                    <option value="0" selected="selected">Filter by Company</option>
                    <option value="1">Apple</option>
                    <option value="2">Sony</option>
                    <option value="3">Envato</option>
                    <option value="4">Microsoft</option>
                    <option value="5">Google</option>
                  </select><i class="arrow double"></i>
                </label>
              </div>
              <h5><small>Registration Date</small></h5>
              <div class="section row">
                <div class="col-md-6">
                  <label for="date1" class="field prepend-icon">
                    <input id="date1" type="text" name="date1" placeholder="01/01/14" class="gui-input">
                    <label for="date1" class="field-icon"><i class="fa fa-calendar"></i></label>
                  </label>
                </div>
                <div class="col-md-6">
                  <label for="date2" class="field prepend-icon">
                    <input id="date2" type="text" name="date2" placeholder="06/01/15" class="gui-input">
                    <label for="date2" class="field-icon"><i class="fa fa-calendar"></i></label>
                  </label>
                </div>
              </div>
              <hr class="short">
              <div class="section">
                <button type="button" class="btn btn-default btn-sm ph25">Search</button>
                <label class="field option ml15">
                  <input type="checkbox" name="info"><span class="checkbox"></span><span class="text-muted">Save Search</span>
                </label>
              </div>
            </div>
          </aside>
        </section>
      </section>
      <!-- Start: Right Sidebar-->
      <aside id="sidebar_right" class="nano affix">
        <!-- Start: Sidebar Right Content-->
        <div class="sidebar-right-content nano-content">
          <div class="tab-block sidebar-block br-n">
            <ul class="nav nav-tabs tabs-border nav-justified hidden">
              <li class="active"><a href="#sidebar-right-tab1" data-toggle="tab">Tab 1</a></li>
              <li><a href="#sidebar-right-tab2" data-toggle="tab">Tab 2</a></li>
              <li><a href="#sidebar-right-tab3" data-toggle="tab">Tab 3</a></li>
            </ul>
            <div class="tab-content br-n">
              <div id="sidebar-right-tab1" class="tab-pane active">
                <h5 class="title-divider text-muted mb20">Server Statistics<span class="pull-right">2013<i class="fa fa-caret-down ml5"></i></span></h5>
                <div class="progress mh5">
                  <div role="progressbar" aria-valuenow="45" aria-valuemin="0" aria-valuemax="100" style="width: 44%" class="progress-bar progress-bar-primary"><span class="fs11">DB Request</span></div>
                </div>
                <div class="progress mh5">
                  <div role="progressbar" aria-valuenow="45" aria-valuemin="0" aria-valuemax="100" style="width: 84%" class="progress-bar progress-bar-info"><span class="fs11 text-left">Server Load</span></div>
                </div>
                <div class="progress mh5">
                  <div role="progressbar" aria-valuenow="45" aria-valuemin="0" aria-valuemax="100" style="width: 61%" class="progress-bar progress-bar-warning"><span class="fs11 text-left">Server Connections</span></div>
                </div>
                <h5 class="title-divider text-muted mt30 mb10">Traffic Margins</h5>
                <div class="row">
                  <div class="col-xs-5">
                    <h3 class="text-primary mn pl5">132</h3>
                  </div>
                  <div class="col-xs-7 text-right">
                    <h3 class="text-success-dark mn"><i class="fa fa-caret-up"></i> 13.2%</h3>
                  </div>
                </div>
                <h5 class="title-divider text-muted mt25 mb10">Database Request</h5>
                <div class="row">
                  <div class="col-xs-5">
                    <h3 class="text-primary mn pl5">212</h3>
                  </div>
                  <div class="col-xs-7 text-right">
                    <h3 class="text-success-dark mn"><i class="fa fa-caret-up"></i> 25.6%</h3>
                  </div>
                </div>
                <h5 class="title-divider text-muted mt25 mb10">Server Response</h5>
                <div class="row">
                  <div class="col-xs-5">
                    <h3 class="text-primary mn pl5">82.5</h3>
                  </div>
                  <div class="col-xs-7 text-right">
                    <h3 class="text-danger mn"><i class="fa fa-caret-down"></i> 17.9%</h3>
                  </div>
                </div>
                <h5 class="title-divider text-muted mt40 mb20">Server Statistics<span class="pull-right text-primary fw600">USA</span></h5>
              </div>
              <div id="sidebar-right-tab2" class="tab-pane"></div>
              <div id="sidebar-right-tab3" class="tab-pane"></div>
            </div>
          </div>
        </div>
      </aside>
';




	
	echo '<section>
			<ul id="myTab2" class="nav nav-tabs">
				<li class="active">
					<a href="#home" data-toggle="tab">'. _MAIN .'</a>
				</li>
				<li class="">
					<a href="#addition" data-toggle="tab">'. _ADDITION .'</a>
				</li>
				<li class="">
					<a href="#access" data-toggle="tab">'. _ACCESS .'</a>
				</li>';
	$queryXF = $db->query("SELECT * FROM ".DB_PREFIX."_xfields WHERE module='news'");
	if($db->numRows($queryXF) > 0) 
	{
		echo'	<li class="">
					<a href="#dop" data-toggle="tab">'. _NEWS_DOP .'</a>
				</li>';
	}
	echo'		<li class="">
					<a href="#file" data-toggle="tab" onclick="uploaderStart();">'. _NEWS_FILE .'</a>
				</li>
			</ul>		
			<section class="panel">
				<div class="panel-body">
					<div id="myTabContent2" class="tab-content">				
						<div class="tab-pane active" id="home">					
							<div class="panel-heading no-border"><b>'. $lln .'</b></div>
							<div class="panel-body">
								<div class="switcher-content">
									<form action="{MOD_LINK}/save" onsubmit="return caa(false);" method="post" name="content" role="form">
									<div class="form-horizontal parsley-form" data-parsley-validate>
										<div class="form-group">
											<label class="col-sm-3 control-label">'. _NEWS_ADDTITLE .'</label>
											<div class="col-sm-4">
												<input type="text" name="title" '.(isset($nid) ? '' : 'onchange="getTranslit(gid(\'title\').value, \'translit\'); caa(this);"').' value="' . (isset($title[$config['lang']]) ? $title[$config['lang']] : '') . '" class="form-control" id="title"  data-parsley-required="true" data-parsley-trigger="change">
											</div>
										</div>
										<div class="form-group">
											<label class="col-sm-3 control-label">'. _NEWS_ADDALT .'</label>
											<div class="col-sm-4">
												<input type="text" name="translit" value="'.$altname.'" class="form-control" id="translit"  data-parsley-required="true" data-parsley-trigger="change">
											</div>
										</div>			
										<div class="form-group">
											<label class="col-sm-3 control-label">'. _NEWS_ADDTAG .'</label>
											<div class="col-sm-4">
												<input type="text" name="tags"  value="' . $tags . '" class="form-control" id="tags"  data-parsley-trigger="change">
											</div>
										</div>										
										<div class="form-group">
											<label class="col-sm-3 control-label">'. _NEWS_DATE_PUB .'</label>
											<div class="col-sm-4">
												<div class="input-group date" id="datetimepicker1">
												  <input name="date" type="text" class="form-control" value="'.$date.'"/>
												  <span class="input-group-addon">
													<span class="glyphicon-calendar glyphicon"></span>
												  </span>
												</div>												
												<script type="text/javascript">
													$(function () {
													  $(\'#datetimepicker1\').datetimepicker({language: \'ru\'});
													});
												</script>
											</div>
										</div>				
										<div class="form-group">
											<label class="col-sm-3 control-label">'. _NEWS_AUTHOR .'</label>
											<div class="col-sm-4">
												<input type="text" name="author"  value="' . $author . '" class="form-control" id="author" data-parsley-trigger="change">				
											</div>
										</div>
										<div class="form-group">
											<label class="col-sm-3 control-label">'. _NEWS_ADDCAT .'</label>
											<div class="col-sm-4">
												<select name="category[]" id="maincat" style="width:auto;" onchange="if(this.value != \'0\') {show(\'catSub\');}" >
													<option value="0">'._NEWS_ADD_NOCAT.'</option>';	
	foreach ($cats_arr as $cid => $name) 
	{
		$selected = ($cid == $firstCat) ? "selected" : "";
		echo '<option value="' . $cid . '" ' . $selected . '>' . $name . '</option>';
	}
	echo '
												</select>
											</div>
										</div>
										<div class="form-group" id="catSub" style="' . (isset($nid) ? '' : 'display:none;') . '">
											<label class="col-sm-3 control-label">'. _NEWS_ADDALTCAT .'</label>
											<div class="col-sm-4">
												<select name="category[]" id="category"  style="width:auto;" multiple >';
	foreach ($cats_arr as $cid => $name) 
	{
		if($catttt) $selected = in_array($cid, $catttt) ? "selected" : "";
		echo '<option value="' . $cid . '" ' . $selected . ' id="cat_' . $cid . '">' . $name . '</option>';
	}
	echo '										</select>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
						<div class="tab-pane" id="addition">	
							<div class="panel-heading no-border">
								<b>'. _NEWS_ADDITION .'</b>
							</div>
							<div class="panel-body">
								<div class="switcher-content">
									<div class="form-horizontal parsley-form">
									
										<div class="form-group">
											<label class="col-sm-3 control-label">'. _ACTIVATE_NEWS .'?</label>
											<div class="col-sm-4">
												'.checkbox('status', $active).'
											</div>
										</div>
										<div class="form-group">
											<label class="col-sm-3 control-label">'. _ACTIVATE_RATING .'?</label>
											<div class="col-sm-4">
												'.checkbox('rating', $allow_rating).'
											</div>
										</div>
										<div class="form-group">
											<label class="col-sm-3 control-label">'. _ACTIVATE_COMMENTS .'?</label>
												<div class="col-sm-4">
													'.checkbox('comments', $allow_comments).'
												</div>
										</div>
										<div class="form-group">
											<label class="col-sm-3 control-label">'.  _TOP_SET .'?</label>
											<div class="col-sm-4">
												'.checkbox('fix', $fix).'
											</div>
										</div>
										<div class="form-group">
											<label class="col-sm-3 control-label">'. _PUBLIC_INDEX .'?</label>
											<div class="col-sm-4">
												'.checkbox('index', $allow_index).'
											</div>
										</div>
										<div class="form-group">
											<label class="col-sm-3 control-label">Keywords:</label>
											<div class="col-sm-4">
												<input type="text" name="keywords"  value="' . $keywords . '" class="form-control" id="keywords" data-parsley-trigger="change">	
											</div>
										</div>
										<div class="form-group">
											<label class="col-sm-3 control-label">Description:</label>
											<div class="col-sm-4">
												<input type="text" name="description"  value="' . $description . '" class="form-control" id="description" data-parsley-trigger="change">	
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
						<div class="tab-pane" id="access">
							<div class="panel-heading no-border">
								<b>'. _NEWS_ACCESS .'</b>
							</div>
							<div class="panel-body">
								<div class="switcher-content">
									<div class="form-horizontal parsley-form">
										<div class="form-group">
											<label class="col-sm-3 control-label">'. _NEWS_ABOUT .'</label>
											<div class="col-sm-4">
												<p class="form-control-static">'. _HOW_GROUPS_WORKS .'</p>
											</div>
										</div>
										<div class="form-group">
											<label class="col-sm-3 control-label">'. _GROUP_ACCESS .'?</label>
											<div class="col-sm-4">
												<select name="groups[]" id="group" class="cat_select" multiple>
													<option value="" ' . (empty($grroups) ? 'selected' : '') . '">'. _NEWS_ALL_GROUP .'</option>';
	$query = $db->query("SELECT * FROM `" . USER_DB . "`.`" . USER_PREFIX . "_groups` ORDER BY admin DESC,moderator DESC,user DESC,guest DESC,banned DESC");
	while($rows = $db->getRow($query)) 
	{
		$selected = in_array($rows['id'], $grroups) ? "selected" : "";
		echo '<option value="' . $rows['id'] . '" ' . $selected . '>' . $rows['name'] . '</option>';
	}
	echo'										</select>		
											</div>
										</div>
									</div>	
								</div>		
							</div>			
						</div>
						<div class="tab-pane" id="dop">
							<div class="panel-heading no-border">
								<b>'. _NEWS_DOP .'</b>
							</div>
							<div class="panel-body">
								<div class="switcher-content">
									<div class="form-horizontal parsley-form">';
	if($db->numRows($queryXF) > 0) 
	{		
		while($xfield = $db->getRow($queryXF)) 
		{
			echo '						<div class="form-group">
											<label class="col-sm-3 control-label">'. $xfield['title'] .'</label>
											<div class="col-sm-4">';
			if($xfield['type'] == 3)
			{
				$dxfield = array_map('trim', explode("\n", $xfield['content']));
				$xfieldChange = '<select class="form-control" name="xfield[' . $xfield['id'] . ']"><option value="">Пусто</option>';

				foreach($dxfield as $xfiled_content)
				{
					$xfieldChange .= '<option value="' . $xfiled_content . '" ' . (isset($fields[$xfield['id']][1]) && $fields[$xfield['id']][1] == $xfiled_content ? 'selected' : ''). '>' . $xfiled_content . '</option>';
				}
				$xfieldChange .= '</select>';
			}
			elseif($xfield['type'] == 2)
			{
				$xfieldChange = '<textarea class="form-control" name="xfield[' . $xfield['id'] . ']" >' . (!empty($fields[$xfield['id']][1]) ? $fields[$xfield['id']][1] : ($id ? '' : $xfield['content'])) . '</textarea>';
			}
			else
			{
				$xfieldChange = '<input type="text" class="form-control" name="xfield[' . $xfield['id'] . ']" value="' . (!empty($fields[$xfield['id']][1]) ? $fields[$xfield['id']][1] : ($id ? '' : $xfield['content'])) . '" />';
			}
			echo $xfieldChange;
			echo '</div></div>
					<input type="hidden" name="xfieldT[' . $xfield['id'] . ']" value="' . $xfield['title'] . '" />';
		}
	}
	if ($id == false){
		mkdir(ROOT.'files/news/temp', 0777);
		$_SESSION["RF"]["fff"] ="news/temp/";
		
	}
	else
	{
		$_SESSION["RF"]["fff"] ="news/".$id."/";
	}

	echo'							</div>	
								</div>		
							</div>			
						</div>
						
					</div>
				</section>
			</section> 

			<section>
			<ul id="myTab3" class="nav nav-tabs">
				<li class="active">
					<a href="#lang_main" data-toggle="tab">Основное язык (Русский)</a>
				</li>
				<li class="">
					<a href="#lang_en" data-toggle="tab">Английский</a>
				</li>	
	<li style="margin-right: 0px;" class="pull-right">
					<a href="" >Загрузка файлов</a>
				</li>				
			</ul>
			
			
							
			</ul>
			<section class="panel">
					<header class="panel-heading">'.  _NEWS_SHORT .'</header>			
					<div class="panel-body">
						<div class="form-horizontal bordered-group">
							<div class="form-group">
								
								<div style="padding-left:34px;padding-right:34px"  class="col-sm-12">'
									.adminArea('short[' . $config['lang'] . ']', (isset($short[$config['lang']]) ? $short[$config['lang']] : ''), 5, 'textarea', 'onchange="caa(this);"', true).'
									<input  name="submit" type="submit" class="btn btn-primary btn-parsley" id="sub" value="' . $dosave . '" />
								</div>
								
							</div>			
							
						</div>
					</div>					
					';		 
	if($edit) 
	{
		echo "<input type=\"hidden\" name=\"edit\" value=\"1\" />";
		if($news['active'] == 2) echo "<input type=\"hidden\" name=\"from_user\" value=\"1\" />";
		echo "<input type=\"hidden\" name=\"edit_id\" value=\"".$id."\" />";
	}	
	if(isset($nid)) echo "<input type=\"hidden\" name=\"oldAltName\" value=\"$altname\" />";
	echo'				</form>
				</section>
				</section>
			';	
	$adminTpl->admin_foot();
} 
 
//созранение новости
function news_save() 
{
global $adminTpl, $core, $db, $cats, $groupss, $config;
	$bb = new bb;
	$word_counter = new Counter();
	
	
	$title = $_POST['title'];
	$langTitle = isset($_POST['langtitle']) ? $_POST['langtitle'] : '';
	$langTitle[$config['lang']] = $title;
	$author = filter($_POST['author'], 'nick');
	$ttime = 'UNIX_TIMESTAMP(NOW())';
	$date = !empty($_POST['date']) ? filter($_POST['date']) : $ttime;	
	$oldAltName = !empty($_POST['oldAltName']) ? filter($_POST['oldAltName']) : '';
	$tags = isset($_POST['tags']) ? mb_strtolower(filter($_POST['tags'], 'a'), 'UTF-8') : mb_strtolower(filter($gen_tag, 'a'), 'UTF-8');
	$translit = ($_POST['translit'] !== '') ? mb_strtolower(str_replace(array('-', ' '), array('_', '_'), $_POST['translit']), 'UTF-8') : translit($title);

	
	$full= isset($_POST['full']) ? $_POST['full'] : '';
	$short= isset($_POST['short']) ? $_POST['short'] : '';
	
	$xfield = isset($_POST['xfield']) ? $_POST['xfield'] : '';
	$xfieldT = isset($_POST['xfieldT']) ? ($_POST['xfieldT']) : '';
	$category = isset($_POST['category']) ? array_unique($_POST['category']) : '0';
	$groups = isset($_POST['groups']) ? $_POST['groups'] : '0';
	$comment = isset($_POST['comments']) ? 1 : 0;
	$rating = isset($_POST['rating']) ? 1 : 0;
	$index = isset($_POST['index']) ? 1 : 0;
	$status = isset($_POST['status']) ? 1 : 0;
	$fix = isset($_POST['fix']) ? 1 : 0;	
	$edit_id = isset($_POST['edit_id']) ? intval($_POST['edit_id']) : '';
	echo $cnt;
	$cnt = (($full != '') ? $full : $short);	
	$gen_tag =  $word_counter->get_keywords(substr($cnt, 0, 500)); 
	$keywords = !empty($_POST['keywords']) ? $_POST['keywords'] : $word_counter->get_keywords(substr($cnt, 0, 500)); 	
	$newcnt = $bb->parse(processText(filter(fileInit('news', $edit_id, 'content', $cnt), 'html')), $edit_id, true);	
	echo '<br>';
	echo $cnt;
	
	
	$description = !empty($_POST['description']) ? $_POST['description'] : substr(strip_tags($newcnt), 0, 150); 	
	
	
	if($edit_id > 0)
	{			
		$old_dataQ = $db->query("SELECT * FROM ".DB_PREFIX."_news WHERE id = '" . $edit_id . "'");
		$old_data = $db->getRow($old_dataQ);
	}
	
	if($date != $ttime)
	{
		$parseDate = explode(' ', $date);
		$subDate = explode('.', $parseDate[0]);
		if(isset($parseDate[1]))
		{
			$subTime = explode(':', $parseDate[1]);
		}
		else
		{
			$subTime[0] = 12;
			$subTime[1] = 0;
		}
		$date = gmmktime($subTime[0], $subTime[1], 0, $subDate[1], $subDate[0], $subDate[2]);
	}
	if(is_array($category)) 
	{
		$firstCat = $category[0];
		unset($category[0]);
		$deleteCat = array_search($firstCat, $category);
		unset($category[$deleteCat]);
		$category[0] = $firstCat;
		ksort($category);
		foreach($category as $cid) 
		{
			$cats .= intval($cid) . ",";
		}
	}
	else 
	{
		$cats  = $category . ',';
	}
	
	$fieldsSer = '';
	if(!empty($xfield))
	{
		foreach($xfield as $xId => $xContent)
		{
			if(!empty($xContent) && $xId > 0 && !empty($xfieldT[$xId]))
			{
				$xContent = processText(filter($xContent, 'html'));
				$xId = intval($xId);
				$xfieldT[$xId] = filter($xfieldT[$xId], 'title');
				$fileds[$xId] = array($xfieldT[$xId], $xContent);
			}
		}
		
		$fieldsSer = serialize($fileds);
	}
	
	$cats = ',' . $cats;	
	
	if(is_array($groups)) 
	{
		foreach($groups as $gid) 
		{
			$groupss .= intval($gid) . ",";
		}
	}
	else 
	{
		$groupss  = $groups . ',';
	}
	$groupss = ',' . $groupss;
	$adminTpl->admin_head(_MODULES . ' | ' . _NADD);

	if($title && $short['ru'] && $author && $translit) 
	{
		
		
		if(isset($_POST['edit'])) 
		{
			foreach($langTitle as $k => $v)
			{
				$ntitle = filter(trim(htmlspecialchars_decode($v, ENT_QUOTES)), 'title');
				$nshort = $bb->parse(processText(filter(fileInit('news', $edit_id, 'content', $short[$k]), 'html')), $edit_id, true);
				
				
				
				
				$nfull = $bb->parse(processText(filter(fileInit('news', $edit_id, 'content', $full[$k]), 'html')), $edit_id, true);	
				if(isset($_POST['empty'][$k]) && trim($v) != '' && trim($short[$k]) != '')
				{
					$db->query("INSERT INTO `" . DB_PREFIX . "_langs` ( `postId` , `module` , `title` , `short` , `full` , `lang` ) 
	VALUES ('" . $edit_id . "', 'news', '" . $db->safesql(processText($ntitle)) . "', '" . $db->safesql($nshort) . "', '" . $db->safesql($nfull) . "' , '" . $k . "');");
				}
				elseif(!isset($_POST['empty'][$k])  && (trim($v) == '' OR trim($short[$k]) == ''))
				{
					$db->query("DELETE FROM `" . DB_PREFIX . "_langs` WHERE `postId` ='" . $edit_id . "' AND `module` ='news' AND `lang`='" . $k . "' LIMIT 1");
				}
				elseif(!isset($_POST['empty'][$k]) && trim($v) != '' && trim($short[$k]) != '')
				{
					$db->query("UPDATE `" . DB_PREFIX . "_langs` SET `title` = '" . $db->safesql(processText($ntitle)) . "', `short` = '" . $db->safesql($nshort) . "', `full` = '" . $db->safesql($nfull) . "' WHERE `postId` ='" . $edit_id . "' AND `module` ='news' AND `lang`='" . $k . "' LIMIT 1 ;");
				}
			}
			
			if(!empty($tags) && $status == 1)
			{
				if($old_data['tags'] != $tags)
				{
					workTags($edit_id, $old_data['tags'], 'delete');
					workTags($edit_id, $tags, 'add');
				}
			}			
			
			if(isset($_POST['from_user']) && $status == 0)
			{
				$status = 2;
			}
			
			if(($old_data['active'] == 2 && $status == 1) || ($old_data['active'] == 0 && $status == 1))
			{
				$db->query("UPDATE `" . USER_DB . "`.`" . USER_PREFIX . "_users` SET user_news=user_news+1 WHERE `nick` ='" . $db->safesql($author) . "' LIMIT 1", true);
				user_points($author, 'add_news');
				$access = $db->getRow($db->query("SELECT id FROM `" . USER_DB . "`.`" . USER_PREFIX . "_users` WHERE nick = '" . $db->safesql($author) . "'"));
				delcache('userInfo_'.$access['id']);
			}
			
			if($old_data['active'] == 1 && $status == 0)
			{
				$db->query("UPDATE `" . USER_DB . "`.`" . USER_PREFIX . "_users` SET user_news=user_news-1 WHERE `nick` ='" . $db->safesql($author) . "' LIMIT 1", true);
				$access = $db->getRow($db->query("SELECT id FROM `" . USER_DB . "`.`" . USER_PREFIX . "_users` WHERE nick = '" . $db->safesql($author) . "'"));
				delcache('userInfo_'.$access['id']);
			}

		
			$update = $db->query("UPDATE `" . DB_PREFIX . "_news` SET `author` = '" . $author . "', `date` = '" . $date . "', `tags` = '" . $tags . "', `cat` = '" . $cats . "', `altname` = '" . $translit . "', `keywords` = '" . $keywords . "', `description` = '" . $description . "', `allow_comments` = '" . $comment . "', `allow_rating` = '" . $rating . "', `allow_index` = '" . $index . "', `fields` = '" . $fieldsSer . "', `groups` = '" . $groupss . "', `fixed` = '" . $fix . "', `active` = '" . $status . "' WHERE `id` = '" . $edit_id . "' LIMIT 1 ;");
			if($update)
			{
				$adminTpl->info(_SUCCESS_UPDATE .' ' . _NEWS_NAVS . '?');
			}
		} 
		else 
		{
			$insert = $db->query("INSERT INTO `" . DB_PREFIX . "_news` ( `id` , `author` , `date` , `tags` , `cat` , `altname` ,`keywords`,`description`, `allow_comments` , `allow_rating` , `allow_index` , `score` , `votes` , `views` , `comments` , `fields` , `groups` , `fixed` , `active` ) VALUES (NULL, '" . $author . "', " . $date . ", '" . $tags . "', '" . $cats . "', '" . $db->safesql($translit) . "', '" . $keywords . "', '" . $description . "', '" . $comment . "', '" . $rating . "', '" . $index . "', '0', '0', '0', '0', '" . $fieldsSer . "', '" . $groupss . "', '" . $fix . "', '" . $status . "');");
			if($insert) 
			{
				$adminTpl->info(_SUCCESS_ADD . ' ' . _NEWS_NAVS . '?');
				
				if($status == 1)
				{
					$db->query("UPDATE `" . USER_DB . "`.`" . USER_PREFIX . "_users` SET user_news=user_news+1 WHERE `nick` ='" . $db->safesql($author) . "' LIMIT 1", true);
					user_points($author, 'add_news');
					$access = $db->getRow($db->query("SELECT id FROM `" . USER_DB . "`.`" . USER_PREFIX . "_users` WHERE nick = '" . $db->safesql($author) . "'"));
					delcache('userInfo_'.$access['id']);
				}
				
				$query = $db->query("SELECT * FROM ".DB_PREFIX."_news WHERE altname = '" . $db->safesql($translit) . "'");
				$news = $db->getRow($query);
				foreach($langTitle as $k => $v)
				{
					if(trim($v) != '' && trim($short[$k]) != '')
					{
						$ntitle = filter(trim(htmlspecialchars_decode($v, ENT_QUOTES)), 'title');
						$nshort = fileInit('news', $news['id'], 'content', $bb->parse(processText(filter($short[$k], 'html')), $news['id'], true));
						$nfull = fileInit('news', $news['id'], 'content', $bb->parse(processText(filter($full[$k], 'html')), $news['id'], true));	
						$db->query("INSERT INTO `" . DB_PREFIX . "_langs` ( `postId` , `module` , `title` , `short` , `full` , `lang` ) 
	VALUES ('" . $news['id'] . "', 'news', '" . $db->safesql(processText($ntitle)) . "', '" . $db->safesql($nshort) . "', '" . $db->safesql($nfull) . "' , '" . $k . "');");
					}
				}
			
				fileInit('news', $news['id']);
				
				workTags($news['id'], $tags, 'add');

			}
		}
	}
	else 
	{
		$adminTpl->info(_NOT_FILLEDN, 'error');
	}
	$adminTpl->admin_foot();
}

//удаление
function delete($id) {
global $db;
	$query = $db->query("SELECT * FROM ".DB_PREFIX."_news WHERE id = '" . $id . "'");
	$news = $db->getRow($query);
	$db->query("DELETE FROM `" . DB_PREFIX . "_langs` WHERE `postId` = '" . $news['id'] . "' AND `module` = 'news'");
	full_rmdir(ROOT . initDC('news', $news['id']));
	$db->query("DELETE FROM `" . DB_PREFIX . "_news` WHERE `id` = " . $id . " LIMIT 1");
	$db->query("UPDATE `" . USER_DB . "`.`" . USER_PREFIX . "_users` SET user_news=user_news-1 WHERE `nick` ='" . $news['author'] . "' LIMIT 1", true);
	workTags($id, $news['tags'], 'delete');
	deleteComments('news', $id);
}

//д теги
function workTags($id, $tags, $do = 'add')
{
global $db;
	if(!empty($tags))
	{
		$tag = array_map('trim', explode(',', $tags));
		foreach($tag as $t)
		{
			if($do == 'add')
			{
				$db->query("INSERT INTO `" . DB_PREFIX . "_tags` ( `id` , `tag` , `module` ) VALUES (NULL, '" . $db->safesql($t) . "', 'news');");
			}
			elseif($do == 'delete')
			{
				$db->query("DELETE FROM `" . DB_PREFIX . "_tags` WHERE `tag` = '" . $t . "' and module='news' LIMIT 1");			
			}
		}
	}
}

function changeuGroup($var)
{
global $adminTpl, $db, $news_conf;
    $content = '<select class="form-control" name="{varName}">';
	$query2 = $db->query("SELECT * FROM `" . USER_DB . "`.`" . USER_PREFIX . "_groups`");
	while($rows2 = $db->getRow($query2)) 
	{
		$sel = ($news_conf[$var] == $rows2['id']) ? 'selected' : '';
		$content .= '<option value="' . $rows2['id'] . '" ' . $sel . '>' . $rows2['name'] . '</option>';
	}
	$content .= '</select>';
	return $content;
}

switch(isset($url[3]) ? $url[3] : null) {
	default:
		news_main();
	break;
//теги	
	case 'tags':
		$adminTpl->admin_head(_MODULES . ' | ' . _TAGS);
		if(isset($url[4]))
		{
			switch($url[4])
			{
				case 'addOk':
					$adminTpl->info(_ADD_TAGSUC);
					break;				
					
				case 'delOk':
					$adminTpl->info(_DEL_TAGSUC);
					break;
			}
		}
		$adminTpl->open();
		echo '<div class="row">
			<div class="col-lg-12">
				<section class="panel">
					<div class="panel-heading">	
					<form style="margin:0; padding:0" method="POST" action="{MOD_LINK}/addTag">
					<div class="col-sm-5 input-group mg-b-md">
					<input type="text" name="tag" class="form-control">
					<span class="input-group-btn"><input type="submit" class="btn btn-white" value="' . _NEWS_ADD_TAG . '"></span>
				</div>	
				</form>				
		<b>' . _LIST . '</b></div>';		
		$query = $db->query("SELECT tag FROM " . DB_PREFIX . "_tags WHERE module = 'news'");
		if($db->numRows($query) > 0) {
			echo '<div class="panel-body no-padding">
					<form id="tablesForm" style="margin:0; padding:0" method="POST" action="{MOD_LINK}/actionTag"">
						<table class="table no-margin">
							<thead>
								<tr>									
									<th class="col-md-5"><span class="pd-l-sm"></span>' . _TAG . '</th>
									<th class="col-md-5">' . _TAGIN .  '</th>
									<th class="col-md-3">' . _ACTIONS .'</th>									
									<th class="col-md-1"><input type="checkbox" name="all" onclick="setCheckboxes(\'tablesForm\', true); return false;"></th>
								</tr>
							</thead>
							<tbody>';
			while($tag = $db->getRow($query)) 
			{
				$tags[] = $tag['tag'];
			}			
			$tag_cloud = new TagsCloud;
			$tag_cloud->tags = $tags;
			$cloud = Array();
			$tags_list = $tag_cloud->tags_cloud($tag_cloud->tags);
			$min_count = $tag_cloud->get_min_count($tags_list);			
			foreach ($tags_list as $tag => $count) 
			{
				echo '
				<tr>
					<td><span class="pd-l-sm"></span>' . $tag . '</td>
					<td>' . $count . '</td>
					<td>
					<a href="{MOD_LINK}/tagDelete/' . $tag . '" onClick="return getConfirm(\'' . _DELETE . ' ' . _TAG . ' - ' . $tag . '?\')">
					<button type="button" class="btn btn-danger btn-sm" data-toggle="tooltip" data-placement="top" title="" data-original-title="' . _DELETE .	'">X</button>
					</a>
					</td>
					<td> <input type="checkbox" name="checks[]" value="' . $tag . '"></td>
				</tr>';	
			}
		echo '<tr><td></td><td></td><td></td><td></td></tr></tbody></table>		
		<div align="right">
			<table>
				<tr>		
					<td valign="top">
						<input name="submit" type="submit" class="btn btn btn-danger" id="sub" value="' . _DELETE . '" /><span class="pd-l-sm"></span>
					</td>
				</tr>
			</table>
			<br>	
			</div>
			</form></div>';	
		} else {
			echo '<div class="panel-heading">'  . _NEWS_NO_TAG . '</div>';		
			}
			echo'</section></div></div>';
		$adminTpl->close();
		$adminTpl->admin_foot();
		break;
//добавить теги		
	case 'addTag':
		if(!empty($_POST['tag']))
		{
			$db->query("INSERT INTO `" . DB_PREFIX . "_tags` ( `id` , `tag` , `module` ) VALUES (NULL, '" . filter($_POST['tag']) . "', 'news');");
			location(ADMIN.'/module/news/tags/addOk');
		}
		else
		{
			location(ADMIN.'/module/news/tags');
		}
		break;
//теги удаолить		
	case 'tagDelete':
		if(isset($url[4]))
		{
			$tag = filter(utf_decode($url[4]));
			$db->query("DELETE FROM `" . DB_PREFIX . "_tags` WHERE `tag` = '" . $tag . "'");
			location(ADMIN.'/module/news/tags/delOk');
		}
		else
		{
			location(ADMIN.'/module/news/tags');
		}
		break;
//теги	 удалить через action	
	case 'actionTag':
		if(!empty($_POST['checks']))
		{
			foreach($_POST['checks'] as $id) 
			{
				if(trim($id))
				{
					$db->query("DELETE FROM `" . DB_PREFIX . "_tags` WHERE `tag` = '" . $id . "'");
				}
			}
			
			location(ADMIN.'/module/news/delOk');
		}
		else
		{
			location(ADMIN.'/module/news/tags');
		}
		break;
	
	case "add":
		news_add();
	break;
	
	case "save":
		news_save();
	break;
	
	case "edit":
		$id = intval($url[4]);
		news_add($id);
	break;
	
	case "delete":
		$id = intval($url[4]);
		delete($id);
		location(ADMIN.'/module/news');
	break;
	
	case "activate":
		$id = intval($url[4]);
		$db->query("UPDATE `" . DB_PREFIX . "_news` SET `active` = '1' WHERE `id` = " . $id . " LIMIT 1 ;");
		$query = $db->query("SELECT * FROM ".DB_PREFIX."_news WHERE id = '" . $id . "'");
		$news = $db->getRow($query);
		$db->query("UPDATE `" . USER_DB . "`.`" . USER_PREFIX . "_users` SET user_news=user_news+1 WHERE `nick` ='" . $news['author'] . "' LIMIT 1", true);
		location(ADMIN.'/module/news');
	break;	
	
	case "deactivate":
	global $adminTpl, $db;
		$id = intval($url[4]);
		$db->query("UPDATE `" . DB_PREFIX . "_news` SET `active` = '0' WHERE `id` = " . $id . " LIMIT 1 ;");
		$query = $db->query("SELECT * FROM ".DB_PREFIX."_news WHERE id = '" . $id . "'");
		$news = $db->getRow($query);
		$db->query("UPDATE `" . USER_DB . "`.`" . USER_PREFIX . "_users` SET user_news=user_news-1 WHERE `nick` ='" . $news['author'] . "' LIMIT 1", true);
		location(ADMIN.'/module/news');
	break;

	case "action":
	$type = $_POST['act'];
	if(is_array($_POST['checks'])) {
		switch($type) {
			case "activate":
				foreach($_POST['checks'] as $id) {
					$db->query("UPDATE `" . DB_PREFIX . "_news` SET `active` = '1' WHERE `id` = " . intval($id) . " LIMIT 1 ;");
				}
				break;			
			
			case "deActivate":
				foreach($_POST['checks'] as $id) {
					$db->query("UPDATE `" . DB_PREFIX . "_news` SET `active` = '0' WHERE `id` = " . $id . " LIMIT 1 ;");
				}
				break;			
				
			case "reActivate":
				foreach($_POST['checks'] as $id) {
					$db->query("UPDATE `" . DB_PREFIX . "_news` SET `active` = NOT `active` WHERE `id` = " . $id . " LIMIT 1 ;");
				}
				break;
				
			case "nowDate":
				foreach($_POST['checks'] as $id) {
					$db->query("UPDATE `" . DB_PREFIX . "_news` SET `date` = '" . time() . "' WHERE `id` = " . $id . " LIMIT 1 ;");
				}
				break;			
				
			case "blockIndex":
				foreach($_POST['checks'] as $id) {
					$db->query("UPDATE `" . DB_PREFIX . "_news` SET `allow_index` = '0' WHERE `id` = " . $id . " LIMIT 1 ;");
				}
				break;			
				
			case "blockComment":
				foreach($_POST['checks'] as $id) {
					$db->query("UPDATE `" . DB_PREFIX . "_news` SET `	allow_comments` = '0' WHERE `id` = " . $id . " LIMIT 1 ;");
				}
				break;
			
			case "delete":
				foreach($_POST['checks'] as $id) {
					delete(intval($id));
				}
				break;
		}
	}
		if(isset($_GET['moderate']))
		{
			location(ADMIN.'/publications/mod/news');
		}
		else
		{
			location(ADMIN.'/module/news');
		}
	break;
//настройки	
	case 'config':
		require (ROOT.'etc/news.config.php');		
		$configBox = array(
			'news' => array(
				'varName' => 'news_conf',
				'title' => _APNEWS,
				'groups' => array(
					'main' => array(
						'title' => _APNEWS_MAIN,
						'vars' => array(
							'num' => array(
								'title' => _APNEWS_MAIN_NUMT,
								'description' => _APNEWS_MAIN_NUMD,
								'content' => '<input type="text" size="20" name="{varName}" class="form-control" value="{var}" />',
							),
							'comments_num' => array(
								'title' => _APNEWS_MAIN_COMMENTS_NUMT,
								'description' => _APNEWS_MAIN_COMMENTS_NUMD,
								'content' => '<input type="text" size="20" name="{varName}" class="form-control" value="{var}" />',
							),							
							'fullLink' => array(
								'title' => _APNEWS_MAIN_FULLLINKT,
								'description' => _APNEWS_MAIN_FULLLINKD,
								'content' => radio("fullLink", $news_conf['fullLink']),
							),	
							'noModer' => array(
								'title' => _APNEWS_NOMODER,
								'description' => _APNEWS_NOMODER_DESC,
								'content' => changeuGroup('noModer'),
							),	
							'preModer' => array(
								'title' => _APNEWS_PREMODERT,
								'description' => _APNEWS_PREMODERD,
								'content' => '<input type="text" size="20" name="{varName}" class="form-control" value="{var}" />',
							),									
							'related_news' => array(
								'title' => _APNEWS_RELATEDT,
								'description' => _APNEWS_RELATEDD,
								'content' => '<input type="text" size="20" name="{varName}" class="form-control" value="{var}" />',
							),					
							'addNews' => array(
								'title' => _APNEWS_ADDNEWST,
								'description' => _APNEWS_ADDNEWSD,
								'content' => radio("addNews", $news_conf['addNews']),
							),
						)
					),
					'cats' => array(
						'title' => _APNEWS_CATS,
						'vars' => array(
							'showCat' => array(
								'title' => _APNEWS_CATS_SHOWCATT,
								'description' => _APNEWS_CATS_SHOWCATD,
								'content' => radio("showCat", $news_conf['showCat']),
							),							
							'subLoad' => array(
								'title' => _APNEWS_CATS_SUBLOADT,
								'description' => _APNEWS_CATS_SUBLOADD,
								'content' => radio("subLoad", $news_conf['subLoad']),
							),
							'catCols' => array(
								'title' => _APNEWS_CATS_CATCOLST,
								'description' => _APNEWS_CATS_CATCOLSD,
								'content' => '<input type="text" size="20" name="{varName}" class="form-control" value="{var}" />',
							),
							'showBreadcumb' => array(
								'title' => _APNEWS_CATS_SHOWBREADCUMBT,
								'description' => _APNEWS_CATS_SHOWBREADCUMBD,
								'content' => radio("showBreadcumb", $news_conf['showBreadcumb']),
							),
						)
					),					
					'tags' => array(
						'title' => _APNEWS_TAGS,
						'vars' => array(
							'tags' => array(
								'title' => _APNEWS_TAGS_TAGST,
								'description' => _APNEWS_TAGS_TAGSD,
								'content' => radio("tags", $news_conf['tags']),
							),							
							'tags_num' => array(
								'title' => _APNEWS_TAGS_TAGS_NUMT,
								'description' => _APNEWS_TAGS_TAGS_NUMD,
								'content' => '<input type="text" size="20" name="{varName}" class="form-control" value="{var}" />',
							),
							'tagIll' => array(
								'title' => _APNEWS_TAGS_TAGILLT,
								'description' => _APNEWS_TAGS_TAGILLD,
								'content' => radio("tagIll", $news_conf['tagIll']),
							),
							'illFormat' => array(
								'title' => _APNEWS_TAGS_ILLFORMATT,
								'description' => _APNEWS_TAGS_ILLFORMATD,
								'content' => '<input type="text" size="20" name="{varName}" class="form-control" value="{var}" />',
							),
						)
					),
					'ratings' => array(
						'title' => _APNEWS_RATINGS,
						'vars' => array(
							'limitStar' => array(
								'title' => _APNEWS_RATINGS_LIMITSTART,
								'description' => _APNEWS_RATINGS_LIMITSTARD,
								'content' => '<input type="text" size="20" name="{varName}" class="form-control" value="{var}" />',
							),
							'starStyle' => array(
								'title' => _APNEWS_RATINGS_STARSTYLET,
								'description' => _APNEWS_RATINGS_STARSTYLED,
								'content' => '<input type="text" size="20" name="{varName}" class="form-control" value="{var}" />',
							),
							'carma_rate' => array(
								'title' => _APNEWS_CARMA_RATET,
								'description' => _APNEWS_CARMA_RATED,
								'content' => radio("carma_rate", $news_conf['carma_rate']),
							),
							'carma_summ' => array(
								'title' => _APNEWS_CARMA_SUMMT,
								'description' => _APNEWS_CARMA_SUMMD,
								'content' => radio("carma_summ", $news_conf['carma_summ']),
							),
						)
					),
					'files' => array(
						'title' => _APNEWS_FILES,
						'vars' => array(
							'fileEditor' => array(
								'title' => _APNEWS_FEDITORT,
								'description' => _APNEWS_FEDITORD,
								'content' => radio("fileEditor", $news_conf['fileEditor']),
							),
							'imgFormats' => array(
								'title' => _APNEWS_FILE_IMGFORMATST,
								'description' => _APNEWS_FILE_IMGFORMATSD,
								'content' => '<input type="text" size="20" name="{varName}" class="form-control" value="{var}" />',
							),
							'attachFormats' => array(
								'title' => _APNEWS_FILE_ATTACHFORMATST,
								'description' => _APNEWS_FILE_ATTACHFORMATSD,
								'content' => '<input type="text" size="20" name="{varName}" class="form-control" value="{var}" />',
							),
							'max_size' => array(
								'title' => _APNEWS_FILE_MAX_SIZET,
								'description' => _APNEWS_FILE_MAX_SIZED,
								'content' => '<input type="text" size="20" name="{varName}" class="form-control" value="{var}" />',
							),			
							'thumb_width' => array(
								'title' => _APNEWS_THUMB_THUMB_WIDTHT,
								'description' => _APNEWS_THUMB_THUMB_WIDTHD,
								'content' => '<input type="text" size="20" name="{varName}" class="form-control" value="{var}" />',
							),							
						)
					),
					'admin' => array(
						'title' => _CONFIG_TOPBAR,
						'vars' => array(
							'admin_url_1' => array(
								'title' => _APNEWS_FEDITORT,
								'description' => _APNEWS_FEDITORD,
								'content' => radio("admin_url_1", $news_conf['fileEditor']),
							),
							'admin_url_2' => array(
								'title' => _APNEWS_FILE_IMGFORMATST,
								'description' => _APNEWS_FILE_IMGFORMATSD,
								'content' => '<input type="text" size="20" name="{varName}" class="form-control" value="{var}" />',
							),
							'admin_url_3' => array(
								'title' => _APNEWS_FILE_ATTACHFORMATST,
								'description' => _APNEWS_FILE_ATTACHFORMATSD,
								'content' => '<input type="text" size="20" name="{varName}" class="form-control" value="{var}" />',
							),
							'admin_url_4' => array(
								'title' => _APNEWS_FILE_MAX_SIZET,
								'description' => _APNEWS_FILE_MAX_SIZED,
								'content' => '<input type="text" size="20" name="{varName}" class="form-control" value="{var}" />',
							),														
						)
					),
				),
			),
		);
		
		$ok = false;
		
		if(isset($_POST['conf_file']))
		{
			$ok = true;
		}
		
		generateConfig($configBox, 'news', '{MOD_LINK}/config', $ok);
		break;
		
}
