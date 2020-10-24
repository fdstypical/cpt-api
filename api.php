<?php
session_start();

ini_set('error_reporting', E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

require_once "./libs/DataBase/autoload.php";
require_once "./libs/simple-api/autoload.php";

use DigitalStars\DataBase\DB;
use DigitalStars\SimpleAPI;

header('Access-Control-Expose-Headers: Access-Control-Allow-Origin', false);
header('Access-Control-Allow-Origin: *', false);
header('Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept', false);
header('Access-Control-Allow-Credentials: true');

$db_type = 'mysql';
$db_name = 'learner13';
$login = 'learner13';
$pass = 'taFV4UVFFDiuebeCAu8i';
$ip = 'localhost';

$db = new DB("$db_type:host=$ip;dbname=$db_name", $login, $pass,
    [PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'utf8'"]
);

$api = new SimpleAPI();
switch ($api->module) {
    case 'auth':
        $data = $api->params(['login', 'password', 'name']);
        
        $res = $db->row('SELECT * FROM users WHERE login=?s and password=?s',
            [$data['login'], $data['password']]
        );

        $api->answer['res'] = $res;
        break;
    case 'reg':
        $data = $api->params(['login', 'password', 'name']);
        
        $res = $db->query('INSERT INTO users (login, name, password) VALUES (?as)', [
            [$data['login'], $data['name'], $data['password']]
        ]);

        $answer = [
            'status' => ($res) ? 200 : 409,
            'status_msg' => ($res) ? "Ok" : "User Already Exists",
        ];

        $api->answer['res'] = $answer;
        break;
    case 'operator':
        $data = $api->params(['operator', 'valueDown', 'valueUp']);
        $expression = $data['valueUp'] . $data['operator'] . $data['valueDown'];
        $api->answer['result'] = eval('return ' . $expression . ';');

        break;
}