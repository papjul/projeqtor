<?php
/*** COPYRIGHT NOTICE *********************************************************
 *
* Copyright 2009-2016 ProjeQtOr - Pascal BERNARD - support@projeqtor.org
* Contributors : -
*
* This file is part of ProjeQtOr.
*
* ProjeQtOr is free software: you can redistribute it and/or modify it under
* the terms of the GNU Affero General Public License as published by the Free
* Software Foundation, either version 3 of the License, or (at your option)
* any later version.
*
* ProjeQtOr is distributed in the hope that it will be useful, but WITHOUT ANY
* WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS
* FOR A PARTICULAR PURPOSE.  See the GNU Affero General Public License for
* more details.
*
* You should have received a copy of the GNU Affero General Public License along with
* ProjeQtOr. If not, see <http://www.gnu.org/licenses/>.
*
* You can get complete code of ProjeQtOr, other resource, help and information
* about contributors at http://www.projeqtor.org
*
*** DO NOT REMOVE THIS NOTICE ************************************************/
require_once('_securityCheck.php');
class Plugin extends SqlElement {
    public $id;
    public $name;
    public $description;
    public $zipFile;
    public $isDeployed;
    public $deploymentDate;
    public $deploymentVersion;
    public $compatibilityVersion;
    public $pluginVersion;
    public $uniqueCode;
    public $idle;
    
    private static $_triggeredEventList;
    
    function __construct() {
    }
    
    function __destruct() {
      parent::__destruct();
    }
    
    static function getFromName($name) {
      return SqlElement::getSingleSqlElementFromCriteria('Plugin', array('name'=>$name, 'idle'=>'0'));
    }
    
