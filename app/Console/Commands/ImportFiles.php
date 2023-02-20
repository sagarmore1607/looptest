<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Customer;
use App\Models\Products;
use Illuminate\Support\Facades\Http;

class ImportFiles extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:csvfile';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import CSV file from Url';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $urlsArray = ['Customers' => 'https://backend-developer.view.agentur-loop.com/customers.csv','Products'=>'https://backend-developer.view.agentur-loop.com/products.csv'];
        $logString = '';
        foreach ($urlsArray as $key=>$url)
        {
            $fileRequest = Http::withBasicAuth('loop','backend_dev')->get($url);
            $fileData = $fileRequest->getBody()->getContents();
            $fileData = explode("\r\n",$fileData);
            $added = $failed = 0;
            foreach($fileData as $key1=>$data)
            {
                if($key1 > 0)
                {
                    $stringAsArray = explode("\",",$data);
                    if($key == 'Products')
                    {
                        try {
                            Products::insert([
                                'id'=> trim($stringAsArray[0],"\""),
                                'product_name'=> trim($stringAsArray[1],"\""),
                                'price'=> trim($stringAsArray[2],"\"")
                            ]);
                            $added++;
                        } catch (\Exception $e){
                            $failed++;
                        }
                    }
                    else if($key == 'Customers')
                    {
                        try {
                            Customer::insert([
                                'id'=> trim($stringAsArray[0],"\""),
                                'job_title'=> trim($stringAsArray[1],"\""),
                                'email'=> strtolower(trim($stringAsArray[2],"\"")),
                                'fname_lname'=> trim($stringAsArray[3],"\""),
                                'registered_since'=> trim($stringAsArray[4],"\""),
                                'phone'=> trim($stringAsArray[5],"\"")
                            ]);
                            $added++;
                        } catch (\Exception $e){
                            $failed++;
                        }
                    }
                }                    
            }
            
            $logString .= "$key : Added Records = $added and Failed Records = $failed. \n";
        }
        echo $logString;
    }
}

