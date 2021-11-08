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
                    <tr style="color:<?= $user['level'] == 0 ? "BLUE" : "BLACK" ?>">
                        <td class="edit_user_info" style="padding:8px;">
                            <?= $user['username'] . " ( " . $user['birthday'] . " )" ?>
                        </td>
                        <td class="edit_user_level" data-id="<?= $user['level'] ?>" style="padding:8px; text-align:right;">
                            <?= getUserLevel($user['level']) ?>
                        </td>
                    </tr>
                <?php endforeach ?>

            </table>

        </div>
    </div>
    
</form>

<div id="edit_user_info_modal" class="ui mini modal" style="padding:16px;">

    <div class="ui input" style="display:flex; align-items:center; margin-bottom:8px;">
        <label style="width:70px">아이디</label>
        <input type="text" name="name" placeholder="이름" value="">
    </div>

    <div class="ui input" style="display:flex; align-items:center; margin-bottom:24px;">
        <label style="width:70px">패스워드</label>
        <input type="text" name="birthday" placeholder="생년월일 (예 740101)" value="">
    </div>

    <div style="display:flex; justify-content:space-between; align-items:center;">
        <span style="color:#5599DD; cursor:pointer;">사용자 삭제</span>
        <div class="actions">
            <span class="cancel" style="color:#5599DD; cursor:pointer;">취소</span>
            <span style="color:#5599DD; margin-left:32px; cursor:pointer;">확인</span>
        </div>
    </div>

    <form id="edit_user_info_form" class="ui form" method="POST" action="">
        <input id="edit_user_info_name" type="hidden" name="user_name" />
        <input id="edit_user_info_birthday" type="hidden" name="user_birthday" />
    </form>

</div>

<div id="edit_user_level_modal" class="ui mini modal">

    <div style="padding-top:4px; padding-bottom:4px">

        <div style="font-size:large; padding:16px;">김두한 직원등급</div>
        <?php if($user['level'] != 3 && $level == 4) : ?>
            <div class="item user_level_change" data-id="<?= $user['id'] ?>" style="padding:16px; cursor:pointer;">최고관리자</div>
        <?php endif ?>
        <?php if($user['level'] != 2) : ?>
            <div class="item user_level_change" data-id="<?= $user['id'] ?>" style="padding:16px; cursor:pointer;">관리자</div>
        <?php endif ?>
        <?php if($user['level'] != 1) : ?>
            <div class="item user_level_change" data-id="<?= $user['id'] ?>" style="padding:16px; cursor:pointer;">팀장</div>
        <?php endif ?>
        <?php if($user['level'] != 0) : ?>
            <div class="item user_level_change" data-id="<?= $user['id'] ?>" style="padding:16px; cursor:pointer;">대기자</div>
        <?php endif ?>

        <div class="actions" style="float:right; padding:16px">
            <span class="cancel" style="color:#5599DD; cursor:pointer;">취소</span>
        </div>

    </div>
    
    <form id="edit_user_level_form" class="ui form" method="POST" action="">
        <input id="edit_user_level" type="hidden" name="user_level" />
    </form>

</div>
    
<?= $this->endSection() ?>


<?= $this->section('custom_js') ?>

<script type="text/javascript">

    $(document).ready(function() {
        
        $('.item.user_level_change').click(function() {

            
        });

        $('td.edit_user_info').click(function() {

            $('#edit_user_info_modal').modal('show');

        });

        $('td.edit_user_level').click(function() {

            $('#edit_user_level').val($(this).data('id'));
            $('#edit_user_level_modal').modal('show');

        });

    });

</script>

<?= $this->endSection() ?>
