<?php
/*	Project:	EQdkp-Plus
 *	Package:	World of Warcraft game package
 *	Link:		http://eqdkp-plus.eu
 *
 *	Copyright (C) 2006-2016 EQdkp-Plus Developer Team
 *
 *	This program is free software: you can redistribute it and/or modify
 *	it under the terms of the GNU Affero General Public License as published
 *	by the Free Software Foundation, either version 3 of the License, or
 *	(at your option) any later version.
 *
 *	This program is distributed in the hope that it will be useful,
 *	but WITHOUT ANY WARRANTY; without even the implied warranty of
 *	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *	GNU Affero General Public License for more details.
 *
 *	You should have received a copy of the GNU Affero General Public License
 *	along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

if (!defined('EQDKP_INC')){
	die('Do not access this file directly.');
}

/*+----------------------------------------------------------------------------
  | pdh_r_wow
  +--------------------------------------------------------------------------*/
if (!class_exists('pdh_r_wowclassic')) {
	class pdh_r_wowclassic extends pdh_r_generic {

		/**
		* Data array loaded by initialize
		*/
		private $data;
		private $guilddata;

		/**
		* Hook array
		*/
		public $hooks = array(
			'member_update',
		);

		/**
		* Presets array
		*/
		public $presets = array(
			'wowclassic_charicon'			=> array('charicon', array('%member_id%'),			array()),
			'wowclassic_profiler'			=> array('profilers', array('%member_id%'),			array()),
		);

		/**
		* Constructor
		*/
		public function __construct(){
		}
	
		public function reset(){
		}

		/**
		* init
		*
		* @returns boolean
		*/
		public function init(){
		
		}

		public function get_charicon($member_id){
			$membername = $this->pdh->get('member', 'name', array($member_id));
			$char_server	= $this->pdh->get('member', 'profile_field', array($member_id, 'servername'));
			$servername		= ($char_server != '') ? $char_server : $this->config->get('servername');
			
			$a = $this->pdh->get('member', 'profiledata', array($member_id));		
			$race = (int)$a['race'];			
			$intGender = ($a['gender'] == 'female') ? 1 : 0;
			
			
			$strPicture = $this->pdh->get('member', 'picture', array($member_id));
			if (!strlen($strPicture)){
				$strImg = $this->server_path.'games/wowclassic/avatars/'.$race.'-'.$intGender.'.jpg';
			} else {
				$strImg = str_replace($this->root_path, $this->server_path, $strPicture);
			}
			return $strImg;
		}

		public function get_html_charicon($member_id){
			$charicon = $this->get_charicon($member_id);
			if ($charicon == '') {
				$charicon = $this->server_path.'images/global/avatar-default.svg';
			}
			return '<img src="'.$charicon.'" alt="Char-Icon" height="48" class="gameicon"/>';
		}


		public function get_profilers($member_id){
			$membername		= $this->pdh->get('member', 'name', array($member_id));
			$char_server	= $this->pdh->get('member', 'profile_field', array($member_id, 'servername'));
			$servername		= ($char_server != '') ? $char_server : $this->config->get('servername');
			
			$output			= '';
			$a_profilers	= array(
					1	=> array(
							'icon'	=> $this->server_path.'games/wowclassic/profiles/profilers/wowicon.png',
							'name'	=> 'worldofwarcraft.com',
							'url'	=> $this->bnlink(unsanitize($membername), unsanitize($servername), 'char')
					),
					5	=> array(
							'icon'	=> $this->server_path.'games/wowclassic/profiles/profilers/warcraftlogs.png',
							'name'	=> 'Warcraftlogs',
							'url'	=> $this->bnlink(unsanitize($membername), unsanitize($servername), 'warcraftlogs')
					),		
			);
			
			
			if(is_array($a_profilers)){
				foreach($a_profilers as $v_profiler){
					$output	.= '<a href="'.$v_profiler['url'].'"><img src="'.$v_profiler['icon'].'" alt="'.$v_profiler['name'].'" height="22" class="gameicon"/></a> '; 
				}
			}
			return $output;
		}
		
		public function bnlink($user, $server, $mode='char', $guild='', $talents=array()){
			switch ($mode) {
				case 'char':
					return $this->getProfileULR().sprintf('character/%s/%s', $this->ConvertInput($server, true, true), $this->ConvertInput($user));break;
				case 'reputation':
					return $this->getProfileULR().sprintf('character/%s/%s/reputation', $this->ConvertInput($server, true, true), $this->ConvertInput($user));break;
				case 'pvp':
					return $this->getProfileULR().sprintf('character/%s/%s/pvp', $this->ConvertInput($server, true, true), $this->ConvertInput($user));break;
				case 'pve':
					return $this->getProfileULR().sprintf('character/%s/%s/pve', $this->ConvertInput($server, true, true), $this->ConvertInput($user));break;
				case 'achievements':
					return $this->getProfileULR().sprintf('character/%s/%s/achievements', $this->ConvertInput($server, true, true), $this->ConvertInput($user));break;
				case 'collections':
					return $this->getProfileULR().sprintf('character/%s/%s/collections', $this->ConvertInput($server, true, true), $this->ConvertInput($user));break;
				case 'talent-calculator':
					return $this->getProfileULR().sprintf('game/talent-calculator#%s/%s/talents=%s', $talents['class'], $talents['type'], $talents['calcTalent']);break;
				case 'guild':
					return $this->getProfileULR('guild').sprintf('guild/%s/%s/', $this->ConvertInput($server, true, true), $this->ConvertInput($guild));break;
				case 'guild-achievements':
					return $this->getProfileULR('guild').sprintf('guild/%s/%s/achievement', $this->ConvertInput($server, true, true), $this->ConvertInput($guild));break;
				case 'askmrrobot':
					return sprintf('https://www.askmrrobot.com/optimizer#%s/%s/%s', $this->config->get('uc_server_loc'), $this->ConvertInput($server, true, true), $this->ConvertInput($user));break;
				case 'raiderio':
					return sprintf('http://raider.io/characters/%s/%s/%s', $this->config->get('uc_server_loc'), $this->ConvertInput($server, true, true), $this->ConvertInput($user));break;
				case 'wowprogress':
					return sprintf('http://www.wowprogress.com/character/%s/%s/%s', $this->config->get('uc_server_loc'), $this->ConvertInput($server, true, true), $this->ConvertInput($user));break;
				case 'warcraftlogs':
					return sprintf('https://classic.warcraftlogs.com/character/%s/%s/%s', $this->config->get('uc_server_loc'), $this->ConvertInput($server, true, true), $this->ConvertInput($user));break;
					
					
			}
		}
		
		public function ConvertInput($input, $removeslash=false, $removespace=false){
			// new servername convention: mal'ganis = malganis
			$input = ($removespace) ? str_replace(" ", "-", $input) : $input;
			return ($removeslash) ? stripslashes(str_replace("'", "", $input)) : stripslashes(rawurlencode($input));
		}
		
		public function getProfileULR($type='char'){
			$profileurlChar		= 'https://worldofwarcraft.com/{locale}/';
			$profileurlGuild		= 'https://{region}.battle.net/';
			
			if($type=='char'){
				return str_replace('{locale}', $this->config->get('uc_data_lang'), $profileurlChar);
			}else{
				$tmp_url	= str_replace('{region}', $this->config->get('uc_server_loc'), $profileurlGuild);
				return str_replace('{locale}', substr($this->config->get('uc_data_lang'),0,2), $tmp_url.'/wow/{locale}/');
			}
		}
	} //end class
} //end if class not exists
?>