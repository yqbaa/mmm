<?php
class Logger_Config {

    private $level;
    private $file;
	
    public function __construct() {
    }
	
    public function getLevel() {
        return $this->level;
    }
    
    public function getFile() {
        return $this->file;
    }
    
    public function setLevel($level) {
        $this->level = Logger_Level::toLevel($level);
    }
    
    public function setFile($file) {
        if(strpos($file, "/") !== 0) {
            $file = Common::getConfig('siteConfig', 'logPath') . $file;
        }
        $this->file = $file;
    }
    
    public function __toString() {
        return $this->level . "   " . $this->file;
    }
}
