<?php

namespace App\Http\Controllers;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DataController extends Controller
{
    function import(Request $request){
        $request->validate([
            "file" => "required|file|mimetypes:csv,txt,application/octet-stream,text/plain"
        ]);

        $file = $request->file("file");
        $csvData = file_get_contents($file->getRealPath());
        $csvData = mb_convert_encoding($csvData, 'UTF-8', 'Windows-1252');
        $parseData = preg_split('/\r\n|\n|\r/', $csvData);
        $totalRecords = 0;

        logger("Starting CSV file parsing.");
        foreach ($parseData as $rowNumber => $data) {
           if($rowNumber > 0){
                $member =  (array) str_getcsv($data);
                try {
                    $insertData[] = [
                        "memid" => $member[0],
                        "pbno" => $member[1],
                        "firstname" => $member[2],
                        "middlename" => $member[3],
                        "lastname" => $member[4],
                        "birthdate" => Carbon::createFromFormat('m/d/Y', $member[5])->format('Y-m-d'),
                        "branch" => $member[6],
                        "status" => $member[7],
                        "created_at" => Carbon::now()
                    ];
                    $totalRecords++;
                } catch (\Exception $e) {
                    logger("Skipping invalid row #{$rowNumber}: " . json_encode($member));
                    continue;
                }
           } 
        }

        logger("CSV file parsing completed.");
        logger("Inserting data into the database.");
        $data = collect($insertData);
        $data->chunk(1000)->each(function ($chunk) use($request) {
            DB::table($request->tablename)->insert($chunk->toArray());
            logger("Inserted " . count($chunk->toArray()) . " data.");
        });
        logger("All data inserted successfully.");

        $result = [
            "success" => true,
            "totalRecords" => $totalRecords,
            "message" => "All data inserted successfully."
        ];

        return response()->json($result, 200);
    }
}
