<?php

$Module = $Params['Module'];
$offset = $Params['Offset'];

$listLimitPreferenceName = 'admin_state_group_list_limit';
$listLimitPreferenceValue = eZPreferences::value( $listLimitPreferenceName );

switch( $listLimitPreferenceValue )
{
    case '2': { $limit = 25; } break;
    case '3': { $limit = 50; } break;
    default:  { $limit = 10; } break;
}

$languages = eZContentLanguage::fetchList();

require_once( 'kernel/common/template.php' );

$tpl = templateInit();

eZDebug::writeDebug( $Module->currentAction() );
if ( $Module->isCurrentAction( 'Remove' ) && $Module->hasActionParameter( 'RemoveIDList' ) )
{
    $removeIDList = $Module->actionParameter( 'RemoveIDList' );

    foreach ( $removeIDList as $removeID )
    {
        $group = eZContentObjectStateGroup::fetchById( $removeID );
        if ( $group && !$group->isInternal() )
        {
            eZContentObjectStateGroup::removeByID( $removeID );
        }
    }
}
else if ( $Module->isCurrentAction( 'Create' ) )
{
    return $Module->redirectTo( 'state/group_edit' );
}

$groups = eZContentObjectStateGroup::fetchByOffset( $limit, $offset );
$groupCount = eZPersistentObject::count( eZContentObjectStateGroup::definition() );

$viewParameters = array( 'offset' => $offset );

$tpl->setVariable( 'limit', $limit );
$tpl->setVariable( 'list_limit_preference_name', $listLimitPreferenceName );
$tpl->setVariable( 'list_limit_preference_value', $listLimitPreferenceValue );
$tpl->setVariable( 'groups', $groups );
$tpl->setVariable( 'group_count', $groupCount );
$tpl->setVariable( 'view_parameters', $viewParameters );
$tpl->setVariable( 'languages', $languages );

$Result = array(
    'content' => $tpl->fetch( 'design:state/groups.tpl' ),
    'path'    => array(
        array( 'url' => false, 'text' => ezi18n( 'kernel/state', 'State' ) ),
        array( 'url' => false, 'text' => ezi18n( 'kernel/state', 'Groups' ) )
    )
);

?>