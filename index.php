<!doctype html>
<head>
</head>
<body>
    <div class="container">
        <h2>Welcome
            <?php session_start(); print $_SESSION['user']['id'].' '.$_SESSION['user']['permission']; ?>
        </h2>
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