<?= $this->extend('common_layout') ?>

<?= $this->section('content') ?>

<form method="POST">

    <div style="width:fit-content; margin:0 auto; padding:16px;">
        <div class="uiframe" style="width:2600px;">
            
            <div style="display:flex; align-items:center; padding:16px;">
                <i class="hamburger icon" onclick="location.href='/fm/menu'" style="cursor: pointer;"></i>
                <select id="mode_select" class="ui dropdown">
                    <option value="1" selected> 도면있는 작업 조회 </option>
                    <option value="2"> 도면없는 작업 조회 </option>
                </select>
            </div>

            <div style="height:1px; background-color:#e8e9e9;"></div>

            <div style="padding:16px">

                <div style="display:flex;">

                    <div style="display:flex; align-items:center; margin-right:16px;">
                        <form action="" method="POST">
                            <label style="width:fit-content; margin-right:8px;">승인번호검색</label>

                            <input type="text" name="search_serial" style="width:180px;" value="<?= $search_serial ?>">
                            <button type="submit" class="bluebutton" style="padding: 11px 16px 11px 16px;">검색</button>
                        </form>

                    </div>

                    <div style="display:flex; align-items:center; margin-right:16px;">
                        <label style="width:fit-content; margin-right:8px;">필터</label>
                        <div class="ui multiple selection four column clearable dropdown" style="width:600px">
                            <input id="state_select" type="hidden" name="state" value="<?= $state ?>">
                            <i class="dropdown icon"></i>
                            <div class="default text" style="margin-bottom:0px"></div>
                            <div class="menu">
                                <div class="item" data-value="1">설비</div>
                                <div class="item" data-value="2">전기</div>
                                <div class="item" data-value="3">건축</div>
                                <div class="item" data-value="4">기타</div>
                                <div class="item" data-value="o">원도면</div>
                                <div class="item" data-value="r">수정도면</div>
                                <div class="item" data-value="l">최종도면</div>
                                <div class="item" data-value="a">설치전</div>
                                <div class="item" data-value="b">설치중</div>
                                <div class="item" data-value="c">승인완료</div>
                                <div class="item" data-value="d">수정중</div>
                                <div class="item" data-value="e">수정완료</div>
                                <div class="item" data-value="f">해체중</div>
                                <div class="item" data-value="g">해체완료</div>
                            </div>
                        </div>
                    </div>

                    <div style="display:flex; align-items:center;">
                        <label style="width:fit-content; margin-right:8px;">사용업체</label>
                        <div style="width:200px">
                            <select class="ui fluid dropdown" name="subcontractor">

                                <option value=""></option>
                                
                                <?php $old_subcontractor = old('subcontractor') ?>

                                <?php foreach($subcontractors as $subcontractor) : ?>

                                    <option value="<?= $subcontractor ?>"    <?= $subcontractor == $old_subcontractor ? "SELECTED" : "" ?>    > <?= $subcontractor ?> </option>

                                <?php endforeach ?>

                            </select>
                        </div>
                    </div>

                </div>

                <?php $facilities_count = count($facilities); ?>

                <p style="margin-top:16px; margin-bottom:8px">총: <?= $facilities_count ?>개</p>

                <div>

                    <table class="ui sortable compact selectable celled table" style="table-layout:fixed;">

                        <thead>
                            <tr align="center">
                                <th width="150px" height="40px" style="font-weight:normal; border-right:0px; padding:0px">승인번호</th>
                                <th width="55px" style="font-weight:normal; border-left:0px; border-right:0px; padding:0px">공종</th>
                                <th width="100px" style="font-weight:normal; border-left:0px; border-right:0px; padding:0px">담당자</th>
                                <th width="80px" style="font-weight:normal; border-left:0px; border-right:0px; padding:0px">사용업체</th>
                                <th width="65px" style="font-weight:normal; border-left:0px; border-right:0px; padding:0px">설치동</th>
                                <th width="50px" style="font-weight:normal; border-left:0px; border-right:0px; padding:0px">층</th>
                                <th width="140px" style="font-weight:normal; border-left:0px; border-right:0px; padding:0px">설치위치</th>
                                <th width="80px" style="font-weight:normal; border-left:0px; border-right:0px; padding:0px">설치구간</th>
                                <th width="150px" style="font-weight:normal; border-left:0px; border-right:0px; padding:0px">설치목적</th>
                                <th width="180px" style="font-weight:normal; border-left:0px; border-right:0px; padding:0px">강관비계 산출식</th>
                                <th width="75px" style="font-weight:normal; border-left:0px; border-right:0px; padding:0px">물량(㎥)</th>
                                <th width="180px" style="font-weight:normal; border-left:0px; border-right:0px; padding:0px">안전발판 산출식</th>
                                <th width="75px" style="font-weight:normal; border-left:0px; border-right:0px; padding:0px">물량(㎡)</th>
                                <th width="180px" style="font-weight:normal; border-left:0px; border-right:0px; padding:0px">달대비계 산출식</th>
                                <th width="75px" style="font-weight:normal; border-left:0px; border-right:0px; padding:0px">물량(㎡)</th>
                                <th width="95px" style="font-weight:normal; border-left:0px; border-right:0px; padding:0px">도면등록일</th>
                                <th width="95px" style="font-weight:normal; border-left:0px; border-right:0px; padding:0px">설치시작일</th>
                                <th width="95px" style="font-weight:normal; border-left:0px; border-right:0px; padding:0px">승인완료일</th>
                                <th width="95px" style="font-weight:normal; border-left:0px; border-right:0px; padding:0px">수정시작일</th>
                                <th width="95px" style="font-weight:normal; border-left:0px; border-right:0px; padding:0px">수정완료일</th>
                                <th width="95px" style="font-weight:normal; border-left:0px; border-right:0px; padding:0px">해체시작일</th>
                                <th width="95px" style="font-weight:normal; border-left:0px; border-right:0px; padding:0px">해체완료일</th>
                                <th width="95px" style="font-weight:normal; border-left:0px; border-right:0px; padding:0px">만료일</th>
                                <th style="font-weight:normal; border-left:0px; padding:0px">비고</td>
                            </tr>

                        </thead>

                        <?php foreach($facilities as $facility) : ?>

                            <?php
                                //공종
                                switch($facility['type']) {
                                    case "1":
                                        $type_string = "설비"; break;
                                    case "2":
                                        $type_string = "전기"; break;
                                    case "3":
                                        $type_string = "건축"; break;
                                    default:
                                        $type_string = "기타"; break;
                                }

                                //길이에 따라 글자크기 바꿈
                                $serial_fontsize = mb_strlen($facility['serial'],'utf-8') > 14 ? "font-size:small;" : "";
                                $super_manager_fontsize = mb_strlen($facility['super_manager'],'utf-8') > 6 ? "font-size:small;" : "";
                                if(mb_strlen($facility['subcontractor'],'utf-8') > 7) {
                                    $subcontractor_fontsize = "font-size:x-small;";
                                } else if(mb_strlen($facility['subcontractor'],'utf-8') > 5) {
                                    $subcontractor_fontsize = "font-size:smaller;";
                                } else if(mb_strlen($facility['subcontractor'],'utf-8') > 3) {
                                    $subcontractor_fontsize = "font-size:small;";
                                } else {
                                    $subcontractor_fontsize = "";
                                }
                                $building_fontsize = mb_strlen($facility['building'],'utf-8') > 5 ? "font-size:small;" : "";
                                $spot_fontsize = mb_strlen($facility['spot'],'utf-8') > 10 ? "font-size:small;" : "";
                                $section_fontsize = mb_strlen($facility['section'],'utf-8') > 3 ? "font-size:smaller;" : "";
                                $purpose_fontsize = mb_strlen($facility['purpose'],'utf-8') > 20 ? "font-size:smaller;" : (mb_strlen($facility['purpose'],'utf-8') > 10 ? "font-size:small;" : "");
                                if(mb_strlen($facility['cube_data'],'utf-8') > 50) {
                                    $cube_data_fontsize = "font-size:x-small;";
                                } else if(mb_strlen($facility['cube_data'],'utf-8') > 35) {
                                    $cube_data_fontsize = "font-size:smaller;";
                                } else if(mb_strlen($facility['cube_data'],'utf-8') > 20) {
                                    $cube_data_fontsize = "font-size:small;";
                                } else {
                                    $cube_data_fontsize = "";
                                }
                                $cube_result_fontsize = mb_strlen($facility['cube_result'],'utf-8') > 6 ? "font-size:smaller;" : "";
                                if(mb_strlen($facility['area_data'],'utf-8') > 50) {
                                    $area_data_fontsize = "font-size:x-small;";
                                } else if(mb_strlen($facility['area_data'],'utf-8') > 35) {
                                    $area_data_fontsize = "font-size:smaller;";
                                } else if(mb_strlen($facility['area_data'],'utf-8') > 20) {
                                    $area_data_fontsize = "font-size:small;";
                                } else {
                                    $area_data_fontsize = "";
                                }
                                $area_result_fontsize = mb_strlen($facility['area_result'],'utf-8') > 6 ? "font-size:smaller;" : "";
                                if(mb_strlen($facility['danger_data'],'utf-8') > 50) {
                                    $danger_data_fontsize = "font-size:x-small;";
                                } else if(mb_strlen($facility['danger_data'],'utf-8') > 35) {
                                    $danger_data_fontsize = "font-size:smaller;";
                                } else if(mb_strlen($facility['danger_data'],'utf-8') > 20) {
                                    $danger_data_fontsize = "font-size:small;";
                                } else {
                                    $danger_data_fontsize = "";
                                }
                                $danger_result_fontsize = mb_strlen($facility['danger_result'],'utf-8') > 6 ? "font-size:smaller;" : "";
                                if(mb_strlen($facility['memo'],'utf-8') > 20) {
                                    $memo_fontsize = "font-size:xx-small;";
                                } else if(mb_strlen($facility['memo'],'utf-8') > 16) {
                                    $memo_fontsize = "font-size:x-small;";
                                } else if(mb_strlen($facility['memo'],'utf-8') > 12) {
                                    $memo_fontsize = "font-size:smaller;";
                                } else if(mb_strlen($facility['memo'],'utf-8') > 8) {
                                    $memo_fontsize = "font-size:small;";
                                } else {
                                    $memo_fontsize = "";
                                }

                                //물량 0이면 표시 안하기
                                $cube_result = $facility['cube_result'] != "0" ? $facility['cube_result'] : "";
                                $area_result = $facility['area_result'] != "0" ? $facility['area_result'] : "";
                                $danger_result = $facility['danger_result'] != "0" ? $facility['danger_result'] : "";

                                //날짜 문자열로
                                $created_at = !is_null($facility['created_at']) ? CodeIgniter\I18n\Time::createFromFormat('Y-m-d H:i:s', $facility['created_at'])->toDateString() : "";
                                $started_at = !is_null($facility['started_at']) ? CodeIgniter\I18n\Time::createFromFormat('Y-m-d H:i:s', $facility['started_at'])->toDateString() : "";
                                $finished_at = !is_null($facility['finished_at']) ? CodeIgniter\I18n\Time::createFromFormat('Y-m-d H:i:s', $facility['finished_at'])->toDateString() : "";
                                $edit_started_at = !is_null($facility['edit_started_at']) ? CodeIgniter\I18n\Time::createFromFormat('Y-m-d H:i:s', $facility['edit_started_at'])->toDateString() : "";
                                $edit_finished_at = !is_null($facility['edit_finished_at']) ? CodeIgniter\I18n\Time::createFromFormat('Y-m-d H:i:s', $facility['edit_finished_at'])->toDateString() : "";
                                $dis_started_at = !is_null($facility['dis_started_at']) ? CodeIgniter\I18n\Time::createFromFormat('Y-m-d H:i:s', $facility['dis_started_at'])->toDateString() : "";
                                $dis_finished_at = !is_null($facility['dis_finished_at']) ? CodeIgniter\I18n\Time::createFromFormat('Y-m-d H:i:s', $facility['dis_finished_at'])->toDateString() : "";
                                $expired_at = !is_null($facility['expired_at']) ? CodeIgniter\I18n\Time::createFromFormat('Y-m-d H:i:s', $facility['expired_at'])->toDateString() : "";

                            ?>

                            <tr class="facility select" data-id="<?= $is_guest ? -1 : $facility['id'] ?>" align="center">
                                <td style="height: 52px; <?= $serial_fontsize ?>"><?= $facility['serial'] ?></td>
                                <td><?= $type_string ?></td>
                                <td style=<?= $super_manager_fontsize ?>><?= $facility['super_manager'] ?></td>
                                <td style=<?= $subcontractor_fontsize ?>><?= $facility['subcontractor'] ?></td>
                                <td style=<?= $building_fontsize ?>><?= $facility['building'] ?></td>
                                <td><?= $facility['floor'] ?></td>
                                <td style=<?= $spot_fontsize ?>><?= $facility['spot'] ?></td>
                                <td style=<?= $section_fontsize ?>><?= $facility['section'] ?></td>
                                <td style=<?= $purpose_fontsize ?>><?= $facility['purpose'] ?></td>
                                <td style=<?= $cube_data_fontsize ?>><?= $facility['cube_data'] ?></td>
                                <td style=<?= $cube_result_fontsize ?>><?= $cube_result ?></td>
                                <td style=<?= $area_data_fontsize ?>><?= $facility['area_data'] ?></td>
                                <td style=<?= $area_result_fontsize ?>><?= $area_result ?></td>
                                <td style=<?= $danger_data_fontsize ?>><?= $facility['danger_data'] ?></td>
                                <td style=<?= $danger_result_fontsize ?>><?= $danger_result ?></td>
                                <td style="font-size:small"><?= $created_at ?></td>
                                <td style="font-size:small"><?= $started_at ?></td>
                                <td style="font-size:small"><?= $finished_at ?></td>
                                <td style="font-size:small"><?= $edit_started_at ?></td>
                                <td style="font-size:small"><?= $edit_finished_at ?></td>
                                <td style="font-size:small"><?= $dis_started_at ?></td>
                                <td style="font-size:small"><?= $dis_finished_at ?></td>
                                <td style="font-size:small"><?= $expired_at ?></td>
                                <td style=<?= $memo_fontsize ?>><?= $facility['memo'] ?></td>
                            </tr>
                        
                        <?php endforeach ?>


                    </table>

                </div>
            
            </div>

        </div>
    </div>

</form>



<?= $this->endSection() ?>

<?= $this->section('custom_js') ?>

<script type="text/javascript">

    $(document).ready(function() {
        
        $('#mode_select').val(1);
        $('#mode_select').on('change', function() {
            if(this.value == 2) {
            location.href = '/fm/view_etc_task';
            }
        });

        $('#state_select').on('change', function() {

            arr = this.value.split(",");
            arr.sort();
            state_num = arr.join("");
            location.href = '/fm/view_facility/' + state_num;

        });

        $('tr.facility.select').click(function() {

            var id = $(this).data('id');

            if(id < 0) return;

            location.href = '/fm/view_facility_info/' + id;

        });

        $('table').tablesort();

    });


</script>

<?= $this->endSection() ?>