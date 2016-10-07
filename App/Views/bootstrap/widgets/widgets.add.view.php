<?php
echo '<form class="do-submit" name="addbuilding" method="POST" action="'.$app_http.'">
			<input type="hidden" name="action" value="insert" />
			<div class="form-group">
				<label for="widget[name]">Name</label>
				<input class="form-control" type="text" name="widget[name]" />
			</div>
			<div class="form-group">
				<label for="widget[description]">Description</label>
				<textarea class="form-control" name="widget[description]"></textarea>
			</div>
			<input class="btn btn-default" type="submit" name="submitwidget" value="Add Widget" />
		</form>';
?>