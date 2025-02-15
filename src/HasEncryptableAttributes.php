<?php

namespace Alemian95\LaravelEncryptableAttributes;

use Exception;
use Illuminate\Support\Facades\Crypt;

/**
 * Trait HasEncryptableAttributes
 * 
 * @author Alessandro Mian <alessandromian95@gmail.com>
 *
 * Adds support for automatic encryption of attributes specified in an Eloquent model.
 */
trait HasEncryptableAttributes
{

    /**
     * Retrieves an attribute from the model and decrypts it if it is in the list of encrypted attributes.
     *
     * @param string $key The name of the attribute to be retrieved.
     * @return mixed The decrypted value of the attribute or the original value in case of an error.
     */
    public function getAttribute($key)
    {
        $value = parent::getAttribute($key);

        try {
            if (in_array($key, $this->encryptable ?? [])) {
                return $value ? Crypt::decrypt($value) : null;
            }
        } catch (Exception $e) {
            return $value;
        }

        return $value;
    }

    /**
     * Sets an attribute in the model, encrypting it if it is in the list of encrypted attributes.
     *
     * @param string $key The name of the attribute to be set.
     * @param mixed $value The value of the attribute.
     * @return mixed The encrypted value if the attribute is in the list, otherwise the original value.
     */
    public function setAttribute($key, $value)
    {
        if (in_array($key, $this->encryptable ?? [])) {
            $value = $value ? Crypt::encrypt($value) : null;
        }

        return parent::setAttribute($key, $value);
    }

    /**
     * Converts the attributes of the model into an array, decrypting those in the list of encrypted attributes.
     *
     * @return array An array with all the attributes of the model, with the encrypted ones decrypted.
     */
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