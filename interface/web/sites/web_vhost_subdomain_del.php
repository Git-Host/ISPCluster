<?php

/*
Copyright (c) 2007, Till Brehm, projektfarm Gmbh
All rights reserved.

Redistribution and use in source and binary forms, with or without modification,
are permitted provided that the following conditions are met:

    * Redistributions of source code must retain the above copyright notice,
      this list of conditions and the following disclaimer.
    * Redistributions in binary form must reproduce the above copyright notice,
      this list of conditions and the following disclaimer in the documentation
      and/or other materials provided with the distribution.
    * Neither the name of ISPConfig nor the names of its contributors
      may be used to endorse or promote products derived from this software without
      specific prior written permission.

THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND
ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED
WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE DISCLAIMED.
IN NO EVENT SHALL THE COPYRIGHT OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT,
INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING,
BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE,
DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY
OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING
NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE,
EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
*/

/******************************************
* Begin Form configuration
******************************************/

$list_def_file = "list/web_vhost_subdomain.list.php";
$tform_def_file = "form/web_vhost_subdomain.tform.php";

/******************************************
* End Form configuration
******************************************/

require_once '../../lib/config.inc.php';
require_once '../../lib/app.inc.php';

//* Check permissions for module
$app->auth->check_module_permissions('sites');

$app->uses('tpl,tform,tform_actions');
$app->load("tform_actions");
class page_action extends tform_actions {

	function onBeforeDelete() {
		global $app; $conf;

		//* Delete all web folders
		$records = $app->db->queryAllRecords("SELECT web_folder_id FROM web_folder WHERE parent_domain_id = '".$app->functions->intval($this->id)."'");
		foreach($records as $rec) {
			//* Delete all web folder users
			$records2 = $app->db->queryAllRecords("SELECT web_folder_user_id FROM web_folder_user WHERE web_folder_id = '".$app->functions->intval($rec['web_folder_id'])."'");
			foreach($records2 as $rec2) {
				$app->db->datalogDelete('web_folder_user', 'web_folder_user_id', $rec2['web_folder_user_id']);
			}
			$app->db->datalogDelete('web_folder', 'web_folder_id', $rec['web_folder_id']);
		}

	}

}

$page = new page_action;
$page->onDelete();

?>
