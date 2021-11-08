<?= $this->extend('common_layout') ?>

<?= $this->section('content') ?>

<form method="POST">

    <div style="width:fit-content; margin:0 auto; padding:16px;">
        <div class="uiframe" style="width:1200px; padding:16px;">

            <div style="display:flex; justify-content:space-between; align-items:center;">

                <div style="display:flex; align-items:center;">
                    <i class="arrow left icon" onclick="history.back()" style="cursor: pointer;"></i> <!-- 뒤로가기 -->
                    <span style="font-size:x-large; font-weight:normal;"><?= $facility['serial'] ?></span>
                </div>
                
                <button class="filletbutton" type="button">도면삭제</button>
            </div>

            <div style="width:100%; display:flex; justify-content:space-between; padding-left:16px; padding-right:16px;">

                <div style="width:500px;">

                    <table class="ui very basic table" style="margin-top:-16px; margin-bottom:-16px;">
                        <tr>
                            <td colspan="3"></td>
                        </tr>
                        <tr>
                            <td>공종</td>
                            <td><?= getTypeText($facility['type']) ?></td>
                            <td style="color:blue; cursor:pointer;" class="data_edit" data-type="1">[수정]</td>
                        </tr>
                        <tr>
                            <td>담당자</td>
                            <td><?= $facility['super_manager'] ?></td>
                            <td style="color:blue; cursor:pointer;" class="data_edit" data-type="2"><?= empty($facility['super_manager']) ? "[생성]" : "[수정]" ?></td>
                        </tr>
                        <tr>
                            <td>사용업체</td>
                            <td><?= $facility['subcontractor'] ?></td>
                            <td style="color:blue; cursor:pointer;" class="data_edit" data-type="3"><?= empty($facility['subcontractor']) ? "[생성]" : "[수정]" ?></td>
                        </tr>
                        <tr>
                            <td>설치동</td>
                            <td><?= $facility['building'] ?></td>
                            <td style="color:blue; cursor:pointer;" class="data_edit" data-type="4"><?= empty($facility['building']) ? "[생성]" : "[수정]" ?></td>
                        </tr>
                        <tr>
                            <td>설치층</td>
                            <td><?= $facility['floor'] ?></td>
                            <td style="color:blue; cursor:pointer;" class="data_edit" data-type="5"><?= empty($facility['floor']) ? "[생성]" : "[수정]" ?></td>
                        </tr>
                        <tr>
                            <td>설치위치</td>
                            <td><?= $facility['spot'] ?></td>
                            <td style="color:blue; cursor:pointer;" class="data_edit" data-type="6"><?= empty($facility['spot']) ? "[생성]" : "[수정]" ?></td>
                        </tr>
                        <tr>
                            <td>설치구간</td>
                            <td><?= $facility['section'] ?></td>
                            <td style="color:blue; cursor:pointer;" class="data_edit" data-type="7"><?= empty($facility['section']) ? "[생성]" : "[수정]" ?></td>
                        </tr>
                        <tr>
                            <td>설치목적</td>
                            <td><?= $facility['purpose'] ?></td>
                            <td style="color:blue; cursor:pointer;" class="data_edit" data-type="8"><?= empty($facility['purpose']) ? "[생성]" : "[수정]" ?></td>
                        </tr>
                        
                        <?php if(empty($facility['danger_data']) || $facility['danger_result'] == 0) : ?>
                            <tr>
                                <td>강관비계 산출식</td>
                                <td><?= $facility['cube_data'] ?></td>
                                <td style="color:blue; cursor:pointer;" class="data_edit" data-type="9"><?= empty($facility['cube_data']) ? "[생성]" : "[수정]" ?></td>
                            </tr>
                            <tr>
                                <td>강관비계 물량</td>
                                <td><?= $facility['cube_result'] != 0 ? $facility['cube_result'] . "<span>㎥</span>" : "" ?></td>
                                <td style="color:blue; cursor:pointer;" class="data_edit" data-type="10"><?= empty($facility['cube_result']) ? "[생성]" : "[수정]" ?></td>
                            </tr>
                        <?php endif ?>

                        <tr>
                            <td>안전발판 산출식</td>
                            <td><?= $facility['area_data'] ?></td>
                            <td style="color:blue; cursor:pointer;" class="data_edit" data-type="11"><?= empty($facility['area_data']) ? "[생성]" : "[수정]" ?></td>
                        </tr>
                        <tr>
                            <td>안전발판 물량</td>
                            <td><?= $facility['area_result'] != 0 ? $facility['area_result'] . "㎡" : "" ?></td>
                            <td style="color:blue; cursor:pointer;" class="data_edit" data-type="12"><?= empty($facility['area_result']) ? "[생성]" : "[수정]" ?></td>
                        </tr>

                        <?php if(empty($facility['cube_data']) || $facility['cube_result'] == 0) : ?>
                            <tr>
                                <td>달대비계 산출식</td>
                                <td><?= $facility['danger_data'] ?></td>
                                <td style="color:blue; cursor:pointer;" class="data_edit" data-type="13"><?= empty($facility['danger_data']) ? "[생성]" : "[수정]" ?></td>
                            </tr>
                            <tr>
                                <td>달대비계 물량</td>
                                <td><?= $facility['danger_result'] != 0 ? $facility['danger_result'] . "㎡" : "" ?></td>
                                <td style="color:blue; cursor:pointer;" class="data_edit" data-type="14"><?= empty($facility['danger_result']) ? "[생성]" : "[수정]" ?></td>
                            </tr>
                        <?php endif ?>

                        <tr>
                            <td>도면등록일</td>
                            <td><?= $facility['created_at'] ?></td>
                            <td style="color:blue; cursor:pointer;" class="data_edit" data-type="15"><?= is_null($facility['created_at']) || empty($facility['created_at']) ? "[생성]" : "[수정]" ?></td>
                        </tr>
                        <tr>
                            <td>설치시작일</td>
                            <td><?= $facility['started_at'] ?></td>
                            <td style="color:blue; cursor:pointer;" class="data_edit" data-type="16"><?= is_null($facility['started_at']) || empty($facility['started_at']) ? "[생성]" : "[수정]" ?></td>
                        </tr>
                        <tr>
                            <td>승인완료일</td>
                            <td><?= $facility['finished_at'] ?></td>
                            <td style="color:blue; cursor:pointer;" class="data_edit" data-type="17"><?= is_null($facility['finished_at']) || empty($facility['finished_at']) ? "[생성]" : "[수정]" ?></td>
                        </tr>
                        <tr>
                            <td>수정시작일</td>
                            <td><?= $facility['edit_started_at'] ?></td>
                            <td style="color:blue; cursor:pointer;" class="data_edit" data-type="18"><?= is_null($facility['edit_started_at']) || empty($facility['edit_started_at']) ? "[생성]" : "[수정]" ?></td>
                        </tr>
                        <tr>
                            <td>수정완료일</td>
                            <td><?= $facility['edit_finished_at'] ?></td>
                            <td style="color:blue; cursor:pointer;" class="data_edit" data-type="19"><?= is_null($facility['edit_finished_at']) || empty($facility['edit_finished_at']) ? "[생성]" : "[수정]" ?></td>
                        </tr>
                        <tr>
                            <td>해체시작일</td>
                            <td><?= $facility['dis_started_at'] ?></td>
                            <td style="color:blue; cursor:pointer;" class="data_edit" data-type="20"><?= is_null($facility['dis_started_at']) || empty($facility['dis_started_at']) ? "[생성]" : "[수정]" ?></td>
                        </tr>
                        <tr>
                            <td>해체완료일</td>
                            <td><?= $facility['dis_finished_at'] ?></td>
                            <td style="color:blue; cursor:pointer;" class="data_edit" data-type="21"><?= is_null($facility['dis_finished_at']) || empty($facility['dis_finished_at']) ? "[생성]" : "[수정]" ?></td>
                        </tr>
                        <tr>
                            <td>만료일</td>
                            <td><?= $facility['expired_at'] ?></td>
                            <td style="color:blue; cursor:pointer;" class="data_edit" data-type="22"><?= is_null($facility['expired_at']) || empty($facility['expired_at']) ? "[생성]" : "[수정]" ?></td>
                        </tr>
                        <tr>
                            <td>비고</td>
                            <td><?= $facility['memo'] ?></td>
                            <td style="color:blue; cursor:pointer;" class="data_edit" data-type="23"><?= is_null($facility['memo']) || empty($facility['memo']) ? "[생성]" : "[수정]" ?></td>
                        </tr>
                        <tr><td colspan="3"></td></tr>
                    </table>
                </div>

                <div style="width:500px; margin-top:24px; margin-right:16px;">

                    <?php if(count($tasks) > 0) : ?>
                        <table style="width:100%;">
                            <tr>
                                <td colspan="5">※작업내역</td>
                            </tr>

                            <?php foreach($tasks as $task) : ?>
                                <tr>
                                    <td><?= $task['created_at'] ?></td>
                                    <td style="width:100px"><?= $task['team_name'] ?></td>
                                    <td style="width:100px"><?= getTaskTypeText($task['type']) ?>작업</td>
                                    <td style="width:70px"><?= $task['manday'] ?>공수</td>
                                    <td style="width:60px; color:red;">[삭제]</td>
                                </tr>
                            <?php endforeach ?>

                        </table>
                    <?php endif ?>
                </div>
            </div>

        </div>
    </div>

