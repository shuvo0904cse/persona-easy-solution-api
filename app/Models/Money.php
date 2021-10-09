<?php

namespace App\Models;

use App\Traits\BaseModel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Money extends Model
{
    use BaseModel;

    protected $table = 'money';

    protected $fillable = [
        'category_id',
        'amount',
        'title',
        'note'
    ];

    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id', 'id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    /**
     * Get Total Income Or Expense
     */
    public function getTotalIncomeOrExpense($type = "", $month = "", $year = "")
    {
        $query = DB::table('money')->join('categories', 'money.category_id', '=', 'categories.id');

        //if empty not empty
        if(!empty($type)) $query = $query->where('categories.type', '=', $type);

        //if month not empty
        if(!empty($month)) $query = $query->whereMonth('money.created_at', $month);

        //if year not empty
        if(!empty($year)) $query = $query->whereYear('money.created_at', $year);

        return $query->sum('money.amount');
    }

    /**
     * Get last ten transactions
     */
    public function getLastTransactions($limit = "10")
    {
        return DB::table('money')
               ->join('categories', 'money.category_id', '=', 'categories.id')
               ->orderByDesc("money.created_at")
               ->select("money.id", "money.title", "money.amount", "categories.title as category_title", "categories.type as category_type", "money.created_at")
               ->limit($limit)
               ->get();
    }
}
