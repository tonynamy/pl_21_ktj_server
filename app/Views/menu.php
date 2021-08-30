<?= $this->extend('common_layout') ?>

<?= $this->section('content') ?>

<form>

    <div style="width:fit-content; margin:0 auto; padding:16px;">
        <div class="uiframe" style="width:400px; padding-left:0px; padding-right:0px;">

            <div style="height:200px; display:flex; flex-direction:column; text-align:center; padding-top:16px;">
                
                    <div>
                        <img src="/static/fmenc_logo.jpg">
                    </div>
                    <div style="margin-bottom:12px;">
                        <label><?= $login_info ?></label>
                    </div>
                    <div>
                        <button class="filletbutton" formaction="logout">로그아웃</button>
                    </div>

            </div>

            <div class="ui divider"></div>

            <div style="height:160px; display:flex; justify-content:center; align-items:center;">
                
                <div>
                    <label>등록</label>
                    <div style="margin-top:4px; margin-bottom:16px;">
                        <button class="bluebutton" type="button" style="width:150px" onclick="location.href='/fm/add_team'">팀 등록</button>
                        <button class="bluebutton" type="button" style="width:150px" onclick="location.href='/fm/add_facility'">도면 등록</button>
                    </div>

                    <label>조회</label>
                    <div style="margin-top:4px; margin-bottom:16px;">
                        <button class="bluebutton" type="button" style="width:150px" onclick="location.href='/fm/view_attendance'">출퇴근 조회</button>
                        <button class="bluebutton" type="button" style="width:150px" onclick="location.href='/fm/view_facility'">현장 조회</button>
                    </div>
                </div>
                
            </div>

        </div>
    </div>
    
    <div id="credit"></div>

</form>
    
<?= $this->endSection() ?>

<?= $this->section('custom_js') ?>

<script type="text/javascript">

    $(document).ready(function() {

        $('#credit').html('Developed by 푸고소프트');
        $('#credit').width('fit-content');
        $('#credit').css({
            'position': 'absolute',
            'top': $(document).height() - document.getElementById('credit').offsetHeight + 'px',
            'left': $(document).width() - document.getElementById('credit').offsetWidth + 'px',
        });
    });

</script>

<?= $this->endSection() ?>