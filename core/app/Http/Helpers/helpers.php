<?php

use App\Lib\GoogleAuthenticator;
use App\Models\EmailTemplate;
use App\Models\Frontend;
use App\Models\GeneralSetting;
use App\Models\EmailLog;
use App\Models\Counter;
use Carbon\Carbon;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\PHPMailer;


function sidebarVariation(){

    /// for sidebar
    $variation['sidebar'] = 'bg_img';

    //for selector
    $variation['selector'] = 'capsule--rounded';

    //for overlay
    $variation['overlay'] = 'overlay--indigo';

    //Opacity
    $variation['opacity'] = 'overlay--opacity-8'; // 1-10

    return $variation;

}

function systemDetails()
{
    $system['name'] = 'BusExpress';
    $system['version'] = '1.0';
    return $system;
}

function slug($string)
{
    return Illuminate\Support\Str::slug($string);
}


function shortDescription($string, $length = 120)
{
    return Illuminate\Support\Str::limit($string, $length);
}


function shortCodeReplacer($shortCode, $replace_with, $template_string)
{
    return str_replace($shortCode, $replace_with, $template_string);
}


function verificationCode($length)
{
    if ($length == 0) return 0;
    $min = pow(10, $length - 1);
    $max = 0;
    while ($length > 0 && $length--) {
        $max = ($max * 10) + 9;
    }
    return random_int($min, $max);
}

function getNumber($length = 8)
{
    $characters = '1234567890';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}



//moveable
function uploadImage($file, $location, $size = null, $old = null, $thumb = null)
{
    $path = makeDirectory($location);
    if (!$path) throw new Exception('File could not been created.');

    if ($old) {
        removeFile($location . '/' . $old);
        removeFile($location . '/thumb_' . $old);
    }
    $filename = uniqid() . time() . '.' . $file->getClientOriginalExtension();
    $image = Image::make($file);
    if ($size) {
        $size = explode('x', strtolower($size));
        $image->resize($size[0], $size[1]);
    }
    $image->save($location . '/' . $filename);

    if ($thumb) {
        $thumb = explode('x', $thumb);
        Image::make($file)->resize($thumb[0], $thumb[1])->save($location . '/thumb_' . $filename);
    }

    return $filename;
}

function uploadFile($file, $location, $size = null, $old = null){
    $path = makeDirectory($location);
    if (!$path) throw new Exception('File could not been created.');

    if ($old) {
        removeFile($location . '/' . $old);
    }

    $filename = uniqid() . time() . '.' . $file->getClientOriginalExtension();
    $file->move($location,$filename);
    return $filename;
}

function makeDirectory($path)
{
    if (file_exists($path)) return true;
    return mkdir($path, 0755, true);
}


function removeFile($path)
{
    return file_exists($path) && is_file($path) ? @unlink($path) : false;
}


function activeTemplate($asset = false)
{
    $general = GeneralSetting::first(['active_template']);
    $template = $general->active_template;
    $sess = session()->get('template');
    if (trim($sess)) {
        $template = $sess;
    }
    if ($asset) return 'assets/templates/' . $template . '/';
    return 'templates.' . $template . '.';
}

function activeTemplateName()
{
    $general = GeneralSetting::first(['active_template']);
    $template = $general->active_template;
    $sess = session()->get('template');
    if (trim($sess)) {
        $template = $sess;
    }
    return $template;
}

function getTrx($length = 12)
{
    $characters = 'ABCDEFGHJKMNOPQRSTUVWXYZ123456789';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}

function getAmount($amount, $length = 2)
{
    $amount = round($amount, $length);
    return $amount + 0;
}

function seatLayoutToArray($layoutString) {
    return $seat_layout = explode('x', str_replace(' ','', $layoutString));
}

function showAmount($amount, $decimal = 2, $separate = true, $exceptZeros = false){
    $separator = '';
    if($separate){
        $separator = ',';
    }
    $printAmount = number_format($amount, $decimal, '.', $separator);
    if($exceptZeros){
    $exp = explode('.', $printAmount);
        if($exp[1]*1 == 0){
            $printAmount = $exp[0];
        }
    }
    return $printAmount;
}


function removeElement($array, $value)
{
    return array_diff($array, (is_array($value) ? $value : array($value)));
}

function cryptoQR($wallet)
{

    return "https://chart.googleapis.com/chart?chs=300x300&cht=qr&chl=$wallet&choe=UTF-8";
}

