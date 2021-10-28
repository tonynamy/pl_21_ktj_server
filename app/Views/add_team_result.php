<?= $this->extend('common_layout') ?>

<?= $this->section('content') ?>

<form>

    <div style="width:fit-content; margin:0 auto; padding:16px;">
        <div class="uiframe" style="width:800px;">

            <div style="padding:16px">
                <i class="arrow left icon" onclick="location.href='/fm/add_team'" style="cursor: pointer;"></i> <!-- 뒤로가기 -->
                <label>팀등록 결과</label>

                <div style="margin-top:16px;">
                    ※업로드 성공 정보
                    <table>

                        <?php foreach($teammate_inserted_data as $teammate) : ?>

                            <tr>
                                <td><?=$teammate['team_name']?></td>
                                <td><?=$teammate['name']?></td>
                                <td><?=$teammate['birthday']?></td>
                            </tr>

                        <?php endforeach ?>

                    </table>

                </div>

                <div style="margin-top:16px;">
                    ※확인이 필요한 정보 - 문제점에 마우스를 대면 이유와 해결방안이 나옵니다.
                    <table>

                        <?php foreach($data_errors as $data) : ?>

                            <?php if($data['type'] != 1) continue; ?>

                            <tr onclick="window.open('view_attendance/<?=$data['team_id']?>')" style="cursor: pointer;">
                                <td style="color:red;" title="다른팀에 등록되어있습니다. 출퇴근조회에서 팀변경을 눌러 변경하세요."><?=$data['team_name']?></td>
                                <td><?=$data['name']?></td>
                                <td><?=$data['birthday']?></td>
                            </tr>

                        <?php endforeach ?>

                    </table>

                </div>

                <div style="margin-top:16px;">
                    ※업로드 실패 정보
                    <table>

                        <?php foreach($data_errors as $data) : ?>

                            <?php if($data['type'] != 2) continue; ?>

                            <tr>
                                <td><?=$data['team_name'] ?? "&lt;팀이름누락&gt;"?></td>
                                <td><?=$data['name'] ?? "&lt;이름누락&gt;"?></td>
                                <td><?=$data['birthday'] ?? "&lt;생년월일이상&gt;"?></td>
                            </tr>

                        <?php endforeach ?>

                    </table>

                </div>
            
                <div align="right">
                    <button class="bluebutton" style="width:80px">복사하기</button>
                </div>
                
            </div>
            
        </div>
    </div>

</form>

<?= $this->endSection() ?>