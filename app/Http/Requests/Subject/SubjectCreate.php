<?php

namespace App\Http\Requests\Subject;

use App\Helpers\Qs;
use App\Http\Requests\BaseFormRequestApi;

class SubjectCreate extends BaseFormRequestApi
{

    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => 'required|string|min:3',
            'my_class_id' => 'required',
            'slug' => 'nullable|string|min:3',
        ];
    }

    public function attributes()
    {
        return  [
            'my_class_id' => 'Class',
        //    'teacher_id' => 'Teacher',
            'slug' => 'Short Name',
        ];
    }

    protected function getValidatorInstance()
    {
        $input = $this->all();

        if(isset($input['teacher_id']) && !empty($input['teacher_id']))
            $input['teacher_id'] = $input['teacher_id'] ? Qs::decodeHash($input['teacher_id']) : NULL;

        $this->getInputSource()->replace($input);

        return parent::getValidatorInstance();
    }
    
}
