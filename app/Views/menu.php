<?= $this->extend('common_layout') ?>

<?= $this->section('content') ?>


    <div class="uiframe" style="margin:0 auto; width:500px">

        <div style="margin-bottom:8px">
            <label><?= $login_info ?></label>
        </div>

        <div style="display:flex; align-items:center">
            <label style="width:50px">등록</label>
            <button class="bluebutton" style="width:150px" onclick="location.href='/fm/add_team'">팀 등록</button>
            <button class="bluebutton" style="width:150px" onclick="location.href='/fm/add_facility'">도면 등록</button>
        </div>

        <div style="margin-bottom:8px; display:flex; align-items:center">
            <label style="width:50px">조회</label>
            <button class="bluebutton" style="width:150px" onclick="location.href='/fm/view_attendance'">출퇴근 조회</button>
            <button class="bluebutton" style="width:150px">현장 조회</button>
        </div>

    </div>


<?= $this->endSection() ?>