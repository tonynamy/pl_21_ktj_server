<?= $this->extend('common_layout') ?>

<?= $this->section('content') ?>

    <form method="POST">

        <div style="width:fit-content; margin:0 auto; padding:16px;">
            <div class="uiframe" style="width:800px; padding:16px;">

                <div style="display:flex; justify-content:space-between; align-items:center;">

                    <div style="display:flex; align-items:center;">
                        <i class="arrow left icon" onclick="location.href='/fm/view_productivity/'" style="cursor: pointer;"></i> <!-- 뒤로가기 -->
                        <span style="font-size:x-large; font-weight:normal;">굴다리에서 싱하형과 양중작업</span>
                    </div>

                    <button id="add_task" class="filletbutton" type="button">작업추가</button>
                </div>

                <div style="margin-top:16px;">
                    <table style="width:100%;">

                        <tr>
                            <td style="width:200px">2021-10-29 13:31:42</td>
                            <td style="width:130px">김두한팀</td>
                            <td style="width:90px">7명</td>
                            <td><span style="color:red">[삭제]</span></td>
                        </tr>

                        <tr>
                            <td style="width:200px">2021-10-29 13:46:12</td>
                            <td style="width:130px">김두한팀</td>
                            <td style="width:90px">1명</td>
                            <td><span style="color:red">[삭제]</span></td>
                        </tr>

                        <tr>
                            <td style="width:200px">2021-10-29 13:47:03</td>
                            <td style="width:130px">김두한팀</td>
                            <td style="width:90px">1명</td>
                            <td><span style="color:red">[삭제]</span></td>
                        </tr>

                    </table>
            
                </div>


            </div>
        </div>
        
    </form>

    <div id="add_task_modal" class="ui mini modal">

        <div style="padding:16px;">

            <div style="margin-top:4px;">
                작업시간
                <div class="ui calendar" id="inline_calendar" style="margin-top:8px"></div>
                <input type="hidden" name="task_calendar" value="">

            </div>
            <div style="display:flex; justify-content:space-between; align-items:center; margin-top:16px;">
                <div>작업팀</div>
                <div style="width:278px;">
                    <select id="team_select" class="ui fluid dropdown" name="team">
                        <option value="">팀이름</option>

                        <?php foreach($teams as $team) : ?>

                        <option value="<?=$team['id']?>" > <?= $team['name'] ?> </option>

                        <?php endforeach ?>

                    </select>
                </div>
            </div>
            <div style="display:flex; justify-content:space-between; align-items:center; margin-top:16px">
                <div>인원</div>
                <div class="ui input" style="width:278px;">
                    <input type="text" name="manday" placeholder="0을 포함하지 않는 자연수" value="">
                </div>
            </div>
            <div class="actions" style="text-align:right; margin-top:32px; margin-bottom:4px">
                <span class="cancel" style="color:#5599DD; cursor:pointer; margin-right:32px;">취소</span>
                <span style="color:#5599DD; cursor:pointer;">수정</span>
            </div>

        </div>


        <form id="edit_point_form" class="ui form" method="POST" action="/fm/edit_team_safe_point">
            <input id="edit_task_id" type="hidden" name="task_id" />
            <input id="edit_task_manday" type="hidden" name="task_manday" />
        </form>

    </div>

<?= $this->endSection() ?>


<?= $this->section('custom_js') ?>

    <script type="text/javascript">
        
        $('#inline_calendar').calendar({
            text: {
                months: ['1월', '2월', '3월', '4월', '5월', '6월', '7월', '8월', '9월', '10월', '11월', '12월'],
                monthsShort: ['1월', '2월', '3월', '4월', '5월', '6월', '7월', '8월', '9월', '10월', '11월', '12월'],
            },
            ampm: false,
            onChange: function(date) {
                
                year = date.getFullYear().toString();
                month = ('0' + (date.getMonth() + 1)).slice(-2);
                day = ('0' + date.getDate()).slice(-2);

                date_str = year + "-" + month + "-" + day;

                document.form.task_calendar.value = date_str;
            }
        });
            
        $(document).ready(function() {
            
            $('#add_task').click(function() {

                $('#add_task_modal').modal('show');

            });

        });

    </script>

<?= $this->endSection() ?>