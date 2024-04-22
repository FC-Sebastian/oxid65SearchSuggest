<?php

/**
 * Metadata version
 */
$sMetadataVersion = '2.1';

/**
 * Module information
 */
$aModule = [
    'id'          => 'fcsearchsuggest',
    'title'       => [
        'de' => 'Suchvorschläge',
        'en' => 'Search suggestions'
    ],
    'description' => [
        'de' => 'Schlägt passende Artikel bei Suchen vor',
        'en' => 'Recommends fitting products during searches'
    ],
    'version'     => '1.0',
    'author'      => 'Ich',
    'blocks'      => [
        [
            'template' => 'widget/header/search.tpl',
            'block'    => 'widget_header_search_form',
            'file'     => 'out/blocks/widget/header/widget_header_search_form.tpl'
        ],
    ],
    'extend' => [
    ],
    'controllers' => [
        'fc_search_suggest' => \FATCHIP\SearchSuggest\Application\Controller\SearchSuggestionsAjax::class
    ],
    'templates' => [
    ]
];