</form>

<!-- 일반모달부분 -->
<div id="edit_fac_data_modal" class="ui mini modal" style="padding:16px;">

    <form id="edit_data_form" method="POST" action="/fm/edit_facility_info">
        <div class="ui input" style="display:flex; align-items:center; margin-bottom:24px;">
            <label id="label_data_name" style="width:80px; padding-right:8px">데이터제목</label>
            <input id="input_edit_data" type="text" name="data" placeholder="변경할 데이터">
        </div>

        <div class="actions" style="text-align:right;">
            <span class="cancel" style="color:#5599DD; cursor:pointer; margin-right:32px;">취소</span>
            <span class="submit" style="color:#5599DD; cursor:pointer;">수정</span>
        </div>
        
        <input id="input_edit_type" type="hidden" name="data_type" value="">
        <input type="hidden" name="id" value="<?=$facility['id']?>">
    </form>

</div>

<!-- 달력모달부분 -->


<?= $this->endSection() ?>

<?= $this->section('custom_js') ?>

    <script type="text/javascript">

        $(document).ready(function() {

            $('td.data_edit').click(function() {

                var data = $(this).prev().clone().children().remove().end().text();

                $('#input_edit_data').val(data);

                var data_type = $(this).data('type');
                var data_name = '';
                switch(data_type) {
                    case 1: data_name = '공종'; break;
                    case 2: data_name = '담당자'; break;
                    case 3: data_name = '사용업체'; break;
                    case 4: data_name = '설치동'; break;
                    case 5: data_name = '설치층'; break;
                    case 6: data_name = '설치위치'; break;
                    case 7: data_name = '설치구간'; break;
                    case 8: data_name = '설치목적'; break;
                    case 9: data_name = '강관비계 산출식'; break;
                    case 10: data_name = '강관비계 물량'; break;
                    case 11: data_name = '안전발판 산출식'; break;
                    case 12: data_name = '안전발판 물량'; break;
                    case 13: data_name = '달대비계 물량'; break;
                    case 14: data_name = '달대비계 물량'; break;
                    case 15: data_name = '도면등록일'; break;
                    case 16: data_name = '설치시작일'; break;
                    case 17: data_name = '승인완료일'; break;
                    case 18: data_name = '수정시작일'; break;
                    case 19: data_name = '수정완료일'; break;
                    case 20: data_name = '해체시작일'; break;
                    case 21: data_name = '해체완료일'; break;
                    case 22: data_name = '만료일'; break;
                    case 23: data_name = '비고'; break;
                }
                

                $('#label_data_name').text(data_name);
                $('#input_edit_type').val(data_type);

                if(data_type < 15 || data_type == 23) {
                    $('#edit_fac_data_modal').modal('show');
                } else {
                    
                }
                
            });

            
            $('span.submit').click(function() {

                $('#edit_data_form').submit();

            });

        });

    </script>

<?= $this->endSection() ?>