<?php
/**
 * File containing the eZXMLExportTranslationsExporter class
 *
 * @copyright Copyright (C) 1999-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/gnu_gpl GNU GPLv2
 * @package ezxmlexport
 *
 */

class eZXMLExportTranslationsExporter extends eZXMLExportExporter
{
    /**
     * Instruct the export to export an content object attribute
     *
     * @param  eZContentObjectAttribute $contentObjectAttribute The eZContentObjectAttribute to export
     * @return void
     */
    public function exportAttribute( eZContentObjectAttribute $contentObjectAttribute, $lang = false )
    {
        if( !$contentObjectAttribute instanceof eZContentObjectAttribute )
        {
            return false;
        }

        if( eZXMLExportAvailableContentClassAttributes::isExportable( $contentObjectAttribute->attribute( 'contentclassattribute_id' ) ) )
        {
            $className = $contentObjectAttribute->attribute('data_type_string') . 'xmlexport';

            $fileToInclude = 'extension/ezxmlexport/classes/datatypes/'
                            . $contentObjectAttribute->attribute( 'data_type_string')
                            . '/'
                            . $className . '.php';

            if( !file_exists( $fileToInclude ) )
            {
                return;
            }

            include_once( $fileToInclude );

            $contentClassAttribute = eZContentClassAttribute::fetch( $contentObjectAttribute->attribute( 'contentclassattribute_id' ) );
            $xmlSchemaDatatype     = new $className( $contentClassAttribute );

            $this->XMLResultArray['objects'][$this->CurrentObjectKey]['attributes'][$lang][] = $xmlSchemaDatatype->xmlize( $contentObjectAttribute );
        }
    }
    
    public static function exportContentObjectsWithTranslations( eZXMLExportTranslationsExporter $eZXMLExporter, array $exportableNodeList, $offset = null, $limit = null )
    {
        foreach( $exportableNodeList as $classIdentifier => $childExportableNodeList )
        {
            foreach( $childExportableNodeList as $exportableNode )
            {
                $contentObject   = $exportableNode->object();
                $contentObjectID = $contentObject->attribute( 'id' );
    
                if( in_array( $contentObjectID, $eZXMLExporter->AlreadyExportedOjectIDList ) )
                {
                    continue;
                }
    
                $eZXMLExporter->objectExportStart( $contentObject );
    
                $contentObjectExportStartTime = time();
    
                $localizedAttributeList = self::organizeDataMap( $eZXMLExporter, $contentObject );
    
                foreach( $localizedAttributeList as $lang => $attributeList )
                {
                    foreach( $attributeList as $contentObjectAttribute )
                    {
                        $eZXMLExporter->exportAttribute( $contentObjectAttribute, $lang );
                    }
                }
    
                $eZXMLExporter->objectExportEnd( $contentObjectID,
                                                 $contentObjectExportStartTime,
                                                 time() );
            }
        }
    }
    
    /**
     * Copy of organizeDataMap original function, supporting translations
     * @param eZXMLExportTranslationsExporter$eZXMLExporter
     * @param eZContentObject $contentObject
     */
    public static function organizeDataMap( eZXMLExportTranslationsExporter $eZXMLExporter, eZContentObject $contentObject )
    {
        // I can not use the native dataMap directly
        // as attribute must be ordered the way they
        // have been in the XML Schema definition
        $aLangs = $contentObject->availableLanguages();
        $rearrangedDataMap = array();
        
        foreach( $aLangs as $lang )
        {
            $originalDataMap   = $contentObject->fetchDataMap( false, $lang );
            foreach( $eZXMLExporter->ExportableContentClasses as $exportableContentClass )
            {
                if( $exportableContentClass['contentclass_id'] != $contentObject->attribute( 'contentclass_id' ) )
                {
                    continue;
                }
        
                foreach( $exportableContentClass['attribute_id_list'] as $attributeID )
                {
                    $rearrangedDataMap[$lang][] = self::fetchAttribute( $originalDataMap, $attributeID );
                }
            }
        }
    
        return $rearrangedDataMap;
    }
    
    public static function fetchAttribute( $dataMap, $contentClassAttributeID )
    {
        foreach( $dataMap as $eZContentObjectAttribute )
        {
            if( $eZContentObjectAttribute->attribute( 'contentclassattribute_id' ) == $contentClassAttributeID )
            {
                return $eZContentObjectAttribute;
            }
        }
    
        // there is almost no chances
        // to reach this line
        return false;
    }
    
    /**
     * Extracts the object list from the XML result array
     * with all translations
     *
     * @return array The object list
     */
    protected function extractObjectList()
    {
        $objectList = '';

        if( !isset( $this->XMLResultArray['objects'] ) )
        {
            return $objectList;
        }

        foreach( $this->XMLResultArray['objects'] as $contentObjectDataList => $contentObjectData )
        {
            $objectInfoAttributeGroup = '';

            foreach( $contentObjectData['object_info'] as $attributeName => $attributeValue )
            {
                $objectInfoAttributeGroup .= $attributeName . '="' . $attributeValue . '" ';
            }

            $contentObjectID = $contentObjectData['external_meta_data']['contentobject_id'];

            $tagName = $contentObjectData['external_meta_data']['class_identifier'];

            $objectMetaDataCustomAttribute = $this->getObjectMetaDataCustomTag( $contentObjectData['ezobject_custom_meta_data'], $contentObjectID );

            $objectList .= '<' . $tagName . ' ' . $objectInfoAttributeGroup. '>'
                           . $objectMetaDataCustomAttribute;
                           
            $aTranslations = array();
            foreach( $contentObjectData['attributes'] as $lang => $translatedAttributes )
            {
                $translationTag  = '<translation lang="'.$lang.'">';
                $translationTag .= join( "\n", $translatedAttributes );
                $translationTag .= '</translation>';
                $aTranslations[] = $translationTag;
            }
                           
            $objectList .= '<translations>'
                           . join( "\n", $aTranslations )
                           . '</translations>'
                           . '</' . $tagName . '>';
        }

        $this->RelatedObjectList = array();

        return $objectList;
    }
}
?>