//moveable
function curlContent($url)
{
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $result = curl_exec($ch);
    curl_close($ch);
    return $result;
}

//moveable
function curlPostContent($url, $arr = null)
{
    if ($arr) {
        $params = http_build_query($arr);
    } else {
        $params = '';
    }
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $result = curl_exec($ch);
    curl_close($ch);
    return $result;
}


function inputTitle($text)
{
    return ucfirst(preg_replace("/[^A-Za-z0-9 ]/", ' ', $text));
}


function titleToKey($text)
{
    return strtolower(str_replace(' ', '_', $text));
}


function str_limit($title = null, $length = 10)
{
    return \Illuminate\Support\Str::limit($title, $length);
}

//moveable
function getIpInfo()
{
    $ip = $_SERVER["REMOTE_ADDR"];

    //Deep detect ip
    if (filter_var(@$_SERVER['HTTP_X_FORWARDED_FOR'], FILTER_VALIDATE_IP)){
        $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
    }
    if (filter_var(@$_SERVER['HTTP_CLIENT_IP'], FILTER_VALIDATE_IP)){
        $ip = $_SERVER['HTTP_CLIENT_IP'];
    }


    $xml = @simplexml_load_file("http://www.geoplugin.net/xml.gp?ip=" . $ip);


    $country = @$xml->geoplugin_countryName;
    $city = @$xml->geoplugin_city;
    $area = @$xml->geoplugin_areaCode;
    $code = @$xml->geoplugin_countryCode;
    $long = @$xml->geoplugin_longitude;
    $lat = @$xml->geoplugin_latitude;

    $data['country'] = $country;
    $data['city'] = $city;
    $data['area'] = $area;
    $data['code'] = $code;
    $data['long'] = $long;
    $data['lat'] = $lat;
    $data['ip'] = request()->ip();
    $data['time'] = date('d-m-Y h:i:s A');


    return $data;
}

function siteName()
{
    $general = GeneralSetting::first();
    $sitname = str_word_count($general->sitename);
    $sitnameArr = explode(' ', $general->sitename);
    if ($sitname > 1) {
        $title = "<span>$sitnameArr[0] </span> " . str_replace($sitnameArr[0], '', $general->sitename);
    } else {
        $title = "<span>$general->sitename</span>";
    }

    return $title;
}

function getPageSections($arr = false)
{

    $jsonUrl = resource_path('views/') . str_replace('.', '/', activeTemplate()) . 'sections.json';
    $sections = json_decode(file_get_contents($jsonUrl));
    if ($arr) {
        $sections = json_decode(file_get_contents($jsonUrl), true);
        ksort($sections);
    }
    return $sections;
}


function getImage($image,$size = null)
{
    $clean = '';
    if (file_exists($image) && is_file($image)) {
        return asset($image) . $clean;
    }
    if ($size) {
        return route('placeholder.image',$size);
    }
    return asset('assets/images/default.png');
}

function notify($user, $type, $shortCodes = null)
{

    sendEmail($user, $type, $shortCodes);
}

function sendEmail($user, $type = null, $shortCodes = [])
{
    $general = GeneralSetting::first();

    $emailTemplate = EmailTemplate::where('act', $type)->where('email_status', 1)->first();
    if ($general->en != 1 || !$emailTemplate) {
        return;
    }


    $message = shortCodeReplacer("{{fullname}}", $user->fullname, $general->email_template);
    $message = shortCodeReplacer("{{username}}", $user->username, $message);
    $message = shortCodeReplacer("{{message}}", $emailTemplate->email_body, $message);

    if (empty($message)) {
        $message = $emailTemplate->email_body;
    }

    foreach ($shortCodes as $code => $value) {
        $message = shortCodeReplacer('{{' . $code . '}}', $value, $message);
    }

    $config = $general->mail_config;

    $emailLog = new EmailLog();
    $emailLog->user_id = $user->id;
    $emailLog->email_expediteur = $config->name;
    $emailLog->email_de = $general->sitename.' '.$general->email_de ;
    $emailLog->email_a = $user->email;
    $emailLog->sujet = $emailTemplate->subj;
    $emailLog->message = $message;
    $emailLog->save();


    if ($config->name == 'php') {
        sendPhpMail($user->email, $user->username,$emailTemplate->subj, $message, $general);
    } else if ($config->name == 'smtp') {
        sendSmtpMail($config, $user->email, $user->username, $emailTemplate->subj, $message,$general);
    } else if ($config->name == 'sendgrid') {
        sendSendGridMail($config, $user->email, $user->username, $emailTemplate->subj, $message,$general);
    } else if ($config->name == 'mailjet') {
        sendMailjetMail($config, $user->email, $user->username, $emailTemplate->subj, $message,$general);
    }
}


