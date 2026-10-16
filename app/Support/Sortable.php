<?php

namespace App\Support;

use Illuminate\Database\Eloquent\Model;

class Sortable
{
    /**
     * Geser satu item ke atas/bawah, lalu rapikan urutan jadi 1..n.
     * $scope opsional untuk membatasi kelompok (misalnya anggota dalam satu divisi).
     */
    public static function move(string $modelClass, Model $item, string $direction, ?callable $scope = null): void
    {
        $query = $modelClass::query();
        if ($scope) {
            $scope($query);
        }

        $list = $query->orderBy('sort_order')->orderBy('id')->get()->all();

        $index = null;
        foreach ($list as $i => $model) {
            if ($model->getKey() === $item->getKey()) {
                $index = $i;
                break;
            }
        }
        if ($index === null) {
            return;
        }

        $swap = $direction === 'up' ? $index - 1 : $index + 1;
        if (! isset($list[$swap])) {
            return;
        }

        [$list[$index], $list[$swap]] = [$list[$swap], $list[$index]];

        foreach ($list as $position => $model) {
            if ((int) $model->sort_order !== $position + 1) {
                $model->forceFill(['sort_order' => $position + 1])->save();
            }
        }
    }
}