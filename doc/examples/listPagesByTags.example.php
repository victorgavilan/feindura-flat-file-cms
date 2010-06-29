<?php
/*                               *** CODE *** 
--------------------------------------------------------------------------------
This example shows only the usage of tags for listing pages,
for a detailed example see listPages()
*/

// the feindura.include.php has to be included BEFORE the header of the HTML page is sent
// because a session is startet in this file
require('cms/feindura.include.php');

// creates a new feindura instance
$myCms = new feinduraPages();

// the tags where the pages in the menu should have atleast one
$tags = 'winter summer spring';
// could also be an array like
// $tags = array(0 => 'winter', 1 => 'summer', 2 => 'spring');


// return the pages from the category with ID "1"
// the page content will be shorten to "200" characters
$pages = $myCms->listPagesByTags($tags,'category',1,200,true,true);

// displays the pages (the "\n" creates a line break for a better look)
foreach($pages as $page) {
  echo $page['title'].'<br />
       Has the following Tags: '.$page['tags']."\n";
  echo $page['content']."<br />-----------------------<br />\n";
}


/*                              *** RESULT with page *** 
--------------------------------------------------------------------------------
*/

Example Page 1<br />
Has the following Tags: Winter antum

<h2>Example Headline</h2>
<p>Lorem ipsum dolor sit amet, consetetur sadipscing dolores et ea rebum.
Stet clita kasd gubergren, no sea takimata sanctus.</p>
<a href="?category=1&amp;page=1">mehr</a>
<br />-----------------------<br />

Example Page 2<br />
Has the following Tags: winter spring summer

<h2>Another Example Headline</h2>
<p>Lorem ipsum dolor sit amet, consetetur sadipscing dolores et ea rebum.</p>
<h2>And one more Example Headline</h2>
<p>Stet clita kasd gubergren, no sea takimata sanctus est...</p>
<a href="?category=1&amp;page=2">mehr</a>
<br />-----------------------<br />

Example Page 3<br />
Has the following Tags: spring antum

<p>Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor
invidunt ut labore et dolore magna aliquyam erat, ur sadipscing elitr,
Stet clita kasd...</p>
<a href="?category=1&amp;page=3">mehr</a>
<br />-----------------------<br />

?>