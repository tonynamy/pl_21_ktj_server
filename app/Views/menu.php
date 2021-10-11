<?= $this->extend('common_layout') ?>

<?= $this->section('content') ?>

<form>

    <?php 
        switch($level) {
            case 2: 
                $rolename = "관리자";
                break;
            case 3:
            case 4:
                $rolename = "최고관리자";
                break;
            default:
                $rolename = "담당자";
                break;
        }
        $login_info = $placename . ' ' . $username . ' ' . $rolename;
    ?>

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
                        <?php if($level==3 || $level == 4) : ?>
                            <button class="filletbutton" type="button" id="password_change" data-id="<?= $id ?>" style="margin-right:4px;">비밀번호변경</button>
                        <?php endif ?>
                        <button class="filletbutton" type="submit" formaction="logout">로그아웃</button>
                    </div>

            </div>

            <div class="ui divider"></div>

            <div style="display:flex; flex-wrap:wrap; justify-content:center; align-items:center; margin-top:4px;">
                
                <div style="width:305px;">
                    <label>등록</label>
                    <div style="display:flex; flex-wrap:wrap; justify-content:space-between; align-items:center; margin-top:4px; margin-bottom:8px;">
                        <button class="bluebutton" type="button" style="width:150px; margin-bottom:8px;" onclick="location.href='/fm/add_team'">팀 등록</button>
                        <button class="bluebutton" type="button" style="width:150px; margin-bottom:8px;" onclick="location.href='/fm/add_facility'">도면 등록</button>
                    </div>

                    <label>조회</label>
                    <div style="display:flex; flex-wrap:wrap; justify-content:space-between; align-items:center; margin-top:4px; margin-bottom:8px;">
                        <button class="bluebutton" type="button" style="width:150px; margin-bottom:8px;" onclick="location.href='/fm/view_attendance'">출퇴근 조회</button>
                        <button class="bluebutton" type="button" style="width:150px; margin-bottom:8px;" onclick="location.href='/fm/view_facility'">작업 조회</button>
                        <button class="bluebutton" type="button" style="width:150px; margin-bottom:8px;" onclick="location.href='/fm/view_productivity'">생산성 조회</button>
                        <button class="bluebutton" type="button" style="width:150px; margin-bottom:8px;" onclick="location.href='/fm/view_safe_point'">안전점수 조회</button>
                    </div>
                    
                    <?php if($level == 3 || $level ==4) : ?>
                        <label>관리</label>
                        <div style="display:flex; flex-wrap:wrap; justify-content:space-between; align-items:center; margin-top:4px; margin-bottom:8px;">
                            <?php if($level ==4) : ?>
                                <button class="bluebutton" type="button" style="width:150px; margin-bottom:8px;" onclick="location.href='/fm/set_place'">현장 관리</button>
                            <?php endif ?>
                            <button class="bluebutton" type="button" style="width:150px; margin-bottom:8px;" onclick="location.href='/fm/set_user'">직원등급 관리</button>
                        </div>
                    <?php endif ?>
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



        
        $('#password_change').click(function() {

            var id = $(this).data('id');

            var new_password = prompt('변경할 비밀번호를 입력하세요. (8자리 이상의 숫자)');

            if(new_password != null) {

                var newForm = $('<form></form>');

                newForm.attr("name", "newForm");
                newForm.attr("method", "post");
                newForm.attr("action", "/fm/change_password");
                
                newForm.append($('<input/>', {type: 'hidden', name: 'id', value: id }));
                newForm.append($('<input/>', {type: 'hidden', name: 'new_birthday', value: new_password }));

                newForm.appendTo('body');
                newForm.submit();

            } 
        });

    });
    

</script>

<?= $this->endSection() ?>