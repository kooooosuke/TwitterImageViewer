<?php
require_once('/var/www/html/bot/twitteroauth/twitteroauth.php');
require_once('/var/www/html/bot/config2.php');

define('DIR' , '/var/www/html/bot/');
date_default_timezone_set('Asia/Tokyo');

print("
<html>
<head>
<style type=\"text/css\">
.item {
  max-height:10000; 
}
</style>
<script src=\"jquery-1.11.1.min.js\"></script>
<script src=\"masonry.pkgd.min.js\"></script>
<script>
  $(function(){
    $('#container').masonry({
      columnWidth: 200,
      itemSelector: '.item',
      isAnimated: true
    });
  });
</script>
</head>
<body>
");
print("<div id=\"container\">");

$link = mysql_connect('localhost', '****', '****');
if (!$link) {
    die('接続失敗です。'.mysql_error());
}
print('<p>接続に成功しました。</p>');
$db_selected = mysql_select_db('twitter_images', $link);
if (!$db_selected){
    print(mysql_error());
    mysql_close($link);
    die('データベース選択失敗です');
}
print('<p>データベースを選択しました。</p>');
mysql_set_charset('utf8');

$to = new TwitterOAuth(CONSUMER_KEY, CONSUMER_SECRET, ACCESS_TOKEN, ACCESS_TOKEN_SECRET);
$req = $to->OAuthRequest("https://api.twitter.com/1.1/statuses/home_timeline.json","GET",array("count"=>"200"));
//var_dump($req);
$result = json_decode($req);
//var_dump($result);
foreach($result as $tweet){
	//tweet
        if( property_exists($tweet,"extended_entities") ){
        if( property_exists($tweet->extended_entities,"media") ){
                foreach($tweet->extended_entities->media as $media){
			//var_dump($media);
                        //echo "<div class=\"item\"><a href=\"".$media->url."\" target=\"_blank\"><img src=\"".$media->media_url."\" width=200 height=auto></a></div>";
                        $url = explode("/", $media->media_url);
			$img_url = $url[4];
                        $url = explode("/", $media->url);
			$page_url = $url[3];
			$result = mysql_query('insert into images(img_url, page_url) values(\''.$img_url.'\',\''.$page_url.'\')');
			if (!$result) {
    				print(mysql_error());
				mysql_close($link);
				die('クエリーが失敗しました。');
			}
                }
        }}
} 

print("
</div>
</body>
</html>
");

?>
