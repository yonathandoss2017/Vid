<?php
	ini_set('display_errors', 1);
	
	require __DIR__ . '/vendor/autoload.php';
	
	use HeadlessChromium\BrowserFactory;

    $browserFactory = new BrowserFactory('google-chrome');
    $browser = $browserFactory->createBrowser();

    // navigate to a page with a form
	$page = $browser->createPage();
	$page->navigate('http://127.0.0.1:22999')->waitForNavigation();
	// put 'hello' in the input and submit the form
	//echo $pageTitle = $page->evaluate('document.title')->getReturnValue();
	//exit(0);
	
	$screenshot = $page->screenshot([
        'format'  => 'jpeg',  // default to 'png' - possible values: 'png', 'jpeg',
        'quality' => 80       // only if format is 'jpeg' - default 100 
    ]);
    $JPGName = 'screens/'.time().'.jpg';
    $screenshot->saveToFile($JPGName);
    
    echo '<a href="'.$JPGName.'">'.$JPGName.'</a><br/>';
    exit(0);
	
	$evaluation = $page->evaluate(
	'(() => {
	        document.querySelector("#username").value = "Vidoomy_admin";
	        document.querySelector("#password").value = "Vidoomy_LKQD2020";
	        document.querySelector("button.btn-primary").click();
	    })()'
	);
	sleep(1);
	
	$screenshot = $page->screenshot([
        'format'  => 'jpeg',  // default to 'png' - possible values: 'png', 'jpeg',
        'quality' => 80       // only if format is 'jpeg' - default 100 
    ]);
    $JPGName = 'screens/'.time().'.jpg';
    $screenshot->saveToFile($JPGName);
    
	echo '<a href="'.$JPGName.'">'.$JPGName.'</a><br/>';
	
	//$page->navigate('https://ui.lkqd.com/reports')->waitForNavigation();
	sleep(5);
	//echo $body = $page->evaluate('document.body.innerHTML')->getReturnValue();
    
    
    $screenshot = $page->screenshot([
        'format'  => 'jpeg',  // default to 'png' - possible values: 'png', 'jpeg',
        'quality' => 80       // only if format is 'jpeg' - default 100 
    ]);
    $JPGName = 'screens/'.time().'.jpg';
    $screenshot->saveToFile($JPGName);
    
	echo '<a href="'.$JPGName.'">'.$JPGName.'</a><br/>';
	
    // wait for the value to return and get it
    //$value = $evaluation->getReturnValue();
	//var_dump($value);
	
	//$value = $page->evaluate('document.querySelector("#value").innerHTML')->getReturnValue();
	//var_dump($value);
    
    
    
    
    
    
    
    
    
    
    
    // bye
    $browser->close();