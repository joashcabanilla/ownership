<?php
    error_reporting(0);
    require_once(app_path('Includes/excel/spreadsheet/Writer.php'));
    
    function convertEncoding($string) {
        return iconv('UTF-8', 'ISO-8859-1//TRANSLIT', $string);
    }
    
    $xls = new Spreadsheet_Excel_Writer();
    $header = $xls->addFormat(array('Size' => 11));
    $header->setLocked();
    $header->setFontFamily('Arial');
    $header->setAlign("center");
    $header->setAlign("vcenter");
    $header->setBold();
    $header->setBorder(1);
    $header->setFgColor('yellow');

    $headerB = $xls->addFormat(array('Size' => 11));
    $headerB->setLocked();
    $headerB->setFontFamily('Arial');
    $headerB->setAlign("center");
    $headerB->setAlign("vcenter");
    $headerB->setBold();
    $headerB->setBorder(1);

    $headerTotal = $xls->addFormat(array('Size' => 11));
    $headerTotal->setLocked();
    $headerTotal->setFontFamily('Arial');
    $headerTotal->setAlign("center");
    $headerTotal->setAlign("vcenter");
    $headerTotal->setBold();
    $headerTotal->setBorder(1);
    $headerTotal->setColor("red");

    $normal = $xls->addFormat(array('Size' => 10));
    $normal->setFontFamily('Arial');
    $normal->setAlign("left");
    $normal->setAlign("vcenter");
    $normal->setTextWrap();
    $normal->setLocked();
    $normal->setBorder(1);
    
    $normalC = $xls->addFormat(array('Size' => 10));
    $normalC->setFontFamily('Arial');
    $normalC->setAlign("center");
    $normalC->setAlign("vcenter");
    $normalC->setTextWrap();
    $normalC->setLocked();
    $normalC->setBorder(1);

    $sheet = $xls->addWorksheet($title);

    $fields = array(
        array('MemId',15),
        array('PbNo',15),
        array('Name',20),
        array('Branch',20),
        array('Registered By',20),
        array('Date and Time',15)
    );

    $c = $r = 0;
    foreach($fields as $fieldinfo):
        list($caption,$colwidth) = $fieldinfo;
        $sheet->setColumn($c,$c,$colwidth);
        $sheet->setRow($r,20);
        $sheet->write($r,$c,$caption,$header);$c++;
    endforeach;
    $r++;
    
    foreach($registeredList as $member){
        $c = 0;
        $sheet->writeString($r,$c,$member["memid"],$normalC);$c++;
        $sheet->writeString($r,$c,$member["pbno"],$normalC);$c++;
        $sheet->writeString($r,$c,convertEncoding($member["name"]),$normal);$c++;
        $sheet->writeString($r,$c,$member["branch"],$normalC);$c++;
        $sheet->writeString($r,$c,$member["updated_by"],$normalC);$c++;
        $sheet->writeString($r,$c,$member["received_at"],$normalC);$c++;
        $r++;
    }

    $sheet1 = $xls->addWorksheet("Summary");
    $c = $r = 0;
    
    $sheet1->setRow($r,20);
    $sheet1->write($r,$c,"BRANCH",$header);
    $r++;
    
    $sheet1->setRow($r,20);
    $sheet1->writeBlank($r,$c,$header);

    $sheet1->mergeCells(0,$c,$r,$c);
    $sheet1->setColumn($c,$c,25);

    $dayCol = 2;
    foreach($scheduleList as $date => $timeData){
        foreach($timeData as $time => $description){
            $c++;
            $r = 1;
            $sheet1->write($r,$c,date("A",strtotime($time)),$header);
            $sheet1->setColumn($c,$c,8);
        }

        $r = 0;
        $sheet1->write($r,$dayCol-1,$description." - ".strtoupper(date("M j",strtotime($date))),$header);
        $sheet1->writeBlank($r,$dayCol,$header);
        $sheet1->mergeCells($r,$dayCol-1,$r,$dayCol);
        $dayCol+=2;
    }

    $c++;
    $r = 0;
    $sheet1->write($r,$c,"TOTAL PER BRANCH",$header);
    $r++;
    $sheet1->writeBlank($r,$c,$header);
    $sheet1->mergeCells(0,$c,$r,$c);
    $sheet1->setColumn($c,$c,18);

    $r = 2;
    foreach($summaryList as $branch => $dateData){
        $c = 0;
        $totalBranch = 0;
        $sheet1->write($r,$c,$branch,$headerB);
        $sheet1->setRow($r,20);

        foreach($dateData as $date => $timeData){
            foreach($timeData as $time => $total){
                $c++;
                $sheet1->write($r,$c,number_format($total,0,".",","),$headerB); 
                $totalBranch += $total;
            }
        }

        $c++;
        $sheet1->write($r,$c,number_format($totalBranch,0,".",","),$headerTotal); 
        $r++;
    }

    $c = 0;
    $sheet1->write($r,$c,"TOTAL PER DAY",$header);
    $sheet1->setRow($r,20);
    $totalDay = 0;
    foreach($totalPerDay as $day => $timeData){
        foreach ($timeData as $time => $total){
            $totalDay += $total;
            $c++;
            $sheet1->write($r,$c,number_format($total,0,".",","),$header);
        }
    }
    $c++;
    $sheet1->write($r,$c,number_format($totalDay,0,".",","),$header);

    $xls->send($title.".xls");
    $xls->close();
    die;
?>
    