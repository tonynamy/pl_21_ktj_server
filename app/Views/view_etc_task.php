<?= $this->extend('common_layout') ?>

<?= $this->section('content') ?>

    <form method="POST">

        <div style="width:fit-content; margin:0 auto; padding:16px;">
            <div class="uiframe" style="width:800px;">

                <div style="padding:16px">
                    <i class="hamburger icon" onclick="location.href='/fm/menu'" style="cursor: pointer;"></i>
                    <label>작업 조회</label>
                </div>

                <div style="height:1px; background-color:#e8e9e9;"></div>

                <div style="width:100%; padding:16px;">
                    <table class="ui sortable compact selectable celled table">
                        <thead class="full-width">
                            <tr align="center">
                                <th style="font-weight:normal;">작업명</th>
                                <th width="150px" style="font-weight:normal;">총투입인원</th>
                                <th width="150px" style="font-weight:normal;">작업시작일</th>
                                <th width="150px" style="font-weight:normal;">작업완료일</th>
                            </tr>
                        </thead>

                        <tbody>

                            <?php foreach($etc_tasks as $facility_serial => $etc_task) : ?>

                                <tr class="view_info" align="center">
                                    <td><?=$facility_serial?></td>
                                    <td><?=$etc_task['total_manday']?>명/<?=$etc_task['total_task']?>회</td>
                                    <td><?=$etc_task['started_at'] != null ? $etc_task['started_at']->toDateString() : ""?></td>
                                    <td><?=$etc_task['finished_at'] != null ? $etc_task['finished_at']->toDateString() : ""?></td>
                                </tr>

                            <?php endforeach ?>

                        </tbody>
                    </table>
            
                </div>


            </div>
        </div>
        
    </form>

<?= $this->endSection() ?>


<?= $this->section('custom_js') ?>

<script type="text/javascript">

    $(document).ready(function() {
        
        
        $('tr.view_info').click(function() {

            location.href = '/fm/view_etc_task_info/';

        });

        $('table').tablesort();

    });

</script>

<?= $this->endSection() ?>