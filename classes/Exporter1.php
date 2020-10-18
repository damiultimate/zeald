<?php
// retrieves & formats data from the database for export
class Exporter {



    public function __construct() {
        
    }

    function setHelperClass($classobject){

        $this->classObject = $classobject;
    }



    function getPlayerStats($search) {
        $data = $this->queryDB("SELECT roster.name, player_totals.* FROM player_totals INNER JOIN roster ON (roster.id = player_totals.player_id) WHERE", $search) ?: [];

        // calculate totals
        
        $this->calculateTotals($data);

      
        return collect($data);
    }


//Delegation of operations to make the code look as neat as possible
    function calculateTotals($data){
        foreach ($data as &$row) {
            unset($row['player_id']);
            $row['total_points'] = ($row['3pt'] * 3) + ($row['2pt'] * 2) + $row['free_throws'];
            $row['field_goals_pct'] = $row['field_goals_attempted'] ? (round($row['field_goals'] / $row['field_goals_attempted'], 2) * 100) . '%' : 0;
            $row['3pt_pct'] = $row['3pt_attempted'] ? (round($row['3pt'] / $row['3pt_attempted'], 2) * 100) . '%' : 0;
            $row['2pt_pct'] = $row['2pt_attempted'] ? (round($row['2pt'] / $row['2pt_attempted'], 2) * 100) . '%' : 0;
            $row['free_throws_pct'] = $row['free_throws_attempted'] ? (round($row['free_throws'] / $row['free_throws_attempted'], 2) * 100) . '%' : 0;
            $row['total_rebounds'] = $row['offensive_rebounds'] + $row['defensive_rebounds'];
        }
    }


    function getPlayers($search) {
        return collect($this->queryDB("SELECT roster.* FROM roster WHERE", $search))
            ->map(function($item, $key) {
                unset($item['id']);
                return $item;
            });
    }


//Delegation of operations to make the code look neat
    function queryDB($query,$search){
         $where = [];
        if ($search->has('playerId')) $where[] = "roster.id = '" . $search['playerId'] . "'";
        if ($search->has('player')) $where[] = "roster.name = '" . $search['player'] . "'";
        if ($search->has('team')) $where[] = "roster.team_code = '" . $search['team']. "'";
        if ($search->has('position')) $where[] = "roster.position = '" . $search['position'] . "'";
        if ($search->has('country')) $where[] = "roster.nationality = '" . $search['country'] . "'";
        $where = implode(' AND ', $where);
        $sql = $query." ".$where;
        return query($sql);
    }


//Here, an object of a class is used here that must have the methods XML(), JSON(), CSV() and HTML(). For this example, i have implemented this design with the FormatHelper class.

    public function format($data, $format = 'html') {
        // return the right data format
        $this->classObject->setData($data);
        
        switch($format) {
            case 'xml':
                return $this->classObject->XML();
                break;
            case 'json':
                return $this->classObject->JSON();
                break;
            case 'csv':
                return $this->classObject->CSV();
                break;
            default: // html
                return $this->classObject->HTML($this);
                break;
        }
    }


    // wrap html in a standard template
    public function htmlTemplate($html) {
        return <<<HTML
<html>
<head>
<style type="text/css">
    body {
        font: 16px Roboto, Arial, Helvetica, Sans-serif;
    }
    td, th {
        padding: 4px 8px;
    }
    th {
        background: #eee;
        font-weight: 500;
    }
    tr:nth-child(odd) {
        background: #f4f4f4;
    }
</style>
</head>
<body>
 $html
</body>
</html>
HTML;
    }
}

?>