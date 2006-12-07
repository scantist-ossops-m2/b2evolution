<?php
/**
 * This file implements the UI view for the general settings.
 *
 * This file is part of the evoCore framework - {@link http://evocore.net/}
 * See also {@link http://sourceforge.net/projects/evocms/}.
 *
 * @copyright (c)2003-2006 by Francois PLANQUE - {@link http://fplanque.net/}
 *
 * {@internal License choice
 * - If you have received this file as part of a package, please find the license.txt file in
 *   the same folder or the closest folder above for complete license terms.
 * - If you have received this file individually (e-g: from http://evocms.cvs.sourceforge.net/)
 *   then you must choose one of the following licenses before using the file:
 *   - GNU General Public License 2 (GPL) - http://www.opensource.org/licenses/gpl-license.php
 *   - Mozilla Public License 1.1 (MPL) - http://www.opensource.org/licenses/mozilla1.1.php
 * }}
 *
 * {@internal Open Source relicensing agreement:
 * }}
 *
 * @package admin
 *
 * {@internal Below is a list of authors who have contributed to design/coding of this file: }}
 * @author fplanque: Francois PLANQUE.
 * @author blueyed: Daniel HAHLER.
 */
if( !defined('EVO_MAIN_INIT') ) die( 'Please, do not access this page directly.' );

/**
 * @var User
 */
global $current_User;
/**
 * @var GeneralSettings
 */
global $Settings;

$Form = & new Form( NULL, 'settings_checkchanges' );
$Form->begin_form( 'fform', T_('General Settings'),
	// enable all form elements on submit (so values get sent):
	array( 'onsubmit'=>'var es=this.elements; for( var i=0; i < es.length; i++ ) { es[i].disabled=false; };' ) );

$Form->hidden( 'ctrl', 'settings' );
$Form->hidden( 'action', 'update' );
$Form->hidden( 'tab', 'general' );

// --------------------------------------------

$Form->begin_fieldset( T_('Display options') );

$BlogCache = & get_Cache( 'BlogCache' );
$Form->select_object( 'default_blog_ID', $Settings->get('default_blog_ID'), $BlogCache, T_('Default blog to display'),
											T_('This blog will be displayed on index.php .'), true );

$Form->end_fieldset();

// --------------------------------------------

$Form->begin_fieldset( T_('Default user permissions') );

	$Form->checkbox( 'newusers_canregister', $Settings->get('newusers_canregister'), T_('New users can register'), T_('Check to allow new users to register themselves.' ) );

	$GroupCache = & get_Cache( 'GroupCache' );
	$Form->select_object( 'newusers_grp_ID', $Settings->get('newusers_grp_ID'), $GroupCache, T_('Group for new users'), T_('Groups determine user roles and permissions.') );

	$Form->text_input( 'newusers_level', $Settings->get('newusers_level'), 1, T_('Level for new users'), array( 'note'=>T_('Levels determine hierarchy of users in blogs.' ), 'maxlength'=>1, 'required'=>true ) );

$Form->end_fieldset();

// --------------------------------------------

$Form->begin_fieldset( T_('Email validation') );

	$Form->checkbox( 'newusers_mustvalidate', $Settings->get('newusers_mustvalidate'), T_('New users must validate email'), T_('Check to require users to validate their email by clicking a link sent to them.' ) );

	$Form->checkbox( 'newusers_revalidate_emailchg', $Settings->get('newusers_revalidate_emailchg'), T_('Validate email changes'), T_('Check to require users to re-validate when they change their email address.' ) );

$Form->end_fieldset();

// --------------------------------------------

$Form->begin_fieldset( T_('Link options') );

$Form->radio( 'links_extrapath', $Settings->get('links_extrapath'),
							array(  array( 'disabled', T_('Do not use extra path info'), T_('Permalinks will look like: \'stub?title=post-title&amp;c=1&amp;tb=1&amp;pb=1&amp;more=1\'') ),
											array( 'short', T_('Only use post URL title'), T_('Permalinks will look like \'stub/post-title\'' ) ),
											array( 'y', T_('Use year'), T_('Permalinks will look like \'stub/2006/post-title\'' ) ),
											array( 'ym', T_('Use year & month'), T_('Permalinks will look like \'stub/2006/12/post-title\'' ) ),
											array( 'ymd', T_('Use year, month & day'), T_('Permalinks will look like \'stub/2006/12/31/post-title\'' ) ),
											array( 'subchap', T_('Use sub-chapter'), T_('Permalinks will look like \'stub/subchap/post-title\'' ) ),
											array( 'chapters', T_('Use chapter path'), T_('Permalinks will look like \'stub/chapter/subchap/post-title\'' ) ),
										), T_('Extra path info'), true );

