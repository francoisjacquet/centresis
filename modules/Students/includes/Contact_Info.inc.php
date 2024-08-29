<?php
include_once('modules/Students/includes/functions.php');

$label = array('PHONE'=>array(_('Cell Phone'),_('Work Phone').' 1',_('Work Phone').' 2',_('Other Phone')),
               'EMAIL'=>array(_('Personal Email').' 1',_('Personal Email').' 2',_('Work Email'),_('Other Email')));
$help = array('emergency'=>array('off'=>'Receive all communications','on'=>'For emergency communications only'),
              'private'=>array('off'=>'Publish in School Directory','on'=>'Do not publish in School Directory'));

echo '<FIELDSET><LEGEND><FONT color=gray>'._('Phone Information').'</FONT></LEGEND>';
echo '<TABLE cellpadding=5 width=100%>';
for ($i=0;$i<4;$i++) {
    echo '<tr><td width=36 valign=top>'.FlagInput($this_contact['PHONE_'.($i+1).'_FLAGS'],'values[PEOPLE][PHONE_'.($i+1).'_FLAGS]','E','emergency',$help['emergency']);
    echo FlagInput($this_contact['PHONE_'.($i+1).'_FLAGS'],'values[PEOPLE][PHONE_'.($i+1).'_FLAGS]','P','private',$help['private']).'</td>';
    echo '<td>'.TextInput($this_contact['PHONE_'.($i+1)],'values[PEOPLE][PHONE_'.($i+1).']',$label['PHONE'][$i]).'</td></tr>';    
}
echo '</TABLE>';
echo '</FIELDSET>';

echo '<FIELDSET><LEGEND><FONT color=gray>'._('Email Information').'</FONT></LEGEND>';
echo '<TABLE cellpadding=5 width=100%>';
for ($i=0;$i<4;$i++) {
    echo '<tr><td width=36 valign=top>'.FlagInput($this_contact['EMAIL_'.($i+1).'_FLAGS'],'values[PEOPLE][EMAIL_'.($i+1).'_FLAGS]','E','emergency',$help['emergency']);
    echo FlagInput($this_contact['EMAIL_'.($i+1).'_FLAGS'],'values[PEOPLE][EMAIL_'.($i+1).'_FLAGS]','P','private',$help['private']).'</td>';
    echo '<td>'.TextInput($this_contact['EMAIL_'.($i+1)],'values[PEOPLE][EMAIL_'.($i+1).']',$label['EMAIL'][$i]).'</td></tr>';    
}
echo '</TABLE>';
echo '</FIELDSET>';
?>