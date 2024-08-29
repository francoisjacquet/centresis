<?php
// This file is responsible for schema updates to the database
// It reads the schema version number from the Centre config
// and does any necessary updates and consistency checks as needed

$version = ConfigRead('core.dbversion');
if ($version === false)
    die('You Centre database is not versionned. Please call Centre technical support.');

if ($version == 1) {
    DBGet(DBQuery("
        ALTER TABLE custom_fields ADD COLUMN \"table\" character varying(25);
        UPDATE custom_fields SET \"table\"='students';
        ALTER TABLE custom_fields DROP CONSTRAINT custom_fields_pkey;
        ALTER TABLE custom_fields ADD CONSTRAINT custom_fields_pkey PRIMARY KEY (\"table\", id);"));
    $version = ConfigWrite('core.dbversion',$version+1);
}
if ($version == 2) {
    DBGet(DBQuery("
        ALTER TABLE people ADD COLUMN username character varying(100);
        ALTER TABLE people ADD COLUMN \"password\" character varying(100);
        ALTER TABLE people ADD COLUMN last_login timestamp(0) without time zone;
        ALTER TABLE people ADD COLUMN failed_login numeric;
        ALTER TABLE people ADD COLUMN profile_id numeric"));
    // Fill out usernames/password from staff table
    $login_RET = DBGet(DBQuery("SELECT DISTINCT st.syear,st.staff_id,p.person_id,st.username,st.password,st.last_login,st.profile_id
        FROM staff st, students_join_users sju,
             people p, students_join_people sjp,
             user_profiles up
        WHERE sju.staff_id=st.staff_id AND sjp.person_id=p.person_id
            AND sju.student_id=sjp.student_id AND sjp.custody='Y'
            AND st.first_name=p.first_name AND st.last_name=p.last_name
            AND up.id=st.profile_id AND up.profile='parent'
        ORDER BY st.syear"));
    foreach ($login_RET as $login) {
        DBGet(DBQuery("UPDATE people SET username='$login[USERNAME]',\"password\"='".DBEscapeString($login[PASSWORD])."',profile_id='$login[PROFILE_ID]',last_login='$login[LAST_LOGIN]' WHERE person_id='$login[PERSON_ID]'"));
        DBGet(DBQuery("UPDATE staff SET username='$login[USERNAME]' WHERE staff_id='$login[STAFF_ID]'"));
    } 
    $version = ConfigWrite('core.dbversion',$version+1);
}
if ($version == 3) {
    for($i=0;$i<4;$i++) { 
        DBGet(DBQuery("
            ALTER TABLE people ADD COLUMN phone_".($i+1)." character varying(100);
            ALTER TABLE people ADD COLUMN phone_".($i+1)."_flags character varying(10);
            ALTER TABLE people ADD COLUMN email_".($i+1)." character varying(100);
            ALTER TABLE people ADD COLUMN email_".($i+1)."_flags character varying(10);
            "));
    }
    $version = ConfigWrite('core.dbversion',$version+1);
}
if ($version == 4) {
    DBGet(DBQuery("
          ALTER TABLE custom_fields ADD COLUMN description character varying(10000);
          ALTER TABLE staff_fields ADD COLUMN description character varying(10000);
          ALTER TABLE people_fields ADD COLUMN description character varying(10000);
          ALTER TABLE address_fields ADD COLUMN description character varying(10000);
          "));
    $version = ConfigWrite('core.dbversion',$version+1);
}
            
// Centre configuration read/write functions
function ConfigRead($key, $syear='') {
    $RET = DBGet(DBQuery("SELECT LOGIN FROM config WHERE TITLE='$key'".(!empty($syear)?" AND SYEAR=$syear":"")));
    if (empty($RET[1]))
        return false;
    else
        return $RET[1]['LOGIN'];
}

function ConfigWrite($key, $value, $syear='') {
    if (ConfigRead($key,$syear) !== false)
        $RET = DBGet(DBQuery("UPDATE config SET LOGIN='$value' WHERE TITLE='$key'".(!empty($syear)?" AND SYEAR=$syear":"")));
    else
        $RET = DBGet(DBQuery("INSERT INTO config VALUES ('$key','$value','$syear')"));
    return $value;
}
?>
