<?php
namespace Core\Classes\ViewRenderers;

/**
*	An implementation of the ViewRenderer interface for rendering registered viewvariables as CSV
*
*	@author Jason Savell <jsavell@library.tamu.edu>
*/

class CSVViewRenderer extends JSONViewRenderer {
	protected $csvFileName = 'data.csv';

	/**
	* 	This method will produce a downloadable CSV by taking the *first* registered view variable (others will be ignored)
	*	and using it to generate field names from the first row and writing the rest to the CSV as data.
	*
	*/
	public function renderView() {
		header('Content-Type: text/csv; charset=utf-8');
		header('Content-Disposition: attachment; filename='.$this->csvFileName);
		$out = fopen('php://output', 'w');
		$viewVars = $this->getViewVariables();
		$csvRows = reset($viewVars);
		if (is_array($csvRows)) {
			$fields = array_keys(reset($csvRows));
			if (is_array($fields)) {
				fputcsv($out, $fields);
				foreach ($csvRows as $row) {
					fputcsv($out, $row);
				}
				fclose($out);
			}
		}
	}

	/**
	*	This method can be used to override the default file name for the generated csv.
	*
	*	@param string $csvFileName The desired filename
	*/
	public function setCsvFileName($csvFileName) {
		$this->csvFileName = $csvFileName;
	}
}
