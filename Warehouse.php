<?php
if(WAREHOUSE_PHP==0)
{
	define("WAREHOUSE_PHP",1);
    $staticpath = dirname(__FILE__).'/';
    if (file_exists($staticpath."version.txt"))
        $CentreVersion = "SaaS build ".file_get_contents($staticpath."version.txt");
    else
        $CentreVersion = "SaaS dvlp";

    if (!file_exists ($staticpath."config.inc.php"))
        die ('config.inc.php not found. Please read the configuration guide at <a href="http://doc.centresis.org">http://doc.centresis.org</a>');
	require_once($staticpath."config.inc.php");
    $CentrePath = $staticpath;

    // Load Nu-Coder license
    if (!pelm_load_license($CentrePath."centre.lic"))
        pelm_load_license($CentrePath."../server.lic");
    // calls to check the license have to be made in a licensed file
    // so ... these are included in function/DBGet.php

	// Load functions
	if($handle = opendir("$CentrePath/functions"))
	{
		if(!is_array($IgnoreFiles))
			$IgnoreFiles=Array();

		while (false !== ($file = readdir($handle)))
		{
			// if filename isn't '.' '..' or in the Ignore list... load it.
			if($file!='.' && $file!='..' && !in_array($file,$IgnoreFiles))
				require_once($CentrePath.'/functions/'.$file);
		}
	}

    // Database functions & schema updates
    require_once("database.inc.php");
    require_once("database.php");

    // Check that the license is valid
    // Note: Checks on IP, hostname and all are performed automatically by PHP-Express
    // Note: These functions are defined in functions/License.php
    if (LicenseIsActive()) {
        $licensed = true;
        if (($_SESSION['STAFF_ID'] || $_SESSION['STUDENT_ID']) && LicenseAttribute('Schools') !== FALSE) {
            $licensed = false;
            $RET = DBGet(DBQuery("SELECT TITLE FROM SCHOOLS WHERE SYEAR='".UserSyear()."'"." AND ID='".UserSchool()."'"));
            $currentSchool = $RET[1]['TITLE'];
            foreach (explode('||',LicenseAttribute('Schools')) as $school)
                $licensed = $licensed || ($school == $currentSchool);
        }
        if (!$licensed) die(_('The software license is incorrect.').' '._('Please call Centre SIS technical support.'));
    }    

    // Load hooks
    foreach ($CentreModules as $module => $status)
        if ($status && is_readable($CentrePath."/modules/$module/Hooks.php"))
            require_once($CentrePath."/modules/$module/Hooks.php");

	// Start Session.
    session_name('CentreSIS');
    if ($_SERVER['SCRIPT_NAME']!='/index.php')
        session_set_cookie_params(0,dirname($_SERVER['SCRIPT_NAME']).'/'); //,'',$false,$true);
    ini_set('session.gc_maxlifetime',(isset($SessionTimeout)?$SessionTimeout*60:30*60));
	session_start();
	if(!$_SESSION['STAFF_ID'] && !$_SESSION['PERSON_ID'] && !$_SESSION['STUDENT_ID'] && strpos($_SERVER['PHP_SELF'],'index.php')===false)
	{
		header('Location: index.php');
		exit;
	}

    // Internationalization
    if (!empty($_GET['locale'])) $_SESSION['locale'] = $_GET['locale'];
    $locale = $_SESSION['locale'];
    putenv("LC_ALL=$locale");
    setlocale(LC_ALL, $locale);
    bindtextdomain("centre", $LocalePath);    //binds the messages domain to the locale folder
    bind_textdomain_codeset("centre","UTF-8");     //ensures text returned is utf-8, quite often this is iso-8859-1 by default
    textdomain("centre");    //sets the domain name, this means gettext will be looking for a file called centre.mo

	function Warehouse($mode)
	{	global $__SHOW_FOOTER,$_CENTRE,$CentreTitle,$locale;

		switch($mode)
		{
			case 'header':
                echo '<HTML'.(substr($locale,0,2)=='he'?' dir="RTL"':'').'>';
                echo "<HEAD><TITLE>$CentreTitle</TITLE>";
                echo "<meta http-equiv=\"Content-Type\" content=\"text/html;charset=utf-8\" />";
				if(basename($_SERVER['PHP_SELF'])!='index.php')
					echo "<noscript><META http-equiv=REFRESH content='0;url=index.php?modfunc=logout&reason=javascript' /></noscript>";
				echo "<script language=\"JavaScript\" type=\"text/javascript\">";
				if(basename($_SERVER['PHP_SELF'])=='index.php')
					echo "
							if(parent.frames.length > 0)
							{
								parent.location.href = 'index.php?modfunc=logout';
							}";
				echo "
							var locked;
							function doOnload()
							{
								var layer;
								if(layer = document.getElementById('LOx_layer'))
								{
									var right,left = getLeft(1);
									layer.style.left = left;
									layer.style.top = 0;
									document.getElementById('LOx_table').left = left;

									for(i=2;column = document.getElementById('LOx'+i);i++)
									{
										right = getLeft(i);
										document.getElementById('LO_col'+(i-1)).width = right-left-14;
										left = right;
									}
									document.getElementById('LO_col'+(i-2)).width = left-getLeft(i-2)-10;
									total_width = getLeft(i-1)-getLeft(1) + 7;
									document.getElementById('LOx_table').width = total_width;
									if(navigator.userAgent.indexOf('Safari')==-1&&navigator.userAgent.indexOf('Microsoft Internet Explorer')==-1)
										window.setInterval('doOnscroll()',50);
									else
										window.onscroll = doOnscroll;
									window.onresize = doOnresize;
								}

								if(layer = document.getElementById('LOy_layer'))
								{
									var bottom,top = getTop(1);
									layer.style.left = getLeft(1);
									layer.style.top = top;
									document.getElementById('LOy_table').left = left;

									for(i=2;row = document.getElementById('LOy'+i);i++)
									{
										bottom = getTop(i);
										document.getElementById('LO_row'+(i-1)).height = bottom-top-2;
										top = bottom;
									}
									document.getElementById('LOy_table').width = getLeft(2)-getLeft(1);
									if(navigator.userAgent.indexOf('Safari')==-1&&navigator.userAgent.indexOf('Microsoft Internet Explorer')==-1)
										window.setInterval('doOnscroll()',50);
									else
										window.onscroll = doOnscroll;
									window.onresize = doOnresize;
								}
							}

							function doOnresize()
							{
								doOnload();
								doOnscroll();
							}

							function doOnscroll()
							{
								var layer;
								if(layer = document.getElementById(\"LOx_layer\"))
								{
									var pY = (window.document.body.scrollTop ? window.document.body.scrollTop : window.pageYOffset);
									if(pY > getListTop())
										layer.style.visibility = \"visible\";
									else if(pY < getListTop())
										layer.style.visibility = \"hidden\";
									if(pY)
										layer.style.top = (window.document.body.scrollTop ? window.document.body.scrollTop : window.pageYOffset);
								}

								if(layer = document.getElementById(\"LOy_layer\"))
								{
									var pX = (window.document.body.scrollLeft ? window.document.body.scrollLeft : window.pageXOffset);
									if(pX > 0)
										layer.style.visibility = \"visible\";
									else if(pX == 0)
										layer.style.visibility = \"hidden\";
									if(pX)
										layer.style.left = (window.document.body.scrollLeft ? window.document.body.scrollLeft : window.pageXOffset);
								}
							}

							function getLeft(column_number)
							{
								var x = 0;
								if(document.layers)
									x = document.layers['LOx'+column_number].pageX;
								else if(document.all || document.getElementById)
								{
									var cell = document.all ? document.all['LOx'+column_number] : document.getElementById('LOx'+column_number);
									while(cell)
									{
										x += cell.offsetLeft;
										cell = cell.offsetParent;
									}
								}
								return x-8;
							}

							function getTop(row_number)
							{
								var y = 0;
								if(document.layers)
									y = document.layers['LOy'+row_number].pageY;
								else if(document.all || document.getElementById)
								{
									var cell = document.all ? document.all['LOy'+row_number] : document.getElementById('LOy'+row_number);
									while(cell)
									{
										y += cell.offsetTop;
										cell = cell.offsetParent;
									}
								}
								return y-8;
							}

							function getListTop()
							{
								var y = 0;
								if(document.layers)
									y = document.layers['LOx1'].pageY;
								else if(document.all || document.getElementById)
								{
									var cell = document.all ? document.all['LOx1'] : document.getElementById('LOx1');
									while(cell)
									{
										y += cell.offsetTop;
										cell = cell.offsetParent;
									}
								}
								return y;
							}

							function putFocus()
							{
								if(document.forms.length > 0)
								{
									document.forms[0].elements[0].focus();
								}
							}

							function addHTML(html,id,replace)
							{
								if(locked!=false)
								{
									if(replace==true)
										document.getElementById(id).innerHTML = html;
									else
										document.getElementById(id).innerHTML = document.getElementById(id).innerHTML + html;
								}
							}

							function changeHTML(show,hide)
							{
								for(key in show)
									document.getElementById(key).innerHTML = document.getElementById(show[key]).innerHTML;
								for(i=0;i<hide.length;i++)
									document.getElementById(hide[i]).innerHTML = '';
							}

							function checkAll(form,value,name_like)
							{
								if(value==true)
									checked = true;
								else
									checked = false;

								for(i=0;i<form.elements.length;i++)
								{
									if(form.elements[i].type=='checkbox' && form.elements[i].name!='controller' && form.elements[i].name.substr(0,name_like.length)==name_like)
										form.elements[i].checked = checked;
								}
							}

							function switchMenu(id)
							{
								if(document.getElementById(id).style.display=='none')
								{
									document.getElementById(id).style.display = 'block';
									document.getElementById(id+'_arrow').src = 'assets/arrow_down.gif';
								}
								else
								{
									document.getElementById(id).style.display = 'none';
									document.getElementById(id+'_arrow').src = 'assets/arrow_right.gif';
								}
							}
                            
                            function setMLvalue(id,loc,value)
                            {
                                res = document.getElementById(id).value.split('|');
                                if(loc=='') {
                                    if (value == '') {
                                        alert('"._('The first translation string cannot be empty.')."');
                                        value = 'Something';
                                    }
                                    res[0] = value;
                                } else {
                                    found = 0;
                                    for (i=1;i<res.length;i++) {
                                        if (res[i].substring(0,loc.length) == loc) {
                                            found = 1;
                                            if (value == '') {
                                                for (j=i+1;j<res.length;j++)
                                                    res[j-1] = res[j];
                                                res.pop();
                                            } else {
                                                res[i] = loc+':'+value;
                                            }
                                        }
                                    }    
                                    if ((found == 0) && (value != '')) res.push(loc+':'+value);
                                }
                                document.getElementById(id).value = res.join('|');                                
                            }

                            function setAmount(sel,box)
                            {
                                var str=sel.options[sel.selectedIndex].text.match(/\(.*\)/);
                                if(str!=null)
                                    box.value=str[0].slice(2,-1).replace(',','').replace('.00','');
                            }

                            function toggleFlag(field_id,flag,icon_id,tips)
                            {
                                if (document.getElementById(field_id).value.indexOf(flag) < 0) {
                                    document.getElementById(icon_id).src = document.getElementById(icon_id).src.replace('-off.gif','-on.gif');
                                    document.getElementById(icon_id).title = tips[1];
                                    document.getElementById(field_id).value = document.getElementById(field_id).value + flag;
                                } else {
                                    document.getElementById(icon_id).src = document.getElementById(icon_id).src.replace('-on.gif','-off.gif');
                                    document.getElementById(icon_id).title = tips[0];
                                    document.getElementById(field_id).value = document.getElementById(field_id).value.replace(flag,'');
                                }
                            }

						</script>
					</HEAD>
					<link rel=stylesheet type=text/css href=assets/themes/".Preferences('THEME')."/stylesheet.css>";
echo '  <link rel="stylesheet" type="text/css" media="all" href="assets/jscalendar/calendar-blue2.css" />
  <script type="text/javascript" src="assets/jscalendar/calendar.js"></script>
  <script type="text/javascript" src="assets/jscalendar/lang/calendar-en.js"></script>
  <script type="text/javascript" src="assets/jscalendar/calendar-setup.js"></script>
  <script type="text/javascript" src="assets/tipmessage/main15.js"></script>';

			break;
			case "footer":
				echo '</TD></TR></TABLE>';

				for($i=1;$i<=$_CENTRE['PrepareDate'];$i++)
				{
					echo '<script type="text/javascript">
				Calendar.setup({
					monthField     :    "monthSelect'.$i.'",
					dayField       :    "daySelect'.$i.'",
					yearField      :    "yearSelect'.$i.'",
					ifFormat       :    "%d-%b-%y",
					button         :    "trigger'.$i.'",
					align          :    "Tl",
					singleClick    :    true
				});
			</script>';
				}
				echo '</BODY>';
				echo '</HTML>';
			break;
		}
	}
}
?>
