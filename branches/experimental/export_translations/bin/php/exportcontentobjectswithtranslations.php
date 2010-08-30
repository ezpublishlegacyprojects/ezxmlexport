<?php

require 'autoload.php';
include( 'extension/ezxmlexport/functions/functions.php' );

$cli = eZCLI::instance();

$script = eZScript::instance( array( 'description' => ( "Export Content Objects with all translations\n" .
                                                        "exportcontentobjectswithtranslations.php"),
                                     'use-session' => true,
                                     'use-modules' => true,
                                     'use-extensions' => true ) );

$script->startup();

$options = $script->getOptions( "[export-id:][exportable-node-list:][offset:][limit:][pid:]",
                                "",
                                array( 'export-id'             => "exportID",
                                        'exportable-node-list' => "List of exportable nodes",
                                        'offset'               => "Offset",
                                        'limit'                => "Limit",
                                        'pid'                  => 'PID' ) );

$script->initialize();

$offset = $options['offset'];
$limit  = $options['limit'];
$pid    = $options['pid'];

$eZXMLExporter = getPersistentVariable( $pid );

$exportableNodeList = $eZXMLExporter->fetchExportableNodes( $offset, $limit );
eZXMLExportTranslationsExporter::exportContentObjectsWithTranslations( $eZXMLExporter, $exportableNodeList );

storePersistentVariable( $eZXMLExporter, $pid );

$script->shutdown();

?>
