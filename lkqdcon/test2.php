<?php
	ini_set('display_errors', 1);
	
	require __DIR__ . '/vendor/autoload.php';
	
	use HeadlessChromium\BrowserFactory;

    $browserFactory = new BrowserFactory('google-chrome-stable');
    $browser = $browserFactory->createBrowser([
        'customFlags' => [
            '--proxy-server="socks5://127.0.0.1:24000"'
       ]
	]);

    // navigate to a page with a form
	$page = $browser->createPage();
	$page->navigate('http://lumtest.com/myip.json')->waitForNavigation();
	// put 'hello' in the input and submit the form
	//echo $pageTitle = $page->evaluate('document.title')->getReturnValue();
	//exit(0);
	//sleep(5);
	
	$screenshot = $page->screenshot([
        'format'  => 'jpeg',  // default to 'png' - possible values: 'png', 'jpeg',
        'quality' => 80       // only if format is 'jpeg' - default 100 
    ]);
    $JPGName = 'screens/'.time().'.jpg';
    $screenshot->saveToFile($JPGName);
    
	echo '<a href="'.$JPGName.'">'.$JPGName.'</a><br/>';
    
    // bye
    $browser->close();