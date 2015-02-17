<?php
$out .= '<form class="do-submit" name="editwidget" method="POST" action="'.$app_http.'">
			<input type="hidden" name="action" value="update" />
			<input type="hidden" name="id" value="'.$widget['id'].'" />
			<div class="column column-half">
				<label for="widget[name]">Name</label>
				<input type="text" name="widget[name]" value="'.$widget['name'].'" />
				<label for="widget[description]">Description</label>
				<textarea name="widget[description]">'.$widget['description'].'</textarea>
			</div>
			<input type="submit" name="submitwidget" value="Update Widget" />
		</form>';
?>