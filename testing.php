<?php
    include 'jargonLibrary.php';
    //include ("template.html");

    /*$a_jarData = getJargon('Nightmare File System');

    echo '<h1>name:</h1>' . $a_jarData['name'];
    
    echo '<br><h1>def:</h1> ';
    
    foreach($a_jarData['def'] as $defItem)
        echo $defItem;*/
    
    //gen clean jargon
    /*foreach($a_lexiList as $item)
        getJargon($item);*/
    
    // gen ratings
    /*foreach($a_lexiList as $item)
        getRating($item);*/
    
    /*$myLexiSearch = lexiSearch('hac');
    if(gettype($myLexiSearch) == "array")
        foreach($myLexiSearch as $i => $item)
            echo '<br> possiblity ' . $i . ' ' . $a_lexiList['name'][$item];
    else
        echo '<br> lexiSearch: ' . $a_lexiList['name'][$myLexiSearch]; */
    
    $searchJargon = new JarSearch();
    $searchJargon->search(1337);
    
    //$a_lexiList = $searchJargon->getLexiList();
    
    echo '<br>';
    
    print_r($searchJargon->getNames());
    
    foreach($searchJargon->getNames() as $i => $item)
        echo '<br> $item['.$i. ']: ' . $item;
    
    $word = randomWord();
    echo '<br>';
    print_r($word);
    echo '<br>';
    $a_top = getTopRate();
    echo '<br>';
    print_r($a_top);
    
    /*$a_ratJar = getRating('hack');
    
    $a_ratJar = setRating(true, 'hack', $a_ratJar);
    echo '<br>';
    print_r($a_ratJar);*/

?>