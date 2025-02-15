<?php

namespace Alemian95\LaravelEncryptableAttributes;

use Illuminate\Support\Facades\Crypt;

trait HasEncryptableAttributes
{

    public function getAttribute($key)
    {
        $value = parent::getAttribute($key);

        if (in_array($key, $this->encryptable ?? [])) {
            return $value ? Crypt::decrypt($value) : null;
        }

        return $value;
    }

    public function setAttribute($key, $value)
    {
        if (in_array($key, $this->encryptable ?? [])) {
            $value = $value ? Crypt::encrypt($value) : null;
        }

        return parent::setAttribute($key, $value);
    }

    public function attributesToArray()
    {
        $attributes = parent::attributesToArray();

        foreach ($this->encryptable ?? [] as $key) {
            if (isset($attributes[$key])) {
                try {
                    $attributes[$key] = Crypt::decrypt($attributes[$key]);
                } catch (\Exception $e) {
                    $attributes[$key] = $attributes[$key];
                }
            }
        }

        return $attributes;
    }

}