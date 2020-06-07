<?php
include_once ("includes/header.php");
?>
<div class="row">
	<div class="col-lg-8 col-lg-offset-2">
		<div class='page-title'>
			<h2>All Users</h2>
		</div>
		<table class="table">
			<thead>
				<th style="width:80%">UserName</th>
				<th style="text-align:center;">Edit</th>
				<th style="text-align:center;">Delete</th>
			</thead>
			<tbody>
				<?php 
					foreach ($users->result() as $user) {
						echo "<tr><td>{$user->user_name}</td>
						<td style='text-align:center;'><a href='/users/edit_user/{$user->user_id}'><span class='glyphicon glyphicon-pencil'></span></a></td>
						<td style='text-align:center;'><a href='/users/delete_user/{$user->user_id}'><span class='glyphicon glyphicon-remove'></span></a></td>
						</tr>";
					}
				?>
			</tbody>
		</table>
	</div>
</div>

<?php
include_once ("includes/footer.php");
?>