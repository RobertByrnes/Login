<?php
/* Smarty version 3.1.34-dev-7, created on 2021-01-04 20:23:08
  from 'C:\wamp64\www\Repositories\login\templates\userpage.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '3.1.34-dev-7',
  'unifunc' => 'content_5ff3792caed4f2_59974879',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    'c8408bf5540eb0d0fa565a7e914ab797cd541251' => 
    array (
      0 => 'C:\\wamp64\\www\\Repositories\\login\\templates\\userpage.tpl',
      1 => 1609539225,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_5ff3792caed4f2_59974879 (Smarty_Internal_Template $_smarty_tpl) {
?><div class="container">
  <h2>Welcome <?php echo '<?php ';?>
print $_SESSION['user']['fname'].' '.$_SESSION['user']['lname']; <?php echo '?>';?>
</h2>           
  <table class="table table-striped">
    <thead>
      <tr>
        <th>Firstname</th>
        <th>Lastname</th>
        <th>Email</th>
      </tr>
    </thead><tbody>
    <?php echo '<?php ';?>
if($_SESSION['user']['user_role'] == 2){
    	foreach ($vars as $user) {
    	<?php echo '?>';?>

    		<tr>
		        <td><?php echo '<?=';?>
$user['fname']<?php echo '?>';?>
</td>
		        <td><?php echo '<?=';?>
$user['lname']<?php echo '?>';?>
</td>
		        <td><?php echo '<?=';?>
$user['email']<?php echo '?>';?>
</td>
		    </tr>
    	<?php echo '<?php
    	';?>
}
    }else{ <?php echo '?>';?>

      <tr>
        <td><?php echo '<?=';?>
$_SESSION['user']['fname']<?php echo '?>';?>
</td>
        <td><?php echo '<?=';?>
$_SESSION['user']['lname']<?php echo '?>';?>
</td>
        <td><?php echo '<?=';?>
$_SESSION['user']['email']<?php echo '?>';?>
</td>
      </tr>    
    <?php echo '<?php ';?>
} <?php echo '?>';?>
</tbody>
  </table>
  <p><a href='logout.php'>Logout</a></p>
</div>

</body>
</html>
<?php }
}
