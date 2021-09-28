<?= $this->extend('common_layout') ?>

<?= $this->section('content') ?>

<form method="POST">

    <div style="width:fit-content; margin:0 auto; padding:16px;">
        <div class="uiframe" style="width:400px;">
        
        <i class="hamburger icon" onclick="location.href='/fm/menu'" style="cursor: pointer;"></i>

        <div style="height:1px; background-color:#e8e9e9; margin-top:16px; margin-bottom:8px;"></div>

        <table class="ui very basic table" style="margin-top:-8px; margin-bottom:-8px;">
        
            <?php foreach($users as $user) : ?>
                <tr style="color:<?= $user['level'] == 0 ? "BLUE" : "BLACK" ?>">
                    <td>
                        <div class="ui left pointing dropdown">
                            <?= $user['username'] . " ( " . $user['birthday'] . " )" ?>
                            <div class="menu">
                                <a class="item user_birthday_change" data-id="<?= $user['id'] ?>" href="#">생년월일 변경</a>
                                <a class="item user_delete" data-id="<?= $user['id'] ?>" href="#">사용자 삭제</a>
                            </div>
                        </div>
                    </td>
                    <td style="text-align:right;">
                        <div class="ui left pointing dropdown">
                            <?= getUserLevel($user['level']) ?>
                            <div class="menu">
                                <a class="item user_level_change" data-id="<?= $user['id'] ?>" href="#">최고관리자</a>
                                <a class="item user_level_change" data-id="<?= $user['id'] ?>" href="#">관리자</a>
                                <a class="item user_level_change" data-id="<?= $user['id'] ?>" href="#">팀장</a>
                                <a class="item user_level_change" data-id="<?= $user['id'] ?>" href="#">대기자</a>
                            </div>
                        </div>
                    </td>
                </tr>
            <?php endforeach ?>

        </table>

        <div style="height:1px; background-color:#e8e9e9; margin-top:8px; margin-bottom:8px;"></div>

        </div>
    </div>
    
</form>
    
<?= $this->endSection() ?>


<?= $this->section('custom_js') ?>

<script type="text/javascript">

    $(document).ready(function() {
        
        $('.item.user_level_change').click(function() {

            
        });

    });

</script>

<?= $this->endSection() ?>
