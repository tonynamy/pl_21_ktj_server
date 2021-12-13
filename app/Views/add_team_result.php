<?= $this->extend('common_layout') ?>

<?= $this->section('content') ?>

    <div style="width:fit-content; margin:0 auto; padding:16px;">
        <div class="uiframe" style="width:800px;">

            <div style="padding:16px">
                <i class="arrow left icon" onclick="location.href='/fm/add_team'" style="cursor: pointer;"></i> <!-- 뒤로가기 -->
                <label>팀등록 결과</label>

                <?php if(count($upload_success_data) > 0) : ?>
                    <div style="margin-top:16px;">
                        ※업로드 성공 정보
                        <table class="excel" style="width:fit-content; margin-top:4px;">

                            <tr bgcolor="#DDEEFF" align="center">
                                <td style="white-space:nowrap; padding-left:8px; padding-right:8px;">팀이름</td>
                                <td style="white-space:nowrap; padding-left:8px; padding-right:8px;">이름</td>
                                <td style="white-space:nowrap; padding-left:8px; padding-right:8px;">생년월일</td>
                            </tr>

                            <?php foreach($upload_success_data as $data) : ?>

                                <tr align="center" onclick="window.open('view_attendance/<?=$data['team_id']?>')" style="cursor: pointer;">
                                    <td style="white-space:nowrap; padding-left:8px; padding-right:8px;"><?=$data['team_name']?></td>
                                    <td style="white-space:nowrap; padding-left:8px; padding-right:8px;"><?=$data['name']?></td>
                                    <td style="white-space:nowrap; padding-left:8px; padding-right:8px;"><?=$data['birthday']?></td>
                                </tr>

                            <?php endforeach ?>

                        </table>
                    </div>
                <?php endif ?>

                <?php if(count($pre_exist_data) > 0) : ?>
                    <div style="margin-top:16px;">
                        ※이미 존재하는 정보 혹은 중복 입력된 정보
                        <table class="excel" style="width:fit-content; margin-top:4px;">
                            
                            <tr bgcolor="#DDEEFF" align="center">
                                <td style="white-space:nowrap; padding-left:8px; padding-right:8px;">팀이름</td>
                                <td style="white-space:nowrap; padding-left:8px; padding-right:8px;">이름</td>
                                <td style="white-space:nowrap; padding-left:8px; padding-right:8px;">생년월일</td>
                            </tr>

                            <?php foreach($pre_exist_data as $data) : ?>

                                <tr align="center" onclick="window.open('view_attendance/<?=$data['team_id']?>')" style="cursor: pointer;">
                                    <td style="white-space:nowrap; padding-left:8px; padding-right:8px;"><?=$data['team_name']?></td>
                                    <td style="white-space:nowrap; padding-left:8px; padding-right:8px;"><?=$data['name']?></td>
                                    <td style="white-space:nowrap; padding-left:8px; padding-right:8px;"><?=$data['birthday']?></td>
                                </tr>

                            <?php endforeach ?>

                        </table>
                    </div>
                <?php endif ?>

                <?php if(count($upload_error_data) > 0) : ?>
                    <div style="margin-top:16px;">
                        ※업로드 실패 정보 - 아래의 버튼으로 정보를 복사할수 있습니다.
                        <table class="excel" style="width:fit-content; margin-top:4px;">

                            <thead>
                                <tr bgcolor="#DDEEFF" align="center">
                                    <td style="white-space:nowrap; padding-left:8px; padding-right:8px;">팀이름</td>
                                    <td style="white-space:nowrap; padding-left:8px; padding-right:8px;">이름</td>
                                    <td style="white-space:nowrap; padding-left:8px; padding-right:8px;">생년월일</td>
                                </tr>
                            </thead>

                            <tbody id="upload_error_tbody">
                                <?php foreach($upload_error_data as $data) : ?>

                                    <tr align="center">
                                        <?php if(in_array(11, $data['error_types'])) : ?>
                                            <td style="white-space:nowrap; padding-left:8px; padding-right:8px; background-color:blanchedalmond">&lt;팀이름누락&gt;</td>
                                        <?php elseif(in_array(12, $data['error_types'])) : ?>
                                            <td style="white-space:nowrap; padding-left:8px; padding-right:8px; background-color:blanchedalmond">&lt;팀이름이상&gt;</td>
                                        <?php else : ?>
                                            <td style="white-space:nowrap; padding-left:8px; padding-right:8px;"><?=$data['team_name']?></td>
                                        <?php endif ?>

                                        <?php if(in_array(21, $data['error_types'])) : ?>
                                            <td style="white-space:nowrap; padding-left:8px; padding-right:8px; background-color:blanchedalmond">&lt;이름누락&gt;</td>
                                        <?php elseif(in_array(22, $data['error_types'])) : ?>
                                            <td style="white-space:nowrap; padding-left:8px; padding-right:8px; background-color:blanchedalmond">&lt;이름이상&gt;</td>
                                        <?php else : ?>
                                            <td style="white-space:nowrap; padding-left:8px; padding-right:8px;"><?=$data['name']?></td>
                                        <?php endif ?>

                                        <?php if(in_array(31, $data['error_types'])) : ?>
                                            <td style="white-space:nowrap; padding-left:8px; padding-right:8px; background-color:blanchedalmond">&lt;생년월일누락&gt;</td>
                                        <?php elseif(in_array(32, $data['error_types'])) : ?>
                                            <td style="white-space:nowrap; padding-left:8px; padding-right:8px; background-color:blanchedalmond">&lt;생년월일이상&gt;</td>
                                        <?php elseif(in_array(33, $data['error_types'])) : ?>
                                            <td style="white-space:nowrap; padding-left:8px; padding-right:8px; background-color:blanchedalmond">&lt;데이터개수초과&gt;</td>
                                        <?php else : ?>
                                            <td style="white-space:nowrap; padding-left:8px; padding-right:8px;"><?=$data['birthday']?></td>
                                        <?php endif ?>
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


</script>

<?= $this->endSection() ?>