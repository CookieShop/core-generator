<?php
return [
    [
        'name' => 'create skeleton basic repo [<repo>] package [<package>] namespace [<namespace>]',
        'description' => 'Show welcome message',
        'short_description' => 'Show welcome message',
        'handler' => 'Adteam\Core\Generator\Component',
    ],    
    [
        'name' => 'create skeleton cli basic repo [<repo>] package [<package>] namespace [<namespace>]',
        'description' => 'Show welcome message',
        'short_description' => 'Show welcome message',
        'handler' => 'Adteam\Core\Generator\Clicomponent',
    ],   
    [
        'name' => 'create skeleton doctrine repo [<repo>] package [<package>] namespace [<namespace>]',
        'description' => 'Show welcome message',
        'short_description' => 'Show welcome message',
        'handler' => 'Adteam\Core\Generator\Doctrinecomponent',
    ],  
    [
        'name' => 'create skeleton api repo [<repo>] package [<package>] namespace [<namespace>] service [<service>]',
        'description' => 'Show welcome message',
        'short_description' => 'Show welcome message',
        'handler' => 'Adteam\Core\Generator\Apicomponent',
    ]     
];