function sendPhpMail($receiver_email, $receiver_name, $sujet, $message,$general)
{
    $headers = "From: $general->sitename <$general->email_de> \r\n";
    $headers .= "Reply-To: $general->sitename <$general->email_de> \r\n";
    $headers .= "MIME-Version: 1.0\r\n";
    $headers .= "Content-Type: text/html; charset=utf-8\r\n";
    @mail($receiver_email, $sujet, $message, $headers);
}


function sendSmtpMail($config, $receiver_email, $receiver_name, $sujet, $message,$general)
{
    $mail = new PHPMailer(true);

    try {
        //Server settings
        $mail->isSMTP();
        $mail->Host       = $config->host;
        $mail->SMTPAuth   = true;
        $mail->Username   = $config->username;
        $mail->Password   = $config->password;
        if ($config->enc == 'ssl') {
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
        }else{
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        }
        $mail->Port       = $config->port;
        $mail->CharSet = 'UTF-8';
        //Recipients
        $mail->setFrom($general->email_de, $general->sitename);
        $mail->addAddress($receiver_email, $receiver_name);
        $mail->addReplyTo($general->email_de, $general->sitename);
        // Content
        $mail->isHTML(true);
        $mail->Subject = $sujet;
        $mail->Body    = $message;
        $mail->send();
    } catch (Exception $e) {
        throw new Exception($e);
    }
}


function sendSendGridMail($config, $receiver_email, $receiver_name, $sujet, $message,$general)
{
    $sendgridMail = new \SendGrid\Mail\Mail();
    $sendgridMail->setFrom($general->email_de, $general->sitename);
    $sendgridMail->setSubject($sujet);
    $sendgridMail->addTo($receiver_email, $receiver_name);
    $sendgridMail->addContent("text/html", $message);
    $sendgrid = new \SendGrid($config->appkey);
    try {
        $response = $sendgrid->send($sendgridMail);
    } catch (Exception $e) {
        throw new Exception($e);
    }
}


function sendMailjetMail($config, $receiver_email, $receiver_name, $sujet, $message,$general)
{
    $mj = new \Mailjet\Client($config->public_key, $config->secret_key, true, ['version' => 'v3.1']);
    $body = [
        'Messages' => [
            [
                'From' => [
                    'Email' => $general->email_de,
                    'Name' => $general->sitename,
                ],
                'To' => [
                    [
                        'Email' => $receiver_email,
                        'Name' => $receiver_name,
                    ]
                ],
                'Subject' => $sujet,
                'TextPart' => "",
                'HTMLPart' => $message,
            ]
        ]
    ];
    $response = $mj->post(\Mailjet\Resources::$Email, ['body' => $body]);
}


function getPaginate($paginate = 20)
{
    return $paginate;
}

function paginateLinks($data, $design = 'admin.partials.paginate'){
    return $data->appends(request()->all())->links($design);
}


function menuActive($routeName, $type = null)
{
    if ($type == 3) {
        $class = 'side-menu--open';
    } elseif ($type == 2) {
        $class = 'sidebar-submenu__open';
    } else {
        $class = 'active';
    }
    if (is_array($routeName)) {
        foreach ($routeName as $key => $value) {
            if (request()->routeIs($value)) {
                return $class;
            }
        }
    } elseif (request()->routeIs($routeName)) {
        return $class;
    }
}


function imagePath()
{
    $data['gateway'] = [
        'path' => 'assets/images/gateway',
        'size' => '800x800',
    ];
    $data['verify'] = [
        'withdraw'=>[
            'path'=>'assets/images/verify/withdraw'
        ],
        'deposit'=>[
            'path'=>'assets/images/verify/deposit'
        ]
    ];
    $data['image'] = [
        'default' => 'assets/images/default.png',
    ];
    $data['withdraw'] = [
        'method' => [
            'path' => 'assets/images/withdraw/method',
            'size' => '800x800',
        ]
    ];
    $data['ticket'] = [
        'path' => 'assets/support',
    ];
    $data['language'] = [
        'path' => 'assets/images/lang',
        'size' => '64x64'
    ];
    $data['logoIcon'] = [
        'path' => 'assets/images/logoIcon',
    ];
    $data['favicon'] = [
        'size' => '128x128',
    ];
    $data['profile'] = [
        'user'=> [
            'path'=>'assets/images/user/profile',
            'size'=>'350x300'
        ],
        'admin'=> [
            'path'=>'assets/admin/images/profile',
            'size'=>'400x400'
        ]
    ];
    return $data;
}

