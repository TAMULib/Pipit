<?php
$widget = $parameters['widget'];
echo '<form class="do-submit" name="editwidget" method="POST" action="'.$app_http.'">
			<input type="hidden" name="action" value="update" />
			<input type="hidden" name="id" value="'.$widget['id'].'" />
			<div class="form-group">
				<label for="widget[name]">Name</label>
				<input class="form-control" type="text" name="widget[name]" value="'.$widget['name'].'" />
			</div>
			<div class="form-group">
				<label for="widget[description]">Description</label>
				<textarea class="form-control" name="widget[description]">'.$widget['description'].'</textarea>
			</div>
			<input class="btn btn-default" type="submit" name="submitwidget" value="Update Widget" />
		</form>';
?>