<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CustomerFormRequest extends FormRequest
{

    public function authorize()
    {
        return true;
    }

    public function rules() {
        $rules = [
            'name' => 'bail|required|string',
            'contact_no' => 'bail|required|numeric',
            'email' => 'bail|required|string',
            'farm_name' => 'bail|string',
            'address' => 'bail|required|string',
        ]; 
        return $rules;
    }

    // public function rules()
    // {
    //     $installment_plans = ['full','1','3','12'];
    //     return [
    //         'total_installment' => 'required_if:installment_plan,1,3,12',
    //         'th_date' => 'required_if:installment_plan,1,3,12|min:0|max:31',
    //         'commission_amount' => 'required_with:agent_id',
    //         'installment_plan' => [Rule::in($installment_plans),]
    //     ];   
    // }
}
