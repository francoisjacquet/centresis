<?php
if($_CENTRE['modules_search'] && $extra['force_search'])
	$_REQUEST['search_modfunc'] = '';

if(Preferences('SEARCH')!='Y' && !$extra['force_search'])
	$_REQUEST['search_modfunc'] = 'list';
if($_REQUEST['search_modfunc']=='search_fnc' || !$_REQUEST['search_modfunc'])
{
	//if($_SESSION['student_id'] && User('PROFILE')!='parent' && User('PROFILE')!='student' && ($_REQUEST['modname']!='Students/Search.php' || $_REQUEST['student_id']=='new'))
	switch(User('PROFILE'))
	{
		case 'admin':
		case 'teacher':
			//if($_SESSION['student_id'] && ($_REQUEST['modname']!='Students/Search.php' || $_REQUEST['student_id']=='new'))
			if($_SESSION['address_id'] && $_REQUEST['address_id']=='new')
			{
				unset($_SESSION['address_id']);
				echo '<script language=JavaScript>parent.side.location="'.$_SESSION['Side_PHP_SELF'].'?modcat="+parent.side.document.forms[0].modcat.value;</script>';
			}

			$_SESSION['Search_PHP_SELF'] = PreparePHP_SELF($_SESSION['_REQUEST_vars'],array('bottom_back','advanced'));
			if($_SESSION['Back_PHP_SELF']!='address')
			{
				$_SESSION['Back_PHP_SELF'] = 'address';
				unset($_SESSION['List_PHP_SELF']);
			}
			echo '<script language=JavaScript>parent.help.location.reload();</script>';
			echo '<BR>';
			PopTable('header',$extra['search_title']?$extra['search_title']:_('Find a Family'));
			echo "<FORM name=search action=Modules.php?modname=$_REQUEST[modname]&modfunc=$_REQUEST[modfunc]&search_modfunc=list&next_modname=$_REQUEST[next_modname]&advanced=$_REQUEST[advanced]$extra[action] method=POST>";
			echo '<TABLE border=0>';

			echo '<TR valign=top><TD>';
			echo '<TABLE bgcolor=#f8f8f9 width=100% id=general_table>';
//            Search('general_info',$extra['grades']);
            echo '<TR><TD align=right width=120>'._('Last Name').'</TD><TD><input type=text name="last" size=30></TD></TR>';
            echo '<TR><TD align=right width=120>'._('First Name').'</TD><TD><input type=text name="first" size=30></TD></TR>';
            echo '<TR><TD align=right width=120>'._('Address').'</TD><TD><input type=text name="addr" size=30></TD></TR>';
            echo '<TR><TD align=right width=120>'._('City').'</TD><TD><input type=text name="city" size=30></TD></TR>';
            echo '<TR><TD align=right width=120>'._('Zipcode').'</TD><TD><input type=text name="zip" size=30></TD></TR>';
			if(!isset($extra))
				$extra = array();
//			Widgets('user',$extra);
			if($extra['search'])
				echo $extra['search'];
			if($extra['extra_search'])
				echo $extra['extra_search'];
//			Search('student_fields',is_array($extra['student_fields'])?$extra['student_fields']:array());
			echo '</TABLE>';
			echo '</TD><TD>';
			echo '<TABLE width=100%><TR><TD align=center><BR>';
			if($extra['search_second_col'])
				echo $extra['search_second_col'];
			echo '<BR>';
			echo Buttons(_('Submit'),_('Reset'));
			echo '</TD></TR>';
			echo '</TABLE>';
			if($extra['second_col'])
				echo '<BR><TABLE>'.$extra['second_col'].'</TABLE>';
			echo '</TD></TR>';

			echo '<TR valign=top><TD><TABLE cellpadding=0 cellspacing=0 width=100%>';
/*			if($_REQUEST['advanced']=='Y')
			{
				$extra['search'] = '';
				Widgets('all',$extra);
				echo '<TR><TD>';
				echo '<FONT COLOR='.Preferences('HEADER').'><B>'._('Widgets').'</B></FONT><BR>';
				echo $extra['search'];
				echo '</TD></TR>';

				echo '<TR><TD>';
				echo '<FONT COLOR='.Preferences('HEADER').'><B>'._('Student Fields').'</B></FONT><BR>';
				Search('student_fields_all',is_array($extra['student_fields'])?$extra['student_fields']:array());
				echo '</TD></TR>';
				echo '<TR><TD><BR><A href='.PreparePHP_SELF($_REQUEST,array(),array('advanced'=>'N')).'>'._('Basic Search').'</A></TD></TR>';
			}
			else
				echo '<TR><TD><BR><A href='.PreparePHP_SELF($_REQUEST,array(),array('advanced'=>'Y')).'>'._('Advanced Search').'</A></TD></TR>';
*/			echo '</TABLE></TD>';
			//if($extra['second_col'])
			//	echo '<TD>'.$extra['second_col'].'</TD>';
			echo '</TR>';

			echo '</TABLE>';
			echo '</FORM>';
			// set focus to last name text box
			echo '<script type="text/javascript"><!--
				document.search.last.focus();
				--></script>';
			PopTable('footer');
		break;

		case 'parent':
		case 'student':
			echo '<BR>';
			PopTable('header',_('Search'));
			echo "<FORM action=Modules.php?modname=$_REQUEST[modname]&modfunc=$_REQUEST[modfunc]&search_modfunc=list&next_modname=$_REQUEST[next_modname]".$extra['action']." method=POST>";
			echo '<TABLE border=0>';
			if($extra['search'])
				echo $extra['search'];
			echo '<TR><TD colspan=2 align=center>';
			echo '<BR>';
			echo Buttons(_('Submit'),_('Reset'));
			echo '</TD></TR>';
			echo '</TABLE>';
			echo '</FORM>';
			PopTable('footer');
		break;
	}
}
//if($_REQUEST['search_modfunc']=='list')
else
{
    if(!$_REQUEST['next_modname'])
        $_REQUEST['next_modname'] = $_REQUEST['modname'];

    if(User('PROFILE')=='admin')
    {
/*        if(!isset($extra))
            $extra = array();
        StaffWidgets('user',$extra);
        if($_REQUEST['advanced']=='Y')
            StaffWidgets('all',$extra);
*/    }

    if(!$extra['NoSearchTerms'])
    {
        if($_REQUEST['_search_all_schools']=='Y')
            $_CENTRE['SearchTerms'] .= '<font color=gray><b>'._('Search All Schools').'</b></font><BR>';
    }
    if($_REQUEST['addid'])
    {
        $extra['WHERE'] .= " AND sjp.ADDRESS_ID='".$_REQUEST['addid']."'";
        if(!$extra['NoSearchTerms'])
            $_CENTRE['SearchTerms'] .= '<font color=gray><b>'.Localize('colon',_('Address ID')).' </b></font>'.$_REQUEST['usrid'].'<BR>';
    }
    if($_REQUEST['last'])
    {
        $extra['FROM'] .= ",people p";
        $extra['WHERE'] .= " AND p.person_id=sjp.person_id AND UPPER(p.LAST_NAME) LIKE '".strtoupper($_REQUEST['last'])."%'";
        if(!$extra['NoSearchTerms'])
            $_CENTRE['SearchTerms'] .= '<font color=gray><b>'.Localize('colon',_('Last Name starts with')).' </b></font>'.$_REQUEST['last'].'<BR>';
    }
    if($_REQUEST['first'])
    {
        $extra['FROM'] .= ",people p";
        $extra['WHERE'] .= " AND p.person_id=sjp.person_id AND UPPER(p.FIRST_NAME) LIKE '".strtoupper($_REQUEST['first'])."%'";
        if(!$extra['NoSearchTerms'])
            $_CENTRE['SearchTerms'] .= '<font color=gray><b>'.Localize('colon',_('First Name starts with')).' </b></font>'.$_REQUEST['first'].'<BR>';
    }
    if($_REQUEST['addr'])
    {
        $extra['FROM'] .= ",address a";
        $extra['WHERE'] .= " AND a.address_id=sjp.address_id AND UPPER(a.address) LIKE '%".strtoupper($_REQUEST['addr'])."%'";
        if(!$extra['NoSearchTerms'])
            $_CENTRE['SearchTerms'] .= '<font color=gray><b>'.Localize('colon',_('Address contains')).' </b></font>'.$_REQUEST['addr'].'<BR>';
    }
    if($_REQUEST['city'])
    {
        $extra['FROM'] .= ",address a";
        $extra['WHERE'] .= " AND a.address_id=sjp.address_id AND UPPER(a.city) LIKE '".strtoupper($_REQUEST['city'])."%'";
        if(!$extra['NoSearchTerms'])
            $_CENTRE['SearchTerms'] .= '<font color=gray><b>'.Localize('colon',_('City')).' </b></font>'.$_REQUEST['city'].'<BR>';
    }
    if($_REQUEST['zip'])
    {
        $extra['FROM'] .= ",address a";
        $extra['WHERE'] .= " AND a.address_id=sjp.address_id AND a.zipcode LIKE '".strtoupper($_REQUEST['zip'])."%'";
        if(!$extra['NoSearchTerms'])
            $_CENTRE['SearchTerms'] .= '<font color=gray><b>'.Localize('colon',_('Zipcode')).' </b></font>'.$_REQUEST['zip'].'<BR>';
    }
    if($_REQUEST['profile'])
    {
        $extra['FROM'] .= ",people p";
        $extra['WHERE'] .= " AND p.person_id=sjp.person_id AND p.PROFILE='".$_REQUEST['profile']."'";
        if(!$extra['NoSearchTerms'])
            $_CENTRE['SearchTerms'] .= '<font color=gray><b>'.Localize('colon',_('Profile')).' </b></font>'.$_REQUEST['profile'].'<BR>';
    }
    if($_REQUEST['username'])
    {
        $extra['FROM'] .= ",people p";
        $extra['WHERE'] .= " AND p.person_id=sjp.person_id AND UPPER(p.USERNAME) LIKE '".strtoupper($_REQUEST['username'])."%'";
        if(!$extra['NoSearchTerms'])
            $_CENTRE['SearchTerms'] .= '<font color=gray><b>'.Localize('colon',_('UserName starts with')).' </b></font>'.$_REQUEST['username'].'<BR>';
    }

//    $extra['WHERE'] .= CustomFields('where','staff',array('NoSearchTerms'=>$extra['NoSearchTerms']));
    if(!isset($_CENTRE['DrawHeader'])) DrawHeader(_('Choose An Address'));
    $address_RET = GetAddressList($extra);

    $name_link['FAMILY_NAME']['link'] = "Modules.php?modname=$_REQUEST[next_modname]";
    $name_link['FAMILY_NAME']['variables'] = array('address_id'=>'ADDRESS_ID');
    if(is_array($extra['link']))
        $link = $extra['link'] + $name_link;
    else
        $link = $name_link;

    if(is_array($extra['columns']))
        $columns = $extra['columns'];
    else
        $columns = array('FAMILY_NAME'=>_('Family Name'),'COUNT_DEPENDANTS'=>_('# Dependants'));
    if(is_array($extra['columns_before']))
        $columns = $extra['columns_before'] + $columns;
    if(is_array($extra['columns_after']))
        $columns += $extra['columns_after'];

    if(count($address_RET)>1 || $link['add'] || !$link['FAMILY_NAME'] || $extra['columns_before'] || $extra['columns_after'] || ($extra['BackPrompt']==false && count($staff_RET)==0) || ($extra['Redirect']===false && count($address_RET)==1))
    {
        if($_REQUEST['expanded_view']!='true')
            DrawHeader("<A HREF=".PreparePHP_SELF($_REQUEST,array(),array('expanded_view'=>'true')) . ">"._('Expanded View')."</A>",$extra['header_right']);
        else
            DrawHeader("<A HREF=".PreparePHP_SELF($_REQUEST,array(),array('expanded_view'=>'false')) . ">"._('Original View')."</A>",$extra['header_right']);
        DrawHeader($extra['extra_header_left'],$extra['extra_header_right']);
        DrawHeader(str_replace('<BR>','<BR> &nbsp;',substr($_CENTRE['SearchTerms'],0,-4)));
        if(!$_REQUEST['LO_save'] && !$extra['suppress_save'])
        {
            $_SESSION['List_PHP_SELF'] = PreparePHP_SELF($_SESSION['_REQUEST_vars'],array('bottom_back'));
            if($_SESSION['Back_PHP_SELF']!='address')
            {
                $_SESSION['Back_PHP_SELF'] = 'address';
                unset($_SESSION['Search_PHP_SELF']);
            }
            echo '<script language=JavaScript>parent.help.location.reload();</script>';
        }
        ListOutput($address_RET,$columns,"Family","Families",$link,false,$extra['options']);
    }
    elseif(count($address_RET)==1)
    {
        if(count($link['FAMILY_NAME']['variables']))
        {
            foreach($link['FAMILY_NAME']['variables'] as $var=>$val)
                $_REQUEST[$var] = $address_RET['1'][$val];
        }
        if(!is_array($address_RET[1]['STAFF_ID']))
        {
            $_SESSION['address_id'] = $address_RET[1]['ADDRESS_ID'];
            echo '<script language=JavaScript>parent.side.location="'.$_SESSION['Side_PHP_SELF'].'?modcat="+parent.side.document.forms[0].modcat.value;</script>';
            unset($_REQUEST['search_modfunc']);
        }
        if($_REQUEST['modname']!=$_REQUEST['next_modname'])
        {
            $modname = $_REQUEST['next_modname'];
            if(strpos($modname,'?'))
                $modname = substr($_REQUEST['next_modname'],0,strpos($_REQUEST['next_modname'],'?'));
            if(strpos($modname,'&'))
                $modname = substr($_REQUEST['next_modname'],0,strpos($_REQUEST['next_modname'],'&'));
            if($_REQUEST['modname'])
                $_REQUEST['modname'] = $modname;
            include('modules/'.$modname);
        }
    }
    else
        BackPrompt(_('No Families were found.'));
}
?>
