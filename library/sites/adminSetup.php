<?php
/*
    feindura - Flat File Content Management System
    Copyright (C) 2009 Fabian Vogelsteller [frozeman.de]

    This program is free software;
    you can redistribute it and/or modify it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 3 of the License, or (at your option) any later version.

    This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY;
    without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
    See the GNU General Public License for more details.

    You should have received a copy of the GNU General Public License along with this program;
    if not,see <http://www.gnu.org/licenses/>.

* adminSetup.php version 2.23
*/

//error_reporting(E_ALL);

include_once(dirname(__FILE__)."/../backend.include.php");

// VARs
// ---------------------------------------------------------------------------
$documentSaved = false;
$errorWindow = false;
$savedForm = false;

// ----------------------------------------------------------------------------------------------------------------------------------------
// **--** SAVE PROCESS --------------------------------------------------------------------------------------------------------------------
// ----------------------------------------------------------------------------------------------------------------------------------------


// ****** ---------- EINSTELLUNEGN SPEICHERN in config/adminConfig.php
if($_POST['send'] && isset($_POST['adminConfig'])) {
  
  // ** ensure the the post vars with a 'Path' in the key value ending with a '/'
  foreach($_POST as $postKey => $post) {

    if(strstr($postKey,'Path'))
      if(!empty($post) && substr($post,-1) !== '/')
        $_POST[$postKey] = $post.'/';
  }
  
  // ** adds a "/" on the beginning of all absolute paths
  if(!empty($_POST['cfg_uploadPath']) && substr($_POST['cfg_uploadPath'],0,1) !== '/')
        $_POST['cfg_uploadPath'] = '/'.$_POST['cfg_uploadPath'];  
  if(!empty($_POST['cfg_langPath']) && substr($_POST['cfg_langPath'],0,1) !== '/')
        $_POST['cfg_langPath'] = '/'.$_POST['cfg_langPath'];        
  if(!empty($_POST['cfg_stylesheetPath']) && substr($_POST['cfg_stylesheetPath'],0,1) !== '/')
        $_POST['cfg_stylesheetPath'] = '/'.$_POST['cfg_stylesheetPath'];
        
  if(!empty($_POST['cfg_editorStyleFile']) && substr($_POST['cfg_editorStyleFile'],0,1) !== '/')
        $_POST['cfg_editorStyleFile'] = '/'.$_POST['cfg_editorStyleFile'];
  
  // ** removes a "/" on the beginning of all relative paths
  if(!empty($_POST['cfg_savePath']) && substr($_POST['cfg_savePath'],0,1) == '/')
        $_POST['cfg_savePath'] = substr($_POST['cfg_savePath'],1);
  if(!empty($_POST['cfg_thumbPath']) && substr($_POST['cfg_thumbPath'],0,1) == '/')
        $_POST['cfg_thumbPath'] = substr($_POST['cfg_thumbPath'],1);
  
  // ->> SPEAKING URL .htaccess
  // --------------------------
    $speakingUrlCode = '<IfModule mod_rewrite.c>
RewriteEngine On
RewriteBase /
# rewrite "/page/*.html" and "/category/*/*.html"
# and also passes the session var
RewriteRule ^category/([^/]+)/(.*)\.html?$ index.php?category=$1&page=$2$3 [QSA,L]
RewriteRule ^pages/(.*)\.html?$ index.php?page=$1$2 [QSA,L]
</IfModule>';
    
    $htaccessFile = DOCUMENTROOT.'/.htaccess';
  
  // ** looks if the MOD_REWRITE modul exists
  if(!in_array('mod_rewrite',apache_get_modules()))
    $_POST['cfg_speakingUrl'] = '';
  // ** ->> looks for a .htacces file with the speaking url mod_rewrite
  elseif(in_array('mod_rewrite',apache_get_modules()) && $_POST['cfg_speakingUrl'] == 'true') {

    // -> looks if the existing .htaccess file has the SPEAKING URL code
    if(file_exists($htaccessFile)) {

      if(strstr(file_get_contents($htaccessFile),$speakingUrlCode) === false) {
        if($htaccess = @fopen($htaccessFile,"a")) {
          flock($htaccess,2); // LOCK_EX
          fwrite($htaccess,"\n".$speakingUrlCode);
          flock($htaccess,3); //LOCK_UN
          fclose($htaccess);
        } else {
          $_POST['cfg_speakingUrl'] = '';
          $errorWindow = $langFile['adminSetup_fmsSettings_speakingUrl_error_save'];
        }
      }
    // -> creates a NEW .htaccess file
    } else {
      if($htaccess = @fopen($htaccessFile,"w")) {
        flock($htaccess,2); // LOCK_EX
        fwrite($htaccess,$speakingUrlCode);
        flock($htaccess,3); //LOCK_UN
        fclose($htaccess);
      } else {
        $_POST['cfg_speakingUrl'] = '';
        $errorWindow = $langFile['adminSetup_fmsSettings_speakingUrl_error_save'];
      }
    }
    
  // ->> deletes the SPEAKING URL code if SPEAKING URL turned OFF
  } elseif($_POST['cfg_speakingUrl'] == '') {
    
    if(file_exists($htaccessFile)) {
      $currrentHtaccess = file_get_contents($htaccessFile);
      // if ONLY the SPEAKING URL code is in the .htaccess then DELTE the .htaccess file
      if($currrentHtaccess == $speakingUrlCode ||
         $currrentHtaccess == "\n".$speakingUrlCode ||
         $currrentHtaccess == "\n\n".$speakingUrlCode) {
        @unlink($htaccessFile);
           
      // looks if SPEAKING URL code EXISTs in the .htaccess file
      } elseif(strstr($currrentHtaccess,$speakingUrlCode)) {
        $newHtaccess = str_replace($speakingUrlCode,'',$currrentHtaccess);
        $newHtaccess = preg_replace("/ +/", ' ', $newHtaccess);
        $newHtaccess = preg_replace("/\n+/", "\n", $newHtaccess);
        
        if($htaccess = @fopen($htaccessFile,"w")) {
          flock($htaccess,2); // LOCK_EX
          fwrite($htaccess,$newHtaccess);
          flock($htaccess,3); //LOCK_UN
          fclose($htaccess);
        } else {
          $errorWindow = $langFile['adminSetup_fmsSettings_speakingUrl_error_save'];
        }
      }
    }
  }
  
  // checks if the varNames are empty, and add the last ones, if speaking url = true
  if($_POST['cfg_speakingUrl'] == 'true') {
    if(empty($_POST['cfg_varNamePage']))
      $_POST['cfg_varNamePage'] = $adminConfig['varName']['page'];
    if(empty($_POST['cfg_varNameCategory']))
      $_POST['cfg_varNameCategory'] = $adminConfig['varName']['category'];
    if(empty($_POST['cfg_varNameModul']))
      $_POST['cfg_varNameModul'] = $adminConfig['varName']['modul'];
  } else {
    if(empty($_POST['cfg_varNamePage']))
      $_POST['cfg_varNamePage'] = 'page';
    if(empty($_POST['cfg_varNameCategory']))
      $_POST['cfg_varNameCategory'] = 'category';
    if(empty($_POST['cfg_varNameModul']))
      $_POST['cfg_varNameModul'] = 'modul';
  }
  
  // **** opens adminConfig.php for writing
  if($file = @fopen("config/adminConfig.php","w")) {
    flock($file,2); // LOCK_EX
    fwrite($file,PHPSTARTTAG); //< ?php
    
    fwrite($file,"\$adminConfig['url'] =             '".$_SERVER["HTTP_HOST"]."';\n");
    fwrite($file,"\$adminConfig['basePath'] =        '".dirname($_SERVER['PHP_SELF']).'/'."';\n");
    fwrite($file,"\$adminConfig['savePath'] =        '".$_POST['cfg_savePath']."';\n");
    fwrite($file,"\$adminConfig['uploadPath'] =      '".$_POST['cfg_uploadPath']."';\n");  
    fwrite($file,"\$adminConfig['langPath'] =        '".$_POST['cfg_langPath']."';\n");
    fwrite($file,"\$adminConfig['stylesheetPath'] =  '".$_POST['cfg_stylesheetPath']."';\n");    
    fwrite($file,"\$adminConfig['dateFormat'] =      '".$_POST['cfg_dateFormat']."';\n");
    fwrite($file,"\$adminConfig['speakingUrl'] =      '".$_POST['cfg_speakingUrl']."';\n\n");
    
    fwrite($file,"\$adminConfig['varName']['page'] =     '".$_POST['cfg_varNamePage']."';\n");  
    fwrite($file,"\$adminConfig['varName']['category'] = '".$_POST['cfg_varNameCategory']."';\n");  
    fwrite($file,"\$adminConfig['varName']['modul'] =    '".$_POST['cfg_varNameModul']."';\n\n");  
    
    fwrite($file,"\$adminConfig['user']['editLanguage'] =    '".$_POST['cfg_userLanguage']."';\n");
    fwrite($file,"\$adminConfig['user']['editStylesheet'] =  '".$_POST['cfg_userStylesheet']."';\n");  
    fwrite($file,"\$adminConfig['user']['info'] =        '".nl2br(stripslashes(htmlentities($_POST['cfg_userInfo'],ENT_QUOTES, 'UTF-8')))."';\n\n");
    
    fwrite($file,"\$adminConfig['setStartPage'] =            '".$_POST['cfg_setStartPage']."';\n");
    fwrite($file,"\$adminConfig['page']['createPages'] =     '".$_POST['cfg_pageCreatePages']."';\n");
    fwrite($file,"\$adminConfig['page']['thumbnailUpload'] = '".$_POST['cfg_pageThumbnailUpload']."';\n\n");
    
    fwrite($file,"\$adminConfig['editor']['enterMode'] =   '".$_POST['cfg_editorEnterMode']."';\n");
    fwrite($file,"\$adminConfig['editor']['styleFile'] =   '".$_POST['cfg_editorStyleFile']."';\n");
    fwrite($file,"\$adminConfig['editor']['styleId'] =     '".$_POST['cfg_editorStyleId']."';\n");  
    fwrite($file,"\$adminConfig['editor']['styleClass'] =  '".$_POST['cfg_editorStyleClass']."';\n\n");  
  
    fwrite($file,"\$adminConfig['pageThumbnail']['width'] =      '".$_POST['cfg_thumbWidth']."';\n");
    fwrite($file,"\$adminConfig['pageThumbnail']['height'] =     '".$_POST['cfg_thumbHeight']."';\n");
    fwrite($file,"\$adminConfig['pageThumbnail']['path'] =       'images/".$_POST['cfg_thumbPath']."';\n\n");
    
    fwrite($file,"return \$adminConfig;");
       
    fwrite($file,PHPENDTAG); //? >
    flock($file,3); //LOCK_UN
    fclose($file);
    
    // give documentSaved status
    $documentSaved = true;
    saveTaskLog($langFile['log_adminSetup_saved']); // <- SAVE the task in a LOG FILE
  } else {
    $errorWindow = $langFile['adminSetup_fmsSettings_error_save'];
  }
  
  $savedForm = $_POST['savedBlock'];

}

