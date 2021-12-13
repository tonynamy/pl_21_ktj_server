<?= $this->extend('common_layout') ?>

<?= $this->section('content') ?>

    <?php 
        $state_column = [
            'created_at',       //0
            'started_at',       //1
            'finished_at',      //2
            'edit_started_at',  //3
            'edit_finished_at', //4
            'dis_started_at',   //5
            'dis_finished_at',  //6
        ];

        $facility_state = 0;

        for($i=6; $i>0; $i--) {
            if($facility[$state_column[$i]]) {
                $facility_state = $i;
                break;
            }
        }
    ?>

    <form method="POST">

        <div style="width:fit-content; margin:0 auto; padding:16px;">
            <div class="uiframe" style="width:1200px; padding:16px;">

                <div style="height:30px; display:flex; justify-content:space-between; align-items:center;">

                    <div style="display:flex; align-items:center;">
                        <i class="arrow left icon" onclick="location.href='/fm/view_facility/<?= $filter_key ?>'" style="cursor: pointer;"></i> <!-- 뒤로가기 -->
                        <span style="font-size:x-large; font-weight:normal;"><?= $facility['serial'] ?></span>
                    </div>
                    <?php if($user_level != -1) : ?>
                        <button id="facility_delete_button" class="filletbutton" type="button">도면삭제</button>
                    <?php endif ?>
                </div>

                <div style="width:100%; display:flex; justify-content:space-between; padding-left:16px; padding-right:16px;">

                    <div style="width:500px;">

                        <table class="ui very basic table" style="margin-top:-16px; margin-bottom:-16px;">
                            <colgroup>
                                <col width="152px">
                                <col>
                                <?php if($user_level != -1) : ?>
                                    <col width="80px">
                                <?php endif ?>
                            </colgroup>
                            <tr>
                                <td colspan="2"></td>
                                <?php if($user_level != -1) : ?>
                                    <td></td>
                                <?php endif ?>
                            </tr>
                            <tr>
                                <td>원도면번호</td>
                                <td><?= $facility['o_serial'] ?></td>
                                <?php if($user_level != -1) : ?>
                                    <td style="color:blue; cursor:pointer;" class="facility_edit" data-type="0">[수정]</td>
                                <?php endif ?>
                            </tr>
                            <tr>
                                <td>리비전번호</td>
                                <td><?= $facility['r_num'] ?></td>
                                <?php if($user_level != -1) : ?>
                                    <td style="color:blue; cursor:pointer;" class="facility_edit" data-type="1">[수정]</td>
                                <?php endif ?>
                            </tr>
                            <tr>
                                <td>공종</td>
                                <td><?= getTypeText($facility['type']) ?></td>
                                <?php if($user_level != -1) : ?>
                                    <td style="color:blue; cursor:pointer;" class="facility_edit" data-type="2">[수정]</td>
                                <?php endif ?>
                            </tr>
                            <tr>
                                <td>담당자</td>
                                <td><?= $facility['super_manager'] ?></td>
                                <?php if($user_level != -1) : ?>
                                    <td style="color:blue; cursor:pointer;" class="facility_edit" data-type="3"><?= empty($facility['super_manager']) ? "[생성]" : "[수정]" ?></td>
                                <?php endif ?>
                            </tr>
                            <tr>
                                <td>사용업체</td>
                                <td><?= $facility['subcontractor'] ?></td>
                                <?php if($user_level != -1) : ?>
                                    <td style="color:blue; cursor:pointer;" class="facility_edit" data-type="4"><?= empty($facility['subcontractor']) ? "[생성]" : "[수정]" ?></td>
                                <?php endif ?>
                            </tr>
                            <tr>
                                <td>설치동</td>
                                <td><?= $facility['building'] ?></td>
                                <?php if($user_level != -1) : ?>
                                    <td style="color:blue; cursor:pointer;" class="facility_edit" data-type="5"><?= empty($facility['building']) ? "[생성]" : "[수정]" ?></td>
                                <?php endif ?>
                            </tr>
                            <tr>
                                <td>설치층</td>
                                <td><?= $facility['floor'] ?></td>
                                <?php if($user_level != -1) : ?>
                                    <td style="color:blue; cursor:pointer;" class="facility_edit" data-type="6"><?= empty($facility['floor']) ? "[생성]" : "[수정]" ?></td>
                                <?php endif ?>
                            </tr>
                            <tr>
                                <td>설치위치</td>
                                <td><?= $facility['spot'] ?></td>
                                <?php if($user_level != -1) : ?>
                                    <td style="color:blue; cursor:pointer;" class="facility_edit" data-type="7"><?= empty($facility['spot']) ? "[생성]" : "[수정]" ?></td>
                                <?php endif ?>
                            </tr>
                            <tr>
                                <td>설치구간</td>
                                <td><?= $facility['section'] ?></td>
                                <?php if($user_level != -1) : ?>
                                    <td style="color:blue; cursor:pointer;" class="facility_edit" data-type="8"><?= empty($facility['section']) ? "[생성]" : "[수정]" ?></td>
                                <?php endif ?>
                            </tr>
                            <tr>
                                <td>설치목적</td>
                                <td><?= $facility['purpose'] ?></td>
                                <?php if($user_level != -1) : ?>
                                    <td style="color:blue; cursor:pointer;" class="facility_edit" data-type="9"><?= empty($facility['purpose']) ? "[생성]" : "[수정]" ?></td>
                                <?php endif ?>
                            </tr>
                            
                            <?php //if(empty($facility['danger_data']) || $facility['danger_result'] == 0) : ?>
                                <tr>
                                    <td>강관비계 산출식</td>
                                    <td><?= $facility['cube_data'] ?></td>
                                    <?php if($user_level != -1) : ?>
                                        <td style="color:blue; cursor:pointer;" class="facility_edit" data-type="10"><?= empty($facility['cube_data']) ? "[생성]" : "[수정]" ?></td>
                                    <?php endif ?>
                                </tr>
                                <tr>
                                    <td>강관비계 물량</td>
                                    <td><?= $facility['cube_result'] != 0 ? $facility['cube_result'] . "<span>㎥</span>" : "" ?></td>
                                    <?php if($user_level != -1) : ?>
                                        <td style="color:blue; cursor:pointer;" class="facility_edit" data-type="11"><?= empty($facility['cube_result']) ? "[생성]" : "[수정]" ?></td>
                                    <?php endif ?>
                                </tr>
                            <?php //endif ?>

                            <tr>
                                <td>안전발판 산출식</td>
                                <td><?= $facility['area_data'] ?></td>
                                <?php if($user_level != -1) : ?>
                                    <td style="color:blue; cursor:pointer;" class="facility_edit" data-type="12"><?= empty($facility['area_data']) ? "[생성]" : "[수정]" ?></td>
                                <?php endif ?>
                            </tr>
                            <tr>
                                <td>안전발판 물량</td>
                                <td><?= $facility['area_result'] != 0 ? $facility['area_result'] . "㎡" : "" ?></td>
                                <?php if($user_level != -1) : ?>
                                    <td style="color:blue; cursor:pointer;" class="facility_edit" data-type="13"><?= empty($facility['area_result']) ? "[생성]" : "[수정]" ?></td>
                                <?php endif ?>
                            </tr>

                            <?php //if(empty($facility['cube_data']) || $facility['cube_result'] == 0) : ?>
                                <tr>
                                    <td>달대비계 산출식</td>
                                    <td><?= $facility['danger_data'] ?></td>
                                    <?php if($user_level != -1) : ?>
                                        <td style="color:blue; cursor:pointer;" class="facility_edit" data-type="14"><?= empty($facility['danger_data']) ? "[생성]" : "[수정]" ?></td>
                                    <?php endif ?>
                                </tr>
                                <tr>
                                    <td>달대비계 물량</td>
                                    <td><?= $facility['danger_result'] != 0 ? $facility['danger_result'] . "㎡" : "" ?></td>
                                    <?php if($user_level != -1) : ?>
                                        <td style="color:blue; cursor:pointer;" class="facility_edit" data-type="15"><?= empty($facility['danger_result']) ? "[생성]" : "[수정]" ?></td>
                                    <?php endif ?>
                                </tr>
                            <?php //endif ?>

                            <tr>
                                <td>도면등록일</td>
                                <td><?= str_replace(" 00:00:00", "", $facility['created_at']) ?></td>
                                <?php if($user_level != -1) : ?>
                                    <td style="color:blue; cursor:pointer;" class="facility_edit" data-type="16">[수정]</td>
                                <?php endif ?>
                            </tr>
                            <tr>
                                <td>설치시작일</td>
                                <td><?= str_replace(" 00:00:00", "", $facility['started_at']) ?></td>
                                <?php if($user_level != -1) : ?>
                                    <td style="color:blue; cursor:pointer;" class="facility_edit" data-type="17"><?= is_null($facility['started_at']) ? "[생성]" : "[수정]" ?></td>
                                <?php endif ?>
                            </tr>
                            <tr>
                                <td>승인완료일</td>
                                <td><?= str_replace(" 00:00:00", "", $facility['finished_at']) ?></td>
                                <?php if($user_level != -1) : ?>
                                    <td style="color:blue; cursor:pointer;" class="facility_edit" data-type="18"><?= is_null($facility['finished_at']) ? "[생성]" : "[수정]" ?></td>
                                <?php endif ?>
                            </tr>
                            <tr>
                                <td>수정시작일</td>
                                <td><?= str_replace(" 00:00:00", "", $facility['edit_started_at']) ?></td>
                                <?php if($user_level != -1) : ?>
                                    <td style="color:blue; cursor:pointer;" class="facility_edit" data-type="19"><?= is_null($facility['edit_started_at']) ? "[생성]" : "[수정]" ?></td>
                                <?php endif ?>
                            </tr>
                            <tr>
                                <td>수정완료일</td>
                                <td><?= str_replace(" 00:00:00", "", $facility['edit_finished_at']) ?></td>
                                <?php if($user_level != -1) : ?>
                                    <td style="color:blue; cursor:pointer;" class="facility_edit" data-type="20"><?= is_null($facility['edit_finished_at']) ? "[생성]" : "[수정]" ?></td>
                                <?php endif ?>
                            </tr>
                            <tr>
                                <td>해체시작일</td>
                                <td><?= str_replace(" 00:00:00", "", $facility['dis_started_at']) ?></td>
                                <?php if($user_level != -1) : ?>
                                    <td style="color:blue; cursor:pointer;" class="facility_edit" data-type="21"><?= is_null($facility['dis_started_at']) ? "[생성]" : "[수정]" ?></td>
                                <?php endif ?>
                            </tr>
                            <tr>
                                <td>해체완료일</td>
                                <td><?= str_replace(" 00:00:00", "", $facility['dis_finished_at']) ?></td>
                                <?php if($user_level != -1) : ?>
                                    <td style="color:blue; cursor:pointer;" class="facility_edit" data-type="22"><?= is_null($facility['dis_finished_at']) ? "[생성]" : "[수정]" ?></td>
                                <?php endif ?>
                            </tr>
                            <tr>
                                <td>만료일</td>
                                <td><?= str_replace(" 00:00:00", "", $facility['expired_at']) ?></td>
                                <?php if($user_level != -1) : ?>
                                    <td style="color:blue; cursor:pointer;" class="facility_edit" data-type="23"><?= is_null($facility['expired_at']) ? "[생성]" : "[수정]" ?></td>
                                <?php endif ?>
                            </tr>
                            <tr>
                                <td>비고</td>
                                <td><?= $facility['memo'] ?></td>
                                <?php if($user_level != -1) : ?>
                                    <td style="color:blue; cursor:pointer;" class="facility_edit" data-type="24"><?= is_null($facility['memo']) || empty($facility['memo']) ? "[생성]" : "[수정]" ?></td>
                                <?php endif ?>
                            </tr>
                            <tr>
                                <td colspan="2"></td>
                                <?php if($user_level != -1) : ?>
                                    <td></td>
                                <?php endif ?>
                            </tr>
                        </table>
                    </div>

                    <div style="width:500px; margin-top:16px; margin-right:16px;">

                        <?php
                            $taskplan_id = $taskplan['id'] ?? '';
                            $taskplan_team_id = $taskplan['team_id'] ?? '';
                            $taskplan_type = $taskplan['type'] ?? 1;
                            if($facility['danger_result'] != 0) {
                                $size = $facility['danger_result'];
                                $is_square = 1;
                            } else {
                                $size = $facility['cube_result'];
                                $is_square = 0;
                            }
                        ?>

                        <?php if($facility['dis_finished_at'] == null && ($taskplan != null || $user_level != -1)) : ?>
                            <div>※작업계획 - 도면당 한개만 가능합니다.</div>

                            <table style="width:100%; margin-bottom:32px;">
                                <tr>
                                    <?php if($taskplan != "VALID") : ?>

                                        <?php if($taskplan != null) : ?>
                                            <td><?= getTaskTypeText($taskplan['type']) ?>계획</td>
                                            <td><?= $taskplan['team_name'] ?></td>
                                        <?php endif ?>

                                        <td style="width:60px;">
                                            <?php if($user_level != -1) : ?>
                                                <span id="taskplan_edit" data-taskplan_id="<?= $taskplan_id ?>" data-team_id="<?= $taskplan_team_id ?>" style="color:blue; cursor:pointer;">
                                                    <?= $taskplan == null ? "[추가]" : "[수정]" ?>
                                                </span>
                                            <?php endif ?>
                                        </td>

                                    <?php else : ?>
                                        <td style="width:60px; color:red">작업계획이 문제가 생겨 여러개가 생성되었습니다. 개발자에게 문의해주세요.<br>010-2820-0762</td>
                                    <?php endif ?>
                                    
                                </tr>
                            </table>
                        <?php endif ?>

                        <?php if($taskplan != null || $user_level != -1) : ?>
                            <div>※작업내역</div>

                            <table style="width:100%;">

                                <?php foreach($tasks as $task) : ?>
                                    <tr>
                                        <td style="width:170px"><?= $task['created_at'] ?></td>
                                        <td style="width:90px"><?= getTaskTypeText($task['type']) ?>작업</td>
                                        <td><?= $task['team_name'] ?></td>
                                        <td style="width:70px"><?= $task['manday'] ?>공수</td>
                                        <td style="width:60px;">
                                            <?php if($user_level != -1) : ?>
                                                <span class="task_edit" data-task_id="<?= $task['id'] ?>" data-team_id="<?= $task['team_id'] ?>" style="color:blue; cursor:pointer;">[수정]</span>
                                            <?php endif ?>
                                        </td>
                                    </tr>
                                <?php endforeach ?>

                                <?php if($user_level != -1) : ?>
                                    <tr>
                                        <?php if(count($tasks) > 0) : ?>
                                            <td colspan="4"></td>
                                        <?php endif ?>
                                        <td style="width:60px;">
                                            <span id="task_add" data-team_id="<?= $taskplan_team_id ?>" data-taskplan_type="<?= $taskplan_type ?>" style="color:blue; cursor:pointer;">[추가]</span>
                                        </td>
                                    </tr>
                                <?php endif ?>

                            </table>
                        <?php endif ?>
                    </div>
                </div>
            </div>
        </div>
    </form>

    <!-- 도면삭제 modal -->
    <div id="facility_delete_modal" class="ui mini modal">

        <form id="facility_delete_form" class="ui form" method="POST" action="/fm/delete_facility">
            <div style="padding:16px;">

                <div style="margin-top:4px; line-height:150%">해당 도면의 정보를 삭제하며 복구는 불가합니다.</div>
                <div class="actions" style="text-align:right; margin-top:24px; margin-bottom:4px">
                    <span class="cancel" style="color:#5599DD; cursor:pointer; margin-right:32px;">취소</span>
                    <span id="facility_delete_submit" style="color:#5599DD; cursor:pointer;">확인</span>
                </div>

            </div>

            <input type="hidden" name="facility_id" value="<?= $facility['id'] ?>">
        </form>

    </div>

    <!-- 리비전번호수정 modal -->
    <div id="facility_rnum_edit_modal" class="ui mini modal" style="padding:16px;">

        <form id="facility_rnum_edit_form" class="ui form" method="POST" action="/fm/edit_facility_info">

            <div style="display:flex; justify-content:space-between; align-items:center;">
                <label class="data_name">데이터 라벨</label>
                <input type="text" name="data" placeholder="변경할 데이터" style="width:270px;">
            </div>

            <div style="margin-top:8px; line-height:150%;">
                ※원도면번호나 리비전번호는 수정하지 않는것이 좋습니다.<br>
                ※원도면번호나 리비전번호를 바꿔도 도면번호는 바뀌지 않습니다.<br>
                ※도면번호가 잘못되었다면 도면을 삭제하고 다시 등록해주세요.
            </div>

            <div class="actions" style="text-align:right; margin-top:16px">
                <span class="cancel" style="color:#5599DD; cursor:pointer; margin-right:32px;">취소</span>
                <span id="facility_rnum_edit_submit" style="color:#5599DD; cursor:pointer;">수정</span>
            </div>
            
            <input type="hidden" name="facility_id" value="<?=$facility['id']?>">
            <input type="hidden" name="data_type">
            <input type="hidden" name="is_edit">
            <input type="hidden" name="o_serial" value="<?=$facility['o_serial']?>">
            <input type="hidden" name="r_num" value="<?=$facility['r_num']?>">
        </form>
    </div>

    <!-- 공종정보수정 modal -->
    <div id="facility_type_edit_modal" class="ui mini modal" style="padding:16px;">

        <form id="facility_type_edit_form" class="ui form" method="POST" action="/fm/edit_facility_info">
            <div class="ui input" style="display:flex; justify-content:space-between; align-items:center; margin-bottom:24px;">
                <label>공종</label>
                <div style="width:270px;">
                    <select class="ui fluid dropdown" name="data">
                        <option value="1">설비</option>
                        <option value="2">전기</option>
                        <option value="3">건축</option>
                        <option value="4">기타</option>
                    </select>
                </div>
            </div>

            <div class="actions" style="text-align:right;">
                <span class="cancel" style="color:#5599DD; cursor:pointer; margin-right:32px;">취소</span>
                <span id="facility_type_edit_submit" style="color:#5599DD; cursor:pointer;">수정</span>
            </div>
            
            <input type="hidden" name="facility_id" value="<?=$facility['id']?>">
            <input type="hidden" name="data_type" value="2">
            <input type="hidden" name="is_edit">
        </form>
    </div>

    <?php $today = date("Y-m-d"); ?>

    <!-- 달력정보수정 modal -->
    <div id="facility_calendar_edit_modal" class="ui mini modal" style="padding:16px;">

        <form id="facility_calendar_edit_form" class="ui form" method="POST" action="/fm/edit_facility_info">
            <div style="margin-bottom:24px;">
                <label class="data_name">데이터 라벨</label>
                <div id="facility_info_calendar" class="ui calendar" style="margin-top:4px"></div>
                <input type="hidden" name="data">

            </div>

            <div style="display:flex; justify-content:space-between; margin-top:24px; margin-bottom:4px">
                <span id="facility_calendar_delete_submit" style="color:#5599DD; cursor:pointer;">날짜기록 삭제</span>
                <div class="actions" style="text-align:right;">
                    <span class="cancel" style="color:#5599DD; cursor:pointer; margin-right:32px;">취소</span>
                    <span id="facility_calendar_edit_submit" style="color:#5599DD; cursor:pointer;">수정</span>
                </div>
            </div>
            
            <input type="hidden" name="facility_id" value="<?=$facility['id']?>">
            <input type="hidden" name="data_type">
            <input type="hidden" name="is_edit">
            <input type="hidden" name="o_serial" value="<?=$facility['o_serial']?>">
            <input type="hidden" name="r_num" value="<?=$facility['r_num']?>">
        </form>
    </div>

    <!-- 일반정보수정 modal -->
    <div id="facility_edit_modal" class="ui mini modal" style="padding:16px;">

        <form id="facility_edit_form" class="ui form" method="POST" action="/fm/edit_facility_info">
            <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:24px;">
                <label class="data_name">데이터 라벨</label>
                <input type="text" name="data" placeholder="변경할 데이터" style="width:270px;">
            </div>

            <div class="actions" style="text-align:right;">
                <span class="cancel" style="color:#5599DD; cursor:pointer; margin-right:32px;">취소</span>
                <span id="facility_edit_submit" style="color:#5599DD; cursor:pointer;">수정</span>
            </div>
            
            <input type="hidden" name="data_type">
            <input type="hidden" name="is_edit">
            <input type="hidden" name="facility_id" value="<?=$facility['id']?>">
        </form>
    </div>

                                    
    <!-- 작업계획 수정 modal -->
    <div id="taskplan_edit_modal" class="ui mini modal">

        <form id="taskplan_edit_form" class="ui form" method="POST" action="/fm/edit_taskplan">
            <div style="padding:16px;">

                <label id="testt" style="margin-top:4px">작업내용</label>
                <label style="margin-top:4px">작업내용</label>
                <div style="display:flex; justify-content:space-between; align-items:center; margin-top:16px">
                    <div id="taskplan_type_1" class="ui radio checkbox" style="flex:1;">
                        <input type="radio" name="taskplan_type" value="1" class="hidden">
                        <label style="font-weight:normal">설치계획</label>
                    </div>
                    <div id="taskplan_type_2" class="ui radio checkbox" style="flex:1;">
                        <input type="radio" name="taskplan_type" value="2" class="hidden">
                        <label style="font-weight:normal">수정계획</label>
                    </div>
                    <div id="taskplan_type_3" class="ui radio checkbox" style="flex:1;">
                        <input type="radio" name="taskplan_type" value="3" class="hidden">
                        <label style="font-weight:normal">해체계획</label>
                    </div>
                </div>
                <div style="display:flex; justify-content:space-between; align-items:center; margin-top:16px">
                    <label>작업팀</label>
                    <div style="width:270px;">
                        <select id="taskplan_edit_team" class="ui fluid dropdown" name="team_id">
                            
                            <?php foreach($teams as $team) : ?>

                                <option value="<?=$team['id']?>" > <?= $team['name'] ?> </option>

                            <?php endforeach ?>

                        </select>
                    </div>
                </div>
                <div style="display:flex; justify-content:space-between; margin-top:24px; margin-bottom:4px">
                    <span id="taskplan_delete_submit" style="color:#5599DD; cursor:pointer;">작업계획 삭제</span>
                    <div class="actions" style="text-align:right;">
                        <span class="cancel" style="color:#5599DD; cursor:pointer; margin-right:32px;">취소</span>
                        <span id="taskplan_edit_submit" style="color:#5599DD; cursor:pointer;">수정</span>
                    </div>
                </div>
            </div>
            
            <input type="hidden" name="o_serial" value="<?= $facility['o_serial'] ?>">
            <input type="hidden" name="facility_state" value="<?= $facility_state ?>">
            <input type="hidden" name="taskplan_id">
        </form>
    </div>

    <!-- 작업내역 수정 modal -->
    <div id="task_edit_modal" class="ui mini modal">

        <form id="task_edit_form" class="ui form" method="POST" action="/fm/edit_task">
            <div style="padding:16px;">

                <div style="display:flex; justify-content:space-between; align-items:center; margin-top:4px">
                    <label>작업일시</label>
                    <div id="task_edit_date" style="width:270px">작업일시</div>
                </div>
                <div style="display:flex; justify-content:space-between; align-items:center; margin-top:16px">
                    <label>작업내용</label>
                    <div style="width:270px;">
                        <select id="task_type_select" class="ui fluid dropdown" name="task_type">
                            <option value="1">설치작업</option>
                            <option value="2">수정작업</option>
                            <option value="3">해체작업</option>
                        </select>
                    </div>
                </div>
                <div style="display:flex; justify-content:space-between; align-items:center; margin-top:4px">
                    <label>작업팀</label>
                    <div style="width:270px;">
                        <select id="task_team" class="ui fluid dropdown" name="team_id">
                            
                            <?php foreach($teams as $team) : ?>

                                <option value="<?=$team['id']?>" > <?= $team['name'] ?> </option>

                            <?php endforeach ?>

                        </select>
                    </div>
                </div>
                <div style="display:flex; justify-content:space-between; align-items:center; margin-top:4px">
                    <label>작업인원</label>
                    <input type="number" min="0" name="manday" placeholder="0을 포함하는 자연수" style="width:270px">
                </div>
                <div style="display:flex; justify-content:space-between; margin-top:24px; margin-bottom:4px">
                    <span id="task_delete_submit2" style="color:#5599DD; cursor:pointer;">작업기록 삭제</span>
                    <div class="actions" style="text-align:right;">
                        <span class="cancel" style="color:#5599DD; cursor:pointer; margin-right:32px;">취소</span>
                        <span id="task_edit_submit" style="color:#5599DD; cursor:pointer;">수정</span>
                    </div>
                </div>

            </div>

            <input type="hidden" name="o_serial" value="<?= $facility['o_serial'] ?>">
            <input type="hidden" name="task_id">
        </form>
    </div>

    <!-- 작업내역 추가 modal -->
    <div id="task_add_modal" class="ui mini modal">
        
        <form id="task_add_form" class="ui form" method="POST" action="/fm/edit_task">
            <div style="padding:16px;">

                <div style="display:flex; justify-content:space-between; margin-bottom:8px">
                    <label>작업일시</label>
                    <div id="calendar_display" style="width:270px"></div>
                </div>

                <div id="task_calendar" class="ui calendar"></div>
                <input type="hidden" name="task_date">

                <div style="display:flex; justify-content:space-between; align-items:center; margin-top:8px">
                    <label>작업내용</label>
                    <div style="width:270px;">
                        <select id="task_type_select" class="ui fluid dropdown" name="task_type">
                            <option value="1">설치작업</option>
                            <option value="2">수정작업</option>
                            <option value="3">해체작업</option>
                        </select>
                    </div>
                </div>
                <div style="display:flex; justify-content:space-between; align-items:center; margin-top:8px">
                    <label>작업팀</label>
                    <div style="width:270px;">
                        <select id="task_team" class="ui fluid dropdown" name="team_id">
                            
                            <?php foreach($teams as $team) : ?>

                                <option value="<?=$team['id']?>" > <?= $team['name'] ?> </option>

                            <?php endforeach ?>

                        </select>
                    </div>
                </div>
                <div style="display:flex; justify-content:space-between; align-items:center; margin-top:8px">
                    <?php
                        $attendance = $taskplan['attendance'] ?? 1;
                        $attendance = $attendance != 0 ? $attendance : 1;
                    ?>
                    <label>작업인원</label>
                    <input type="number" min="1" name="manday" value="<?= $attendance ?>" placeholder="0을 포함하는 자연수" style="width:270px">
                </div>
                
                <div class="actions" style="text-align:right; margin-top:24px; margin-bottom:4px">
                    <span class="cancel" style="color:#5599DD; cursor:pointer; margin-right:32px;">취소</span>
                    <span id="task_add_submit" style="color:#5599DD; cursor:pointer;">추가</span>
                </div>
            </div>

            <input type="hidden" name="o_serial" value="<?= $facility['o_serial'] ?>">
            <input type="hidden" name="size" value="<?= $size ?>">
            <input type="hidden" name="is_square" value="<?= $is_square ?>">
        </form>
    </div>

