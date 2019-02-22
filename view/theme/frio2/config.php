<?php

use Friendica\App;
use Friendica\Core\Config;
use Friendica\Core\L10n;
use Friendica\Core\PConfig;
use Friendica\Core\Renderer;
use Friendica\Core\System;

require_once 'view/theme/frio2/php/Image.php';

function theme_post(App $a)
{
	if (!local_user()) {
		return;
	}

	if (isset($_POST['frio2-settings-submit'])) {
		PConfig::set(local_user(), 'frio2', 'scheme',           defaults($_POST, 'frio2_scheme', ''));
		PConfig::set(local_user(), 'frio2', 'nav_bg',           defaults($_POST, 'frio2_nav_bg', ''));
		PConfig::set(local_user(), 'frio2', 'nav_icon_color',   defaults($_POST, 'frio2_nav_icon_color', ''));
		PConfig::set(local_user(), 'frio2', 'link_color',       defaults($_POST, 'frio2_link_color', ''));
		PConfig::set(local_user(), 'frio2', 'background_color', defaults($_POST, 'frio2_background_color', ''));
		PConfig::set(local_user(), 'frio2', 'contentbg_transp', defaults($_POST, 'frio2_contentbg_transp', ''));
		PConfig::set(local_user(), 'frio2', 'background_image', defaults($_POST, 'frio2_background_image', ''));
		PConfig::set(local_user(), 'frio2', 'bg_image_option',  defaults($_POST, 'frio2_bg_image_option', ''));
		PConfig::set(local_user(), 'frio2', 'css_modified',     time());
	}
}

function theme_admin_post(App $a)
{
	if (!local_user()) {
		return;
	}

	if (isset($_POST['frio2-settings-submit'])) {
		Config::set('frio2', 'scheme',           $_POST['frio2_scheme']);
		Config::set('frio2', 'nav_bg',           $_POST['frio2_nav_bg']);
		Config::set('frio2', 'nav_icon_color',   $_POST['frio2_nav_icon_color']);
		Config::set('frio2', 'link_color',       $_POST['frio2_link_color']);
		Config::set('frio2', 'background_color', $_POST['frio2_background_color']);
		Config::set('frio2', 'contentbg_transp', $_POST['frio2_contentbg_transp']);
		Config::set('frio2', 'background_image', $_POST['frio2_background_image']);
		Config::set('frio2', 'bg_image_option',  $_POST['frio2_bg_image_option']);
		Config::set('frio2', 'login_bg_image',   $_POST['frio2_login_bg_image']);
		Config::set('frio2', 'login_bg_color',   $_POST['frio2_login_bg_color']);
		Config::set('frio2', 'css_modified',     time());
	}
}

function theme_content(App $a)
{
	if (!local_user()) {
		return;
	}
	$arr = [];

	$arr['scheme']           = PConfig::get(local_user(), 'frio2', 'scheme', PConfig::get(local_user(), 'frio2', 'schema'));
	$arr['nav_bg']           = PConfig::get(local_user(), 'frio2', 'nav_bg');
	$arr['nav_icon_color']   = PConfig::get(local_user(), 'frio2', 'nav_icon_color');
	$arr['link_color']       = PConfig::get(local_user(), 'frio2', 'link_color');
	$arr['background_color'] = PConfig::get(local_user(), 'frio2', 'background_color');
	$arr['contentbg_transp'] = PConfig::get(local_user(), 'frio2', 'contentbg_transp');
	$arr['background_image'] = PConfig::get(local_user(), 'frio2', 'background_image');
	$arr['bg_image_option']  = PConfig::get(local_user(), 'frio2', 'bg_image_option');

	return frio2_form($arr);
}

