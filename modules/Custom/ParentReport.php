<?php
DrawHeader(_(ProgramTitle()));

if(!$_REQUEST['modfunc'])
{
	if($_REQUEST['search_modfunc']=='list')
	{
		$people_fields_RET = DBGet(DBQuery("SELECT ID,TITLE,TYPE FROM PEOPLE_FIELDS"));

		foreach($people_fields_RET as $field)
			$extra['SELECT'] .= ",p.CUSTOM_".$field['ID']." AS PEOPLE_".$field['ID'];

		$extra['WHERE'] .= appendParentSQL('',array('NoSearchTerms'=>$extra['NoSearchTerms']));

		$LO_columns = array();

		$parents_RET = GetParList($extra);
		$LO_columns += array('FULL_NAME'=>_('Parent'),'PERSON_ID'=>_('Centre ID'));

		foreach($people_fields_RET as $field)
			$LO_columns += array('PEOPLE_'.$field['ID']=>$field['TITLE']); 

		DrawHeader($header_left);
		DrawHeader(str_replace('<BR>','<BR> &nbsp;',substr($_CENTRE['SearchTerms'],0,-4)));

		ListOutput($parents_RET,$LO_columns,'Parent','Parents',false);
	}
	else
	{
		$extra['new'] = true;
		Search('parent_id',$extra);
	}
}
?>