    public function load($file) {
      global $globalCatchErrors;
      traceLog("New plugin found : ".$file['name']);
      $this->name=str_replace('.zip','',$file['name']);
      $pos=strpos(strtolower($this->name),'_v');
      if ($pos) $this->name=substr($this->name,0,$pos);
      $this->zipFile=$file['path'];
      $plugin=$this->name;
      
      $result="OK";
      // unzip plugIn files
      $zip = new ZipArchive;
      $globalCatchErrors=true;
      $res = $zip->open($this->zipFile);
      if ($res === TRUE) {
        $res=$zip->extractTo(self::getDir());
        $zip->close();
      } 
      if ($res !== TRUE) {
        $result=i18n('pluginUnzipFail', array(self::unrelativeDir($this->zipFile), self::unrelativeDir(self::getDir()) ));
        errorLog("Plugin::load() : $result");
        return $result;
      }
      traceLog("Plugin unzipped succefully");
      
      $descriptorFileName=self::getDir()."/$plugin/pluginDescriptor.xml";
      if (! is_file($descriptorFileName)) {
        $result=i18n('pluginNoXmlDescriptor',array('pluginDescriptor.xml',self::unrelativeDir($descriptorFileName),$plugin));
        errorLog("Plugin::load() : $result");
        return $result;
      }
      $descriptorXml=file_get_contents($descriptorFileName);
      $parse = xml_parser_create();
      xml_parse_into_struct($parse, $descriptorXml, $value, $index);
      xml_parser_free($parse);
    
      $testUnicity=false;
      $testCompatibility=false;
      $arrayTriggers=array();
      foreach($value as $ind=>$prop) {
        if ($prop['tag']=='PLUGIN') {
          if (isset($prop['attributes']['NAME'])) {
            $pluginName=$prop['attributes']['NAME'];
            if ($plugin!=$pluginName) {
              $result=i18n('pluginNameIncompatibility',array($pluginName, $plugin,self::unrelativeDir($this->zipFile)));
              errorLog("Plugin::load() : $result");
              return $result;
            }           
          }
        }
        if ($prop['tag']=='PROPERTY') {
          $name='plugin'.ucfirst($prop['attributes']['NAME']);
          $value=$prop['attributes']['VALUE'];
          if (isset($prop['attributes']['VERSION'])) {
            $version=$prop['attributes']['VERSION'];
            if (!isset($$name) or !is_array($$name)) {
              if (isset($$name)) {
                $$name=array('1.0'=>$$name);
              } else {
                $$name=array();
              }
            }
            $tempArray=$$name;
            $tempArray[$version]=$value;
            $$name=$tempArray;
          } else {
            $$name=$value;
          }
        }
        if (isset($pluginName) and isset($pluginUniqueCode) and ! $testUnicity) {
          $testUnicity=true;
          $old=self::getFromName($pluginName);
          if ($old->uniqueCode and $pluginUniqueCode and $pluginUniqueCode!=$old->uniqueCode) {
            $result=i18n('pluginAlreadyExistsWithName',array($pluginName, $old->uniqueCode, $pluginUniqueCode));
            errorLog("Plugin::load() : $result");
            return $result;
          }
          $crit=array('uniqueCode'=>$pluginUniqueCode, 'idle'=>'0');
          $old=SqlElement::getSingleSqlElementFromCriteria('Plugin', $crit);
          if ($old->name and $pluginName and $pluginName!=$old->name) {
            $result=i18n('pluginAlreadyExistsWithCode',array($pluginUniqueCode, $old->name, $pluginName));
            errorLog("Plugin::load() : $result");
            return $result;
          }
        }
        if (isset($pluginCompatibility) and !$testCompatibility) {
          $testCompatibility=true;
          global $version;
          if (version_compare(ltrim($version,'V'),ltrim($pluginCompatibility,'V'),"<")) {
            $result=i18n('pluginVersionNotCompatible',array($version, $pluginCompatibility));
            errorLog("Plugin::load() : $result");
            return $result;
          }
        }
        if ($prop['tag']=='FILE') {
          if (isset($prop['attributes']) and is_array($prop['attributes'])) {
            $attr=$prop['attributes'];
            $fileName=(isset($attr['NAME']))?$attr['NAME']:null;
            $fileTarget=(isset($attr['TARGET']))?$attr['TARGET']:null;
            $fileSource=(isset($attr['SOURCE']))?$attr['SOURCE']:'';
            $fileAction=(isset($attr['ACTION']))?$attr['ACTION']:null;
            if (!is_dir($fileTarget)) {
              mkdir($fileTarget,0777,true);
            }
            if ($fileName and $fileTarget and ($fileAction=='move' or $fileAction=='copy')) {
              $res=copy(self::getDir()."/$plugin/$fileSource/$fileName","$fileTarget/$fileName");
              if (! $res) {
                $result=i18n('pluginErrorCopy',array($fileName,$fileTarget,$plugin));
                errorLog("Plugin::load() : $result");
                return $result;
              } else {
                if ($fileAction=='move') {
                  $res=kill(self::getDir()."/$plugin/$fileName");
                  if (! $res) {
                    // Not blocking error : delete of source is not really mandatory as long as copy is ok
                    //$result=i18n('pluginErrorMove',array($fileName,$plugin));
                    //errorLog("Plugin::load() : $result");
                  }
                }
              }
            }
            if ($fileName and $fileTarget and $fileAction=='delete') {
              $res=kill("$fileTarget/$fileName");
              if (! $res) {
                $result=i18n('pluginErrorDelete',array("$fileTarget/$fileName"));
                errorLog("Plugin::load() : $result");
                return $result;
              }
            }
          }
        }
        if ($prop['tag']=='TRIGGER') {
          if (isset($prop['attributes']) and is_array($prop['attributes'])) {
            $attr=$prop['attributes'];
            $event=(isset($attr['EVENT']))?$attr['EVENT']:null;
            $className=(isset($attr['CLASS']))?$attr['CLASS']:null;
            $script=(isset($attr['SCRIPT']))?$attr['SCRIPT']:null;
            if ($event and $className and $script) {
              $evt=new PluginTriggeredEvent();
              $evt->idPlugin=null; // to be defined later
              $evt->idle=0; // Active by default
              if ($className!='Planning' and $className!='ResourcePlanning' and $className!='PortfolioPlanning') { // Pseudo Classes
                Security::checkValidClass($className);
              }
              $evt->className=$className;
              $evt->script=$script;
              $evt->event=$event;
              if (in_array($event,PluginTriggeredEvent::$_allowedEvents)) { // Will store event only if event is valid
                $arrayTriggers[]=$evt;
              } else {
                traceLog("trigger not stored : '$event' is not a valid event");
              }
            }
          }
        }
      }
      $globalCatchErrors=false;
      
      if (isset($pluginName)) $this->name=$pluginName;
      if (isset($pluginDescription)) $this->description=$pluginDescription;
      if (isset($pluginVersion)) $this->pluginVersion=$pluginVersion;
      if (isset($pluginCompatibility)) $this->compatibilityVersion=$pluginCompatibility;
      if (isset($pluginUniqueCode)) $this->uniqueCode=$pluginUniqueCode;
      
      $old=self::getFromName($this->name);
      // Must purge previous events stored in Database
      $evt=new PluginTriggeredEvent();
      $evt->purge('idPlugin='.Sql::fmtId($old->id));
      
      traceLog("Plugin descriptor information :");
      traceLog(" => name : $this->name");
      traceLog(" => description : $this->description");
      traceLog(" => version : $this->pluginVersion");
      traceLog(" => compatibility : $this->compatibilityVersion");
      
      // Update database for plugIn
      $crit=array('uniqueCode'=>$this->uniqueCode, 'idle'=>'0', 'isDeployed'=>'1');
      $existingPlugin=SqlElement::getSingleSqlElementFromCriteria('Plugin', $crit);
      $currentVersion=($existingPlugin and $existingPlugin->id)?$existingPlugin->pluginVersion:'0.0';
      if (isset($pluginSql) and $pluginSql) {
        if (!is_array($pluginSql)) $pluginSql=array('1.0'=>$pluginSql); // First plugins did not take into account upgrades of SQL
        foreach ($pluginSql as $pluginSqlVersion=>$pluginSqlValue) {
          if ($pluginSqlVersion>$currentVersion or $pluginSqlVersion=='all' or $pluginSqlVersion=='*') {
            $sqlfile=self::getDir()."/$plugin/$pluginSqlValue";
            if (! is_file($sqlfile)) {
              $result=i18n("pluginSqlFileError",array($sqlfile, $plugin));
              errorLog("Plugin::load() : $result");
              return $result;
            }
            // Run Sql defined in Descriptor
            // !IMPORTANT! to be able to call runScrip, the calling script must include "../db/maintenanceFunctions.php"
            $nbErrors=runScript(null,$sqlfile);
            traceLog("Plugin updated database with $nbErrors errors from script $sqlfile");
          }
        }
        deleteDuplicate(); // Avoid dupplicate for habilitation, ....
      }
      
      if (isset($pluginPostInstall)) {
        include self::getDir()."/$plugin/$pluginPostInstall";
      }
      
      // Delete zip
      kill($this->zipFile);
      // set previous version to idle (if exists)
      if ($old->id) {
        $old->idle=1;
        $old->save();
      }
      // Save deployment data
      $this->deploymentVersion=Parameter::getGlobalParameter('dbVersion');
      $this->deploymentDate=date('Y-m-d');
      $this->isDeployed=1;
      $this->idle=0;
      $resultSave=$this->save();
      // Now can save events
      foreach ($arrayTriggers as $evt) {
        $evt->idPlugin=$this->id;
        $evt->script=self::getDir().'/'.$this->name.'/'.$evt->script;
        $resEvt=$evt->save();
      }
      
      unsetSessionValue('triggeredEventList'); // Reset triggeredEventList (will be refresed on first need)
      self::$_triggeredEventList=null;
      
      traceLog("Plugin $plugin V".$this->pluginVersion. " completely deployed");
      if (isset($pluginReload) and $pluginReload) {
        return 'RELOAD';
      }
      return "OK";
    }
    
