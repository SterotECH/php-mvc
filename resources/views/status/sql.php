<?php
/**
 * @var PDOException|Exception $description
 */
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Error Modal</title>
    <style>
        /* Modal styles */
        .modal {
            display: none;
            position: fixed;
            z-index: 1;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0, 0, 0, 0.4);
        }

        .modal-content {
            background-color: #fefefe;
            margin: 15% auto;
            padding: 20px;
            border: 1px solid #888;
            width: 80%;
        }

        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
        }

        .close:hover,
        .close:focus {
            color: black;
            text-decoration: none;
            cursor: pointer;
        }
    </style>
</head>
<body>

<div id="errorModal" class="modal">
    <div class="modal-content">
        <span class="close"                     style="padding: 8px 16px; background-color: #dc3545; color: #fff; border: none; border-radius: 4px; cursor: pointer; margin-top: 10px;"
        >&times;</span>
        <p style="">Error Message: <?= $description->getMessage() ?></p>
        <?php  if (env('APP_ENV') === 'development'): ?>
        <p><?= $description->getFile() ?> at line <?= $description->getLine() ?></p>
        <pre>Error Trace: <?= $description->getTraceAsString(); ?></pre>
        <?php endif; ?>
    </div>
</div>

<script>
    // Get the modal
    const modal = document.getElementById('errorModal');

    // Get the <span> element that closes the modal
    const span = document.getElementsByClassName('close')[0];

    // When the user clicks on <span> (x), close the modal
    span.onclick = function () {
        modal.style.display = 'none';
    };

    window.onclick = function (event) {
        if (event.target === modal) {
            modal.style.display = 'none';
        }
    };

    // Show the modal
    modal.style.display = 'block';
</script>

</body>
</html>
