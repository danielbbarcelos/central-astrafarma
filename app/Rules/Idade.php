<?php

namespace App\Rules;

use Carbon\Carbon;
use Illuminate\Contracts\Validation\Rule;
use Respect\Validation\Validator;

class Idade implements Rule
{
    private $min;
    private $max;
    private $errorMessage;

    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct($min = 0, $max = 100, $errorMessage = null)
    {
        $this->min = $min;
        $this->max = $max;
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
                $value = Carbon::createFromFormat('d/m/Y', $value)->format('Y-m-d');
            }
            catch(\Exception $ex)
            {
                $value = Carbon::createFromFormat('Y-m-d', $value)->format('Y-m-d');
            }

            $validation = Validator::age($this->min, $this->max)->validate($value);

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

        return 'Idade inválida';
    }
}
