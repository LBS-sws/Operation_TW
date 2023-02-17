<?php
class RptSalesSummary extends CReport {
	protected $year;
	protected $month;
	protected $region;
	protected $posrow = 4;
	protected $poscol = 2;
	protected $labels;
	
	public function genReport() {
		$this->labels = array(
				'date'=>Yii::t('report','Date'),
				'total_sales'=>Yii::t('report','Total Sales'),
				'grand_total'=>Yii::t('report','Grand Total'),
				'china_region'=>Yii::t('report','China & Franchise'),
				'sea_region'=>Yii::t('report','South East Asia'),
				'all'=>Yii::t('report','All'),
				'clean'=>Yii::t('report','Clean_PC_Misc_Paper'),
				'puri'=>Yii::t('report','Puriscent'),
				'meth'=>Yii::t('report','Formaldehye'),
				'air'=>Yii::t('report','Air purifier rental'),//¿ÕÆø¾»»¯»ú×âÁÞ
				'rptname'=>Yii::t('report','Sales Summary Report'),
			);
		$this->retrieveData();
		return $this->printReport();
	}
		public function retrieveData() {
		$this->year = $this->criteria['YEAR'];
		$year = $this->year;
		$this->month = $this->criteria['MONTH'];
		$month = $this->month;
		$this->region = $this->criteria['REGION'];
		$city = $this->criteria['CITY'];
		
		$suffix = Yii::app()->params['envSuffix'];

		$allowcities = ($this->region==0)
						? City::model()->getDescendantList($city)
						: City::model()->getDescendantList(($this->region==2 ? 'A1' : 'CN'));
		$allowcities = "'$city'".(empty($allowcities) ? "" : ",").$allowcities;
		
		$citylist = General::getCityListWithNoDescendant($allowcities);
		$list = '';
		foreach ($citylist as $key=>$value) {
			$list .= (empty($list) ? '' : ',')."'".$key."'";
		}
		
		$sql = "select a.*, h.region, h.name as city_name, 
					b.data_value as cln, c.data_value as pc, d.data_value as misc,
					e.data_value as puri, f.data_value as meth, k.data_value as air, g.data_value as ppr,
					workflow$suffix.RequestStatus('OPRPT',a.id,a.lcd) as wfstatus
				from opr_monthly_hdr a 
					inner join security$suffix.sec_city h on a.city=h.code 
					left outer join opr_monthly_dtl b on a.id=b.hdr_id and b.data_field='10001'
					left outer join opr_monthly_dtl c on a.id=c.hdr_id and c.data_field='10002'
					left outer join opr_monthly_dtl d on a.id=d.hdr_id and d.data_field='10003'
					left outer join opr_monthly_dtl e on a.id=e.hdr_id and e.data_field='10004'
					left outer join opr_monthly_dtl f on a.id=f.hdr_id and f.data_field='10005'
					left outer join opr_monthly_dtl g on a.id=g.hdr_id and g.data_field='10006'
					left outer join opr_monthly_dtl k on a.id=k.hdr_id and k.data_field='11001'
				where a.year_no=$year and a.month_no<=$month and
					a.city in ($list)
				order by h.region, a.city, a.year_no, a.month_no 
			";
		$rows = Yii::app()->db->createCommand($sql)->queryAll();
		if (count($rows) > 0) {
			$temp = array();
			$detail = array();
			$citycode = '';
			foreach ($rows as $row) {
				if (empty($citycode)) {
					$citycode = $row['city'];
				}
				if ($citycode!=$row['city']) {
					$temp[$citycode] = $detail;
					$detail = array();
					$citycode = $row['city'];
				}
				
				// Not accepted item
				if ($row['wfstatus']!='ED') {
					$row['cln'] = 0;
					$row['pc'] = 0;
					$row['misc'] = 0;
					$row['puri'] = 0;
					$row['meth'] = 0;
					$row['air'] = 0;
					$row['ppr'] = 0;
				}
				
				$detail[] = $row;
			}
			if (!empty($detail)) {
				$temp[$citycode] = $detail;
			}
			
			$this->data = $temp;
		}
		return (count($rows) > 0);	}

	public function getReportName() {
		$city_name = isset($this->criteria) ? ' - '.General::getCityName($this->criteria['CITY']) : '';
		return (isset($this->criteria) ? Yii::t('report',$this->criteria['RPT_NAME']) : Yii::t('report','Nil')).$city_name;
	}
	
	public function printReport() {
		$title = ($this->region==2) 
			? $this->labels['rptname'].' - '.$this->labels['sea_region']
			: (($this->region==1) 
				? $this->labels['rptname'].' - '.$this->labels['china_region']
				: $this->labels['rptname'].' - '.General::getCityName($this->criteria['CITY'])
				)
			;
		$subtitle = $this->labels['date'].': '.$this->year.'/'.substr('00'.$this->month,-2);
			
		$this->excel = new ExcelTool();
		$this->excel->start();
		
		$this->excel->newFile();
		for ($sht=1; $sht<=5; $sht++) {
			if ($sht>1) {
				$this->excel->createSheet();
				$this->excel->setActiveSheet($sht-1);
			}
			if ($sht==1) $sheetname = $this->labels['all'];
			if ($sht==2) $sheetname = $this->labels['clean'];
			if ($sht==3) $sheetname = $this->labels['puri'];
			if ($sht==4) $sheetname = $this->labels['meth'];
			if ($sht==5) $sheetname = $this->labels['air'];

			$this->excel->getActiveSheet()->setTitle($sheetname);
			$this->excel->setReportDefaultFormat();
			$this->printHeader($title, $subtitle);
			$this->printDetail($sht);
		}
		$outstring = $this->excel->getOutput();
		
		$this->excel->end();
		return $outstring;
	}
	
