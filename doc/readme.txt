eZ subtree relation filter extension for eZ publish
----------------------------------------------------

Extended attribute filter to get all objects (not) reverse related to any object in a specific subtree.
It also works in the content/tree_count fetch function as of eZ publish 3.9.

Usage example
--------------

The project content class has one object relations attribute to select topics from another subtree. On the full view 
of a specific topic in that subtree, we'd like to display a list of all projects with the current topic or with any topics beneath.


{def $extendedAttributeFilter=hash( 'id', 'SubTreeRelationFilter',
                                    'params', array( 'project/component_types', $node.path_identification_string ) )
     $project_count=fetch( 'content', 'tree_count', hash( 'parent_node_id', 2,
                                           'class_filter_type', 'include',
                                           'class_filter_array', array( 'project' ),
                                           'extended_attribute_filter', $extendedAttributeFilter
                                          ) )
     $projects=fetch( 'content', 'tree', hash( 'parent_node_id', 2,
                                           'class_filter_type', 'include',
                                           'class_filter_array', array( 'project' ),
                                           'extended_attribute_filter', $extendedAttributeFilter,
                                           'offset', $view_parameters.offset,
                                           'limit', 10
                                          ) )}


...

{undef $extendedAttributeFilter
	 $project_count
	 $projects}