function theme_admin(App $a)
{
	if (!local_user()) {
		return;
	}
	$arr = [];

	$arr['scheme']           = Config::get('frio2', 'scheme', Config::get('frio2', 'scheme'));
	$arr['nav_bg']           = Config::get('frio2', 'nav_bg');
	$arr['nav_icon_color']   = Config::get('frio2', 'nav_icon_color');
	$arr['link_color']       = Config::get('frio2', 'link_color');
	$arr['background_color'] = Config::get('frio2', 'background_color');
	$arr['contentbg_transp'] = Config::get('frio2', 'contentbg_transp');
	$arr['background_image'] = Config::get('frio2', 'background_image');
	$arr['bg_image_option']  = Config::get('frio2', 'bg_image_option');
	$arr['login_bg_image']   = Config::get('frio2', 'login_bg_image');
	$arr['login_bg_color']   = Config::get('frio2', 'login_bg_color');

	return frio2_form($arr);
}

function frio2_form($arr)
{
	require_once 'view/theme/frio2/php/scheme.php';

	$scheme_info = get_scheme_info($arr['scheme']);
	$disable = $scheme_info['overwrites'];
	if (!is_array($disable)) {
		$disable = [];
	}

	$scheme_choices = [];
	$scheme_choices['---'] = L10n::t('Custom');
	$files = glob('view/theme/frio2/scheme/*.php');
	if ($files) {
		foreach ($files as $file) {
			$f = basename($file, '.php');
			if ($f != 'default') {
				$scheme_name = ucfirst($f);
				$scheme_choices[$f] = $scheme_name;
			}
		}
	}

	$background_image_help = '<strong>' . L10n::t('Note') . ': </strong>' . L10n::t('Check image permissions if all users are allowed to see the image');

	$t = Renderer::getMarkupTemplate('theme_settings.tpl');
	$ctx = [
		'$submit'           => L10n::t('Submit'),
		'$baseurl'          => System::baseUrl(),
		'$title'            => L10n::t('Theme settings'),
		'$scheme'           => ['frio2_scheme', L10n::t('Select color scheme'), $arr['scheme'], '', $scheme_choices],
		'$nav_bg'           => array_key_exists('nav_bg', $disable) ? '' : ['frio2_nav_bg', L10n::t('Navigation bar background color'), $arr['nav_bg'], '', false],
		'$nav_icon_color'   => array_key_exists('nav_icon_color', $disable) ? '' : ['frio2_nav_icon_color', L10n::t('Navigation bar icon color '), $arr['nav_icon_color'], '', false],
		'$link_color'       => array_key_exists('link_color', $disable) ? '' : ['frio2_link_color', L10n::t('Link color'), $arr['link_color'], '', false],
		'$background_color' => array_key_exists('background_color', $disable) ? '' : ['frio2_background_color', L10n::t('Set the background color'), $arr['background_color'], '', false],
		'$contentbg_transp' => array_key_exists('contentbg_transp', $disable) ? '' : ['frio2_contentbg_transp', L10n::t('Content background opacity'), defaults($arr, 'contentbg_transp', 100), ''],
		'$background_image' => array_key_exists('background_image', $disable) ? '' : ['frio2_background_image', L10n::t('Set the background image'), $arr['background_image'], $background_image_help, false],
		'$bg_image_options_title' => L10n::t('Background image style'),
		'$bg_image_options' => Image::get_options($arr),
	];

	if (array_key_exists('login_bg_image', $arr) && !array_key_exists('login_bg_image', $disable)) {
		$ctx['$login_bg_image'] = ['frio2_login_bg_image', L10n::t('Login page background image'), $arr['login_bg_image'], $background_image_help, false];
	}

	if (array_key_exists('login_bg_color', $arr) && !array_key_exists('login_bg_color', $disable)) {
		$ctx['$login_bg_color'] = ['frio2_login_bg_color', L10n::t('Login page background color'), $arr['login_bg_color'], L10n::t('Leave background image and color empty for theme defaults'), false];
	}

	$o = Renderer::replaceMacros($t, $ctx);

	return $o;
}
