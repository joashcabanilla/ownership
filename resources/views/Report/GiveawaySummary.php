<?php
    error_reporting(0);
    require_once(app_path('Includes/excel/spreadsheet/Writer.php'));

    function convertEncoding($string) {
        return iconv('UTF-8', 'ISO-8859-1//TRANSLIT', $string);
    }
    
    $xls = new Spreadsheet_Excel_Writer();
    $header = $xls->addFormat(array('Size' => 11));
    $header->setLocked();
    $header->setBold();
    $header->setFontFamily('Arial');
    $header->setAlign("center");
    $header->setAlign("vcenter");

    $subheader = $xls->addFormat(array('Size' => 10));
    $subheader->setLocked();
    $subheader->setFontFamily('Arial');
    $subheader->setAlign("center");
    $subheader->setAlign("vcenter");
    $subheader->setBold();
    $subheader->setBorder(1);
    $subheader->setFgColor('yellow');

    $subheaderB = $xls->addFormat(array('Size' => 11));
    $subheaderB->setLocked();
    $subheaderB->setFontFamily('Arial');
    $subheaderB->setAlign("center");
    $subheaderB->setAlign("vcenter");
    $subheaderB->setBold();
    $subheaderB->setBorder(1);
    $subheaderB->setFgColor('yellow');

    $subheaderNB = $xls->addFormat(array('Size' => 10));
    $subheaderNB->setLocked();
    $subheaderNB->setFontFamily('Arial');
    $subheaderNB->setAlign("left");
    $subheaderNB->setAlign("vcenter");
    $subheaderNB->setBold();

    $subheaderName = $xls->addFormat(array('Size' => 10));
    $subheaderName->setLocked();
    $subheaderName->setFontFamily('Arial');
    $subheaderName->setAlign("left");
    $subheaderName->setAlign("vcenter");
    $subheaderName->setBold();

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

    $normalB = $xls->addFormat(array('Size' => 10));
    $normalB->setFontFamily('Arial');
    $normalB->setAlign("center");
    $normalB->setAlign("vcenter");
    $normalB->setTextWrap();
    $normalB->setLocked();
    $normalB->setBold();
    $normalB->setBorder(1);
    $normalB->setFgColor('yellow');

    $normalR = $xls->addFormat(array('Size' => 10));
    $normalR->setFontFamily('Arial');
    $normalR->setAlign("right");
    $normalR->setAlign("vcenter");
    $normalR->setTextWrap();
    $normalR->setBold();
    $normalR->setLocked();
    $normalR->setBorder(1);

    $normalRY = $xls->addFormat(array('Size' => 10));
    $normalRY->setFontFamily('Arial');
    $normalRY->setAlign("right");
    $normalRY->setAlign("vcenter");
    $normalRY->setTextWrap();
    $normalRY->setBold();
    $normalRY->setLocked();
    $normalRY->setBorder(1);
    $normalRY->setFgColor('yellow');

    $branchHead = $xls->addFormat(array('Size' => 14));
    $branchHead->setLocked();
    $branchHead->setFontFamily('Arial');
    $branchHead->setAlign("center");
    $branchHead->setAlign("vcenter");
    $branchHead->setBold();
    $branchHead->setFgColor('yellow');

    $branchHeadR = $xls->addFormat(array('Size' => 14));
    $branchHeadR->setLocked();
    $branchHeadR->setFontFamily('Arial');
    $branchHeadR->setAlign("right");
    $branchHeadR->setAlign("vcenter");
    $branchHeadR->setBold();
    $branchHeadR->setFgColor('yellow');

    $branchSB = $xls->addFormat(array('Size' => 12));
    $branchSB->setLocked();
    $branchSB->setFontFamily('Arial');
    $branchSB->setAlign("center");
    $branchSB->setAlign("vcenter");
    $branchSB->setBold();
    $branchSB->setBorder(1);
    $branchSB->setFgColor('yellow');

    $branchSBL = $xls->addFormat(array('Size' => 12));
    $branchSBL->setLocked();
    $branchSBL->setFontFamily('Arial');
    $branchSBL->setAlign("left");
    $branchSBL->setAlign("vcenter");
    $branchSBL->setBold();
    $branchSBL->setBorder(1);
    $branchSBL->setFgColor('yellow');
    
    $branchN = $xls->addFormat(array('Size' => 12));
    $branchN->setLocked();
    $branchN->setFontFamily('Arial');
    $branchN->setAlign("left");
    $branchN->setAlign("vcenter");
    $branchN->setBorder(1);

    $branchNR = $xls->addFormat(array('Size' => 12));
    $branchNR->setLocked();
    $branchNR->setFontFamily('Arial');
    $branchNR->setAlign("right");
    $branchNR->setAlign("vcenter");
    $branchNR->setBorder(1);

    $branchNRY = $xls->addFormat(array('Size' => 12));
    $branchNRY->setLocked();
    $branchNRY->setFontFamily('Arial');
    $branchNRY->setAlign("right");
    $branchNRY->setAlign("vcenter");
    $branchNRY->setBorder(1);
    $branchNRY->setFgColor('yellow');
    $branchNRY->setBold();

    $sheet = $xls->addWorksheet($title);
    $category = $title == "Share Capital Giveaway" ? "Share Capital" : "Time Deposit";
    $fields = array(
        array('MemId',15),
        array('PbNo',15),
        array('Name',30),
        array('Branch',20),
        array('Status',15),
        array($category,20),
        array('Giveaway',15),
        array('Quantity',10),
        array('Staff Name', 20),
        array('Date Received', 18)
    );

    $c = $r = 0;
    foreach($fields as $fieldinfo):
        list($caption,$colwidth) = $fieldinfo;
        $sheet->setColumn($c,$c,$colwidth);
        $sheet->write($r,$c,$caption,$subheaderB);$c++;
    endforeach;
    $r++;
    
    foreach($giveawayList as $data){
        $c = 0;
        $category = $title == "Share Capital Giveaway" ? $data["sharecapital"] : $data["timedeposit"];
        $sheet->writeString($r,$c,$data["memid"],$normalC);$c++;
        $sheet->writeString($r,$c,$data["pbno"],$normalC);$c++;
        $sheet->writeString($r,$c,convertEncoding($data["name"]),$normal);$c++;
        $sheet->writeString($r,$c,$data["branch"],$normalC);$c++;
        $sheet->writeString($r,$c,$data["status"],$normalC);$c++;
        $sheet->writeString($r,$c,$category,$normalC);$c++;
        $sheet->writeString($r,$c,$data["giveaway"],$normalC);$c++;
        $sheet->writeNumber($r,$c,$data["quantity"],$normalC);$c++;
        $sheet->writeString($r,$c,$data["updatedBy"],$normalC);$c++;
        $sheet->writeString($r,$c,$data["dataReceived"],$normalC);$c++;
        $r++;
    }

    $branchSheet = $xls->addWorksheet("Branch Summary");
    $c = $r = 1;
    $header = $title == "Share Capital Giveaway" ? "Share Capital" : "Time Deposit";
    $totalMembers = $title == "Share Capital Giveaway" ? $totalMigs : $totalTD;

    $c = 0;
    $totalReceived = 0;
    $totalPerDate = array();
    $branchSheet->setColumn(0,0,20);
    $r++;
    
    $branchSheet->write($r,$c,"Branch",$branchSB);
    ksort($dateList);
    foreach($dateList as $date){
        $c++;
        $branchSheet->setColumn($c,$c,12);
        $branchSheet->write($r,$c,$date,$branchSB);
    }
    $c++;
    $branchSheet->write($r,$c,"Total",$branchSB);

    ksort($branchList);
    foreach($branchList as $branch => $dateData){
        $c = 0;
        $r++;
        $totalPerBranch = 0;
        $branchSheet->writeString($r,$c,$branch,$branchN);
        ksort($dateData);
        foreach($dateList as $date){
            $totalDate = count($dateData[$date]); 
            $totalReceived += $totalDate;
            $totalPerBranch+= $totalDate;
            $totalPerDate[$date] += $totalDate;     
            $c++;   
            $branchSheet->write($r,$c,number_format($totalDate,0, '.', ','),$branchNR);
        }
        $c++;
        $branchSheet->write($r,$c,number_format($totalPerBranch,0, '.', ','),$branchNR);
    }
    $c = 0;
    $r++;
    $branchSheet->write($r,$c,"Grand Total",$branchSBL);
    $grandTotal = 0;
    foreach($dateList as $date){
        $c++;
        $branchSheet->write($r,$c,number_format($totalPerDate[$date],0, '.', ','),$branchNRY);
        $grandTotal += $totalPerDate[$date];
    }
    $c++;
    $branchSheet->write($r,$c,number_format($grandTotal,0, '.', ','),$branchNRY);

    $percentage = ($totalReceived/$totalMembers) * 100;
    $branchSheet->writeString(0,0,$header,$branchHead);
    $branchSheet->writeString(0,1,number_format($percentage,2, '.', ',') . "%",$branchHeadR);

    $overallSheet = $xls->addWorksheet("Overall Summary");
    $c = $r = 0;
    $header = $title == "Share Capital Giveaway" ? "Share Capital Giveaway Summary" : "Time Deposit Giveaway Summary";

    krsort($descList);
    foreach($summaryList as $date => $branchData){
        $c = 0;
        $overallSheet->writeString($r,$c,$header . " As Of ". $date,$subheaderNB);
        $overallSheet->mergeCells($r,$c,$r,4);
        $r++;

        $overallSheet->setColumn($c,$c,20);
        $overallSheet->write($r,$c,"Branch",$subheader);
        foreach($descList as $desc){
            $c++;
            $overallSheet->setColumn($c,$c,20);
            $overallSheet->write($r,$c,ucwords(strtolower($desc)),$subheader);
        }

        $r++;
        $grandTotal = array();
        foreach($branchData as $branch => $descData){
            $c = 0;
            $overallSheet->writeString($r,$c,$branch,$normal);
            foreach($descList as $desc){
                $c++;
                $descTotal = 0;
                foreach($descData[$desc] as $quantity){
                    $descTotal += $quantity;
                }
                $overallSheet->write($r,$c,number_format($descTotal,0, '.', ','),$normalR);
                $grandTotal[$desc] = $grandTotal[$desc] + $descTotal;
            }
            $r++;
        }

        $c = 0;
        $overallSheet->writeString($r,$c,"Total",$subheader);
        foreach($descList as $desc){
            $c++;
            $overallSheet->write($r,$c,number_format($grandTotal[$desc],0, '.', ','),$normalRY);
        }
        $r+=2;
    }

    $xls->send($title.".xls");
    $xls->close();
    die;
?>
    