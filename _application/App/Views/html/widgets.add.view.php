<?php
echo '<form class="do-submit" name="addbuilding" method="POST" action="'.$app_http.'">
			<input type="hidden" name="action" value="insert" />
			<div class="column column-half">
				<label for="widget[name]">Name</label>
				<input type="text" name="widget[name]" />
				<label for="widget[description]">Description</label>
				<textarea name="widget[description]"></textarea>
			</div>
			<input type="submit" name="submitwidget" value="Add Widget" />
		</form>';
?>