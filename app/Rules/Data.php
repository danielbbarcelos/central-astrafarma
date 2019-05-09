<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use Respect\Validation\Validator;

class Data implements Rule
{


    private $errorMessage;

    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct($errorMessage = null)
    {
        $this->errorMessage = $errorMessage;
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        $validation = true;

        if($value !== null and $value !== '')
        {
            try
            {
                $validation = Validator::date('d/m/Y')->validate($value);
            }
            catch(\Exception $e)
            {
                $validation = Validator::date('Y-m-d')->validate($value);
            }
        }

       

        return $validation;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        if(isset($this->errorMessage))
        {
            return $this->errorMessage;
        }

        return 'Data inválida';
    }
}
