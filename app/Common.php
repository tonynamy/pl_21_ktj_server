<?php

/**
 * The goal of this file is to allow developers a location
 * where they can overwrite core procedural functions and
 * replace them with their own. This file is loaded during
 * the bootstrap process and is called during the frameworks
 * execution.
 *
 * This can be looked at as a `master helper` file that is
 * loaded early on, and may also contain additional functions
 * that you'd like to use throughout your entire application
 *
 * @link: https://codeigniter4.github.io/CodeIgniter4/
 */

 function getTypeText($type) {

    if($type==1) {
        return "설비";
    } else if($type==2) {
        return "전기";
    } else if($type==3) {
        return "건축";
    } else {
        return "기타";
    }

 }

 function getTaskTypeText($type) {

    if($type==1) {
        return "설치";
    } else if($type==2) {
        return "수정";
    } else if($type==3) {
        return "해체";
    } else {
        return "기타";
    }

 }