<?php 
$config = json_decode(file_get_contents('config.json'),1);
$id = $config['id'];
$token = $config['token'];
include 'index.php';
$accounts = json_decode(file_get_contents('accounts.json'),1);
$id = $config['words'];
$file = $config['for'];
$a = file_exists('a') ? file_get_contents('a') : 'ap';
if($a == 'new'){
	file_put_contents($file, '');
}
$from = 'Followers';
$mid = bot('sendMessage',[
	'chat_id'=>$config['id'],
	'message_id'=>$mid,
	'text'=>"*𝐂𝐎𝐋𝐋𝐄𝐂𝐓𝐈𝐎𝐍 𝐅𝐑𝐎𝐌 * ~ [ _ $from _ ]\n\n*𝐒𝐓𝐀𝐓𝐔𝐒  * ~> _ 𝐖𝐎𝐑𝐊𝐈𝐍𝐆  _\n*𝐔𝐒𝐄𝐑𝐒 * ~> _ ".count(explode("\n", file_get_contents($file)))."_",
	'parse_mode'=>'markdown',
	'reply_markup'=>json_encode(['inline_keyboard'=>[
			[['text'=>'𝐒𝐓𝐎𝐏 .','callback_data'=>'stopgr']]
		]])
])->result->message_id;
$ids = explode(' ', $config['words']);
foreach($ids as $user){
	echo $user."\n";
	sleep(5);
	$cookies = $accounts[$file];
	$ig = new ig(['account'=>$accounts[$file],'file'=>$file]);
	$info = $ig->getInfo($user);
	$id = $info->pk;
	$ig->getFollowers($id,$mid,$user);
}