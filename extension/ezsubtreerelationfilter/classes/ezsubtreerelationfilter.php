<?php

class eZSubTreeRelationFilter
{
    function eZSubTreeRelationFilter()
    {
    }

    function createSqlParts( $params )
    {
        $attributeID = $params[0];
        if ( !is_numeric( $attributeID ) )
        {
            $attributeID = eZContentObjectTreeNode::classAttributeIDByIdentifier( $attributeID );

            if ( $attributeID === false )
            {
                eZDebug::writeError( 'Unknown attribute identifier: ' . $attributeID, 'eZSubtreeRelationFilter::sqlParams()' );
                return array( 'tables' => '', 'joins' => '', 'columns' => '' );
            }
        }

        include_once( 'lib/ezdb/classes/ezdb.php' );
        $db = eZDB::instance();
        $pathIdentificationString = $db->escapeString( $params[1] );

        $subSelect = "SELECT from_contentobject_id
            FROM ezcontentobject_link l0, ezcontentobject_tree t0
            WHERE l0.from_contentobject_id = ezcontentobject_tree.contentobject_id AND
                  l0.from_contentobject_version = ezcontentobject_tree.contentobject_version AND
                  l0.contentclassattribute_id = $attributeID AND
                  l0.to_contentobject_id = t0.contentobject_id AND
                  (
                      t0.path_identification_string='$pathIdentificationString' OR
                      t0.path_identification_string LIKE '$pathIdentificationString/%'
                  )";

        $negate = ( count( $params ) > 2 && is_bool( $params[2] ) ) ? $params[2] : false;
        if ( $negate )
        {
            $joins = "ezcontentobject_tree.contentobject_id NOT IN ( $subSelect ) AND";
        }
        else
        {
            $joins = "ezcontentobject_tree.contentobject_id IN ( $subSelect ) AND";
        }

        return array( 'tables' => '', 'joins'  => $joins, 'columns' => '' );
    }
}

?>