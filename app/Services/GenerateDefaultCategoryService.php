<?php
namespace App\Services;

use App\Helpers\LogHelper;
use App\Helpers\MessageHelper;
use App\Models\Category;
use App\Models\Setting;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class GenerateDefaultCategoryService
{
    private $message;
    private $log;

    public function __construct()
    {
        $this->message = new MessageHelper();
        $this->log = new LogHelper();
    }
    
    /**
     * Get Logged In User Id
     */
    public function generate()
    {
        $categories = config("seeder");
        if(empty($categories)) $this->message::throwExceptionMessage(config("messages.not_exists"));

        DB::beginTransaction();
        try{
            //default message
            $message = config("message.default_category_already_generated");
            
            $setting = $this->getUserSetting();
            
            //if setting not exists
            if(empty($setting)) $setting = $this->storeUserSetting();

            //if generate_default_category false
            if($setting->generate_default_category == false){
                //store Income
                $this->storeDefaultIncome($categories['incomes']);

                //store Expense
                $this->storeDefaultExpense($categories['expenses']);

                //update setting
                $this->updateUserSetting($setting->id);
                //message
                $message = config("message.default_category_generated_successfully");
            }

            DB::commit();
            return $this->message::successMessage($message);
        } catch (\Exception $e) {
            DB::rollBack();
            $this->log::error("generate", $e);
            return $this->message::throwException($e);
        }
    }

    /**
     * Get User Setting
     */
    private function getUserSetting(){
        return $this->settingModel()->first();
    }

    /**
     * store User Setting
     */
    private function storeUserSetting(){
        return $this->settingModel()->storeData([
            "generate_default_category" => false
        ]);
    }

      /**
     * update User Setting
     */
    private function updateUserSetting($settingId){
        return $this->settingModel()->updateData([
            "generate_default_category" => true
        ], $settingId);
    }

    /**
     * Store Default Income
     */
    private function storeDefaultIncome($incomes){
        foreach($incomes as $income){
            $incomeArray = [
                "name"  => $income,
                "type"  => config("setting.income")
            ];
            $this->categoryModel()->storeData($incomeArray);
        }
    }

      /**
     * Store Default Expense
     */
    private function storeDefaultExpense($expenses){
        foreach($expenses as $expense){
            $expenseArray = [
                "name"  => $expense,
                "type"  => config("setting.expense")
            ];
            $this->categoryModel()->storeData($expenseArray);
        }
    }

    private function categoryModel(){
        return new Category();
    }

    private function settingModel(){
        return new Setting();
    }
}
