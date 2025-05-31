<?php

namespace App\Rules;

use Closure;
use App\Models\Platform;
use Illuminate\Contracts\Validation\ValidationRule;

class ContentCheckCharatersLengthRule implements ValidationRule
{
    protected array $platformIds =[];
    public function __construct(array|null $platformIds)
    {
        if($platformIds){

            $this->platformIds = $platformIds;
        }
    }
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string, ?string=): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
       $platform = null; 
$count=strlen($value);
if(count( $this->platformIds )>0){

    $platform = Platform::whereIn('id', $this->platformIds)->orderBy('char_limit')->first(['name','char_limit']);
}
        
        if($platform && $count>$platform->char_limit){
            $fail(":attribute Charaters Length is $count, maxmium length of ".$platform->name." is ".$platform->char_limit);
    
        }
    }
}
