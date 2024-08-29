<?php

function PDFStop($handle)
{	global $htmldocPath,$htmldocAssetsPath;

	unset($_SESSION['orientation']);
	if($htmldocPath)
	{
		if($htmldocAssetsPath)
			$html = str_replace('assets/',$htmldocAssetsPath,ob_get_contents());
		else
			$html = ob_get_contents();
		ob_end_clean();

		// Gen temp file and save contents
		$temphtml = tempnam('','html');
		$temphtml_tmp = substr($temphtml, 0, strrpos($temphtml,'.')).'html';
		rename($temphtml_tmp, $temphtml);

		$fp = @fopen($temphtml,"w+");
		if (!$fp)
			die("Can't open $temphtml");
		fputs($fp,'<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd"><HTML xmlns="http://www.w3.org/1999/xhtml"><HEAD><TITLE></TITLE></HEAD><BODY>'.$html.'</BODY></HTML>');
		@fclose($fp);

		header("Cache-Control: public");
		header("Pragma: ");
		header("Content-Type: application/pdf");
		header("Content-Disposition: inline; filename=\"".str_replace('Print ','',ProgramTitle()).".pdf\"\n");

		passthru("$htmldocPath $handle[options] \"$temphtml\"");
		@unlink($temphtml);

    }
	else
	{
		$html = ob_get_contents();
		ob_end_clean();
		echo '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd"><HTML xmlns="http://www.w3.org/1999/xhtml"><HEAD><TITLE></TITLE></HEAD><BODY>'.$html.'</BODY></HTML>';
	}
}
?>