// ---------- Speichern in fckstyles.xml
if($_POST['saveFckStyleFile']) {

  $fckstylewrite = $_POST['fckStyleFile'];
  
  $fckstylewrite 	= str_replace('\"', '"', $fckstylewrite);
  
  if($file = fopen("config/htmlEditorStyles.xml","w")) {
    flock($file,2);
    fwrite($file,$fckstylewrite);
    flock($file,3);
    fclose($file);
  
    // give documentSaved status
    $documentSaved = true;
    saveTaskLog($langFile['log_adminSetup_ckstyles']); // <- SAVE the task in a LOG FILE
  } else {
    $errorWindow = $langFile['adminSetup_styleFileSettings_error_save'];
  }
  
  $savedForm = 'fckStyleFile';
}

// ---------- SAVE the editFiles
if(isset($_POST['saveEditedFiles'])) {

  if(saveEditedFiles($_POST)) {
    $documentSaved = true; // give documentSaved status
    saveTaskLog($langFile['log_file_saved'],$_POST['file']); // <- SAVE the task in a LOG FILE
  } else
    $errorWindow = $langFile['editFilesSettings_error_save'];
  
  // sets which block should be opend after saving
  $savedForm = $_POST['savedBlock'];
}


include (dirname(__FILE__)."/../../config/adminConfig.php"); // loads the saved settings again
include (dirname(__FILE__)."/../../config/categoryConfig.php"); // loads the saved categories again

