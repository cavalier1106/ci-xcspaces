<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/*
| -------------------------------------------------------------------------
| URI ROUTING
| -------------------------------------------------------------------------
| This file lets you re-map URI requests to specific controller functions.
|
| Typically there is a one-to-one relationship between a URL string
| and its corresponding controller class/method. The segments in a
| URL normally follow this pattern:
|
|	example.com/class/method/id/
|
| In some instances, however, you may want to remap this relationship
| so that a different class/function is called than the one
| corresponding to the URL.
|
| Please see the user guide for complete details:
|
|	http://codeigniter.com/user_guide/general/routing.html
|
| -------------------------------------------------------------------------
| RESERVED ROUTES
| -------------------------------------------------------------------------
|
| There area two reserved routes:
|
|	$route['default_controller'] = 'welcome';
|
| This route indicates which controller class should be loaded if the
| URI contains no data. In the above example, the "welcome" class
| would be loaded.
|
|	$route['404_override'] = 'errors/page_missing';
|
| This route will tell the Router what URI segments to use if those provided
| in the URL cannot be matched to a valid route.
|
*/

$route['default_controller'] = "xcspace";
$route['404_override'] = '';
// $route['(:any)'] = "m/$1";
$route['go/(.+)'] = "xcspace/go/$1";
$route['detail/(.+)'] = "xcspace/detail/$1";
$route['noteDetail/(.+)'] = "xcspace/noteDetail/$1";
$route['unfavorite/(.+)'] = "xcspace/unfavorite/$1";
$route['favorite/(.+)'] = "xcspace/favorite/$1";
$route['thanksReply/(.+)'] = "xcspace/thanksReply/$1";
$route['noteDetail/(.+)'] = "xcspace/noteDetail/$1";
$route['delTopic/(\d+)'] = "xcspace/delTopic/$1";
// $route['upload'] = "xcspace/upload";
$route['createNewTitle'] = "/xcspace/createNewTitle";
$route['createNewTitle_ajax'] = "xcspace/createNewTitle_ajax";
$route['createNotepad'] = "xcspace/createNotepad";
$route['createNotepad_ajax'] = "xcspace/createNotepad_ajax";
$route['detail_ajax'] = "xcspace/detail_ajax";
$route['coin_ajax'] = "xcspace/coin_ajax";
$route['feedback'] = "xcspace/feedback";
$route['go_ajax'] = "xcspace/go_ajax";
$route['more'] = "/xcspace/more";
$route['more/new'] = "/xcspace/more/new";
$route['more/hot'] = "/xcspace/more/hot";
$route['search'] = "xcspace/search";
$route['donation'] = "xcspace/donation";
$route['tnodes'] = "xcspace/tnodes";
$route['collectionNodes/(\d+)'] = "xcspace/collectionNodes/$1";
$route['collectionTopics/(\d+)'] = "xcspace/collectionTopics/$1";
$route['focusMember/(\d+)'] = "xcspace/focusMember/$1";
$route['focusMemberTopics/(\d+)'] = "xcspace/focusMemberTopics/$1";
$route['getTechTopic'] = "/xcspace/getTechTopic";


/* End of file routes.php */
/* Location: ./application/config/routes.php */
