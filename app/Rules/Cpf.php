<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use Respect\Validation\Validator;

class Cpf implements Rule
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
            $validation = Validator::cpf()->validate($value);
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

        return 'CPF inválido';
    }
}
