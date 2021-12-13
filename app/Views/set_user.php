<?= $this->extend('common_layout') ?>

<?= $this->section('content') ?>

    <form method="POST">

        <div style="width:fit-content; margin:0 auto; padding:16px;">
            <div class="uiframe" style="width:400px;">
            
                <div style="padding:16px;">
                    <i class="bars icon" onclick="location.href='/fm/menu'" style="cursor: pointer;"></i>
                    <label>직원등급 관리</label>
                </div>

                <div style="height:1px; background-color:#e8e9e9;"></div>

                <table width="100%" style="padding:8px;">
                
                    <?php foreach($users as $user) : ?>
                        <tr class="edit_user_info" data-mylevel="<?= $level ?>" data-id="<?= $user['id'] ?>" data-level="<?= $user['level'] ?>" data-username="<?= $user['username'] ?>" data-birthday="<?= $user['birthday'] ?>" style="color:<?= $user['level'] == 0 ? "BLUE" : "BLACK" ?>; cursor:pointer;">
                            <td style="padding:8px;">
                                <?= $user['username'] . " ( " . $user['birthday'] . " )" ?>
                            </td>
                            <td data-id="<?= $user['level'] ?>" style="padding:8px; text-align:right;">
                                <?= getUserLevel($user['level']) ?>
                            </td>
                        </tr>
                    <?php endforeach ?>

                </table>

            </div>
        </div>
        
    </form>

    <!-- 직원정보수정 modal -->
    <div id="edit_user_info_modal" class="ui mini modal" style="padding:16px;">

        <form id="edit_user_info_form" class="ui form" method="POST" action="/fm/edit_user_info">
            
            <div style="display:flex; justify-content:space-between; align-items:center; margin-top:4px">
                <label>아이디</label>
                <span id="edit_user_info_name" style="width:270px;"></span>
            </div>

            <div style="display:flex; justify-content:space-between; align-items:center; margin-top:16px">
                <label>패스워드</label>
                <input id="edit_user_info_birthday" type="text" name="new_birthday" placeholder="생년월일 혹은 8자리 이상의 숫자" style="width:270px;">
            </div>
            
            <div style="display:flex; justify-content:space-between; align-items:center; margin-top:4px">
                <label>직원등급</label>
                <div style="width:270px;">
                    <select id="edit_user_info_level" class="ui fluid dropdown" name="new_level">
                        <option value="3">최고관리자</option>
                        <option value="2">관리자</option>
                        <option value="1">팀장</option>
                        <option value="0">대기자</option>
                    </select>
                </div>
            </div>

            <div style="display:flex; justify-content:space-between; align-items:center; margin-top:24px; margin-bottom:4px">
                <span id="delete_user_submit" style="color:#5599DD; cursor:pointer;">사용자 삭제</span>
                <div class="actions">
                    <span class="cancel" style="color:#5599DD; cursor:pointer;">취소</span>
                    <span id="edit_user_info_submit" style="color:#5599DD; margin-left:32px; cursor:pointer;">확인</span>
                </div>
            </div>

            <input type="hidden" name="user_id">
            <input type="hidden" name="user_delete">

        </form>

    </div>

<?= $this->endSection() ?>


<?= $this->section('custom_js') ?>

<script type="text/javascript">

    $(document).ready(function() {
        
        $('.edit_user_info').click(function() {

            $('#edit_user_info_form [name=user_id]').val($(this).data('id'));   //유저 id
            $('#edit_user_info_name').text($(this).data('username'));            //유저 이름
            $('#edit_user_info_birthday').val($(this).data('birthday'));        //유저 생일

            //admin로그인이 아닐시 최고관리자 등급은 못 건듬
            if($(this).data('mylevel') != 4 && $(this).data('level') >= 3) {
                $('#edit_user_info_level').dropdown().addClass('disabled');
            } else {
                $('#edit_user_info_level').dropdown().removeClass('disabled');
            }
            $('#edit_user_info_level').dropdown('set selected', $(this).data('level'));

            $('#edit_user_info_modal').modal('show');

        });
        $('#edit_user_info_submit').click(function() {

            $('#edit_user_info_form').submit();
        });
        $('#delete_user_submit').click(function() {

            $('#edit_user_info_form [name=user_delete]').val("true");
            $('#edit_user_info_form').submit();
        });

    });

</script>

<?= $this->endSection() ?>
