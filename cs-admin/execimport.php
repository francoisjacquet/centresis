<?php
$conn = mysql_connect($DatabaseServer, $DatabaseUsername, $DatabasePassword);
mysql_select_db($DatabaseName);

include("import.php");

$timeout_passed = FALSE;
$error = FALSE;
$read_multiply = 1;
$finished = FALSE;
$offset = 0;
$max_sql_len = 0;
$file_to_unlink = '';
$sql_query = '';
$sql_query_disabled = FALSE;
$go_sql = FALSE;
$executed_queries = 0;
$run_query = TRUE;
$charset_conversion = FALSE;
$reset_charset = FALSE;
$bookmark_created = FALSE;

$import_file='centre-db.sql';

$import_handle = @fopen($import_file, 'r');


$memory_limit = trim(@ini_get('memory_limit'));

if (empty($memory_limit)) {
    $memory_limit = 2 * 1024 * 1024;
}

if ($memory_limit == -1) {
    $memory_limit = 10 * 1024 * 1024;
}


if (strtolower(substr($memory_limit, -1)) == 'm') {
    $memory_limit = (int)substr($memory_limit, 0, -1) * 1024 * 1024;
} elseif (strtolower(substr($memory_limit, -1)) == 'k') {
    $memory_limit = (int)substr($memory_limit, 0, -1) * 1024;
} elseif (strtolower(substr($memory_limit, -1)) == 'g') {
    $memory_limit = (int)substr($memory_limit, 0, -1) * 1024 * 1024 * 1024;
} else {
    $memory_limit = (int)$memory_limit;
}

$read_limit = $memory_limit / 8; 


$buffer = '';

$sql = '';
$start_pos = 0;
$i = 0;
$len= 0;
$big_value = 2147483647;

$sql_delimiter = ';';


$GLOBALS['finished'] = false;

while (!($GLOBALS['finished'] && $i >= $len) && !$error && 
!$timeout_passed) {
   
    $data = PMA_importGetNextChunk();

if ($data === FALSE) {
       
        $offset -= strlen($buffer);
        break;
    } elseif ($data === TRUE) {
       
    } else {
       
        $buffer .= $data;
       
        unset($data);
      
        if ((strpos($buffer, $sql_delimiter, $i) === FALSE) && 
!$GLOBALS['finished'])  {
            continue;
        }
    }
   
    $len = strlen($buffer);

   
    while ($i < $len) {
        $found_delimiter = false;
       
        $old_i = $i;
        
        if (preg_match('/(\'|"|#|-- |\/\*|`|(?i)DELIMITER)/', $buffer, 
$matches, PREG_OFFSET_CAPTURE, $i)) {
            
            $first_position = $matches[1][1];
        } else {
            $first_position = $big_value;
        }
        
      
        $first_sql_delimiter = strpos($buffer, $sql_delimiter, $i);
        if ($first_sql_delimiter === FALSE) {
            $first_sql_delimiter = $big_value;
        } else {
            $found_delimiter = true;
        }

     
        $i = min($first_position, $first_sql_delimiter);

        if ($i == $big_value) {
         

            $i = $old_i;
            if (!$GLOBALS['finished']) {
                break;
            }
            
            if (trim($buffer) == '') {
                $buffer = '';
                $len = 0;
                break;
            }
          
            $i = strlen($buffer) - 1;
        }

       
        $ch = $buffer[$i];

      
        if (strpos('\'"`', $ch) !== FALSE) {
            $quote = $ch;
            $endq = FALSE;
            while (!$endq) {
                
                $pos = strpos($buffer, $quote, $i + 1);
               
                if ($pos === FALSE) {
                 
                    if ($GLOBALS['finished']) {
                        $endq = TRUE;
                        $i = $len - 1;
                    }
                    $found_delimiter = false;
                    break;
                }
            
                $j = $pos - 1;
                while ($buffer[$j] == '\\') $j--;
               
                $endq = (((($pos - 1) - $j) % 2) == 0);
               
                $i = $pos;

                if ($first_sql_delimiter < $pos) {
                    $found_delimiter = false;
                }
            }
            if (!$endq) {
                break;
            }
            $i++;
            
            if ($GLOBALS['finished'] && $i == $len) {
                $i--;
            } else {
                continue;
            }
        }

        
        if ((($i == ($len - 1) && ($ch == '-' || $ch == '/'))
          || ($i == ($len - 2) && (($ch == '-' && $buffer[$i + 1] == '-')
            || ($ch == '/' && $buffer[$i + 1] == '*')))) && 
!$GLOBALS['finished']) {
            break;
        }

       
        if ($ch == '#'
         || ($i < ($len - 1) && $ch == '-' && $buffer[$i + 1] == '-'
          && (($i < ($len - 2) && $buffer[$i + 2] <= ' ')
           || ($i == ($len - 1)  && $GLOBALS['finished'])))
         || ($i < ($len - 1) && $ch == '/' && $buffer[$i + 1] == '*')
                ) {
                       if ($start_pos != $i) {
                $sql .= substr($buffer, $start_pos, $i - $start_pos);
            }
           
            $j = $i;
            $i = strpos($buffer, $ch == '/' ? '*/' : "\n", $i);
         
            if ($i === FALSE) {
                if ($GLOBALS['finished']) {
                    $i = $len - 1;
                } else {
                    break;
                }
            }
          
            if ($ch == '/') {
               
                if ($buffer[$j + 2] == '!') {
                    $comment = substr($buffer, $j + 3, $i - $j - 3);
                    
                }
                $i++;
            }
           
            $i++;
          
            $start_pos = $i;
           
            if ($i == $len) {
                $i--;
            } else {
                continue;
            }
        }
       
        if (strtoupper(substr($buffer, $i, 9)) == "DELIMITER"
         && ($buffer[$i + 9] <= ' ')
         && ($i < $len - 11)
         && strpos($buffer, "\n", $i + 11) !== FALSE) {
           $new_line_pos = strpos($buffer, "\n", $i + 10);
           $sql_delimiter = substr($buffer, $i + 10, $new_line_pos - $i - 
10);
           $i = $new_line_pos + 1;
        
           $start_pos = $i;
           continue;
        }

       
        if ($found_delimiter || ($GLOBALS['finished'] && ($i == $len - 1))) 
{
            $tmp_sql = $sql;
            if ($start_pos < $len) {
                $length_to_grab = $i - $start_pos;

                if (! $found_delimiter) {
                    $length_to_grab++;
                }
                $tmp_sql .= substr($buffer, $start_pos, $length_to_grab);
                unset($length_to_grab);
            }
$error=0;
         
            if (! preg_match('/^([\s]*;)*$/', trim($tmp_sql))) {
                $sql = $tmp_sql;
if (!$result = mysql_query($sql))
 {
 $dbErr = true;
 }
                $buffer = substr($buffer, $i + strlen($sql_delimiter));
                
                $len = strlen($buffer);
                $sql = '';
                $i = 0;
                $start_pos = 0;
               
                if ((strpos($buffer, $sql_delimiter) === FALSE) && 
!$GLOBALS['finished']) {
                    break;
                }
            } else {
                $i++;
                $start_pos = $i;
            }
        }
    } 
 } 
 ?>