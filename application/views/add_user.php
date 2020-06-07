<?php
include_once ("includes/header.php");
?>
<div class="row">
	<div class="col-lg-8 col-lg-offset-2">
		<div class='page-title'>
			<?php if($mode == "edit") {?>
			<h2>Edit User</h2>
			<?php } else{ ?>
			<h2>Add User</h2>
			<?php } ?>
		</div>
		<?php $action = ($mode == "edit") ? "/users/edit_this_user/{$user_id}" : "/users/add_new_user"; ?>
		<form method="POST" action="<?= $action ?>">
		<div class="add-user-block">
			<div class="form-group">
				<label for="user_name">User Name *</label>
				<br/>
				<?php if($mode == "edit") {?>
				<input type="text" name="user_name" class="user_name" value="<?= $user_name ?>"/>	
				<?php } else {?>
				<input type="text" name="user_name" class="user_name" />
				<?php } ?>
			</div>
			<div class="form-group">
				<label for="password">Password * </label>
				<br/>
				<?php if($mode == "edit") {?>
				<input type="password" name="password" class="password" value="<?= $password ?>"/>		
				<?php } else {?>				
				<input type="password" name="password" class="password" />
				<?php } ?>
			</div>				
			<div class="form-group">
				<label for="email">Email (optional)</label>
				<br/>
				<?php if($mode == "edit") {?>
				<input type="text" name="email" class="email" value="<?= $email ?>"/>
				<?php } else {?>				
				<input type="text" name="email" class="email" />
				<?php } ?>
			</div>					
			<input type="submit" name="add_user" class="btn btn-success btn-md add-user-btn" value="Save" />
		</div>
		</form>
		<p class="message"><?= $message ?></p>
	</div>
</div>
<?php
include_once ("includes/footer.php");
?>