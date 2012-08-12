<?php
    include 'simple_html_dom.php';

    define("CLEANDIR",'clean_jargon/');
    define("JAR_HTML_DIR",'jargon-4.4.7/html');
    define("RATINGDIR",'jar_rating/');
    define("LEXILOC",'lexicon.json');
    define("TOPRATLOC",'topRated.json');
    define("CLEAN_FILE_EXT",'-cleanJar.json');
    define("RATING_FILE_EXT",'-ratJar.json');
        
    /* getJargon returns array in the form
     * array('name' =>  s_jarName
     *       'def'  =>  sa_jarDef[] )*/
    function getJargon($s_jarWord){

        $fp_cleanJar = CLEANDIR . correctJarWord($s_jarWord) . CLEAN_FILE_EXT;

        if(isCleanJar($fp_cleanJar))
            return readData($fp_cleanJar);
        else
            return cleanJargon($s_jarWord, $fp_cleanJar);
    }
    
    function isCleanJar($fp_cleanJar){
        if(!is_dir(CLEANDIR))
            return false;
        
        if(!is_file($fp_cleanJar))
            return false;
        
        return true;
    }

    function cleanJargon($s_jarWord, $fp_cleanJar){
        
        if(!is_dir(CLEANDIR))
            mkdir (CLEANDIR, 0755);
        
        $fp_jarLoc;
        
        if($s_jarWord == '-fu') // yes this is for real
            $c_1stLet = 'f';
        else if($s_jarWord == '-oid')
            $c_1stLet = 'o';
        else if($s_jarWord == '-ware')
            $c_1stLet = 'w';
        else if(strlen($s_jarWord) > 1)
            $c_1stLet = substr(lcfirst($s_jarWord), 0, 1-strlen($s_jarWord));
        else
            $c_1stLet = lcfirst($s_jarWord);
        
        switch ($c_1stLet){
            case a:
                $fp_jarLoc = JAR_HTML_DIR . '/A/';
                break;
            case b:
                $fp_jarLoc = JAR_HTML_DIR . '/B/';
                break;
            case c:
                $fp_jarLoc = JAR_HTML_DIR . '/C/';
                break;
            case d:
                $fp_jarLoc = JAR_HTML_DIR . '/D/';
                break;
            case e:
                $fp_jarLoc = JAR_HTML_DIR . '/E/';
                break;
            case f:
                $fp_jarLoc = JAR_HTML_DIR . '/F/';
                break;
            case g:
                $fp_jarLoc = JAR_HTML_DIR . '/G/';
                break;
            case h:
                $fp_jarLoc = JAR_HTML_DIR . '/H/';
                break;
            case i:
                $fp_jarLoc = JAR_HTML_DIR . '/I/';
                break;
            case j:
                $fp_jarLoc = JAR_HTML_DIR . '/J/';
                break;
            case k:
                $fp_jarLoc = JAR_HTML_DIR . '/K/';
                break;
            case l:
                $fp_jarLoc = JAR_HTML_DIR . '/L/';
                break;
            case m:
                $fp_jarLoc = JAR_HTML_DIR . '/M/';
                break;
            case n:
                $fp_jarLoc = JAR_HTML_DIR . '/N/';
                break;
            case o:
                $fp_jarLoc = JAR_HTML_DIR . '/O/';
                break;
            case p:
                $fp_jarLoc = JAR_HTML_DIR . '/P/';
                break;
            case q:
                $fp_jarLoc = JAR_HTML_DIR . '/Q/';
                break;
            case r:
                $fp_jarLoc = JAR_HTML_DIR . '/R/';
                break;
            case s:
                $fp_jarLoc = JAR_HTML_DIR . '/S/';
                break;
            case t:
                $fp_jarLoc = JAR_HTML_DIR . '/T/';
                break;
            case u:
                $fp_jarLoc = JAR_HTML_DIR . '/U/';
                break;
            case v:
                $fp_jarLoc = JAR_HTML_DIR . '/V/';
                break;
            case w:
                $fp_jarLoc = JAR_HTML_DIR . '/W/';
                break;
            case x:
                $fp_jarLoc = JAR_HTML_DIR . '/X/';
                break;
            case y:
                $fp_jarLoc = JAR_HTML_DIR . '/Y/';
                break;
            case z:
                $fp_jarLoc = JAR_HTML_DIR . '/Z/';
                break;
            default:
                $fp_jarLoc = JAR_HTML_DIR . '/0/';
                break;
        }
        
        $fp_jarLoc .= correctJarWord($s_jarWord) . '.html';
        
        $a_jarData = jargonData($fp_jarLoc);
        
        writeData($a_jarData, $fp_cleanJar);
        
        return $a_jarData;
    }

    function jargonData($fp){
        
        $HDOM_defFile = file_get_html($fp);
        
        // probably don't need to search for the name :/
        $HDOM_e_wordName = $HDOM_defFile->find('title',0);
        $s_wordName = $HDOM_e_wordName->plaintext;
        
        $sa_wordDef;
        
        foreach($HDOM_defFile->find('p') as $i => $HDOM_e_wordDef)
            $sa_wordDef[$i] = $HDOM_e_wordDef->outertext;
        
        //add anchor parser here
        
        $a_jarDef['name'] = $s_wordName;
        $a_jarDef['def'] = $sa_wordDef;
        
        return $a_jarDef;
    }
    
    class JarSearch{
        
        private $sa_lexiList;   // array of all words
        private $i_indexs = -1; // indexes of possible words
        private $s_names = 'NO ITEMS FOUND'; // array of possible words

        public function __construct() {
            $this->initLexiList();
        }
        
        public function search($mix_needle){
            $this->s_names = 'NO ITEMS FOUND';
            $this->i_indexs = -1;
            
            if(gettype($mix_needle) == "array")
                $this->i_indexs = $mix_needle;
            elseif(gettype($mix_needle) == "integer"){
                settype ($mix_needle, 'array');
                $this->i_indexs = $mix_needle;
            } elseif(gettype($mix_needle) == "string")
                $this->i_indexs = $this->lexiSearch($mix_needle);
            
            $this->s_names = $this->lexiSearchName();
        }

        private function initLexiList(){
            if(is_file(LEXILOC))
                $this->sa_lexiList = readData(LEXILOC);
            else
                $this->makeLexiList();
        }

        private function makeLexiList(){
            $fp_lexiLoc = JAR_HTML_DIR . '/go01.html';

            $HDOM_jarGlos  = new simple_html_dom();
            $HDOM_jarGlos->load_file($fp_lexiLoc);

            $i = 0;
            foreach($HDOM_jarGlos->find('a') as $HDOM_e_anchor){
                $bool_isDicTab = false;

                /* if it's one letter AND doesn't have a / in url */
                if(!(strlen($HDOM_e_anchor->plaintext) > 1) && 
                        (!strstr($HDOM_e_anchor->href, '/')))
                    $bool_isDicTab = true; // dicTab being A,B,C, etc...

                /* exclude anchors that aren't links AND dictionary tabs */
                if(($HDOM_e_anchor->href) && !$bool_isDicTab)
                    switch ($HDOM_e_anchor->plaintext) {
                        case 'Prev':
                        case 'Next':    // these names are only used
                        case 'Home':    // for navigation
                        case 'Up':
                            $i--;
                            break;

                        default:
                            $this->sa_lexiList[$i] = $HDOM_e_anchor->plaintext;

                            break;
                    }
                else
                    $i--;

                $i++; // normal indexed foreach loop indexs oddly
            }

            writeData($this->sa_lexiList, LEXILOC);
        }
        
        private function lexiSearch($s_needle){
            
            $ia_possList;
            $j = 0;

            foreach($this->sa_lexiList as $i => $item){
                if(stristr($item, $s_needle)){
                    $ia_possList[$j] = $i;
                    $j++;
                }
            }
            return $ia_possList;
        }

        private function lexiSearchName(){
            $sa_possNameList;
            
            foreach($this->i_indexs as $i => $item)
                $sa_possNameList[$i] = $this->sa_lexiList[$item];
            
            return $sa_possNameList;
        }
        
        public function getNames(){
            return $this->s_names;
        }
        
        public function getLexiList(){
            return $this->sa_lexiList;
        }
        
    }
    
    /* rating data setup:
     * array (  up      =>  i_thumbsUp
     *          down    =>  i_thumbsDown
     *          total   =>  i_thumbs
     *          percent =>  f_upPercent ) */    
    function getRating($s_jarWord){
        $fp_ratJar = RATINGDIR . correctJarWord($s_jarWord) . RATING_FILE_EXT;
        
        if(isRated($fp_ratJar))
            return readData($fp_ratJar);
        else
            return initRating($fp_ratJar);
    }
    
    function isRated($fp_jarRating){
        if(!is_dir(RATINGDIR))
            return false;
        
        if(!is_file($fp_jarRating))
            return false;
        
        return true;
    }
    
    function initRating($fp_jarRating){
        if(!is_dir(RATINGDIR))
            mkdir (RATINGDIR, 0755);
        
        $a_ratJar['up'] = 0;
        $a_ratJar['down'] = 0;
        $a_ratJar['total'] = 0;
        $a_ratJar['percent'] = 0.0;
        
        writeData($a_ratJar, $fp_jarRating);
        
        return $a_ratJar;
    }
    
    function setRating($bool_isUp, $s_jarWord, $a_ratJar){
        $fp_ratJar = RATINGDIR . correctJarWord($s_jarWord) . RATING_FILE_EXT;
        
        if($bool_isUp){
            $a_ratJar['up']++;
            $a_ratJar['total']++;
            $a_ratJar['percent'] = $a_ratJar['up'] / $a_ratJar['total'];
        } else {
            $a_ratJar['down']++;
            $a_ratJar['total']++;
            $a_ratJar['percent'] = $a_ratJar['up'] / $a_ratJar['total'];
        }
        
        writeData($a_ratJar, $fp_ratJar);
        
        return $a_ratJar;
    }
    
    /* simply
     * array(   name    =>  s_name
     *          up      =>  $_thumbUP   ) */
    function topRatedCheck($s_jarWord){
        $a_wordRating = getRating($s_jarWord);
        
        $a_topJars = readData(TOPRATLOC);
        
        foreach($a_topJars['up'] as $i => $item)
            if(($a_wordRating['up'] > $item)&&($i < 3)){
                $a_topJars['name'][$i] = $s_jarWord;
                $a_topJars['up'][$i] = $a_wordRating['up']; 
                break;
            }
        writeData($a_topJars, TOPRATLOC);
    }
    
    function getTopRate(){
        if(is_file(TOPRATLOC))
            return readData(TOPRATLOC);
        else
            return initRating(TOPRATLOC);     
    }
    
    function initTopRate(){
        $a_topJars;
        
        for($i = 0; $i < 3; $i++){
            $s_randWord = randomWord();
            $a_wordRating = getRating($s_randWord);
            
            $a_topJars['name'][$i] = $s_randWord;
            $a_topJars['up'][$i] = $a_wordRating['up'];
        }
        writeData($a_topJars, TOPRATLOC);
        
        return $a_topJars;
    }
    
    function randomWord(){
        $o_search = new JarSearch();
        
        $sa_lexiList = $o_search->getLexiList();
        return $sa_lexiList[rand(0, count($sa_lexiList))];
    }
    
    function correctJarWord($s_jarWord){
        
        switch ($s_jarWord) {
            case '1TBS':
                return 'one-TBS';
            case '0':
                return 'numeral-zero';  // cause jargon creators
            case '2':                   // used inconsistent 
                return 'infix-2';       // naming ><...
            case 'Bzzzt! Wrong.':
                return 'Bzzzt-Wrong';
            case 'can\'t happen':
                return 'can-t-happen';
            case 'con':
                return 'con_';
            case 'C|N&gt;K':
                return 'CNK';
            case 'Don\'t do that then!':
                return 'Don-t-do-that-then-';
            case '-fu':
                return 'suffix-fu';
            case 'I didn\'t change anything!':
                return 'I-didn-t-change-anything-';
            case 'ID10T error': // lol
                return 'idiot';
            case 'mav':
                return 'code-404'; // the mav file is missing lol
            case '-oid':
                return 'suffix-oid';
            case 'OP':                  // who named these
                return 'thread-OP';     // files... ><
            case 'Ping O\' Death':
                return 'Ping-O--Death';
            case 'snarf &amp; barf':
                return 'snarf-ampersand-barf';
            case 'This can\'t happen':
                return 'This-can-t-happen';
            case 'Utah teapot, the':
                return 'Utah-teapot';
            case '-ware':
                return 'suffix-ware';
            case 'You know you\'ve been hacking too long when': // lol
                return 'You-know-you-ve-been-hacking-too-long-when';
            default:
                break;
        }
        
        $c_1stLet = substr($s_jarWord, 0, 1-strlen($s_jarWord));
        
        switch ($c_1stLet){
            case '/':
                $s_jarWord = substr($s_jarWord, 1);
                break;
            case '@':
                $s_jarWord = 'at' . substr($s_jarWord, 1);
                break;

            default:
                break;
        }
        
        $s_jarWord = str_replace(',', '-', $s_jarWord);
        $s_jarWord = str_replace(' ', '-', $s_jarWord);
        $s_jarWord = str_replace('.', '-', $s_jarWord);
        $s_jarWord = str_replace('!', '-', $s_jarWord);
        $s_jarWord = str_replace('/', '-', $s_jarWord);
        $s_jarWord = str_replace('(', '', $s_jarWord);
        $s_jarWord = str_replace(')', '', $s_jarWord);
        $s_jarWord = str_replace('\'', '', $s_jarWord);
        $s_jarWord = str_replace('?', '', $s_jarWord);
        $s_jarWord = str_replace('&amp;', '-ampersand-', $s_jarWord);
        $s_jarWord = str_replace('+', '-plus', $s_jarWord);
        $s_jarWord = str_replace('[tm]', '', $s_jarWord);
        $s_jarWord = str_replace('$', 'S', $s_jarWord);
        $s_jarWord = str_replace('404', 'code-404', $s_jarWord);
        $s_jarWord = str_replace('~', '-tilde-', $s_jarWord);
        $s_jarWord = str_replace('*', '-asterisk-', $s_jarWord);
        
        return $s_jarWord;
    }
    
    function writeData($a_data,$fp){
		$fp = fopen($fp,'w');
		fwrite($fp,json_encode($a_data));
    }
	
    function readData($fp){
		return(json_decode(file_get_contents($fp),true));
    }
    
?>
