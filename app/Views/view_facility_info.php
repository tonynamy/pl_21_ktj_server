<?= $this->extend('common_layout') ?>

<?= $this->section('content') ?>

<form method="POST">

    <div style="width:fit-content; margin:0 auto; padding:16px;">
        <div class="uiframe" style="width:1200px;">

            <div style="display:flex; justify-content:space-between; align-items:center;">

                <div style="display:flex; align-items:center;">
                    <i class="arrow left icon" onclick="history.back()" style="cursor: pointer;"></i> <!-- 뒤로가기 -->
                    <p style="font-size:x-large; font-weight:normal; color:black;"><?= $facility['serial'] ?></p>
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
                            <td style="color:blue;" class="data_edit" data-type="1">[수정]</td>
                        </tr>
                        <tr>
                            <td>담당자</td>
                            <td><?= $facility['super_manager'] ?></td>
                            <td style="color:blue;" class="data_edit"><?= empty($facility['super_manager']) ? "[생성]" : "[수정]" ?></td>
                        </tr>
                        <tr>
                            <td>사용업체</td>
                            <td><?= $facility['subcontractor'] ?></td>
                            <td style="color:blue;" class="data_edit"><?= empty($facility['subcontractor']) ? "[생성]" : "[수정]" ?></td>
                        </tr>
                        <tr>
                            <td>설치동</td>
                            <td><?= $facility['building'] ?></td>
                            <td style="color:blue;" class="data_edit"><?= empty($facility['building']) ? "[생성]" : "[수정]" ?></td>
                        </tr>
                        <tr>
                            <td>설치층</td>
                            <td><?= $facility['floor'] ?></td>
                            <td style="color:blue;" class="data_edit"><?= empty($facility['floor']) ? "[생성]" : "[수정]" ?></td>
                        </tr>
                        <tr>
                            <td>설치위치</td>
                            <td><?= $facility['spot'] ?></td>
                            <td style="color:blue;" class="data_edit"><?= empty($facility['spot']) ? "[생성]" : "[수정]" ?></td>
                        </tr>
                        <tr>
                            <td>설치구간</td>
                            <td><?= $facility['section'] ?></td>
                            <td style="color:blue;" class="data_edit"><?= empty($facility['section']) ? "[생성]" : "[수정]" ?></td>
                        </tr>
                        <tr>
                            <td>설치목적</td>
                            <td><?= $facility['purpose'] ?></td>
                            <td style="color:blue;" class="data_edit"><?= empty($facility['purpose']) ? "[생성]" : "[수정]" ?></td>
                        </tr>
                        
                        <?php if(empty($facility['danger_data']) || $facility['danger_result'] == 0) : ?>
                            <tr>
                                <td>강관비계 산출식</td>
                                <td><?= $facility['cube_data'] ?></td>
                                <td style="color:blue;" class="data_edit"><?= empty($facility['cube_data']) ? "[생성]" : "[수정]" ?></td>
                            </tr>
                            <tr>
                                <td>강관비계 물량</td>
                                <td><?= $facility['cube_result'] != 0 ? $facility['cube_result'] . "<span>㎥</span>" : "" ?></td>
                                <td style="color:blue;" class="data_edit"><?= empty($facility['cube_result']) ? "[생성]" : "[수정]" ?></td>
                            </tr>
                        <?php endif ?>

                        <tr>
                            <td>안전발판 산출식</td>
                            <td><?= $facility['area_data'] ?></td>
                            <td style="color:blue;" class="data_edit"><?= empty($facility['area_data']) ? "[생성]" : "[수정]" ?></td>
                        </tr>
                        <tr>
                            <td>안전발판 물량</td>
                            <td><?= $facility['area_result'] != 0 ? $facility['area_result'] . "<span>㎡</span>" : "" ?></td>
                            <td style="color:blue;" class="data_edit"><?= empty($facility['area_result']) ? "[생성]" : "[수정]" ?></td>
                        </tr>

                        <?php if(empty($facility['cube_data']) || $facility['cube_result'] == 0) : ?>
                            <tr>
                                <td>달대비계 산출식</td>
                                <td><?= $facility['danger_data'] ?></td>
                                <td style="color:blue;" class="data_edit"><?= empty($facility['danger_data']) ? "[생성]" : "[수정]" ?></td>
                            </tr>
                            <tr>
                                <td>달대비계 물량</td>
                                <td><?= $facility['danger_result'] != 0 ? $facility['danger_result'] . "㎡" : "" ?></td>
                                <td style="color:blue;" class="data_edit"><?= empty($facility['danger_result']) ? "[생성]" : "[수정]" ?></td>
                            </tr>
                        <?php endif ?>

                        <tr>
                            <td>도면등록일</td>
                            <td><?= $facility['created_at'] ?></td>
                            <td style="color:blue;" class="data_edit"><?= is_null($facility['created_at']) || empty($facility['created_at']) ? "[생성]" : "[수정]" ?></td>
                        </tr>
                        <tr>
                            <td>설치시작일</td>
                            <td><?= $facility['started_at'] ?></td>
                            <td style="color:blue;" class="data_edit"><?= is_null($facility['started_at']) || empty($facility['started_at']) ? "[생성]" : "[수정]" ?></td>
                        </tr>
                        <tr>
                            <td>승인완료일</td>
                            <td><?= $facility['finished_at'] ?></td>
                            <td style="color:blue;" class="data_edit"><?= is_null($facility['finished_at']) || empty($facility['finished_at']) ? "[생성]" : "[수정]" ?></td>
                        </tr>
                        <tr>
                            <td>수정시작일</td>
                            <td><?= $facility['edit_started_at'] ?></td>
                            <td style="color:blue;" class="data_edit"><?= is_null($facility['edit_started_at']) || empty($facility['edit_started_at']) ? "[생성]" : "[수정]" ?></td>
                        </tr>
                        <tr>
                            <td>수정완료일</td>
                            <td><?= $facility['edit_finished_at'] ?></td>
                            <td style="color:blue;" class="data_edit"><?= is_null($facility['edit_finished_at']) || empty($facility['edit_finished_at']) ? "[생성]" : "[수정]" ?></td>
                        </tr>
                        <tr>
                            <td>해체시작일</td>
                            <td><?= $facility['dis_started_at'] ?></td>
                            <td style="color:blue;" class="data_edit"><?= is_null($facility['dis_started_at']) || empty($facility['dis_started_at']) ? "[생성]" : "[수정]" ?></td>
                        </tr>
                        <tr>
                            <td>해체완료일</td>
                            <td><?= $facility['dis_finished_at'] ?></td>
                            <td style="color:blue;" class="data_edit"><?= is_null($facility['dis_finished_at']) || empty($facility['dis_finished_at']) ? "[생성]" : "[수정]" ?></td>
                        </tr>
                        <tr>
                            <td>만료일</td>
                            <td><?= $facility['expired_at'] ?></td>
                            <td style="color:blue;" class="data_edit"><?= is_null($facility['expired_at']) || empty($facility['expired_at']) ? "[생성]" : "[수정]" ?></td>
                        </tr>
                        <tr>
                            <td>비고</td>
                            <td><?= $facility['memo'] ?></td>
                            <td style="color:blue;" class="data_edit"><?= is_null($facility['memo']) || empty($facility['memo']) ? "[생성]" : "[수정]" ?></td>
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

<div class="ui modal">
  <div class="header">데이터 수정</div>
  <div class="content">

    <form class="ui form" method="POST" action="/fm/edit_facility_info">
        <div class="field">
            <label>변경할 데이터</label>
            <input id="input_edit_data" type="text" name="data" placeholder="변경할 데이터">
        </div>
        <input id="input_edit_type" type="hidden" name="type" value="">
        <input id="input_edit_type" type="hidden" name="id" value="<?=$facility['id']?>">
        <button class="ui button" type="submit">데이터 수정</button>
    </form>
    
  </div>
</div>



<?= $this->endSection() ?>

<?= $this->section('custom_js') ?>

    <script type="text/javascript">

        $(document).ready(function() {

            $('td.data_edit').click(function() {

                var data = $(this).prev().clone().children().remove().end().text();

                $('#input_edit_data').val(data);
                
                var type = $(this).data('type');

                $('#input_edit_type').val(type);

                $('.ui.modal').modal('show');

            });

        });

    </script>

<?= $this->endSection() ?>