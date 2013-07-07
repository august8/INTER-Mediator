<?php
/**
 * Created by JetBrains PhpStorm.
 * User: msyk
 * Date: 2013/07/06
 * Time: 10:01
 * To change this template use File | Settings | File Templates.
 */

require_once('../INTER-Mediator/INTER-Mediator.php');
require_once('../INTER-Mediator/DB_Interfaces.php');
require_once('../INTER-Mediator/DB_Logger.php');
require_once('../INTER-Mediator/DB_Settings.php');
require_once('../INTER-Mediator/DB_UseSharedObjects.php');
require_once('../INTER-Mediator/DB_Proxy.php');
require_once('../INTER-Mediator/DB_Formatters.php');
require_once('../INTER-Mediator/DB_AuthCommon.php');

class INTERMediator_Test extends PHPUnit_Framework_TestCase
{
    function setUp()
    {
        mb_internal_encoding('UTF-8');
        date_default_timezone_set('Asia/Tokyo');

        $this->db_proxy = new DB_Proxy(true);
        $this->db_proxy->initialize(array(),
            array(
                'authentication' => array( // table only, for all operations
                    'user' => array('user1'), // Itemize permitted users
                    'group' => array('group2'), // Itemize permitted groups
                    'privilege' => array(), // Itemize permitted privileges
                    'user-table' => 'authuser', // Default values, or "_Native"
                    'group-table' => 'authgroup',
                    'corresponding-table' => 'authcor',
                    'challenge-table' => 'issuedhash',
                    'authexpired' => '300', // Set as seconds.
                    'storing' => 'cookie-domainwide', // 'cookie'(default), 'cookie-domainwide', 'none'
                ),
            ),
            array(
                'db-class' => 'PDO',
                'dsn' => 'mysql:dbname=test_db;host=127.0.0.1',
                'user' => 'web',
                'password' => 'password',
            ),
            false);
    }

    function test_hex2bin_for53()    {
        $testName = "Check hex2bin_for53 function in INTER-Mediator.php.";

        $hexString = "616263643132333441424344242526";
        $binaryString = "abcd1234ABCD$%&";

        $this->assertTrue(hex2bin_for53($hexString) === $binaryString, $testName);

        $version = explode('.', PHP_VERSION);
        if ( $version[0] >= 5 && $version[1] >= 4 ) {
           $this->assertTrue(hex2bin_for53($hexString) === hex2bin($hexString), $testName);
        }


    }

    function test_randomString()    {
        $testName = "Check randamString function in INTER-Mediator.php.";
        $str = randomString(10);
        $this->assertTrue(is_string($str), $testName);
        $this->assertTrue(strlen($str) == 10, $testName);
        $str = randomString(100);
        $this->assertTrue(is_string($str), $testName);
        $this->assertTrue(strlen($str) == 100, $testName);
        $str = randomString(1000);
        $this->assertTrue(is_string($str), $testName);
        $this->assertTrue(strlen($str) == 1000, $testName);
        $str = randomString(0);
        $this->assertTrue(is_string($str), $testName);
        $this->assertTrue(strlen($str) == 0, $testName);

    }
/*
function IM_Entry($datasource, $options, $dbspecification, $debug = false)
function loadClass($className)
function valueForJSInsert($str)
function arrayToJS($ar, $prefix)
function arrayToJSExcluding($ar, $prefix, $exarray)
function arrayToQuery($ar, $prefix)
function getRelativePath()
function setLocaleAsBrowser($locType)
function getLocaleFromBrowser()
function hex2bin_for53($str)
*/
}