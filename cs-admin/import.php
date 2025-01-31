<?php
define('PMA_CHK_DROP', 1);
function PMA_checkTimeout()
{
    global $timestamp, $maximum_time, $timeout_passed;
    if ($maximum_time == 0) {
        return FALSE;
    } elseif ($timeout_passed) {
        return TRUE;
    
    } elseif ((time() - $timestamp) > ($maximum_time - 5)) {
        $timeout_passed = TRUE;
        return TRUE;
    } else {
        return FALSE;
    }
}


function PMA_detectCompression($filepath)
{
    $file = @fopen($filepath, 'rb');
    if (!$file) {
        return FALSE;
    }
    $test = fread($file, 4);
    $len = strlen($test);
    fclose($file);
    if ($len >= 2 && $test[0] == chr(31) && $test[1] == chr(139)) {
        return 'application/gzip';
    }
    if ($len >= 3 && substr($test, 0, 3) == 'BZh') {
        return 'application/bzip2';
    }
    if ($len >= 4 && $test == "PK\003\004") {
        return 'application/zip';
    }
    return 'none';
}




function PMA_importRunQuery($sql = '', $full = '', $controluser = false)
{
    global $import_run_buffer, $go_sql, $complete_query, $display_query,
        $sql_query, $my_die, $error, $reload,
        $skip_queries, $executed_queries, $max_sql_len, $read_multiply,
        $cfg, $sql_query_disabled, $db, $run_query, $is_superuser;
    $read_multiply = 1;
    if (isset($import_run_buffer)) {
       
        if ($skip_queries > 0) {
            $skip_queries--;
        } else {
            if (!empty($import_run_buffer['sql']) && 
trim($import_run_buffer['sql']) != '') {
                $max_sql_len = max($max_sql_len, 
strlen($import_run_buffer['sql']));
                if (!$sql_query_disabled) {
                    $sql_query .= $import_run_buffer['full'];
                }
                if (!$cfg['AllowUserDropDatabase']
                 && !$is_superuser
                 && preg_match('@^[[:space:]]*DROP[[:space:]]+(IF 
EXISTS[[:space:]]+)?DATABASE @i', $import_run_buffer['sql'])) {
                    $GLOBALS['message'] = 
PMA_Message::error('strNoDropDatabases');
                    $error = TRUE;
                } else {
                    $executed_queries++;
                    if ($run_query && $GLOBALS['finished'] && empty($sql) 
&& !$error && (
                            (!empty($import_run_buffer['sql']) && 
preg_match('/^[\s]*(SELECT|SHOW|HANDLER)/i', $import_run_buffer['sql'])) ||
                            ($executed_queries == 1)
                            )) {
                        $go_sql = TRUE;
                        if (!$sql_query_disabled) {
                            $complete_query = $sql_query;
                            $display_query = $sql_query;
                        } else {
                            $complete_query = '';
                            $display_query = '';
                        }
                        $sql_query = $import_run_buffer['sql'];
                    } elseif ($run_query) {
                        if ($controluser) {
                            $result = 
PMA_query_as_cu($import_run_buffer['sql']);
                        } else {
                            $result = 
PMA_DBI_try_query($import_run_buffer['sql']);
                        }
                        $msg = '# ';
                        if ($result === FALSE) { // execution failed
                            if (!isset($my_die)) {
                                $my_die = array();
                            }
                            $my_die[] = array('sql' => 
$import_run_buffer['full'], 'error' => PMA_DBI_getError());


                            if ($cfg['VerboseMultiSubmit']) {
                                $msg .= $GLOBALS['strError'];
                            }


                            if (!$cfg['IgnoreMultiSubmitErrors']) {
                                $error = TRUE;
                                return;
                            }
                        } elseif ($cfg['VerboseMultiSubmit']) {
                            $a_num_rows = (int)@PMA_DBI_num_rows($result);
                            $a_aff_rows = (int)@PMA_DBI_affected_rows();
                            if ($a_num_rows > 0) {
                                $msg .= $GLOBALS['strRows'] . ': ' . 
$a_num_rows;
                            } elseif ($a_aff_rows > 0) {
                                $msg .= 
sprintf($GLOBALS['strRowsAffected'], $a_aff_rows);
                            } else {
                                $msg .= $GLOBALS['strEmptyResultSet'];
                            }
                        }
                        if (!$sql_query_disabled) {
                            $sql_query .= $msg . "\n";
                        }


                       
                        if ($result != FALSE && 
preg_match('@^[\s]*USE[[:space:]]*([\S]+)@i', $import_run_buffer['sql'], 
$match)) {
                            $db = trim($match[1]);
                            $db = trim($db,';'); 
                            $reload = TRUE;
                        }


                        if ($result != FALSE && 
preg_match('@^[\s]*(DROP|CREATE)[\s]+(IF 
EXISTS[[:space:]]+)?(TABLE|DATABASE)[[:space:]]+(.+)@im', 
$import_run_buffer['sql'])) {
                            $reload = TRUE;
                        }
                    } 
                } 
            } 
            elseif (!empty($import_run_buffer['full'])) {
                if ($go_sql) {
                    $complete_query .= $import_run_buffer['full'];
                    $display_query .= $import_run_buffer['full'];
                } else {
                    if (!$sql_query_disabled) {
                        $sql_query .= $import_run_buffer['full'];
                    }
                }
            }
           
            if (! $go_sql && $run_query) {
                if ($cfg['VerboseMultiSubmit'] && ! empty($sql_query)) {
                    if (strlen($sql_query) > 50000 || $executed_queries > 
50 || $max_sql_len > 1000) {
                        $sql_query = '';
                        $sql_query_disabled = TRUE;
                    }
                } else {
                    if (strlen($sql_query) > 10000 || $executed_queries > 
10 || $max_sql_len > 500) {
                        $sql_query = '';
                        $sql_query_disabled = TRUE;
                    }
                }
            }
        } 
    } 


   
    if (!empty($sql) || !empty($full)) {
        $import_run_buffer = array('sql' => $sql, 'full' => $full);
    } else {
        unset($GLOBALS['import_run_buffer']);
    }
}






