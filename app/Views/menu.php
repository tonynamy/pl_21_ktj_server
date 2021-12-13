<?= $this->extend('common_layout') ?>

<?= $this->section('content') ?>

    <?php
        $rolename = "";

        switch($level) {
            case 2: 
                $rolename = "관리자";
                break;
            case 3:
            case 4:
                $rolename = "최고관리자";
                break;
            case -1:
                $rolename = "담당자";
                break;
        }
        $login_info = $placename . ' ' . $username . ' ' . $rolename;
    ?>

    <div style="width:fit-content; margin:0 auto; padding:16px;">
        <div class="uiframe" style="width:400px;">

            <div style="height:216px; text-align:center; padding:16px;">
                
                <?php if($level != -1) : ?>
                    <i id="pdf_icon" class="file pdf outline icon" title="사용설명서 PDF 다운로드" style="float:right; cursor:pointer;"></i>
                <?php endif ?>
                
                <div style="width:fit-content; margin:0 auto;">
                    <div>
                        <img src="/static/fmenc_logo.jpg">
                    </div>
                    <div style="margin-bottom:12px;">
                        <label><?= $login_info ?></label>
                    </div>
                    <div>
                        <?php if($level==3 || $level == 4) : ?>
                            <button class="filletbutton" type="button" id="password_change" style="margin-right:4px;">비밀번호변경</button>
                        <?php endif ?>
                        <button class="filletbutton" type="submit" onclick="location.href='/fm/logout'">로그아웃</button>
                    </div>

                </div>

            </div>

            <div style="height:1px; background-color:#e8e9e9;"></div>

            <div style="display:flex; flex-wrap:wrap; justify-content:center; align-items:center; padding:16px;">
                
                <div style="width:305px;">
                    <?php if($level != -1) : ?>
                        <label>등록</label>
                        <div style="display:flex; flex-wrap:wrap; justify-content:space-between; align-items:center; margin-top:4px; margin-bottom:8px;">
                            <button class="bluebutton" type="button" style="width:150px; margin-bottom:8px;" onclick="location.href='/fm/add_team'">팀 등록</button>
                            <button class="bluebutton" type="button" style="width:150px; margin-bottom:8px;" onclick="location.href='/fm/add_facility'">도면 등록</button>
                        </div>
                    <?php endif ?>

                    <label>조회</label>
                    <div style="display:flex; flex-wrap:wrap; justify-content:space-between; align-items:center; margin-top:4px; margin-bottom:8px;">
                        <?php if($level != -1) : ?>
                            <button class="bluebutton" type="button" style="width:150px; margin-bottom:8px;" onclick="location.href='/fm/view_attendance'">출퇴근 조회</button>
                        <?php endif ?>
                        
                        <button class="bluebutton" type="button" style="width:150px; margin-bottom:8px;" onclick="location.href='/fm/view_facility'">작업 조회</button>
                        
                        <?php if($level != -1) : ?>
                            <button class="bluebutton" type="button" style="width:150px; margin-bottom:8px;" onclick="location.href='/fm/view_productivity'">생산성 조회</button>
                            <button class="bluebutton" type="button" style="width:150px; margin-bottom:8px;" onclick="location.href='/fm/view_safe_point'">안전점수 조회</button>
                        <?php endif ?>
                    </div>
                    
                    <?php if($level == 3 || $level ==4) : ?>
                        <label>관리</label>
                        <div style="display:flex; flex-wrap:wrap; justify-content:space-between; align-items:center; margin-top:4px;">
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
    <input id="level" type="hidden" name="level" value="<?= $level ?>">


    <!-- pdf 다운로드 modal -->
    <div id="download_pdf_modal" class="ui mini modal">
        <div style="padding-top:4px; padding-bottom:4px">

            <?php
                //파일명을 여기다 쓰세요
                $manual_1 = "사용설명서_팀장님_20211011";
                $manual_2 = "사용설명서_관리자_20211015";
                $manual_3 = "사용설명서_최고관리자_20211022";
            ?>

            <div style="margin-top:4px">
                <div style="padding:16px;">
                    <label>FMENC 비계관리 사용설명서</label>
                </div>
                <div class="download_pdf" data-url="http://49.247.24.170/static/<?= $manual_1 ?>.pdf" style="padding:16px; cursor:pointer">
                    <?= $manual_1 ?>.pdf
                </div>
                <div class="download_pdf" data-url="http://49.247.24.170/static/<?= $manual_2 ?>.pdf" style="padding:16px; cursor:pointer">
                    <?= $manual_2 ?>.pdf
                </div>
                <div id="admin_manual" class="download_pdf" data-url="http://49.247.24.170/static/<?= $manual_3 ?>.pdf" style="padding:16px; cursor:pointer">
                    <?= $manual_3 ?>.pdf
                </div>
            </div>

            <div class="actions" style="text-align:right; padding:16px;">
                <span class="cancel" style="color:#5599DD; cursor:pointer;">닫기</span>
            </div>

        </div>

    </div>

    <!-- 비밀번호 변경 modal -->
    <div id="password_change_modal" class="ui mini modal">

        <form id="password_change_form" class="ui form" method="POST" action="/fm/change_password">
            <div style="padding:16px;">

                <div style="display:flex; justify-content:space-between; align-items:center; margin-top:4px">
                    <label>비밀번호</label>
                    <input type="text" name="new_birthday" placeholder="생년월일 혹은 8자리 이상의 숫자" value="<?= $birthday ?>" style="width:270px;">
                </div>

                <div class="actions" style="text-align:right; margin-top:24px; margin-bottom:4px">
                    <span class="cancel" style="color:#5599DD; cursor:pointer; margin-right:32px;">취소</span>
                    <span id="password_change_submit" style="color:#5599DD; cursor:pointer;">변경</span>
                </div>

            </div>

            <input type="hidden" name="id" value="<?= $id ?>">
        </form>
    </div>

    <!-- 앱버전 정보 modal -->
    <div id="version_info_modal" class="ui mini modal">

        <div style="padding:16px;">

            <div style="margin-top:4px;">
                현재 앱의 버전은 1.0.1 버전입니다.
            </div>
            <div style="margin-top:16px; line-height:180%">
                ※추후 업데이트 기능<br>
                퇴사자관리 기능<br>
                엑셀파일업로드 기능
            </div>

            <div class="actions" style="text-align:right; margin-top:24px; margin-bottom:4px">
                <span class="cancel" style="color:#5599DD; cursor:pointer;">닫기</span>
            </div>
        </div>
    </div>

    <!-- 개발자 정보 modal -->
    <div id="developer_info_modal" class="ui mini modal">

        <div style="padding:16px;">

            <div style="margin-top:4px">
                푸고소프트
            </div>

            <div id="secret_number" style="display:flex; justify-content:space-between; margin-top:16px">
                <label>휴대전화</label>
                <span style="width: 270px;">010-2820-0762</span>
            </div>

            <div style="display:flex; justify-content:space-between; margin-top:16px">
                <label>이메일</label>
                <span style="width: 270px;">jfri13@naver.com</span>
            </div>

            <div class="actions" style="text-align:right; margin-top:24px; margin-bottom:4px">
                <span class="cancel" style="color:#5599DD; cursor:pointer;">닫기</span>
            </div>
        </div>
    </div>
    
