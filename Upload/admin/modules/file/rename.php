<?php
define("IN_MYBB", 1);
define("IN_ADMINCP", 1);
require_once "../../../inc/init.php";

if($mybb->request_method != "post") {
	die("Direct initialization of this file is not allowed.");	
}

if(!isset($cp_language))
{
	if(!file_exists(MYBB_ROOT."inc/languages/".$mybb->settings['cplanguage']."/admin/file_manager.lang.php"))
		$mybb->settings['cplanguage'] = "english";
	$lang->set_language($mybb->settings['cplanguage'], "admin");
}

$lang->load("file_manager");

$data = array();

if($mybb->input['directory']) {
	$dir = htmlspecialchars_uni($mybb->input['directory']);
	$dir = MYBB_ROOT.$dir;
} else {
	$dir = MYBB_ROOT;	
}

if(isset($mybb->input['file']) && !empty($mybb->input['file'])) {
	$file = htmlspecialchars_uni($mybb->input['file']);
	if(isset($mybb->input['new_name']) && !empty($mybb->input['new_name'])) {
		$new_name = htmlspecialchars_uni($mybb->input['new_name']);
		if(file_exists($dir."/".$file)) {
			if(!file_exists($dir."/".$new_name)) {
				if(rename($dir."/".$file, $dir."/".$new_name)) {
					require_once("function.php");
                    $dir = str_replace(MYBB_ROOT, "", $dir);
                    $data['success'] = true;
					if(is_dir($dir."/".$new_name)) {
						$data['text'] = $lang->file_success_rename_dir;
                        $data['icon'] = "extension/folder.png";
					} else {
						$data['text'] = $lang->file_success_rename;
                        $ext = "";
                        if(strpos($new_name, ".") !== false) {
                            $ext = explode(".", $new_name);
                            $ext = end($ext);
                        }
                        $ext = get_file_extension($ext);
                        if($ext == "image")
                            $ext = $mybb->settings['bburl']."/".$dir."/".$new_name;
                        else
                            $ext = "extension/{$ext}.png";
                        $data['icon'] = $ext;
                        $data['edit'] = "";
                        if(is_editable($new_name))
                            $data['edit'] = "<span><a href=\"index.php?module=file&action=edit&directory={$dir}&file={$new_name}\" class=\"file__actions__edit\">{$lang->file_edit}</a> |</span> ";
					}
				} else {
					$data['error'] = true;
					$data['text'] = $lang->file_error_rename;
				}
			} else {
				$data['error'] = true;
				$data['text'] = $lang->file_error_rename_exists;
			}
		} else {
			$data['error'] = true;
			$data['text'] = $lang->file_error_rename_not_exists;
		}
	} else {
		$data['error'] = true;
		$data['text'] = $lang->file_error_rename_name;
	}
} else {
	$data['error'] = true;
	$data['text'] = $lang->file_error_rename_input;
}

echo json_encode($data);