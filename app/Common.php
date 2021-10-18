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

    switch($type) {
        case "1":
            return "설비";
        case "2":
            return "전기";
        case "3":
            return "건축";
        default:
            return "기타";
    }
 }

 function getTypeInt($type) {

    switch($type) {
        case "설비":
            return "1";
        case "전기":
            return "2";
        case "건축":
            return "3";
        default:
            return "4";
    }
 }

 function getTaskTypeText($type) {
     
    switch($type) {
        case "1":
            return "설치";
        case "2":
            return "수정";
        case "3":
            return "해체";
        default:
            return "기타";
    }
 }

 function getUserLevel($level) {
     
    switch($level) {
        case "0":
            return "대기자";
        case "1":
            return "팀장";
        case "2":
            return "관리자";
        case "3":
        case "4":
            return "최고관리자";
    }
 }
 