function diffForHumans($date)
{
    $lang = session()->get('lang');
    Carbon::setlocale($lang);
    return Carbon::parse($date)->diffForHumans();
}

function showDateTime($date, $format = 'Y-m-d h:i A')
{
    $lang = session()->get('lang');
    Carbon::setlocale($lang);
    return Carbon::parse($date)->translatedFormat($format);
}

function showDayOff($val)
{
    $result = '';
    if(gettype($val) == 'array'){
        foreach($val as $value) {
            $result .= getDay($value);
        }

    }else{
        $result = getDay($val);
    }
    return $result;
}

function getDay($val){
    switch ($val) {
        case $val==6:
            $result = 'Saturday';
            break;
        case $val==0:
            $result = 'Sunday';
            break;
        case $val==1:
            $result = 'Monday';
            break;
        case $val==2:
            $result = 'Tuesday';
            break;
        case $val==3:
            $result = 'Wednesday';
            break;
        case $val==4:
            $result = 'Thursday';
            break;
        case $val==5:
            $result = 'Friday';
            break;
        default:
            $result = '';
            break;
    }
    return $result;
}

//moveable
function sendGeneralEmail($email, $sujet, $message, $receiver_name = '')
{

    $general = GeneralSetting::first();


    if ($general->en != 1 || !$general->email_de) {
        return;
    }


    $message = shortCodeReplacer("{{message}}", $message, $general->email_template);
    $message = shortCodeReplacer("{{fullname}}", $receiver_name, $message);
    $message = shortCodeReplacer("{{username}}", $email, $message);

    $config = $general->mail_config;

    if ($config->name == 'php') {
        sendPhpMail($email, $receiver_name, $sujet, $message, $general);
    } else if ($config->name == 'smtp') {
        sendSmtpMail($config, $email, $receiver_name, $sujet, $message, $general);
    } else if ($config->name == 'sendgrid') {
        sendSendGridMail($config, $email, $receiver_name,$sujet, $message,$general);
    } else if ($config->name == 'mailjet') {
        sendMailjetMail($config, $email, $receiver_name,$sujet, $message, $general);
    }
}

function getContent($data_keys, $singleQuery = false, $limit = null,$orderById = false)
{
    if ($singleQuery) {
        $content = Frontend::where('data_keys', $data_keys)->orderBy('id','desc')->first();
    } else {
        $article = Frontend::query();
        $article->when($limit != null, function ($q) use ($limit) {
            return $q->limit($limit);
        });
        if($orderById){
            $content = $article->where('data_keys', $data_keys)->orderBy('id')->get();
        }else{
            $content = $article->where('data_keys', $data_keys)->orderBy('id','desc')->get();
        }
    }
    return $content;
}


function gatewayRedirectUrl($type = false){
    if ($type) {
        return 'user.home';
    }else{
        return 'ticket';
    }
}

function getStoppageInfo($stoppages){
    $data = Counter::routeStoppages($stoppages);
    return $data;
}

function stoppageCombination($numbers, $arraySize, $level = 1, $i = 0, $addThis = [])
{
    // If this is the last layer, use a different method to pass the number.
    if ($level == $arraySize) {
        $result = [];
        for (; $i < count($numbers); $i++) {
            $result[] = array_merge($addThis, array($numbers[$i]));
        }
        return $result;
    }

    $result = [];
    $nextLevel = $level + 1;
    for (; $i < count($numbers); $i++) {
        // Add the data given from upper level to current iterated number and pass
        // the new data to a deeper level.
        $newAdd = array_merge($addThis, array($numbers[$i]));
        $temp = stoppageCombination($numbers, $arraySize, $nextLevel, $i, $newAdd);


        $result = array_merge($result, $temp);

    }

    return $result;
}


function urlPath($routeName,$routeParam=null){
    if($routeParam == null){
        $url = route($routeName);
    } else {
        $url = route($routeName,$routeParam);
    }
    $basePath = route('home');
    $path = str_replace($basePath,'',$url);
    return $path;
}