    static function getDir() {
      return "../plugin"; 
    }
    
    static function getZipList($oneOnlyFile=null) {
      $error='';
      $dir=self::getDir();
      if (! is_dir($dir)) {
        traceLog ("Plugin->getZipList() - directory '$dir' does not exist");
        $error="Plugin->getZipList() - directory '$dir' does not exist";
      }
      if (! $error) {
        $handle = opendir($dir);
        if (! is_resource($handle)) {
          traceLog ("Plugin->getZipList() - Unable to open directory '$dir' ");
          $error="Plugin->getZipList() - Unable to open directory '$dir' ";
        }
      } 
      $files=array();
      while (!$error and ($file = readdir($handle)) !== false) {
        if ($file == '.' || $file == '..' || $file=='index.php') {
          continue;
        }
        $filepath = ($dir == '.') ? $file : $dir . '/' . $file;
        if (is_link($filepath)) {
          continue;
        }
        if ($oneOnlyFile and $oneOnlyFile!=$file) {
          continue;
        }
        if (is_file($filepath) and strtolower(substr($file,-4))=='.zip') {
          $fileDesc=array('name'=>$file,'path'=>$filepath);
          $dt=filemtime ($filepath);
          $date=date('Y-m-d H:i',$dt);
          $fileDesc['date']=$date;
          $fileDesc['size']=filesize($filepath);
          $files[]=$fileDesc;
        }
      }
      if (! $error) closedir($handle);
      return $files;
    }
    
    public static function getActivePluginList() {
      // Retreive list from database
      $plugin=new Plugin();
      $pluginList=$plugin->getSqlElementsFromCriteria(array('idle'=>'0'),false,null,null,true);
      return $pluginList;
    }
    
    public static function getInstalledPluginNames() {
      $dir=self::getDir();
      if (! is_dir($dir)) {
        traceLog ("Plugin->getInstalledPluginNames() - directory '$dir' does not exist");
        return array();
      }
      $handle = opendir($dir);
      if (! is_resource($handle)) {
        return array();
      }
      $files=array();
      while ( ($file = readdir($handle)) !== false) {
        $filepath = ($dir == '.') ? $file : $dir . '/' . $file;
        if (is_dir($filepath)) {
          $files[]=$file;
        }
      }
      closedir($handle);
      return $files;
    } 
    
