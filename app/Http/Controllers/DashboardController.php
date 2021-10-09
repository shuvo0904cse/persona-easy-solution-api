<?php

namespace App\Http\Controllers;

use App\Helpers\LogHelper;
use App\Helpers\MessageHelper;
use App\Helpers\UtilsHelper;
use App\Models\Category;
use App\Models\Money;
use App\Services\GenerateDefaultCategoryService;
use Faker\Provider\bn_BD\Utils;

class DashboardController extends Controller
{
    private $message;
    private $log;

    public function __construct()
    {
        $this->message = new MessageHelper();
        $this->log = new LogHelper();
    }

     /**
     * dashboard
     */
    public function dashboard()
    {
        try{
            $array = [
                "current_month"         => UtilsHelper::getCurrentMonthName(),
                "current_year"         => UtilsHelper::getCurrentYear(),
                "current_month_income"  => $this->moneyModel()->getTotalIncomeOrExpense("INCOME", UtilsHelper::getCurrentMonth()),
                "current_month_expense" => $this->moneyModel()->getTotalIncomeOrExpense("EXPENSE", UtilsHelper::getCurrentMonth()),
                "current_year_income"  => $this->moneyModel()->getTotalIncomeOrExpense("INCOME", "", UtilsHelper::getCurrentYear()),
                "current_year_expense" => $this->moneyModel()->getTotalIncomeOrExpense("EXPENSE", "", UtilsHelper::getCurrentYear()),
                "total_income"          => $this->moneyModel()->getTotalIncomeOrExpense("INCOME"),
                "total_expense"         => $this->moneyModel()->getTotalIncomeOrExpense("EXPENSE"),
                "last_transactions"  => $this->moneyModel()->getLastTransactions()
            ];
            
            return $this->message::successMessage("", $array);
        } catch (\Exception $e) {
            $this->log::error("dashboard", $e);
            return $this->message::errorMessage();
        }
    }

    private function moneyModel(){
        return new Money();
    }

    private function generateDefaultCategoryService(){
        return new GenerateDefaultCategoryService();
    }
}
