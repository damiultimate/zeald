<?php 

interface ExporterInterface{

	public function XML();

	public function JSON();

	public function CSV();

	public function HTML($identifier);



}

 ?>