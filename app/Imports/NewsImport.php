<?php
namespace App\Imports;
use Illuminate\Support\Collection;
use App\Models\News;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\ToCollection;

class NewsImport implements ToCollection
{
    public $error = false;

    public $checkError;

    public $invalidItems = [];

    public $importFile = [];

    public function __construct(bool $checkError, array $invalidItems = [], $importFile = null)
    {
        $this->checkError = $checkError;
        $this->invalidItems = $invalidItems;
        $this->importFile = $importFile;
    }
    public function collection(Collection $rows)
    {


        $newsImportHeader = $rows->first();



        if ($newsImportHeader[0] == 'title' && $newsImportHeader[1] == 'content' && $newsImportHeader[2] == 'date' && $newsImportHeader[3] == 'source'){


            $filteredRows = array_filter($rows->toArray(), function($item) {
                $item = array_slice($item, 0, 4);

                return !empty(array_filter($item, function($value) {
                    return !is_null($value);
                }));
            });


            foreach ($filteredRows as &$subArray) {
                unset($subArray[4],$subArray[5], $subArray[6] ,$subArray[7], $subArray[8]);
                array_splice($subArray, 4, 0, [null]);
            }

            $filteredRows[0][4] = 'remarks';

            $newsRows = array_slice($filteredRows, 1);


            if (empty($newsRows)){
                $this->error = true;
                return true;
            }
        }
        else{
            $this->error = true;
            return true;
        }

        $header[0] = $filteredRows[0];
        DB::beginTransaction();



        foreach ($newsRows as $key => $row) {

            $errorRow[$key] = $row;
            $errorField = false;


            if (empty($row[0])) {
                if ($errorField){
                    $errorRow[$key][4] .= PHP_EOL . 'the title field is required';
                }else{
                    $errorRow[$key][4] =  'the title field is required';
                    $errorField = true;
                }
                $this->error = true;
            }

            if (empty($row[1])) {
                if ($errorField){
                    $errorRow[$key][4] .= PHP_EOL . 'the content field is required';
                } else {
                    $errorRow[$key][4] = 'the content field is required';
                    $errorField = true;
                }
                $this->error = true;
            }




            if (empty($row[2])){

                if ($errorField){
                    $errorRow[$key][4] .= PHP_EOL.'the date field is required' ;
                }else{
                    $errorRow[$key][4] = 'the date field is required' ;
                    $errorField = true;
                }
                $this->error = true;

            }


            if (empty($row[3])){
                if ($errorField){
                    $errorRow[$key][4] .= PHP_EOL . 'the source field is required';
                }else{
                    $errorRow[$key][4] =  'the source field is  required';
                }
                $this->error = true;

            }



            if (!$this->error) {

                $news = new News();
                $news->title = $row[0];
                $news->content = $row[1];
                $news->date = $row[2];
                $news->source = $row[3];
                $news->save();
            }

        }


        $invalidRows = array_merge($header, $errorRow);

        if ($this->error){
            $this->invalidItems = $invalidRows;
        }
        else{
            DB::commit();
            $this->error = false;
        }

    }

    public function getErrorCount(): bool
    {
        return $this->error;
    }

    public function getInvalidItems()
    {
        $datas = $this->invalidItems;


        return $datas;
    }


}
