<?php
define("DEBUG_PLT", microtime(true));
define('MCR', '');

require_once("system.php");

$core->def_header = $core->sp(MCR_THEME_PATH."header.html");

$mode = (isset($_GET['mode'])) ? $_GET['mode'] : $core->config->main['s_dpage'];

if($core->config->main['install']){ $core->notify('Внимание!', 'Необходима установка', 4, 'install/'); }

switch($mode){
	case 'news':		$content = $core->load_def_mode($mode); break;
	case 'search':		$content = $core->load_def_mode($mode); break;
	case 'auth':		$content = $core->load_def_mode($mode); break;
	case 'register':	$content = $core->load_def_mode($mode); break;
	case 'profile':		$content = $core->load_def_mode($mode); break;
	case 'restore':		$content = $core->load_def_mode($mode); break;
	case 'ajax':		$content = $core->load_def_mode($mode); break;
	case 'statics':		$content = $core->load_def_mode($mode); break;

	case '403':
		$core->title = $core->lng['t_403'];
		$content = $core->sp(MCR_THEME_PATH."default_sp/403.html");
	break;

	default:
		$content = $core->load_mode($mode);
	break;
}

$data_global = array(
	"CONTENT"		=> $content,
	"TITLE"			=> $core->title,
	"L_BLOCKS"		=> $core->load_def_blocks(),
	"HEADER"		=> $core->header,
	"DEF_HEADER"	=> $core->def_header,
	"CFG"			=> $core->config->main,
	"ADVICE"		=> $core->advice(),
	"MENU"			=> $core->menu->_list(),
	"BREADCRUMBS"	=> $core->bc,
	"SEARCH"		=> $core->search()
);

// Write global template
echo $core->sp(MCR_THEME_PATH."global.html", $data_global);

if(!$core->config->main['debug'] || !@$core->user->permissions->sys_debug){ exit; }

$data_debug = array(
	"PLT" => number_format(microtime(true)-DEBUG_PLT,3),
	"QUERIES" => $core->db->count_queries,
	"MEMORY_USAGE" => intval(memory_get_usage()/1024),
	"MEMORY_PEAK" => intval(memory_get_peak_usage()/1024),
	"BASE_ERROR" => $core->db->error(),
	"PHP_ERROR" => error_get_last()
);

echo $core->sp(MCR_THEME_PATH."debug.html", $data_debug);
?>