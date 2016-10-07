<div class="do-results">
	<form class="vertical-spacer-bottom do-submit" name="addpart" method="POST" action="<?php echo $app_http;?>">
<?php
$modalContext = 'action=parts&widgetid='.$parameters['widget']['id'];
echo '	<input type="hidden" name="modal_context" value="'.$modalContext.'" />
		<input type="hidden" name="action" value="parts" />
		<input type="hidden" name="subaction" value="add" />
		<input type="hidden" name="widgetid" value="'.$parameters['widget']['id'].'" />
		<input class="inline-block" type="text" name="part[name]" />
		<input class="inline-block small" type="submit" name="submitadd" value="Add" />
	</form>';
if ($parameters['parts']) {
?>
	<table class="list">
		<tr>
			<th>Part</th>
			<th>Actions</th>
		</tr>
<?php
	foreach ($parameters['parts'] as $part) {
		echo "<tr>
					<td>{$part['name']}</td>
					<td class=\"capitalize\">";
echo '					<a class="inline-block button button-small do-loadmodal" href="'.$app_http.'?action=parts&subaction=edit&partid='.$part['id'].'">Edit</a>
						<form class="inline-block do-submit-confirm" name="removepart" method="POST" action="'.$app_http.'">
							<input type="hidden" name="modal_context" value="'.$modalContext.'" />
							<input type="hidden" name="action" value="parts" />
							<input type="hidden" name="subaction" value="remove" />
							<input type="hidden" name="partid" value="'.$part['id'].'" />
							<input class="inline-block small" type="submit" name="submitremove" value="Remove" />
						</form>';
echo "
					</td>
				</tr>";
	}
?>
	</table>
<?php
} else {
	echo 'No parts, yet!';
}

?>
</div>
