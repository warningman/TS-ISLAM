<?php

 function getConfigValue($configClass,$configName) {
    global $botid, $config;
    return $config[$botid][$configClass][$configName];
 }

 function checkConfigs() { // There you can add new config checks to your bot interface
    global $lang;
    $errors = 0;
    // Global Config Checker
    if (!preg_match('/[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}/',getConfigValue('connection','server_ip'))) {
        echo "\n".$lang->getLanguage('MNG_ERROR')." ".$lang->getLanguage('NOT_VALID_IP')."\n";
        $errors++;
    }
    if (!is_numeric(getConfigValue('connection','server_query_port')) || !strlen(getConfigValue('connection','server_query_port')) > 5) {
        echo "\n".$lang->getLanguage('MNG_ERROR')." ".$lang->getLanguage('NOT_VALID_PORT')."\n";
        $errors++;
    }
    if (!is_numeric(getConfigValue('connection','server_id'))) {
        echo "\n".$lang->getLanguage('MNG_ERROR')." ".$lang->getLanguage('NOD_VALID_ID')."\n";
        $errors++;
    }
    if ($errors > 0)
        exit;
    return true;
 }

 function reloadConfig() {
    unset($config);
    include('config/config.php');
    return true;
 }

 function getPluginConfigValue($pluginName) {
    global $botid, $config;
    if (array_key_exists($pluginName, $config[$botid]['plugins']['plugins_configs']))
        return $config[$botid]['plugins']['plugins_configs'][$pluginName];
    else
        return false;
 }

function getAccesoriesConfigValue($accesoriesName) {
    global $botid, $config;
    if (array_key_exists($accesoriesName, $config[$botid]['accesories']['accesories_configs']))
        return $config[$botid]['accesories']['accesories_configs'][$accesoriesName];
    else
        return false;
 }
 
 
 function getCommandConfigValue($commandName) {
    global $botid, $config;
    if (array_key_exists($commandName, $config[$botid]['commands']['commands_configs']))
        return $config[$botid]['commands']['commands_configs'][$commandName];
    else
        return false;
 }

 function getEventConfigValue($eventName) {
    global $botid, $config;
    if (array_key_exists($eventName, $config[$botid]['events']['events_configs']))
        return $config[$botid]['events']['events_configs'][$eventName];
    else
        return false;
 }

  function getEventTimeInfo($eventName) {
    global $botid, $config;
    if (true)
        return array('seconds' => 0,'minutes' => 1, 'hours' => 0, 'days' => 0);
    else
        return false;
 }

?>