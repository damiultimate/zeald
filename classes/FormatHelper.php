<?php 
use Illuminate\Support;
use LSS\Array2Xml;
require_once('interface/ExporterInterface.php');
// This helper class was created to make the exporting process feel dynamic. If anyone pleases and they do not like this code, they can go ahead and build their own helper class.

class FormatHelper implements ExporterInterface{


	public function __construct(){

	}

	public function setData($data){

		$this->data = $data;
	}


	public function XML(){

		$data = $this->data;

		header('Content-type: text/xml');
	                
                // fix any keys starting with numbers
        $keyMap = ['zero', 'one', 'two', 'three', 'four', 'five', 'six', 'seven', 'eight', 'nine'];
            $xmlData = [];
            foreach ($data->all() as $row) {
                $xmlRow = [];
                foreach ($row as $key => $value) {
                    $key = preg_replace_callback('(\d)', function($matches) use ($keyMap) {
                        return $keyMap[$matches[0]] . '_';
                    }, $key);
                    $xmlRow[$key] = $value;
                }
                $xmlData[] = $xmlRow;
            }
	    $xml = Array2XML::createXML('data', [
	        'entry' => $xmlData
	    ]);
	    return $xml->saveXML();
	}


	public function JSON(){
		$data = $this->data;

		header('Content-type: application/json');
        return json_encode($data->all());

	}



	public function CSV(){
		$data = $this->data;

		header('Content-type: text/csv');
        header('Content-Disposition: attachment; filename="export.csv";');
        if (!$data->count()) {
            return;
        }
        $csv = [];
        
        // extract headings
        // replace underscores with space & ucfirst each word for a decent headings
        $headings = collect($data->get(0))->keys();
        $headings = $headings->map(function($item, $key) {
            return collect(explode('_', $item))
                ->map(function($item, $key) {
                    return ucfirst($item);
                })
                ->join(' ');
        });
        $csv[] = $headings->join(',');

        // format data
        foreach ($data as $dataRow) {
            $csv[] = implode(',', array_values($dataRow));
        }
        return implode("\n", $csv);
	}



	public function HTML($identifier){
		
		$data = $this->data;

		if (!$data->count()) {
                    return $identifier->htmlTemplate('Sorry, no matching data was found');
                }
                
                // extract headings
                // replace underscores with space & ucfirst each word for a decent heading
                $headings = collect($data->get(0))->keys();
                $headings = $headings->map(function($item, $key) {
                    return collect(explode('_', $item))
                        ->map(function($item, $key) {
                            return ucfirst($item);
                        })
                        ->join(' ');
                });
                $headings = '<tr><th>' . $headings->join('</th><th>') . '</th></tr>';

                // output data
                $rows = [];
                foreach ($data as $dataRow) {
                    $row = '<tr>';
                    foreach ($dataRow as $key => $value) {
                        $row .= '<td>' . $value . '</td>';
                    }
                    $row .= '</tr>';
                    $rows[] = $row;
                }
                $rows = implode('', $rows);
                return $identifier->htmlTemplate('<table>' . $headings . $rows . '</table>');
	}
      
}

 ?>