$Form->radio( 'permalink_type', $Settings->get('permalink_type'),
							array(  array( 'urltitle', T_('Post called up by its URL title (Recommended)'), T_('Fallback to ID when no URL title available.') ),
											array( 'pid', T_('Post called up by its ID') ),
											array( 'archive#id', T_('Post on archive page, located by its ID') ),
											array( 'archive#title', T_('Post on archive page, located by its title (for Cafelog compatibility)') )
										), T_('Permalink type'), true );

// fp> TODO: A dynamic javascript preview of how the two settings above combine

// fp> TODO: Move both of these settings to blog/collection settings

$Form->end_fieldset();

// --------------------------------------------

$Form->begin_fieldset( T_('Security options') );

$Form->text_input( 'user_minpwdlen', (int)$Settings->get('user_minpwdlen'), 2, T_('Minimum password length'),array( 'note'=>T_('for users.'), 'maxlength'=>2, 'required'=>true ) );

$Form->end_fieldset();

// --------------------------------------------

$Form->begin_fieldset( T_('Timeouts') );

	// fp>TODO: enhance UI with a general Form method for Days:Hours:Minutes:Seconds
	$Form->text_input( 'timeout_sessions', $Settings->get('timeout_sessions'), 9, T_('Session timeout'),
		array( 'note' => T_('seconds. How long can a user stay inactive before automatic logout?'), 'required'=>true) );

	// fp>TODO: It may make sense to have a different (smaller) timeout for sessions with no logged user.
	// fp>This might reduce the size of the Sessions table. But this needs to be checked against the hit logging feature.

	$Form->text_input( 'reloadpage_timeout', (int)$Settings->get('reloadpage_timeout'), 5,
								T_('Reload-page timeout'), array( 'note'=>T_('Time (in seconds) that must pass before a request to the same URI from the same IP and useragent is considered as a new hit.'), 'maxlength'=>5, 'required'=>true ) );

$Form->end_fieldset();

// --------------------------------------------

if( $current_User->check_perm( 'options', 'edit' ) )
{
	$Form->end_form( array( array( 'submit', 'submit', T_('Save !'), 'SaveButton' ),
													array( 'reset', '', T_('Reset'), 'ResetButton' ) ) );
}

/*
 * $Log$
 * Revision 1.23  2006/12/07 00:55:52  fplanque
 * reorganized some settings
 *
 * Revision 1.22  2006/12/06 22:30:08  fplanque
 * Fixed this use case:
 * Users cannot register themselves.
 * Admin creates users that are validated by default. (they don't have to validate)
 * Admin can invalidate a user. (his email, address actually)
 *
 * Revision 1.21  2006/12/04 19:41:11  fplanque
 * Each blog can now have its own "archive mode" settings
 *
 * Revision 1.20  2006/12/04 18:16:51  fplanque
 * Each blog can now have its own "number of page/days to display" settings
 *
 * Revision 1.19  2006/12/03 01:25:49  blueyed
 * Use & instead of &amp; when it gets encoded for output
 *
 * Revision 1.18  2006/11/26 01:37:30  fplanque
 * The URLs are meant to be translated!
 *
 * Revision 1.17  2006/11/24 18:27:26  blueyed
 * Fixed link to b2evo CVS browsing interface in file docblocks
 *
 * Revision 1.15  2006/09/11 19:35:35  fplanque
 * minor
 *
 * Revision 1.14  2006/09/10 23:40:47  fplanque
 * minor
 *
 * Revision 1.12  2006/09/10 20:59:18  fplanque
 * extended extra path info setting
 *
 * Revision 1.11  2006/08/19 08:50:26  fplanque
 * moved out some more stuff from main
 *
 * Revision 1.10  2006/08/19 07:56:31  fplanque
 * Moved a lot of stuff out of the automatic instanciation in _main.inc
 *
 * Revision 1.9  2006/06/15 17:53:38  fplanque
 * minor
 *
 * Revision 1.8  2006/04/27 18:31:06  fplanque
 * no message
 *
 * Revision 1.7  2006/04/24 18:12:54  blueyed
 * Added Setting to invalidate a user account on email address change.
 *
 * Revision 1.6  2006/04/24 17:22:50  blueyed
 * Do not JS-disable options according to "newusers_canregister"
 *
 * Revision 1.5  2006/04/24 15:43:36  fplanque
 * no message
 *
 * Revision 1.4  2006/04/22 03:12:35  blueyed
 * cleanup
 *
 * Revision 1.3  2006/04/22 02:36:38  blueyed
 * Validate users on registration through email link (+cleanup around it)
 *
 * Revision 1.2  2006/04/19 20:13:52  fplanque
 * do not restrict to :// (does not catch subdomains, not even www.)
 *
 */
?>