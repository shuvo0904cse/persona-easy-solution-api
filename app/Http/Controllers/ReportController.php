<?php

namespace App\Http\Controllers;

use App\Helpers\LogHelper;
use App\Helpers\MessageHelper;
use App\Models\Category;
use App\Models\Money;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class ReportController extends Controller
{
    private $message;
    private $log;

    public function __construct()
    {
        $this->message = new MessageHelper();
        $this->log = new LogHelper();
    }

     /**
     * Money
     */
    public function money(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'group_by' => 'in:year,month'
        ], config("message.validation_message"));

        if ($validator->fails()) return $this->message::validationErrorMessage("", $validator->errors());

        try{
            $category = $request->category;
            $type = $request->type;
            $formDate = $request->from;
            $toDate = $request->to;
            $groupBy = $request->group_by;
            $year = $request->year;
    
            $money = Money::query()
                    ->join("categories", "categories.id", "=", "money.category_id");

            if(empty($groupBy)){
                $money = $money->select(
                    'money.id as money_id',
                    'money.title as money_title',
                    'money.amount as amount',
                    'money.category_id as category_id',
                    'categories.title as category_title',
                    'categories.type as category_type',
                    'money.created_at as created_at'
                );
            }else{
                if($groupBy == "month"){
                    $money = $money->select(
                        DB::raw("MONTHNAME(money.created_at) as month"),
                        DB::raw("SUM(amount) as total")
                    )->groupBy(DB::raw("MONTH(money.created_at)"));
                }
                if($groupBy == "year"){
                    $money = $money->select(
                        DB::raw("YEAR(money.created_at) as month"),
                        DB::raw("SUM(amount) as total")
                    )->groupBy(DB::raw("YEAR(money.created_at)"));
                }
            }     
                    
            if(!empty($category)){
                  $money = $money->whereIn("money.category_id", explode(",", $request->category));
            }
    
            if(!empty($formDate) && !empty($toDate)){
                  $money = $money->whereBetween('money.created_at', [$formDate, $toDate]);
            }
    
            if(!empty($type)){
                $money = $money->whereIn('categories.type', explode(",", $type));
            }

            if(!empty($year)){
                $money = $money->where(DB::raw("YEAR(money.created_at)"), $year);
            }
    
            $money = $money->orderBy("money.created_at", "DESC")->get();
            
            return $this->message::successMessage("", $money);
        } catch (\Exception $e) {
            $this->log::error("money", $e);
            return $this->message::errorMessage();
        }
    }

     /**
     * category
     */
    public function category(Request $request)
    {
        try{
            //request
            $formDate = $request->from;
            $toDate = $request->to;
            $categories = $request->categories;
            $categoryType = $request->category_type;

            $category = Category::query()
                        ->join("money", "money.category_id", "=", "categories.id")
                        ->select(
                            'categories.id as category_id',
                            'categories.title as category_title',
                            'categories.type as category_type',
                            DB::raw('sum(money.amount) as total')
                        )->groupBy("categories.id");
            //category type
            if(!empty($categoryType)) $category = $category->where("categories.type", $categoryType);
            
            //categories
            if(!empty($categories)) $category = $category->whereIn("money.category_id", explode(",", $categories));
            
            //from date - to date
            if(!empty($formDate) && !empty($toDate)) $category = $category->whereBetween('money.created_at', [$formDate, $toDate]);
    
            $category = $category->orderBy("total", "DESC")->get();
            
            return $this->message::successMessage("", $category);
        } catch (\Exception $e) {
            $this->log::error("category", $e);
            return $this->message::errorMessage();
        }
    }  
}
