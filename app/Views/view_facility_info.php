<?php

use function PHPUnit\Framework\isNull;
?>
<?= $this->extend('common_layout') ?>

<?= $this->section('content') ?>

<form method="POST">

    <div style="width:fit-content; margin:0 auto; padding:16px;">
        <div class="uiframe" style="display:flex; justify-content:space-between; width:1200px;">
            <div style="width:500px; margin-left:8px">
                <table class="ui very basic table">
                    <tr>
                        <td colspan="3"><p style="font-size:x-large;"><?=$facility['o_serial']?></p></td>
                    </tr>
                    <tr>
                        <td>공종</td>
                        <td><?=getTypeText($facility['type'])?></td>
                        <td>[수정]</td>
                    </tr>
                    <tr>
                        <td>담당자</td>
                        <td><?=$facility['super_manager']?></td>
                        <td><?=is_null($facility['super_manager']) || empty($facility['super_manager']) ? "[생성]" : "[수정]"?></td>
                    </tr>
                    <tr>
                        <td>사용업체</td>
                        <td><?=$facility['subcontractor']?></td>
                        <td><?=is_null($facility['subcontractor']) || empty($facility['subcontractor']) ? "[생성]" : "[수정]"?></td>
                    </tr>
                    <tr>
                        <td>설치동</td>
                        <td><?=$facility['building']?></td>
                        <td><?=is_null($facility['building']) || empty($facility['building']) ? "[생성]" : "[수정]"?></td>
                    </tr>
                    <tr>
                        <td>설치층</td>
                        <td><?=$facility['floor']?></td>
                        <td><?=is_null($facility['floor']) || empty($facility['floor']) ? "[생성]" : "[수정]"?></td>
                    </tr>
                    <tr>
                        <td>설치위치</td>
                        <td><?=$facility['spot']?></td>
                        <td><?=is_null($facility['spot']) || empty($facility['spot']) ? "[생성]" : "[수정]"?></td>
                    </tr>
                    <tr>
                        <td>설치목적</td>
                        <td><?=$facility['purpose']?></td>
                        <td><?=is_null($facility['purpose']) || empty($facility['purpose']) ? "[생성]" : "[수정]"?></td>
                    </tr>
                    <tr>
                        <td>도면등록일</td>
                        <td><?=$facility['created_at']?></td>
                        <td><?=is_null($facility['created_at']) || empty($facility['created_at']) ? "[생성]" : "[수정]"?></td>
                    </tr>
                    <tr>
                        <td>설치시작일</td>
                        <td><?=$facility['started_at']?></td>
                        <td><?=is_null($facility['started_at']) || empty($facility['started_at']) ? "[생성]" : "[수정]"?></td>
                    </tr>
                    <tr>
                        <td>승인완료일</td>
                        <td><?=$facility['finished_at']?></td>
                        <td><?=is_null($facility['finished_at']) || empty($facility['finished_at']) ? "[생성]" : "[수정]"?></td>
                    </tr>
                    <tr>
                        <td>수정시작일</td>
                        <td><?=$facility['edit_started_at']?></td>
                        <td><?=is_null($facility['edit_started_at']) || empty($facility['edit_started_at']) ? "[생성]" : "[수정]"?></td>
                    </tr>
                    <tr>
                        <td>수정완료일</td>
                        <td><?=$facility['edit_finished_at']?></td>
                        <td><?=is_null($facility['edit_finished_at']) || empty($facility['edit_finished_at']) ? "[생성]" : "[수정]"?></td>
                    </tr>
                    <tr>
                        <td>해체시작일</td>
                        <td><?=$facility['dis_started_at']?></td>
                        <td><?=is_null($facility['dis_started_at']) || empty($facility['dis_started_at']) ? "[생성]" : "[수정]"?></td>
                    </tr>
                    <tr>
                        <td>해체완료일</td>
                        <td><?=$facility['dis_finished_at']?></td>
                        <td><?=is_null($facility['dis_finished_at']) || empty($facility['dis_finished_at']) ? "[생성]" : "[수정]"?></td>
                    </tr>
                    <tr>
                        <td>만료일</td>
                        <td><?=$facility['expired_at']?></td>
                        <td><?=is_null($facility['expired_at']) || empty($facility['expired_at']) ? "[생성]" : "[수정]"?></td>
                    </tr>
                    <tr>
                        <td colspan="3"></td>
                    </tr>
                </table>
            </div>

            <div style="width:500px; margin-top:24px; margin-right:8px;">
                <table style="width:100%;">
                    <tr>
                        <td colspan="5">※작업내역</td>
                    </tr>

                    <?php foreach($tasks as $task) : ?>

                        <tr>
                            <td><?=$task['created_at']?></td>
                            <td style="width:100px"><?=$task['team_name']?></td>
                            <td style="width:100px"><?=getTaskTypeText($task['type'])?>작업</td>
                            <td style="width:70px"><?=$task['manday']?>공수</td>
                            <td style="width:60px">[삭제]</td>
                        </tr>

                    <?php endforeach ?>

                </table>
            </div>

        </div>
    </div>

</form>



<?= $this->endSection() ?>

<?= $this->section('custom_js') ?>

<?= $this->endSection() ?>