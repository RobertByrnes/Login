<!doctype html>

<head>
</head>

<body>

    <div class="container">
        <h2>Welcome
            <?php print $_SESSION['user']['fname'].' '.$_SESSION['user']['lname']; ?>
        </h2>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Firstname</th>
                    <th>Lastname</th>
                    <th>Email</th>
                </tr>
            </thead>
            <tbody>
                <?php if($_SESSION['user']['user_role'] == 2){
    	foreach ($vars as $user) {
    	?>
                <tr>
                    <td>
                        <?=$user['fname']?>
                    </td>
                    <td>
                        <?=$user['lname']?>
                    </td>
                    <td>
                        <?=$user['email']?>
                    </td>
                </tr>
                <?php
    	}
    }else{ ?>
                <tr>
                    <td>
                        <?=$_SESSION['user']['fname']?>
                    </td>
                    <td>
                        <?=$_SESSION['user']['lname']?>
                    </td>
                    <td>
                        <?=$_SESSION['user']['email']?>
                    </td>
                </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>

</body>

</html>