<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
try
{
    $bdd = new PDO('mysql:host=127.0.0.1;dbname=test;charset=utf8', 'root', 'Sakina692');
}

catch (Exception $e)
{
    die('Erreur : ' . $e->getMessage());
}