<?= $this->endSection() ?>

<?= $this->section('custom_js') ?>

<script type="text/javascript">

    $(document).ready(function() {

        var version = "1.0.1";
        var level = $('#level').val();

        if(level != -1) {
            $('#credit').html('Application Version <span id="version_info" style="cursor:pointer;">' + version + '</span> Developed by <span id="developer" data-level="' + level + '" style="cursor:pointer;">푸고소프트</span>');
        } else {
            $('#credit').html('Application Version ' + version + ' Developed by <span id="developer" data-level="' + level + '" style="cursor:pointer;">푸고소프트</span>');
        }

        $('#credit').width('fit-content');
        $('#credit').css({
            'position': 'absolute',
            'top': $(document).height() - document.getElementById('credit').offsetHeight + 'px',
            'left': $(document).width() - document.getElementById('credit').offsetWidth + 'px',
        });

        
        $('#pdf_icon').click(function() {

            var level = <?= $level ?>;
            if(level != 3 && level != 4){
                $('#admin_manual').hide();
            }
            $('#download_pdf_modal').modal('show');

        });
        $('.download_pdf').click(function() {
            
            var url = $(this).data('url');

            var link=document.createElement('a');
            document.body.appendChild(link);
            link.href= url;
            link.download = '';
            link.click();

        });

        $('#password_change').click(function() {

            var id = $(this).data('id');
            /*
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
            */
           
            $('#password_change_modal').modal('show');

        });
        $('#password_change_submit').click(function() {

            $('#password_change_form').submit();

        });

        $('#version_info').click(function() {

            $('#version_info_modal').modal('show');

        });

        $('#developer').click(function() {

            var level = $(this).data('level');

            if(level == -1) { $('#secret_number').hide(); }

            $('#developer_info_modal').modal('show');

        });

    });
    

</script>

<?= $this->endSection() ?>