<?= $this->endSection() ?>

<?= $this->section('custom_js') ?>

    <script type="text/javascript">
        
        $('#facility_info_calendar').calendar({
            type: 'date',
            text: {
                months: ['1월,', '2월,', '3월,', '4월,', '5월,', '6월,', '7월,', '8월,', '9월,', '10월,', '11월,', '12월,'],
                monthsShort: ['1월', '2월', '3월', '4월', '5월', '6월', '7월', '8월', '9월', '10월', '11월', '12월'],
            },
            ampm: false,
            onChange: function(date) {
                
                year = date.getFullYear().toString();
                month = ('0' + (date.getMonth() + 1)).slice(-2);
                day = ('0' + date.getDate()).slice(-2);

                date_str = year + "-" + month + "-" + day;
                
                $('#facility_calendar_edit_form [name=data]').val(date_str);

            }
        });

        $('#task_calendar').calendar({
            type: 'datetime',
            text: {
                months: ['1월,', '2월,', '3월,', '4월,', '5월,', '6월,', '7월,', '8월,', '9월,', '10월,', '11월,', '12월,'],
                monthsShort: ['1월', '2월', '3월', '4월', '5월', '6월', '7월', '8월', '9월', '10월', '11월', '12월'],
            },
            ampm: false,
            onChange: function(date) {
                
                year = date.getFullYear().toString();
                month = ('0' + (date.getMonth() + 1)).slice(-2);
                day = ('0' + date.getDate()).slice(-2);
                hour = ('0' + date.getHours()).slice(-2);
                min = ('0' + date.getMinutes()).slice(-2);

                date_str = year + "-" + month + "-" + day + " " + hour +":" + min + ":00";
                
                $('#task_add_form [name=task_date]').val(date_str);
                $('#calendar_display').text(date_str);

            }
        });

        $(document).ready(function() {

            $('#facility_delete_button').click(function() {

                $('#facility_delete_modal').modal('show');

            });
            $('#facility_delete_submit').click(function() {

                $('#facility_delete_form').submit();

            });

            $('td.facility_edit').click(function() {

                var data = $(this).prev().clone().children().remove().end().text();
                var data_type = $(this).data('type');

                var data_name = '';
                switch(data_type) {
                    case 0: data_name = '원도면번호'; break;
                    case 1: data_name = '리비전번호'; break;
                    case 2: data_name = '공종'; break;
                    case 3: data_name = '담당자'; break;
                    case 4: data_name = '사용업체'; break;
                    case 5: data_name = '설치동'; break;
                    case 6: data_name = '설치층'; break;
                    case 7: data_name = '설치위치'; break;
                    case 8: data_name = '설치구간'; break;
                    case 9: data_name = '설치목적'; break;
                    case 10: data_name = '강관비계 산출식'; break;
                    case 11: data_name = '강관비계 물량'; break;
                    case 12: data_name = '안전발판 산출식'; break;
                    case 13: data_name = '안전발판 물량'; break;
                    case 14: data_name = '달대비계 물량'; break;
                    case 15: data_name = '달대비계 물량'; break;
                    case 16: data_name = '도면등록일'; break;
                    case 17: data_name = '설치시작일'; break;
                    case 18: data_name = '승인완료일'; break;
                    case 19: data_name = '수정시작일'; break;
                    case 20: data_name = '수정완료일'; break;
                    case 21: data_name = '해체시작일'; break;
                    case 22: data_name = '해체완료일'; break;
                    case 23: data_name = '만료일'; break;
                    case 24: data_name = '비고'; break;
                }
                $('label.data_name').text(data_name); //라벨에 제목넣기


                //원도면, 리비전번호
                if(data_type == 0 || data_type == 1) {

                    if(data_type == 1) {
                        $('#facility_rnum_edit_form [name=data]').prop('type', 'number');
                        $('#facility_rnum_edit_form [name=data]').prop('min', 0);
                    } else {
                        $('#facility_rnum_edit_form [name=data]').prop('type', 'text');
                    }

                    $('#facility_rnum_edit_form [name=data]').val(data);
                    $('#facility_rnum_edit_form [name=data_type]').val(data_type);
                    $('#facility_rnum_edit_modal').modal('show');

                //공종
                }else if(data_type == 2) {

                    var data = $(this).prev().clone().children().remove().end().text();

                    switch(data) {
                        case "설비": data = 1; break;
                        case "전기": data = 2; break;
                        case "건축": data = 3; break;
                        default: data = 4; break;
                    }

                    $('#facility_type_edit_form [name=data]').dropdown('set selected', data);
                    $('#facility_type_edit_modal').modal('setting', { autofocus: false }).modal('show');

                //달력종류
                } else if(data_type >= 16 && data_type <= 23) {

                    var today = new Date();

                    if(data != "") {
                        $('#facility_info_calendar').calendar('set date', data);
                        $('#facility_calendar_delete_submit').css('visibility', 'visible');
                        $('#facility_calendar_edit_form [name=is_edit]').val(true);
                        $('#facility_calendar_edit_submit').text("수정");
                    } else {
                        $('#facility_info_calendar').calendar('set date', today);
                        $('#facility_calendar_delete_submit').css('visibility', 'hidden');
                        $('#facility_calendar_edit_form [name=is_edit]').val(false);
                        $('#facility_calendar_edit_submit').text("생성");
                    }

                    $('#facility_calendar_edit_form [name=data_type]').val(data_type);
                    $('#facility_calendar_edit_modal').modal('show');

                //나머지 전부
                } else {

                    if(data != "") {
                        $('#facility_edit_form [name=is_edit]').val(true);
                        $('#facility_edit_submit').text("수정");
                    } else {
                        $('#facility_edit_form [name=is_edit]').val(false);
                        $('#facility_edit_submit').text("생성");
                    }

                    $('#facility_edit_form [name=data]').val(data);
                    $('#facility_edit_form [name=data_type]').val(data_type);
                    $('#facility_edit_modal').modal('show');
                    
                }
                
            });
            $('#facility_rnum_edit_submit').click(function() {

                $('#facility_rnum_edit_form').submit();

            });
            $('#facility_type_edit_submit').click(function() {

                $('#facility_type_edit_form').submit();

            });
            $('#facility_calendar_edit_submit').click(function() {

                $('#facility_calendar_edit_form').submit();

            });
            $('#facility_calendar_delete_submit').click(function() {

                $('#facility_calendar_edit_form [name=data]').val("");
                $('#facility_calendar_edit_form').submit();

            });
            $('#facility_edit_submit').click(function() {

                $('#facility_edit_form').submit();

            });

            //작업계획 추가, 수정, 삭제
            $('#taskplan_edit').click(function() {

                var taskplan_id = $(this).data('taskplan_id');
                var team_id = $(this).data('team_id');
                var taskplan_type = $(this).parent().prev().prev().text();
                var facility_state = $('#taskplan_edit_form [name=facility_state]').val();

                switch(taskplan_type) {
                    case "설치계획": taskplan_type = 1; break;
                    case "수정계획": taskplan_type = 2; break;
                    case "해체계획": taskplan_type = 3; break;
                    default : taskplan_type = 0; break;
                }
                if(taskplan_type == 0) {
                    $('#taskplan_edit_form [name=taskplan_type]').prop('checked', false);
                    $('#taskplan_delete_submit').css('visibility', 'hidden');
                    $('#taskplan_edit_submit').text("추가");

                } else {
                    $('#taskplan_edit_form [name=taskplan_type][value=' + taskplan_type + ']').prop('checked', true);
                    $('#taskplan_delete_submit').css('visibility', 'visible');
                    $('#taskplan_edit_submit').text("수정");
                }

                if(facility_state < 2) {
                    $('#taskplan_type_1').show();
                    $('#taskplan_type_2').hide();
                    $('#taskplan_type_3').hide();
                    $('#taskplan_edit_form [name=taskplan_type][value=1]').prop('checked', true);
                } else if(facility_state == 2 || facility_state == 4) {
                    $('#taskplan_type_1').hide();
                    $('#taskplan_type_2').show();
                    $('#taskplan_type_3').show();
                } else if(facility_state == 3) {
                    $('#taskplan_type_1').hide();
                    $('#taskplan_type_2').show();
                    $('#taskplan_type_3').hide();
                    $('#taskplan_edit_form [name=taskplan_type][value=2]').prop('checked', true);
                } else if(facility_state == 5) {
                    $('#taskplan_type_1').hide();
                    $('#taskplan_type_2').hide();
                    $('#taskplan_type_3').show();
                    $('#taskplan_edit_form [name=taskplan_type][value=3]').prop('checked', true);
                } else {
                    $('#taskplan_type_1').hide();
                    $('#taskplan_type_2').hide();
                    $('#taskplan_type_3').hide();
                }
                $('#taskplan_edit_form [name=taskplan_id]').val(taskplan_id);
                $('#taskplan_edit_form [name=team_id]').dropdown('set selected', team_id);

                $('#taskplan_edit_modal').modal('show');

            });
            $('#taskplan_delete_submit').click(function() {

                $('#taskplan_edit_form [name=taskplan_type]').val(-1);
                $('#taskplan_edit_form').submit();

            });
            $('#taskplan_edit_submit').click(function() {

                $('#taskplan_edit_form').submit();

            });

            //작업기록수정, 삭제
            $('.task_edit').click(function() {

                var task_date = $(this).parent().prev().prev().prev().prev().text();
                var task_type = $(this).parent().prev().prev().prev().text();
                
                switch(task_type) {
                    case "설치작업": task_type = 1; break;
                    case "수정작업": task_type = 2; break;
                    case "해체작업": task_type = 3; break;
                }

                var team = $(this).parent().prev().prev().prev().text();
                var manday = $(this).parent().prev().clone().children().remove().end().text().replace(/(공수|명)/g, "");
                var task_id = $(this).data('task_id');
                var team_id = $(this).data('team_id');

                $('#task_edit_date').text(task_date);
                $('#task_edit_form [name=team_id]').dropdown('set selected', team_id);
                $('#task_edit_form [name=task_type]').dropdown('set selected', task_type);
                $('#task_edit_form [name=manday]').val(manday);
                $('#task_edit_form [name=task_id]').val(task_id);

                $('#task_edit_modal').modal('setting', {
                    autofocus: false,
                }).modal('show');

            });
            $('#task_delete_submit2').click(function() {

                $('#task_edit_form [name=manday]').val(-1);
                $('#task_edit_form').submit();

            });
            $('#task_edit_submit').click(function() {

                $('#task_edit_form').submit();

            });

            //작업기록 추가
            $('#task_add').click(function() {

                var now = new Date();
                now = now.getFullYear() + '-' + ('0'+(now.getMonth()+1)).slice(-2) + '-' + ('0'+now.getDate()).slice(-2) + ' ' + ('0'+now.getHours()).slice(-2) + ':' + ('0'+now.getMinutes()).slice(-2) + ":" + ('0'+now.getSeconds()).slice(-2);

                var taskplan_type = $(this).data('taskplan_type');
                var team_id = $(this).data('team_id');

                //$('#calendar_display').text(now);
                $('#task_calendar').calendar('set date', now);
                $('#task_calendar').calendar('set mode', 'day');
                $('#task_add_form [name=task_type]').dropdown('set selected', taskplan_type);
                $('#task_add_form [name=team_id]').dropdown('set selected', team_id);

                $('#task_add_modal').modal('show');

            });
            $('#task_add_submit').click(function() {

                $('#task_add_form').submit();

            });

        });

    </script>

<?= $this->endSection() ?>