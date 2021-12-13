<?= $this->extend('common_layout') ?>

<?= $this->section('content') ?>

<form>

    <div style="width:fit-content; margin:0 auto; padding:16px;">
        <div class="uiframe" style="width:1600px;">

            <div style="padding:16px">
                <i class="arrow left icon" onclick="location.href='/fm/add_facility'" style="cursor: pointer;"></i> <!-- 뒤로가기 -->
                <label>도면등록 결과</label>

                <?php if(count($upload_success_data) > 0) : ?>
                    <div style="margin-top:16px;">※업로드 성공 정보 - 잘못 기입된 정보가 있으면 해당도면에서 수정하거나 해당도면을 삭제 후 다시 등록해주세요.</div>
                    <div style="overflow-x:scroll;">

                        <table class="excel">
                            
                            <tr bgcolor="#DDEEFF" align="center">
                                <td style="white-space:nowrap; padding-left:8px; padding-right:8px;">승인번호</td>
                                <td style="white-space:nowrap; padding-left:8px; padding-right:8px;">공종</td>
                                <td style="white-space:nowrap; padding-left:8px; padding-right:8px;">담당자</td>
                                <td style="white-space:nowrap; padding-left:8px; padding-right:8px;">업체</td>
                                <td style="white-space:nowrap; padding-left:8px; padding-right:8px;">설치동</td>
                                <td style="white-space:nowrap; padding-left:8px; padding-right:8px;">층</td>
                                <td style="white-space:nowrap; padding-left:8px; padding-right:8px;">설치위치</td>
                                <td style="white-space:nowrap; padding-left:8px; padding-right:8px;">설치구간</td>
                                <td style="white-space:nowrap; padding-left:8px; padding-right:8px;">설치목적</td>
                                <td style="white-space:nowrap; padding-left:8px; padding-right:8px;">강관비계 산출식</td>
                                <td style="white-space:nowrap; padding-left:8px; padding-right:8px;">물량(루베)</td>
                                <td style="white-space:nowrap; padding-left:8px; padding-right:8px;">안전발판 산출식</td>
                                <td style="white-space:nowrap; padding-left:8px; padding-right:8px;">물량(헤베)</td>
                                <td style="white-space:nowrap; padding-left:8px; padding-right:8px;">달대비계 산출식</td>
                                <td style="white-space:nowrap; padding-left:8px; padding-right:8px;">물량(헤베)</td>
                                <td style="white-space:nowrap; padding-left:8px; padding-right:8px;">도면등록일</td>
                                <td style="white-space:nowrap; padding-left:8px; padding-right:8px;">설치시작일</td>
                                <td style="white-space:nowrap; padding-left:8px; padding-right:8px;">승인완료일</td>
                                <td style="white-space:nowrap; padding-left:8px; padding-right:8px;">수정시작일</td>
                                <td style="white-space:nowrap; padding-left:8px; padding-right:8px;">수정완료일</td>
                                <td style="white-space:nowrap; padding-left:8px; padding-right:8px;">해체시작일</td>
                                <td style="white-space:nowrap; padding-left:8px; padding-right:8px;">해체완료일</td>
                                <td style="white-space:nowrap; padding-left:8px; padding-right:8px;">만료일</td>
                                <td style="white-space:nowrap; padding-left:8px; padding-right:8px;">비고</td>
                            </tr>

                            <?php foreach($upload_success_data as $data) : ?>

                                <tr onclick="window.open('view_facility_info/<?= $data['serial'] ?>')" style="cursor:pointer" align="center">
                                    <td style="white-space:nowrap; padding-left:8px; padding-right:8px;"><?=$data['serial']?></td>
                                    <td style="white-space:nowrap; padding-left:8px; padding-right:8px;"><?=getTypeText($data['type'])?></td>
                                    <td style="white-space:nowrap; padding-left:8px; padding-right:8px;"><?=$data['super_manager']?></td>
                                    <td style="white-space:nowrap; padding-left:8px; padding-right:8px;"><?=$data['subcontractor']?></td>
                                    <td style="white-space:nowrap; padding-left:8px; padding-right:8px;"><?=$data['building']?></td>
                                    <td style="white-space:nowrap; padding-left:8px; padding-right:8px;"><?=$data['floor']?></td>
                                    <td style="white-space:nowrap; padding-left:8px; padding-right:8px;"><?=$data['spot']?></td>
                                    <td style="white-space:nowrap; padding-left:8px; padding-right:8px;"><?= $data['section'] ?></td>
                                    <td style="white-space:nowrap; padding-left:8px; padding-right:8px;"><?= $data['purpose'] ?></td>
                                    <td style="white-space:nowrap; padding-left:8px; padding-right:8px;"><?= $data['cube_data'] ?></td>
                                    <td style="white-space:nowrap; padding-left:8px; padding-right:8px;"><?= $data['cube_result'] ?></td>
                                    <td style="white-space:nowrap; padding-left:8px; padding-right:8px;"><?= $data['area_data'] ?></td>
                                    <td style="white-space:nowrap; padding-left:8px; padding-right:8px;"><?= $data['area_result'] ?></td>
                                    <td style="white-space:nowrap; padding-left:8px; padding-right:8px;"><?= $data['danger_data'] ?></td>
                                    <td style="white-space:nowrap; padding-left:8px; padding-right:8px;"><?= $data['danger_result'] ?></td>
                                    <td style="white-space:nowrap; padding-left:8px; padding-right:8px;"><?= $data['created_at'] ?></td>
                                    <td style="white-space:nowrap; padding-left:8px; padding-right:8px;"><?= $data['started_at'] ?></td>
                                    <td style="white-space:nowrap; padding-left:8px; padding-right:8px;"><?= $data['finished_at'] ?></td>
                                    <td style="white-space:nowrap; padding-left:8px; padding-right:8px;"><?= $data['edit_started_at'] ?></td>
                                    <td style="white-space:nowrap; padding-left:8px; padding-right:8px;"><?= $data['edit_finished_at'] ?></td>
                                    <td style="white-space:nowrap; padding-left:8px; padding-right:8px;"><?= $data['dis_started_at'] ?></td>
                                    <td style="white-space:nowrap; padding-left:8px; padding-right:8px;"><?= $data['dis_finished_at'] ?></td>
                                    <td style="white-space:nowrap; padding-left:8px; padding-right:8px;"><?= $data['expired_at'] ?></td>
                                    <td style="white-space:nowrap; padding-left:8px; padding-right:8px;"><?=$data['memo']?></td>
                                </tr>

                            <?php endforeach ?>

                        </table>

                    </div>
                <?php endif ?>

                
                <?php if(count($pre_exist_data) > 0) : ?>
                    <!--                                                                                                     새로고침버튼을 눌러주세요. -->
                    <div style="margin-top:16px;">※이미 존재하는 승인번호가 있는 정보 - 해당도면에서 수정하거나 해당도면을 삭제 후 다시 등록해주세요.</div>
                    <div style="overflow-x:scroll;">

                        <table class="excel">
                            
                            <tr bgcolor="#DDEEFF" align="center">
                                <td style="white-space:nowrap; padding-left:8px; padding-right:8px;">승인번호</td>
                                <td style="white-space:nowrap; padding-left:8px; padding-right:8px;">공종</td>
                                <td style="white-space:nowrap; padding-left:8px; padding-right:8px;">담당자</td>
                                <td style="white-space:nowrap; padding-left:8px; padding-right:8px;">업체</td>
                                <td style="white-space:nowrap; padding-left:8px; padding-right:8px;">설치동</td>
                                <td style="white-space:nowrap; padding-left:8px; padding-right:8px;">층</td>
                                <td style="white-space:nowrap; padding-left:8px; padding-right:8px;">설치위치</td>
                                <td style="white-space:nowrap; padding-left:8px; padding-right:8px;">설치구간</td>
                                <td style="white-space:nowrap; padding-left:8px; padding-right:8px;">설치목적</td>
                                <td style="white-space:nowrap; padding-left:8px; padding-right:8px;">강관비계 산출식</td>
                                <td style="white-space:nowrap; padding-left:8px; padding-right:8px;">물량(루베)</td>
                                <td style="white-space:nowrap; padding-left:8px; padding-right:8px;">안전발판 산출식</td>
                                <td style="white-space:nowrap; padding-left:8px; padding-right:8px;">물량(헤베)</td>
                                <td style="white-space:nowrap; padding-left:8px; padding-right:8px;">달대비계 산출식</td>
                                <td style="white-space:nowrap; padding-left:8px; padding-right:8px;">물량(헤베)</td>
                                <td style="white-space:nowrap; padding-left:8px; padding-right:8px;">도면등록일</td>
                                <td style="white-space:nowrap; padding-left:8px; padding-right:8px;">설치시작일</td>
                                <td style="white-space:nowrap; padding-left:8px; padding-right:8px;">승인완료일</td>
                                <td style="white-space:nowrap; padding-left:8px; padding-right:8px;">수정시작일</td>
                                <td style="white-space:nowrap; padding-left:8px; padding-right:8px;">수정완료일</td>
                                <td style="white-space:nowrap; padding-left:8px; padding-right:8px;">해체시작일</td>
                                <td style="white-space:nowrap; padding-left:8px; padding-right:8px;">해체완료일</td>
                                <td style="white-space:nowrap; padding-left:8px; padding-right:8px;">만료일</td>
                                <td style="white-space:nowrap; padding-left:8px; padding-right:8px;">비고</td>
                            </tr>

                            <?php foreach($pre_exist_data as $data) : ?>

                                <tr onclick="window.open('view_facility_info/<?= $data['exist_serial'] ?>')" style="cursor:pointer" align="center">
                                    <td style="white-space:nowrap; padding-left:8px; padding-right:8px; <?= ($data['exist_type'] == 1 || $data['exist_type'] == 2) ? "background-color:blanchedalmond" : "" ?>">
                                        <?= $data['exist_type'] == 1 ? "<승인번호중복>" . $data['serial']  : ($data['exist_type'] == 2 ? "<리비전번호중복>" . $data['serial']  : $data['serial']) ?>
                                    </td>
                                    <td style="white-space:nowrap; padding-left:8px; padding-right:8px;"><?=getTypeText($data['type'])?></td>
                                    <td style="white-space:nowrap; padding-left:8px; padding-right:8px;"><?=$data['super_manager']?></td>
                                    <td style="white-space:nowrap; padding-left:8px; padding-right:8px;"><?=$data['subcontractor']?></td>
                                    <td style="white-space:nowrap; padding-left:8px; padding-right:8px;"><?=$data['building']?></td>
                                    <td style="white-space:nowrap; padding-left:8px; padding-right:8px;"><?=$data['floor']?></td>
                                    <td style="white-space:nowrap; padding-left:8px; padding-right:8px;"><?=$data['spot']?></td>
                                    <td style="white-space:nowrap; padding-left:8px; padding-right:8px;"><?= $data['section'] ?></td>
                                    <td style="white-space:nowrap; padding-left:8px; padding-right:8px;"><?= $data['purpose'] ?></td>
                                    <td style="white-space:nowrap; padding-left:8px; padding-right:8px;"><?= $data['cube_data'] ?></td>
                                    <td style="white-space:nowrap; padding-left:8px; padding-right:8px;"><?= $data['cube_result'] ?></td>
                                    <td style="white-space:nowrap; padding-left:8px; padding-right:8px;"><?= $data['area_data'] ?></td>
                                    <td style="white-space:nowrap; padding-left:8px; padding-right:8px;"><?= $data['area_result'] ?></td>
                                    <td style="white-space:nowrap; padding-left:8px; padding-right:8px;"><?= $data['danger_data'] ?></td>
                                    <td style="white-space:nowrap; padding-left:8px; padding-right:8px;"><?= $data['danger_result'] ?></td>
                                    <td style="white-space:nowrap; padding-left:8px; padding-right:8px;"><?= $data['created_at'] ?></td>
                                    <td style="white-space:nowrap; padding-left:8px; padding-right:8px;"><?= $data['started_at'] ?></td>
                                    <td style="white-space:nowrap; padding-left:8px; padding-right:8px;"><?= $data['finished_at'] ?></td>
                                    <td style="white-space:nowrap; padding-left:8px; padding-right:8px;"><?= $data['edit_started_at'] ?></td>
                                    <td style="white-space:nowrap; padding-left:8px; padding-right:8px;"><?= $data['edit_finished_at'] ?></td>
                                    <td style="white-space:nowrap; padding-left:8px; padding-right:8px;"><?= $data['dis_started_at'] ?></td>
                                    <td style="white-space:nowrap; padding-left:8px; padding-right:8px;"><?= $data['dis_finished_at'] ?></td>
                                    <td style="white-space:nowrap; padding-left:8px; padding-right:8px;"><?= $data['expired_at'] ?></td>
                                    <td style="white-space:nowrap; padding-left:8px; padding-right:8px;"><?=$data['memo']?></td>
                                </tr>

                            <?php endforeach ?>

                        </table>

                    </div>
                <?php endif ?>
                

                <?php if(count($upload_error_data) > 0) : ?>
                    <div style="margin-top:16px;">※업로드 실패 정보 - 아래의 버튼으로 정보를 복사할수 있습니다.</div>
                    <div style="overflow-x:scroll;">

                        <table class="excel">
                            
                            <tr bgcolor="#DDEEFF" align="center">
                                <td style="white-space:nowrap; padding-left:4px; padding-right:4px;">승인번호</td>
                                <td style="white-space:nowrap; padding-left:4px; padding-right:4px;">공종</td>
                                <td style="white-space:nowrap; padding-left:4px; padding-right:4px;">담당자</td>
                                <td style="white-space:nowrap; padding-left:4px; padding-right:4px;">업체</td>
                                <td style="white-space:nowrap; padding-left:4px; padding-right:4px;">설치동</td>
                                <td style="white-space:nowrap; padding-left:4px; padding-right:4px;">층</td>
                                <td style="white-space:nowrap; padding-left:4px; padding-right:4px;">설치위치</td>
                                <td style="white-space:nowrap; padding-left:4px; padding-right:4px;">설치구간</td>
                                <td style="white-space:nowrap; padding-left:4px; padding-right:4px;">설치목적</td>
                                <td style="white-space:nowrap; padding-left:4px; padding-right:4px;">강관비계 산출식</td>
                                <td style="white-space:nowrap; padding-left:4px; padding-right:4px;">물량(루베)</td>
                                <td style="white-space:nowrap; padding-left:4px; padding-right:4px;">안전발판 산출식</td>
                                <td style="white-space:nowrap; padding-left:4px; padding-right:4px;">물량(헤베)</td>
                                <td style="white-space:nowrap; padding-left:4px; padding-right:4px;">달대비계 산출식</td>
                                <td style="white-space:nowrap; padding-left:4px; padding-right:4px;">물량(헤베)</td>
                                <td style="white-space:nowrap; padding-left:4px; padding-right:4px;">도면등록일</td>
                                <td style="white-space:nowrap; padding-left:4px; padding-right:4px;">설치시작일</td>
                                <td style="white-space:nowrap; padding-left:4px; padding-right:4px;">승인완료일</td>
                                <td style="white-space:nowrap; padding-left:4px; padding-right:4px;">수정시작일</td>
                                <td style="white-space:nowrap; padding-left:4px; padding-right:4px;">수정완료일</td>
                                <td style="white-space:nowrap; padding-left:4px; padding-right:4px;">해체시작일</td>
                                <td style="white-space:nowrap; padding-left:4px; padding-right:4px;">해체완료일</td>
                                <td style="white-space:nowrap; padding-left:8px; padding-right:8px;">만료일</td>
                                <td style="white-space:nowrap; padding-left:4px; padding-right:4px;">비고</td>
                            </tr>

                            <tbody id="upload_error_tbody">
                                <?php foreach($upload_error_data as $data) : ?>

                                    <tr align="center">
                                        <td style="white-space:nowrap; padding-left:8px; padding-right:8px; <?= (in_array(-1, $data['error_types']) || in_array(1, $data['error_types']) || in_array(2, $data['error_types'])) ? "background-color:blanchedalmond" : "" ?>">
                                            <?= in_array(-1, $data['error_types']) ? "<승인번호누락>" : (in_array(1, $data['error_types']) ? "<문자열이상>" . $data['serial'] : (in_array(2, $data['error_types']) ? "<중복>" . $data['serial'] : $data['serial'])) ?>
                                        </td>
                                        <td style="white-space:nowrap; padding-left:8px; padding-right:8px;">
                                            <?=getTypeText($data['type'])?>
                                        </td>
                                        <td style="white-space:nowrap; padding-left:8px; padding-right:8px; <?= in_array(3, $data['error_types']) ? "background-color:blanchedalmond" : "" ?>">
                                            <?= in_array(3, $data['error_types']) ? "<문자열이상>" . $data['super_manager'] : $data['super_manager'] ?>
                                        </td>
                                        <td style="white-space:nowrap; padding-left:8px; padding-right:8px; <?= in_array(4, $data['error_types']) ? "background-color:blanchedalmond" : "" ?>">
                                            <?= in_array(4, $data['error_types']) ? "<문자열이상>" . $data['subcontractor'] : $data['subcontractor'] ?>
                                        </td>
                                        <td style="white-space:nowrap; padding-left:8px; padding-right:8px; <?= (in_array(5, $data['error_types']) || in_array(-567, $data['error_types'])) ? "background-color:blanchedalmond" : "" ?>">
                                            <?= in_array(-567, $data['error_types']) ? "<설치위치누락>" : (in_array(5, $data['error_types']) ? "<문자열이상>" . $data['building'] : $data['building']) ?>
                                        </td>
                                        <td style="white-space:nowrap; padding-left:8px; padding-right:8px; <?= (in_array(6, $data['error_types']) || in_array(-567, $data['error_types'])) ? "background-color:blanchedalmond" : "" ?>">
                                            <?= in_array(6, $data['error_types']) ? "<문자열이상>" . $data['floor'] : $data['floor'] ?>
                                        </td>
                                        <td style="white-space:nowrap; padding-left:8px; padding-right:8px; <?= (in_array(7, $data['error_types']) || in_array(-567, $data['error_types']) || in_array(-100, $data['error_types'])) ? "background-color:blanchedalmond" : "" ?>">
                                            <?= in_array(-100, $data['error_types']) ? "<정보부족>" : (in_array(7, $data['error_types']) ? "<문자열이상>" . $data['spot'] : $data['spot']) ?>
                                        </td>
                                        <td style="white-space:nowrap; padding-left:8px; padding-right:8px;">
                                            <?= $data['section'] ?>
                                        </td>
                                        <td style="white-space:nowrap; padding-left:8px; padding-right:8px;">
                                            <?= $data['purpose'] ?>
                                        </td>
                                        <td style="white-space:nowrap; padding-left:8px; padding-right:8px;">
                                            <?= $data['cube_data'] ?>
                                        </td>
                                        <td style="white-space:nowrap; padding-left:8px; padding-right:8px;">
                                            <?= $data['cube_result'] ?>
                                        </td>
                                        <td style="white-space:nowrap; padding-left:8px; padding-right:8px;">
                                            <?= $data['area_data'] ?>
                                        </td>
                                        <td style="white-space:nowrap; padding-left:8px; padding-right:8px;">
                                            <?= $data['area_result'] ?>
                                        </td>
                                        <td style="white-space:nowrap; padding-left:8px; padding-right:8px;">
                                            <?= $data['danger_data'] ?>
                                        </td>
                                        <td style="white-space:nowrap; padding-left:8px; padding-right:8px;">
                                            <?= $data['danger_result'] ?>
                                        </td>
                                        <td style="white-space:nowrap; padding-left:8px; padding-right:8px; <?= in_array(15, $data['error_types']) ? "background-color:blanchedalmond" : "" ?>">
                                            <?= in_array(15, $data['error_types']) ? "<날짜형식이상>" . $data['created_at'] : $data['created_at'] ?>
                                        </td>
                                        <td style="white-space:nowrap; padding-left:8px; padding-right:8px; <?= (in_array(-16, $data['error_types']) || in_array(16, $data['error_types'])) ? "background-color:blanchedalmond" : "" ?>">
                                            <?= in_array(-16, $data['error_types']) ? "<날짜누락>" : (in_array(16, $data['error_types']) ? "<날짜형식이상>" . $data['started_at'] : $data['started_at']) ?>
                                        </td>
                                        <td style="white-space:nowrap; padding-left:8px; padding-right:8px; <?= (in_array(-17, $data['error_types']) || in_array(17, $data['error_types'])) ? "background-color:blanchedalmond" : "" ?>">
                                            <?= in_array(-17, $data['error_types']) ? "<날짜누락>" : (in_array(17, $data['error_types']) ? "<날짜형식이상>" . $data['finished_at'] : $data['finished_at']) ?>
                                        </td>
                                        <td style="white-space:nowrap; padding-left:8px; padding-right:8px; <?= (in_array(-18, $data['error_types']) || in_array(18, $data['error_types'])) ? "background-color:blanchedalmond" : "" ?>">
                                            <?= in_array(-18, $data['error_types']) ? "<날짜누락>" : (in_array(18, $data['error_types']) ? "<날짜형식이상>" . $data['edit_started_at'] : $data['edit_started_at']) ?>
                                        </td>
                                        <td style="white-space:nowrap; padding-left:8px; padding-right:8px; <?= (in_array(-19, $data['error_types']) || in_array(19, $data['error_types'])) ? "background-color:blanchedalmond" : "" ?>">
                                            <?= in_array(-19, $data['error_types']) ? "<날짜누락>" : (in_array(19, $data['error_types']) ? "<날짜형식이상>" . $data['edit_finished_at'] : $data['edit_finished_at']) ?>
                                        </td>
                                        <td style="white-space:nowrap; padding-left:8px; padding-right:8px; <?= (in_array(-20, $data['error_types']) || in_array(20, $data['error_types'])) ? "background-color:blanchedalmond" : "" ?>">
                                            <?= in_array(-20, $data['error_types']) ? "<날짜누락>" : (in_array(20, $data['error_types']) ? "<날짜형식이상>" . $data['dis_started_at'] : $data['dis_started_at']) ?>
                                        </td>
                                        <td style="white-space:nowrap; padding-left:8px; padding-right:8px; <?= in_array(21, $data['error_types']) ? "background-color:blanchedalmond" : "" ?>">
                                            <?= in_array(21, $data['error_types']) ? "<날짜형식이상>" . $data['dis_finished_at'] : $data['dis_finished_at'] ?>
                                        </td>
                                        <td style="white-space:nowrap; padding-left:8px; padding-right:8px; <?= in_array(22, $data['error_types']) ? "background-color:blanchedalmond" : "" ?>">
                                            <?= in_array(22, $data['error_types']) ? "<날짜형식이상>" . $data['expired_at'] : $data['expired_at'] ?>
                                        </td>
                                        <td style="white-space:nowrap; padding-left:8px; padding-right:8px; <?= in_array(100, $data['error_types']) ? "background-color:blanchedalmond" : "" ?>">
                                            <?= in_array(100, $data['error_types']) ? $data['memo'] . "<정보초과>" : $data['memo']?>
                                        </td>
                                    </tr>

                                <?php endforeach ?>
                            </tbody>

                        </table>

                    </div>

                    <div style="margin-top:8px;">
                        <button class="filletbutton" type="button" onclick="copyToClipboard()">업로드 실패 정보 복사하기</button>
                    </div>
                <?php endif ?>
                
            </div>
            
        </div>
    </div>

</form>

<?= $this->endSection() ?>

<?= $this->section('custom_js') ?>

<script type="text/javascript">

    function copyToClipboard() {

        const upload_error_tbody = document.getElementById('upload_error_tbody');
        const textarea = document.createElement('textarea');

        document.body.appendChild(textarea);
        textarea.value = upload_error_tbody.innerText;
        textarea.select();
        document.execCommand('copy');
        document.body.removeChild(textarea);

    }
    /*
    $(document).ready(function() {
        
        $('tr.facility.select').click(function() {

        var serial = $(this).data('serial');

        location.href = '/fm/view_facility_info/' + serial;

        });

    });
    */

</script>

<?= $this->endSection() ?>