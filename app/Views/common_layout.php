<!doctype html>

<html>

<head>

    <!-- You MUST include jQuery before Fomantic -->
    <script src="https://cdn.jsdelivr.net/npm/jquery@3.3.1/dist/jquery.min.js"></script>
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/fomantic-ui@2.8.8/dist/semantic.min.css">
    <script src="https://cdn.jsdelivr.net/npm/fomantic-ui@2.8.8/dist/semantic.min.js"></script>

</head>

<body>

    <div class="ui container">

        <?= $this->renderSection('content') ?>

    </div>

    <script type="text/javascript">

        <?php $error = session()->getFlashdata('error'); ?>

        <?php if(!is_null($error)) : ?>

        $(document).ready(function() {
            alert("<?=$error?>");
        });

        <?php endif ?>

    </script>

</body>

</html>