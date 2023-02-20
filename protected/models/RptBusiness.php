<?php
class RptBusiness extends CReport {
	protected $year;
	protected $month;
	protected $city;
	protected $labels;
	protected $posrow=4;
	protected $cityList=array();

	public function genReport() {
        $this->city = $this->criteria['CITY'];
        $this->month = $this->criteria['MONTH'];
        $this->year = $this->criteria['YEAR'];
		$this->labels = array(
				'title'=>Yii::t('app','Business Report'),
				'date'=>Yii::t('report','Date'),
				'city'=>Yii::t('user','City'),
			);
        $this->title = $this->labels["title"];
        $this->subtitle = $this->labels["title"].':'.$this->criteria['YEAR'].' - '.$this->criteria['YEAR'];
		$this->retrieveData();
		return $this->printReport();
	}
	
	public function retrieveData() {
		$year = $this->year;
		$month = $this->month;
        $city = $this->city;
        $sql = "";
        if(!empty($city)){
            $ids = explode('~',$city);
            if(count($ids)>0){
                $ids = implode("','",$ids);
                $sql = " and a.city in ('$ids')";
            }else{
                $sql = " and a.city = '$city'";
            }
        }

		$sql = "SELECT a.city,b.data_value,c.name,c.code FROM opr_monthly_hdr a,opr_monthly_dtl b,opr_monthly_field c
            WHERE
                a.year_no = '$year'
            AND a.month_no = '$month'
            AND a.id = b.hdr_id
            AND b.data_field = c.code $sql order by a.city, a.year_no, a.month_no, c.code";
		$rows = Yii::app()->db->createCommand($sql)->queryAll();
		if (count($rows) > 0) {
			$temp = array();
			foreach ($rows as $row){
			    if (!key_exists($row["city"],$this->cityList)){
                    $cityName = CGeneral::getCityName($row["city"]);
			        if(!$cityName){
			            $cityName = $row["city"];
                    }
			        $this->cityList[$row["city"]]=$cityName;
                }
			    if (!key_exists($row["code"],$temp)){
                    $temp[$row["code"]]=array();
                    $temp[$row["code"]]["name"]=$row["name"];
                }
                $temp[$row["code"]][$row["city"]]=$row["data_value"];
            }
			
			$this->data = $temp;
		}
		return (count($rows) > 0);
	}

	public function getReportName() {
	    if(empty($this->criteria['CITY'])){
            $city_name="";
        }else{
            $city_name = isset($this->criteria) ? ' - '.General::getCityName($this->criteria['CITY']) : '';
        }
		return (isset($this->criteria) ? Yii::t('report',$this->criteria['RPT_NAME']) : Yii::t('report','Nil')).$city_name;
	}
	
	public function printReport() {
		$title = $this->labels['title'];
		$subtitle = $this->labels['date'].': '.$this->year.'/'.substr('00'.$this->month,-2);
			
		$this->excel = new ExcelTool();
		$this->excel->start();
		
		$this->excel->newFile();
        $this->excel->createSheet();
        $this->excel->setActiveSheet(0);
        $this->printHeader($title, $subtitle);
        $this->printDetail();
		$outstring = $this->excel->getOutput();

		$this->excel->end();
		return $outstring;
	}
	
	// Print Header
	protected function printHeader($title, $subtitle='') {
	    $cityList = $this->cityList;
		$this->excel->writeReportTitle($title, $subtitle);
		
		$this->excel->writeCell(0,$this->posrow, $this->labels['city']);
		$this->excel->setColWidth(0, 25);
		$i=0;
        foreach ($cityList as $city){
            $i++;
            $this->excel->writeCell($i,$this->posrow, $city);
            $this->excel->setColWidth(1, 15);
        }
        $this->posrow++;
	}
	
	protected function printDetail() {
		// Print Date and Total Column
        $data = $this->data;
        $cityList = $this->cityList;
        $rowNum = 0;
        foreach ($data as $row){
            $rowNum++;
            $col=0;
            $this->excel->writeCell(0,$this->posrow+$rowNum, $row["name"]);
            foreach ($cityList as $key=>$city){
                $col++;
                if(!key_exists($key,$row)){
                    $row[$key]="";
                }
                $this->excel->writeCell($col,$this->posrow+$rowNum, $row[$key]);
            }
        }
	}
}
?>