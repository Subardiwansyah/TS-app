<div class="card">
	<div class="card-header">
		<h5 class="card-title">Daftar User</h5>
	</div>
	
	<div class="card-body">
		<?php 
		if (!$users) {
			show_message('Data tidak ditemukan', '', false);
		} else {
			if (!empty($msg)) {
				show_alert($msg);
			}
			?>
			<div class="table-responsive">
			<table class="table table-striped table-bordered table-hover data-tables">
			<thead>
			<tr>
				<th>No</th>
				<th>Username</th>
				<th>Nama</th>
				<th>Email</th>
				<th>Role</th>
				<th>Aksi</th>
			</tr>
			</thead>
			<tbody>
			<?php
			$no = 1;
			// echo $user; die;
			foreach ($users as $key => $val)
			{
				
				$list = '';
				if (key_exists($val['id_role'], $user_role)) {
					$roles = $user_role[$val['id_role']];
					foreach ($roles as $role_id) 
					{
						$list .= '<span class="badge badge-secondary badge-role px-3 py-2 mr-1 mb-1 pr-4">' . $role[$role_id]['judul_role'] . '<a data-action="remove-role" data-pair-id="'.$val['id_role'].'" data-role-id="'.$role_id.'" href="javascript:void(0)" class="text-danger"><i class="fas fa-times"></i></a></span>';
					}
				}
				echo '<tr>
						<td>' . $no . '</td>
						<td>' . $val['username'] . '</td>
						<td>' . $val['nama'] . '</td>
						<td>' . $val['email'] . '</td>
						<td>' . $list . '</td>
						<td>
							<div class="btn-action-group">
							<a data-pair-id="'.$val['id_user'].'" href="' . module_url() . '?action=edit&id=' . $val['id_user'] .'" class="btn btn-success btn-xs mr-1 role-edit"><i class="fa fa-edit"></i>&nbsp;Edit</a>
							</div>
						</td>
					</tr>';
				$no++;
			}
			$settings['order'] = [0,'asc'];
			$settings['columnDefs'][] = ['targets' => 5, 'orderable' => false];
			?>
			</tbody>
			</table>
			</div>
			<span id="dataTables-setting" style="display:none"><?=json_encode($settings)?></span>
			<?php 
		} ?>
		
	</div>
</div>