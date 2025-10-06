<?php

namespace Livingstoneco\Suspicion\Casts;

use Illuminate\Contracts\Database\Eloquent\CastsAttributes;

class SafeJson implements CastsAttributes
{
    public function get($model, $key, $value, $attributes)
    {
        return json_decode($value ?? 'null');
    }

    public function set($model, $key, $value, $attributes)
    {
        $value = $this->sanitizeUtf8($value);

        return json_encode(
            $value,
            JSON_UNESCAPED_UNICODE
                | JSON_INVALID_UTF8_SUBSTITUTE
        );
    }

    private function sanitizeUtf8($value)
    {
        if (is_string($value)) {
            $clean = @iconv('UTF-8', 'UTF-8//IGNORE', $value);
            return $clean === false ? $value : $clean;
        }

        if (is_array($value)) {
            foreach ($value as $k => $v) {
                $value[$k] = $this->sanitizeUtf8($v);
            }
            return $value;
        }

        if (is_object($value)) {
            foreach ($value as $k => $v) {
                $value->{$k} = $this->sanitizeUtf8($v);
            }
            return $value;
        }

        return $value;
    }
}