    public static function includeAllFiles () {
      global $testFlatTheme;
      if (version_compare(Sql::getDbVersion(), 'V5.0.0')<0) return;
      $list=self::getActivePluginList();
      foreach ($list as $plugin) {
        $plugin->includeFiles();
      }
    }
    
    public function includeFiles() {
      $root=self::getDir().'/'.$this->name.'/'.$this->name;
      // Javascript
      $jsFile=$root.'.js';
      if (file_exists($jsFile)) {
        echo '<script type="text/javascript" src="'.$jsFile.'?version='.htmlEncode($this->pluginVersion).'" ></script>';
      }
      // CSS (style sheet)
      $cssFile=$root.'.css';
      if (file_exists($cssFile)) {
        echo '<link rel="stylesheet" type="text/css" href="'.$cssFile.'" />';
      }
    }

    public static function unrelativeDir($dir) {
      return str_replace('../','/',$dir);
    }
    
    public static function getTranslationJsArrayForPlugins($arrayName) {
      global $currentLocale;
      if (version_compare(Sql::getDbVersion(), 'V5.0.0')<0) return;
      $langFileList=array();
      $pluginList=self::getInstalledPluginNames();
      $locale=(isset($currentLocale))?$currentLocale:'';
      foreach ($pluginList as $plugin) {
        $testLocale=Plugin::getDir().'/'.$plugin.'/nls/'.$locale."/lang.js";
        $testDefault=Plugin::getDir().'/'.$plugin."/nls/lang.js";
        if ($locale and file_exists($testLocale)) {
          $langFileList[$plugin]=$testLocale;
        } else if (file_exists($testDefault)){
          $langFileList[$plugin]=$testDefault;
        }
      }
      echo "$arrayName=new Array();\n";
      foreach ($langFileList as $testFile) {
        if (file_exists ( $testFile )) {
          $filename = $testFile;
          $file = fopen ( $filename, "r" );
          while ( $line = fgets ( $file ) ) {
            $split = explode ( ":", $line );
            if (isset ( $split [1] )) {
              $var = trim ( $split [0], " \t" ); // Remove tabs and spaces
              $valTab = explode ( ",", $split [1] );
              $val = trim ( $valTab [0], " \n\r" ); // Remove spaces, carriage returns and new lines
              $val = trim ( $val, '"' );
              //$i18nMessages [$var] = $val;
              echo $arrayName.'["'.$var.'"]="'.$val.'";'."\n";
            }
          }
          fclose ( $file );
        }
      }
    }
    public static function getMetadata($zipFileName) {
      $file=self::getDir().'/'.$zipFileName;
      $zip = new ZipArchive;
      $globalCatchErrors=true;
      $res = $zip->open($file);
      $descriptorXml="";
      if ($res === TRUE) {
        $idx=$zip->locateName('pluginDescriptor.xml',ZIPARCHIVE::FL_NODIR);
        $descriptorXml=$zip->getFromIndex($idx);
        $zip->close();
      }
      $parse = xml_parser_create();
      xml_parse_into_struct($parse, $descriptorXml, $value, $index);
      xml_parser_free($parse);
      $data=array();
      foreach($value as $ind=>$prop) {
        if ($prop['tag']=='PROPERTY') {
          $name='plugin'.ucfirst($prop['attributes']['NAME']);
          $value=$prop['attributes']['VALUE'];
          $data[$name]=$value;
        }
      }
      return $data;
    }
    
    public static function getEventScripts($event, $className) {
      if (version_compare(Sql::getDbVersion(), 'V5.2.0')<0) return array();
      if (!self::$_triggeredEventList) {
        self::getTriggeredEventList();
      }
      if (isset(self::$_triggeredEventList[$event][$className])) {
        return self::$_triggeredEventList[$event][$className];
      } else {
        return array();
      }
    }
    
    private static function getTriggeredEventList() {
      $sessionList=getSessionValue('triggeredEventList',null,true);
      if ($sessionList) {
        self::$_triggeredEventList=$sessionList;
        return;
      }
      $listActivePlugin=self::getActivePluginList();
      $evt=new PluginTriggeredEvent();
      $listEvt=$evt->getSqlElementsFromCriteria(array('idle'=>'0'));
      self::$_triggeredEventList=array();
      foreach ($listEvt as $evt) {
        if (!isset(self::$_triggeredEventList[$evt->event])) {
          self::$_triggeredEventList[$evt->event]=array();
        }
        if (!isset(self::$_triggeredEventList[$evt->event][$evt->className])) {
          self::$_triggeredEventList[$evt->event][$evt->className]=array();
        }
        self::$_triggeredEventList[$evt->event][$evt->className][]=$evt->script;
      }
      setSessionValue('triggeredEventList', self::$_triggeredEventList); // save to session for quick access
    }
    
    
}
 