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
                        <?php
                            if(isset($_SESSION['user']['id'])) {
                                $_SESSION['user']['id'];
                            } else {
                                echo "No user session is set.  Use URL: login/login.manager.php for testing.";
                            }
                        ?>
                    </td>
                    <td>
                        <?php
                            if(isset($_SESSION['user']['permission'])) {
                                $_SESSION['user']['permission'];
                            }
                        ?>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</body>
</html>