// ------------------------------- ENDE DES SCRIPTs ZUM SPEICHERN DER VARIABLEN ----------------------------------


// didnt show the Setup for non-adminstrators
if(isAdmin()) {

// show basePath warning if necessary
basePathWarning();

?>
<!-- documentSaved -->
<div id="documentSaved"<?php if($documentSaved) echo ' class="saved"'; ?>></div>

<!-- errorWindow -->
<?php if($errorWindow !== false) { ?>
<div id="errorWindow">
  <div class="top"><?php echo $langFile['form_errorWindow_h1'];?></div>
  <div class="content warning">
    <p><?php echo $errorWindow; ?></p>
    <a href="?site=<?php echo $_GET['site'] ?>" onclick="$('errorWindow').fade('out');return false;" class="ok"></a>
  </div>
  <div class="bottom"></div>
</div>
<?php }


// CHECKs THE IF THE NECESSARY FILEs ARE WRITEABLE, otherwise throw an error
// ----------------------------------------------------------------------------------------
$unwriteableList = '';

// check config files
$unwriteableList .= fileFolderIsWritableWarning($adminConfig['basePath'].'config/');
if(file_exists(DOCUMENTROOT.$adminConfig['basePath'].'config/adminConfig.php'))
  $unwriteableList .= fileFolderIsWritableWarning($adminConfig['basePath'].'/adminConfig.php');
if(file_exists(DOCUMENTROOT.$adminConfig['basePath'].'config/categoryConfig.php'))
  $unwriteableList .= fileFolderIsWritableWarning($adminConfig['basePath'].'config/categoryConfig.php');
if(file_exists(DOCUMENTROOT.$adminConfig['basePath'].'config/websiteConfig.php'))
  $unwriteableList .= fileFolderIsWritableWarning($adminConfig['basePath'].'config/websiteConfig.php');
$unwriteableList .= fileFolderIsWritableWarning($adminConfig['basePath'].'config/htmlEditorStyles.xml');

$unwriteableList .= fileFolderIsWritableWarning($adminConfig['basePath'].'statistic/');
if(file_exists(DOCUMENTROOT.$adminConfig['basePath'].'statistic/log_tasks.txt'))
  $unwriteableList .= fileFolderIsWritableWarning($adminConfig['basePath'].'statistic/log_tasks.txt');
if(file_exists(DOCUMENTROOT.$adminConfig['basePath'].'statistic/log_referers.txt'))
  $unwriteableList .= fileFolderIsWritableWarning($adminConfig['basePath'].'statistic/log_referers.txt');

// check lang folder/files      
$unwriteableList .= fileFolderIsWritableWarning($adminConfig['langPath']);
$langDir = @opendir(dirname(__FILE__).'/../../../'.$adminConfig['langPath']);
while (false !== ($file = @readdir($langDir))) {
if($file!="." && $file!="..") {
    if(is_file(dirname(__FILE__).'/../../../'.$adminConfig['langPath']."/".$file)){
      $unwriteableList .= fileFolderIsWritableWarning($adminConfig['langPath'].$file);
    }
  }
}
@closedir($langDir);

// check page folder/files
$unwriteableList .= fileFolderIsWritableWarning($adminConfig['basePath'].$adminConfig['savePath']);

foreach($categories as $category) {
  $unwriteableList .= fileFolderIsWritableWarning($adminConfig['basePath'].$adminConfig['savePath'].$category['id'].'/');
  $category = loadPages($category['id'],false);
  
  if(is_array($category)) { // prevent error, if the category is empty
    foreach($category as $page) {
      $unwriteableList .= fileFolderIsWritableWarning($adminConfig['basePath'].$adminConfig['savePath'].$page[1].'/'.$page[0].'.php');
    }
  }
}

// gives the error OUTPUT if one of these files in unwriteable
if($unwriteableList && checkBasePath()) {
  echo '<div class="block warning">
    <h1>'.$langFile['adminSetup_writeAccess'].'</h1>
    <div class="content">
      <p>'.$unwriteableList.'</p><!-- needs <p> tags for margin-left:..-->
    </div>
    <div class="bottom"></div>  
  </div>'; 
  
  echo '<div class="blockSpacer"></div>';
}
// ------------------------------------------------------------------------------------------- end WRITEABLE CHECK

?>


<form action="?site=adminSetup#top" method="post" enctype="multipart/form-data" accept-charset="ISO-8859-1,ISO-8859-2,ISO-8859-15,UTF-8">
  <div>
  <input type="hidden" name="send" value="true" />
  <input type="hidden" name="savedBlock" id="savedBlock" value="" />
  </div>
  
<!-- FMS SETTINGS -->
<?php
// shows the block below if it is the ones which is saved before
if($savedForm != 'fmsSettings')  $hidden = ' hidden';
else $hidden = '';  
?>
<div class="block<?php echo $hidden; ?>">
  <h1><a href="#" id="fmsSettings" name="fmsSettings"><?php echo $langFile['adminSetup_fmsSettings_h1']; ?></a></h1>
  <div class="content">
    <table>
     
      <colgroup>
      <col class="left" />
      </colgroup>
  
      <tr><td class="leftTop"></td><td></td></tr>
      
      <tr><td class="left">
      <label for="cfg_url"><span class="toolTip mark" title="<?php echo $langFile['adminSetup_fmsSettings_feld1'].'::'.$langFile['adminSetup_fmsSettings_feld1_tip'] ?>">
      <?php echo $langFile['adminSetup_fmsSettings_feld1'] ?></span></label>
      </td><td class="right">
      <input id="cfg_url" name="cfg_url"<?php if($adminConfig['url'] != $_SERVER["HTTP_HOST"]) echo ' style="color:#C5451F;" value="'.$langFile['adminSetup_fmsSettings_feld1_inputWarningText'].'"'; else echo ' value="'.$adminConfig['url'].'"'; ?> readonly="readonly" class="toolTip" title="<?php echo $langFile['adminSetup_fmsSettings_feld1_inputTip']; ?>" />
      </td></tr>
      
      <tr><td class="left">
      <label for="cfg_basePath"><span class="toolTip mark" title="<?php echo $langFile['adminSetup_fmsSettings_feld2'].'::'.$langFile['adminSetup_fmsSettings_feld2_tip'] ?>">
      <?php echo $langFile['adminSetup_fmsSettings_feld2'] ?></span></label>
      </td><td class="right">
      <input id="cfg_basePath" name="cfg_basePath"<?php if($adminConfig['basePath'] != dirname($_SERVER['PHP_SELF']).'/') echo ' style="color:#C5451F;" value="'.$langFile['adminSetup_fmsSettings_feld2_inputWarningText'].'"'; else echo ' value="'.$adminConfig['basePath'].'"'; ?> readonly="readonly" class="toolTip" title="<?php echo $langFile['adminSetup_fmsSettings_feld2_inputTip']; ?>" />
      </td></tr>
      
      <tr><td class="spacer"></td><td></td></tr>
      
      <tr><td class="left">
      <label for="cfg_savePath"><span class="toolTip mark" title="<?php echo $langFile['adminSetup_fmsSettings_feld3'].'::'.$langFile['adminSetup_fmsSettings_feld3_tip'] ?>">
      <?php echo $langFile['adminSetup_fmsSettings_feld3'] ?></span></label>
      </td><td class="right">
      <input size="40" id="cfg_savePath" name="cfg_savePath" value="<?php echo $adminConfig['savePath']; ?>" class="toolTip" title="<?php echo $langFile['adminSetup_fmsSettings_feld3_inputTip']; ?>" />
      <span class="hint"><?php echo $langFile['path_relativepath']; ?></span>
      </td></tr>
      
      <tr><td class="spacer"></td><td></td></tr>
      
      <tr><td class="left">
      <label for="cfg_uploadPath"><span class="toolTip mark" title="<?php echo $langFile['adminSetup_fmsSettings_feld4'].'::'.$langFile['adminSetup_fmsSettings_feld4_tip'] ?>">
      <?php echo $langFile['adminSetup_fmsSettings_feld4'] ?></span></label>
      </td><td class="right">
      <input size="40" id="cfg_uploadPath" name="cfg_uploadPath" value="<?php echo $adminConfig['uploadPath']; ?>" class="toolTip" title="<?php echo $langFile['path_absolutepath_tip']; ?>" />
      <span class="hint"><?php echo $langFile['path_absolutepath']; ?></span>
      </td></tr>
      
      <tr><td class="left">
      <label for="cfg_langPath"><span class="toolTip mark" title="<?php echo $langFile['adminSetup_fmsSettings_feld5'].'::'.$langFile['adminSetup_fmsSettings_feld5_tip'] ?>">
      <?php echo $langFile['adminSetup_fmsSettings_feld5'] ?></span></label>
      </td><td class="right">
      <input size="40" id="cfg_langPath" name="cfg_langPath" value="<?php echo $adminConfig['langPath']; ?>" class="toolTip" title="<?php echo $langFile['path_absolutepath_tip']; ?>" />
      <span class="hint"><?php echo $langFile['path_absolutepath']; ?></span>
      </td></tr>
      
      <tr><td class="left">
      <label for="cfg_stylesheetPath"><span class="toolTip mark" title="<?php echo $langFile['adminSetup_fmsSettings_feld6'].'::'.$langFile['adminSetup_fmsSettings_feld6_tip'] ?>">
      <?php echo $langFile['adminSetup_fmsSettings_feld6'] ?></span></label>
      </td><td class="right">
      <input size="40" id="cfg_stylesheetPath" name="cfg_stylesheetPath" value="<?php echo $adminConfig['stylesheetPath']; ?>" class="toolTip" title="<?php echo $langFile['path_absolutepath_tip']; ?>" />
      <span class="hint"><?php echo $langFile['path_absolutepath']; ?></span>
      </td></tr>      
      
      <tr><td class="spacer"></td><td></td></tr>
      
      <tr><td class="left">
      <label for="cfg_dateFormat"><span class="toolTip mark" title="<?php echo $langFile['adminSetup_fmsSettings_feld7'].'::'.$langFile['adminSetup_fmsSettings_feld7_tip'] ?>">
      <?php echo $langFile['adminSetup_fmsSettings_feld7'] ?></span></label>
      </td><td class="right">
      <select id="cfg_dateFormat" name="cfg_dateFormat">
        <option value="eu"<?php if($adminConfig['dateFormat'] == 'eu') echo ' selected="selected"'; ?>><?php echo $langFile['date_eu'];?></option>
        <option value="int"<?php if($adminConfig['dateFormat'] == 'int') echo ' selected="selected"'; ?>><?php echo $langFile['date_int'];?></option>
      </select>
      </td></tr>
      
      <tr><td class="spacer"></td><td></td></tr>
      
      <!-- URL FORMAT -> SPEAKING URLS -->
      <tr><td class="left">
      <label for="cfg_speakingUrl"><span class="toolTip mark" title="<?php echo $langFile['adminSetup_fmsSettings_speakingUrl'].'::'.$langFile['adminSetup_fmsSettings_speakingUrl_tip'] ?>">
      <?php echo $langFile['adminSetup_fmsSettings_speakingUrl'] ?></span></label>
      </td><td class="right">
      <select id="cfg_speakingUrl" name="cfg_speakingUrl" style="width:160px;" <?php if(!in_array('mod_rewrite',apache_get_modules())) echo 'disabled="disabled"'; ?>>
        <option value="true"<?php if($adminConfig['speakingUrl'] == 'true') echo ' selected="selected"'; ?>><?php echo $langFile['adminSetup_fmsSettings_speakingUrl_true'].' &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;-> '.$langFile['adminSetup_fmsSettings_speakingUrl_true_example'];?></option>
        <option value=""<?php if($adminConfig['speakingUrl'] == '') echo ' selected="selected"'; ?>><?php echo $langFile['adminSetup_fmsSettings_speakingUrl_false'].' &nbsp;&nbsp;&nbsp;-> '.$langFile['adminSetup_fmsSettings_speakingUrl_false_example'];?></option>
      </select>
      <span class="hint">
      <?php
        echo '<b>'.$langFile['text_example'].':</b> ';
        
        // show the right example
        // AND disable varNames if speaking urls = true
        if($adminConfig['speakingUrl'] == 'true') {
          echo $langFile['adminSetup_fmsSettings_speakingUrl_true_example'];
          $varNamesStyle = ' disabled="disabled"';
        } else {
          echo $langFile['adminSetup_fmsSettings_speakingUrl_false_example'];
          $varNamesStyle = '';
        }
      ?>
      </span>
      </td></tr>
      
      <tr><td class="spacer"></td><td></td></tr>
      
      <tr><td class="left">
      <label for="cfg_varNamePage"><span class="toolTip mark" title="<?php echo $langFile['adminSetup_fmsSettings_varName1'].'::'.$langFile['adminSetup_fmsSettings_varName_tip'] ?>">
      <?php echo $langFile['adminSetup_fmsSettings_varName1'] ?></span></label>
      </td><td class="right">
      <input size="40" id="cfg_varNamePage" name="cfg_varNamePage" value=<?php echo '"'.$adminConfig['varName']['page'].'"'.$varNamesStyle; ?> class="toolTip" title="<?php echo $langFile['adminSetup_fmsSettings_varName1'].'::'.$langFile['adminSetup_fmsSettings_varName1_inputTip']; ?>" />
      </td></tr>
      
      <tr><td class="left">
      <label for="cfg_varNameCategory"><span class="toolTip mark" title="<?php echo $langFile['adminSetup_fmsSettings_varName2'].'::'.$langFile['adminSetup_fmsSettings_varName_tip'] ?>">
      <?php echo $langFile['adminSetup_fmsSettings_varName2'] ?></span></label>
      </td><td class="right">
      <input size="40" id="cfg_varNameCategory" name="cfg_varNameCategory" value=<?php echo '"'.$adminConfig['varName']['category'].'"'.$varNamesStyle; ?> class="toolTip" title="<?php echo $langFile['adminSetup_fmsSettings_varName2'].'::'.$langFile['adminSetup_fmsSettings_varName2_inputTip']; ?>" />
      </td></tr>
      
      <tr><td class="left">
      <label for="cfg_varNameModul"><span class="toolTip mark" title="<?php echo $langFile['adminSetup_fmsSettings_varName3'].'::'.$langFile['adminSetup_fmsSettings_varName_tip'] ?>">
      <?php echo $langFile['adminSetup_fmsSettings_varName3'] ?></span></label>
      </td><td class="right">
      <input size="40" id="cfg_varNameModul" name="cfg_varNameModul" value=<?php echo '"'.$adminConfig['varName']['modul'].'"'.$varNamesStyle; ?> class="toolTip" title="<?php echo $langFile['adminSetup_fmsSettings_varName3'].'::'.$langFile['adminSetup_fmsSettings_varName3_inputTip']; ?>" />
      </td></tr>
      
      <tr><td class="leftBottom"></td><td></td></tr>
      
    </table>
    
    <!--<input type="reset" value="" class="toolTip button cancel" title="<?php echo $langFile['form_cancel']; ?>" />-->
    <input type="submit" value="" name="adminConfig" class="toolTip button submit" title="<?php echo $langFile['form_submit']; ?>" onclick="$('savedBlock').value = 'fmsSettings';" />
  </div>
  <div class="bottom"></div>
</div>

<!-- USER SETTINGS -->
<?php
// shows the block below if it is the ones which is saved before
if($savedForm != 'userSettings')  $hidden = ' hidden';
else $hidden = '';  
?>
<div class="block<?php echo $hidden; ?>">
<h1><a href="#" id="userSettings" name="userSettings"><?php echo $langFile['adminSetup_userSettings_h1']; ?></a></h1>
<div class="content">
    <table>
     
      <colgroup>
      <col class="left" />
      </colgroup>
  
      <tr><td class="leftTop"></td><td></td></tr>
      
      <tr><td class="left checkboxes">
      <input type="checkbox" id="cfg_userLanguage" name="cfg_userLanguage" value="true"<?php if($adminConfig['user']['editLanguage']) echo ' checked="checked"'; ?> /><br />
      <input type="checkbox" id="cfg_userStylesheet" name="cfg_userStylesheet" value="true"<?php if($adminConfig['user']['editStylesheet']) echo ' checked="checked"'; ?> />
      
      </td><td class="right checkboxes">
      <label for="cfg_userLanguage"><?php echo $langFile['adminSetup_userSettings_check1']; ?></label><br />
      <label for="cfg_userStylesheet"><?php echo $langFile['adminSetup_userSettings_check2']; ?></label>
      </td></tr>
      
      
      <tr><td class="left">
      <label for="cfg_userInfo"><span class="toolTip mark" title="<?php echo $langFile['adminSetup_userSettings_textarea1_tip']; ?>"><?php echo $langFile['adminSetup_userSettings_textarea1']; ?></span></label>
      </td><td class="right">
      <textarea id="cfg_userInfo" name="cfg_userInfo"  cols="50" rows="8" style="white-space:normal;width:500px;height:250px;margin-bottom: 50px;" class="toolTip" title="<?php echo $langFile['adminSetup_userSettings_textarea1_inputTip']; ?>"><?php echo strip_tags($adminConfig['user']['info']); ?></textarea>
      </td></tr>

      <tr><td class="leftBottom"></td><td></td></tr>
      
    </table>
    
    <!--<input type="reset" value="" class="toolTip button cancel" title="<?php echo $langFile['form_cancel']; ?>" />-->
    <input type="submit" value="" name="adminConfig" class="toolTip button submit" title="<?php echo $langFile['form_submit']; ?>" onclick="$('savedBlock').value = 'userSettings';" />
  </div>
  <div class="bottom"></div>
</div>

<!-- PAGE SETTINGS -->
<?php
// shows the block below if it is the ones which is saved before
if($savedForm != 'pageSettings')  $hidden = ' hidden';
else $hidden = '';  
?>
<div class="block<?php echo $hidden; ?>">
  <h1><a href="#" id="pageSettings" name="pageSettings"><?php echo $langFile['adminSetup_pageSettings_h1']; ?></a></h1>
  <div class="content">
    <table>
     
      <colgroup>
      <col class="left" />
      </colgroup>
  
      <tr><td class="leftTop"></td><td></td></tr>
      
      <tr><td class="left checkboxes">
      <input type="checkbox" id="cfg_setStartPage" name="cfg_setStartPage" value="true" class="toolTip" title="<?php echo $langFile['adminSetup_pageSettings_check1'].'::'.$langFile['adminSetup_pageSettings_check1_tip']; ?>"<?php if($adminConfig['setStartPage']) echo ' checked="checked"'; ?> />
      
      </td><td class="right checkboxes">
      <label for="cfg_setStartPage"><span class="toolTip mark" title="<?php echo $langFile['adminSetup_pageSettings_check1'].'::'.$langFile['adminSetup_pageSettings_check1_tip']; ?>"><?php echo $langFile['adminSetup_pageSettings_check1']; ?></span></label>
      </td></tr>
      
      <tr><td class="spacer"></td><td></td></tr>      
      
      <tr><td class="left checkboxes">
      <input type="checkbox" id="cfg_pageCreatePages" name="cfg_pageCreatePages" value="true" class="toolTip" title="<?php echo $langFile['adminSetup_pageSettings_check2'].'::'.$langFile['adminSetup_pageSettings_check2_tip']; ?>"<?php if($adminConfig['page']['createPages']) echo ' checked="checked"'; ?> /><br />
      <input type="checkbox" id="cfg_pageThumbnailUpload" name="cfg_pageThumbnailUpload" value="true" class="toolTip" title="<?php echo $langFile['adminSetup_pageSettings_check3'].'::'.$langFile['adminSetup_pageSettings_check3_tip']; ?>"<?php if($adminConfig['page']['thumbnailUpload']) echo ' checked="checked"'; ?> />
      
      </td><td class="right checkboxes">
      <label for="cfg_pageCreatePages"><span class="toolTip mark" title="<?php echo $langFile['adminSetup_pageSettings_check2'].'::'.$langFile['adminSetup_pageSettings_check2_tip']; ?>"><?php echo $langFile['adminSetup_pageSettings_check2']; ?></span></label><br />
      <label for="cfg_pageThumbnailUpload"><span class="toolTip mark" title="<?php echo $langFile['adminSetup_pageSettings_check3'].'::'.$langFile['adminSetup_pageSettings_check3_tip']; ?>"><?php echo $langFile['adminSetup_pageSettings_check3']; ?></span></label>
      </td></tr>
      
      <tr><td class="leftBottom"></td><td></td></tr>
      
    </table>
    
    <!--<input type="reset" value="" class="toolTip button cancel" title="<?php echo $langFile['form_cancel']; ?>" />-->
    <input type="submit" value="" name="adminConfig" class="toolTip button submit" title="<?php echo $langFile['form_submit']; ?>" onclick="$('savedBlock').value = 'pageSettings';" />
  </div>
  <div class="bottom"></div>
</div>

<!-- EDITOR SETTINGS -->
<?php
// shows the block below if it is the ones which is saved before
if($savedForm != 'editorSettings')  $hidden = ' hidden';
else $hidden = '';  
?>
<div class="block<?php echo $hidden; ?>">
  <h1><a href="#" id="editorSettings" name="editorSettings"><?php echo $langFile['adminSetup_editorSettings_h1']; ?></a></h1>
  <div class="content">
    <table>
     
      <colgroup>
      <col class="left" />
      </colgroup>
  
      <tr><td class="leftTop"></td><td></td></tr>
      
      <tr><td class="left">
      <label for="cfg_editorEnterMode"><span class="toolTip mark" title="<?php echo $langFile['adminSetup_editorSettings_feld1'].'::'.$langFile['adminSetup_editorSettings_feld1_tip'] ?>">
      <?php echo $langFile['adminSetup_editorSettings_feld1'] ?></span></label>
      </td><td class="right">
      <input id="cfg_editorEnterMode" name="cfg_editorEnterMode" style="width:30px;text-align:center;" value="<?php echo $adminConfig['editor']['enterMode']; ?>" />
      <span class="hint"><?php echo $langFile['adminSetup_editorSettings_feld1_inputTip']; ?></span>
      </td></tr>
      
      <tr><td class="left">
      <label for="cfg_editorStyleFile"><span class="toolTip mark" title="<?php echo $langFile['stylesheet_name_styleFile'].'::'.$langFile['stylesheet_styleFile_tip'] ?>">
      <?php echo $langFile['stylesheet_name_styleFile'] ?></span></label>
      </td><td class="right">
      <input id="cfg_editorStyleFile" name="cfg_editorStyleFile" class="toolTip" value="<?php echo $adminConfig['editor']['styleFile']; ?>" title="<?php echo $langFile['path_absolutepath_tip']; ?>" />
      <span class="hint"><?php echo $langFile['stylesheet_styleFile_example']; ?></span>
      </td></tr>
      
      <tr><td class="left">
      <label for="cfg_editorStyleId"><span class="toolTip mark" title="<?php echo $langFile['stylesheet_name_styleId'].'::'.$langFile['stylesheet_styleId_tip'] ?>">
      <?php echo $langFile['stylesheet_name_styleId'] ?></span></label>
      </td><td class="right">
      <input id="cfg_editorStyleId" name="cfg_editorStyleId" class="toolTip" value="<?php echo $adminConfig['editor']['styleId']; ?>" title="<?php echo $langFile['adminSetup_editorSettings_feld3_inputTip']; ?>" />
      </td></tr>
      
      <tr><td class="left">
      <label for="cfg_editorStyleClass"><span class="toolTip mark" title="<?php echo $langFile['stylesheet_name_styleFile'].'::'.$langFile['stylesheet_styleClass_tip'] ?>">
      <?php echo $langFile['stylesheet_name_styleFile'] ?></span></label>
      </td><td class="right">
      <input id="cfg_editorStyleClass" name="cfg_editorStyleClass" class="toolTip" value="<?php echo $adminConfig['editor']['styleClass']; ?>" title="<?php echo $langFile['adminSetup_editorSettings_feld4_inputTip']; ?>" />
      </td></tr>
      
      <tr><td class="leftBottom"></td><td></td></tr>
      
    </table>
    
    <!--<input type="reset" value="" class="toolTip button cancel" title="<?php echo $langFile['form_cancel']; ?>" />-->
    <input type="submit" value="" name="adminConfig" class="toolTip button submit" title="<?php echo $langFile['form_submit']; ?>" onclick="$('savedBlock').value = 'editorSettings';" />
  </div>
  <div class="bottom"></div>
</div>

<!-- THUMBNAIL SETTINGS -->
<?php
// shows the block below if it is the ones which is saved before
if($savedForm != 'thumbnailSettings')  $hidden = ' hidden';
else $hidden = '';  
?>
<div class="block<?php echo $hidden; ?>">
  <h1><a href="#" id="thumbnailSettings" name="thumbnailSettings"><?php echo $langFile['adminSetup_thumbnailSettings_h1']; ?></a></h1>
  <div class="content">
    <table>
     
      <colgroup>
      <col class="left" />
      </colgroup>
  
      <tr><td class="leftTop"></td><td></td></tr>
      
      <tr><td class="left">
      <label for="cfg_thumbWidth"><span class="toolTip mark" title="<?php echo $langFile['thumbnail_width_tip'] ?>">
      <?php echo $langFile['thumbnail_name_width'] ?></span></label>
      </td><td class="right">
      <input id="cfg_thumbWidth" name="cfg_thumbWidth" class="short" value="<?php echo $adminConfig['pageThumbnail']['width']; ?>" />
      <?php echo $langFile['thumbSize_unit']; ?>
      </td></tr>
      
      <!-- shows the width in a scale -->
      <?php
      if(!empty($adminConfig['pageThumbnail']['width']))
        $style_thumbWidth = 'width:'.$adminConfig['pageThumbnail']['width'].'px;';
      else
        $style_thumbWidth = 'width:0px;';
      ?>
      <tr><td class="left">
      </td><td class="right">
      <div id="thumbWidthScale" class="scale" style="<?php echo $style_thumbWidth; ?>max-width:520px;">
        <div></div>
      </div>
      </td></tr>
      
      <tr><td class="left">
      <label for="cfg_thumbHeight"><span class="toolTip mark" title="<?php echo $langFile['thumbnail_height_tip'] ?>">
      <?php echo $langFile['thumbnail_name_height'] ?></span></label>
      </td><td class="right">
      <input id="cfg_thumbHeight" name="cfg_thumbHeight" class="short" value="<?php echo $adminConfig['pageThumbnail']['height']; ?>" />
      <?php echo $langFile['thumbSize_unit']; ?>
      </td></tr>
      
      <!-- shows the height in a scale -->
      <?php
      if(!empty($adminConfig['pageThumbnail']['height']))
        $style_thumbHeight = 'width:'.$adminConfig['pageThumbnail']['height'].'px;';
      else
        $style_thumbHeight = 'width:0px;';
      ?>
      <tr><td class="left">
      </td><td class="right">
      <div id="thumbHeightScale" class="scale" style="<?php echo $style_thumbHeight; ?>max-width:520px;"><div></div></div>
      </td></tr>
      
      <tr><td class="left">
      <label for="cfg_thumbPath"><span class="toolTip mark" title="<?php echo $langFile['adminSetup_thumbnailSettings_feld3'].'::'.$langFile['adminSetup_thumbnailSettings_feld3_tip'] ?>">
      <?php echo $langFile['adminSetup_thumbnailSettings_feld3'] ?></span></label>
      </td><td class="right">
      <input style="width:auto;" readonly="readonly" size="<?php echo strlen($adminConfig['uploadPath'])+5; ?>" value="<?php echo $adminConfig['uploadPath']; ?>images/" class="toolTip" title="<?php echo $langFile['adminSetup_thumbnailSettings_feld3_inputTip1']; ?>" />
      <input id="cfg_thumbPath" name="cfg_thumbPath" style="width:150px;" value="<?php echo str_replace("images/","",$adminConfig['pageThumbnail']['path']); ?>" class="toolTip" title="<?php echo $langFile['adminSetup_thumbnailSettings_feld3_inputTip2']; ?>" />
      </td></tr>
      
            
      <tr><td class="leftBottom"></td><td></td></tr>
      
    </table>
    
    <!--<input type="reset" value="" class="toolTip button cancel" title="<?php echo $langFile['form_cancel']; ?>" />-->
    <input type="submit" value="" name="adminConfig" class="toolTip button submit" title="<?php echo $langFile['form_submit']; ?>" onclick="$('savedBlock').value = 'thumbnailSettings';" />
  </div>
  <div class="bottom"></div>
</div>

</form>

<div class="blockSpacer"></div>


<!-- HIER BEGINNT DIE STIL-AUSWAHL BEARBEITUNG DES HTML EDITORS -->

<form action="?site=adminSetup#fckstyleFileAnchor" method="post" enctype="multipart/form-data" accept-charset="ISO-8859-1,ISO-8859-2,ISO-8859-2,UTF-8">
  <div><input type="hidden" name="saveFckStyleFile" value="true" /></div>

<?php

$htmlEditorStyleFilePath = "config/htmlEditorStyles.xml";
$htmlEditorStyleFile = fopen($htmlEditorStyleFilePath,"r");

$htmlEditorStyleContent = fread($htmlEditorStyleFile,filesize($htmlEditorStyleFilePath));
fclose($htmlEditorStyleFile);


// shows the block below if it is the ones which is saved before
if($savedForm != 'fckStyleFile')
    $hidden = ' hidden';
  else
    $hidden = '';    
?>
<div class="block<?php echo $hidden; ?>">
  <h1><a href="#" name="fckstyleFileAnchor"><?php echo $langFile['adminSetup_styleFileSettings_h1']; ?></a></h1>
  <div class="content">
    <textarea name="fckStyleFile" cols="90" rows="30" class="editFiles"><?php echo $htmlEditorStyleContent; ?></textarea>
 
    <input type="reset" value="" class="toolTip button cancel" title="<?php echo $langFile['form_cancel']; ?>" />
    <input type="submit" value="" name="saveCategories" class="toolTip button submit" title="<?php echo $langFile['form_submit']; ?>" />
  </div>
  <div class="bottom"></div>
</div>
</form>

<br />
<?php

// BEARBEITUNG DER SPRACHDATEI
editFiles($adminConfig['langPath'], $_GET['site'], "editLangfile",  $langFile['editFilesSettings_h1_lang'], "langFilesAnchor", "php");
  
echo '<br />';

// BEARBEITUNG DER STYLESHEETDATEI
editFiles($adminConfig['stylesheetPath'], $_GET['site'], "editCSSfile", $langFile['editFilesSettings_h1_style'], "cssFilesAnchor", "css");

} // <-- END isAdmin()
?>