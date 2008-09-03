<?php
//
// ## BEGIN COPYRIGHT, LICENSE AND WARRANTY NOTICE ##
// SOFTWARE NAME: eZ Publish Subtree Relation Filter extension
// SOFTWARE RELEASE: 2.x
// COPYRIGHT NOTICE: Copyright (C) 2007-2008 Kristof Coomans <http://blog.kristofcoomans.be>
// SOFTWARE LICENSE: GNU General Public License v2.0
// NOTICE: >
//   This program is free software; you can redistribute it and/or
//   modify it under the terms of version 2.0  of the GNU General
//   Public License as published by the Free Software Foundation.
//
//   This program is distributed in the hope that it will be useful,
//   but WITHOUT ANY WARRANTY; without even the implied warranty of
//   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
//   GNU General Public License for more details.
//
//   You should have received a copy of version 2.0 of the GNU General
//   Public License along with this program; if not, write to the Free
//   Software Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston,
//   MA 02110-1301, USA.
//
//
// ## END COPYRIGHT, LICENSE AND WARRANTY NOTICE ##
//

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