	// Print Header
	protected function printHeader($title, $subtitle='') {
		$this->excel->writeReportTitle($title, $subtitle);
		
		$this->excel->writeCell(0,$this->posrow, $this->labels['date']);
		$this->excel->setColWidth(0, 15);
		
		$this->excel->writeCell(1,$this->posrow, $this->labels['total_sales']);
		$this->excel->setColWidth(1, 15);

		$j = $this->poscol;
		foreach ($this->data as $citycode=>$record) {
			$this->excel->writeCell($j, $this->posrow, $record[0]['city_name']);
			$this->excel->setColWidth($j, 15);
			$j++;
		}
		
		$itemcnt = count($this->data);
		$range = $this->excel->getColumn(0).$this->posrow.':'.$this->excel->getColumn($itemcnt+1).$this->posrow;
		$this->excel->setRangeStyle($range,true,false,'C','C','allborders',true);
	}
	
	protected function printDetail($type) {
		// Print Date and Total Column
		$itemcnt = count($this->data);
		for ($i=1; $i<=12; $i++) {
			$text = $this->year.substr('00'.$i,-2);
			$this->excel->writeCell(0, $this->posrow+$i, $text);
			
			$text = '=SUM('.$this->excel->getColumn($this->poscol).($this->posrow+$i).':'.$this->excel->getColumn($this->poscol-1+$itemcnt).($this->posrow+$i).')';
			$this->excel->writeCell(1, $this->posrow+$i, $text, array('align'=>'R'));
			$this->excel->setCellStyle(1, $this->posrow+$i, array('numberformat'=>'#,##0.00'));
		}

		$range = $this->excel->getColumn(0).($this->posrow+1).':'.$this->excel->getColumn(0).($this->posrow+12);
		$this->excel->setRangeStyle($range,false,false,'L','C','outline',false);

		$range = $this->excel->getColumn(1).($this->posrow+1).':'.$this->excel->getColumn(1).($this->posrow+12);
		$this->excel->setRangeStyle($range,false,false,'R','C','outline',false);

		$this->excel->writeCell(0, $this->posrow+13, $this->labels['grand_total'].' '.$this->year);
		$range = $this->excel->getColumn(0).($this->posrow+13).':'.$this->excel->getColumn(0).($this->posrow+13);
		$this->excel->setRangeStyle($range,true,false,'L','C','allborders',false);

		$text = '=SUM('.$this->excel->getColumn(1).($this->posrow+1).':'.$this->excel->getColumn(1).($this->posrow+12).')';
		$this->excel->writeCell(1, $this->posrow+13, $text, array('align'=>'R'));
		$this->excel->setCellStyle(1, $this->posrow+13, array('numberformat'=>'#,##0.00'));
		$range = $this->excel->getColumn(1).($this->posrow+13).':'.$this->excel->getColumn(1).($this->posrow+13);
		$this->excel->setRangeStyle($range,false,false,'R','C','allborders',false);
		
		
		// Print Detail
		$x = $this->poscol;
		foreach ($this->data as $citycode=>$record) {
			$y = $this->posrow;
			foreach ($record as $row) {
				while ($y - $this->posrow != $row['month_no'] && $y - $this->posrow <= 12) {
					$y++;
				}
				
				$cln = empty($row['cln']) ? 0 : $row['cln'];
				$pc = empty($row['pc']) ? 0 : $row['pc'];
				$misc = empty($row['misc']) ? 0 : $row['misc'];
				$puri = empty($row['puri']) ? 0 : $row['puri'];
				$meth = empty($row['meth']) ? 0 : $row['meth'];
				$ppr = empty($row['ppr']) ? 0 : $row['ppr'];
			
				if ($type==1) $val = $cln + $pc + $misc + $puri + $meth + $ppr;
				if ($type==2) $val = $cln + $pc + $misc + $ppr;
				if ($type==3) $val = $puri;
				if ($type==4) $val = $meth;
			
				$this->excel->writeCell($x, $y, $val, array('align'=>'R'));
				$this->excel->setCellStyle($x, $y, array('numberformat'=>'#,##0.00'));
			}
			$range = $this->excel->getColumn($x).($this->posrow+1).':'.$this->excel->getColumn($x).($this->posrow+12);
			$this->excel->setRangeStyle($range,false,false,'R','C','outline',false);
			
			
			// Total
			$text = '=SUM('.$this->excel->getColumn($x).($this->posrow+1).':'.$this->excel->getColumn($x).($this->posrow+12).')';
			$this->excel->writeCell($x, $this->posrow+13, $text, array('align'=>'R'));
			$this->excel->setCellStyle($x, $this->posrow+13, array('numberformat'=>'#,##0.00'));
			$range = $this->excel->getColumn($x).($this->posrow+13).':'.$this->excel->getColumn($x).($this->posrow+13);
			$this->excel->setRangeStyle($range,false,false,'R','C','allborders',false);
			
			$x++;
		}
	}
}
?>