function PMA_importGetNextChunk($size = 32768)
{
    global $compression, $import_handle, $charset_conversion, 
$charset_of_file,
        $charset, $read_multiply;
 $compression="none"; 
     if ($read_multiply <= 8) {
        $size *= $read_multiply;
    } else {
        $size *= 8;
    }
    $read_multiply++;


   
    if ($size > $GLOBALS['read_limit']) {
        $size = $GLOBALS['read_limit'];
    }


    if (PMA_checkTimeout()) {
        return FALSE;
    }
    if ($GLOBALS['finished']) {
        return TRUE;
    }


    if ($GLOBALS['import_file'] == 'none') {
       
        if (strlen($GLOBALS['import_text']) < $size) {
            $GLOBALS['finished'] = TRUE;
            return $GLOBALS['import_text'];
        } else {
            $r = substr($GLOBALS['import_text'], 0, $size);
            $GLOBALS['offset'] += $size;
            $GLOBALS['import_text'] = substr($GLOBALS['import_text'], 
$size);
            return $r;
        }
    }


    switch ($compression) {
        case 'application/bzip2':
            $result = bzread($import_handle, $size);
            $GLOBALS['finished'] = feof($import_handle);
            break;
        case 'application/gzip':
            $result = gzread($import_handle, $size);
            $GLOBALS['finished'] = feof($import_handle);
            break;
        case 'application/zip':
            $result = substr($GLOBALS['import_text'], 0, $size);
            $GLOBALS['import_text'] = substr($GLOBALS['import_text'], 
$size);
            $GLOBALS['finished'] = empty($GLOBALS['import_text']);
            break;
        case 'none':
     $result = fread($import_handle, $size);
            $GLOBALS['finished'] = feof($import_handle);
            break;
    }
    $GLOBALS['offset'] += $size;


    if ($charset_conversion) {
        return PMA_convert_string($charset_of_file, $charset, $result);
    } else {
     
        if ($GLOBALS['offset'] == $size) {
            // UTF-8
            if (strncmp($result, "\xEF\xBB\xBF", 3) == 0) {
                $result = substr($result, 3);
            // UTF-16 BE, LE
            } elseif (strncmp($result, "\xFE\xFF", 2) == 0 || 
strncmp($result, "\xFF\xFE", 2) == 0) {
                $result = substr($result, 2);
            }
        }
        return $result;
    }
}
?>