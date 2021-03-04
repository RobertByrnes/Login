<?php session_start(); ?>
<!doctype html>
<head>
</head>
<body>
    <div class="container">
        <h2>Welcome...</h2>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>User ID</th>
                    <th>Permission Level</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>
                        <?=$_SESSION['user']['id']?>
                    </td>
                    <td>
                        <?=$_SESSION['user']['permission']?>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</body>
</html>